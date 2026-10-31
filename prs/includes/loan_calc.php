<?php
/*
 * Loan / advance installment calculation.
 *
 * A loan (loanmast) has an amount, a tenure (NOINST months), a yearly RATE % and a METHOD:
 *   NONE      - principal only
 *   REDUCING  - interest each month = outstanding principal x rate / 12
 *   FLAT      - interest each month = loan amount x rate / 12 (same every month)
 *   PRINFIRST - principal is recovered first; interest accrues on the reducing balance meanwhile
 *               and is recovered afterwards in INT_MONTHS equal installments
 *
 * Principal (premium) per month is amount / tenure, or the loan's PREMIUM when one is set;
 * the last installment takes whatever is left.
 * Installments already recorded (loandet PRIN / INTAMT) drive the state, so a manually
 * changed deduction simply shortens or lengthens the loan.
 */

function loanMethods()
{
    return array(
        'NONE' => 'No interest',
        'REDUCING' => 'Reducing balance',
        'FLAT' => 'Flat interest',
        'PRINFIRST' => 'Principal first, then interest',
    );
}

// Loan row ready for calculation; a loan type without an interest head charges no interest.
function loanNormalize($loan, $type)
{
    if (!$type || trim((string) $type['INT_HEAD']) === '' || !isset(loanMethods()[$loan['METHOD']])) {
        $loan['METHOD'] = 'NONE';
    }
    return $loan;
}

// Balance after the given installments (loandet rows ordered by SLNO).
function loanState($loan, $history)
{
    $state = array('outstanding' => round((float) $loan['AMT'], 2), 'accrued' => 0.0, 'intPaid' => 0.0, 'count' => 0);
    foreach ($history as $row) {
        $prin = $row['PRIN'] !== null ? (float) $row['PRIN'] : (float) $row['INSTAMT'];
        $state = loanApply($loan, $state, $prin, (float) $row['INTAMT']);
    }
    return $state;
}

// State after paying one month's principal and interest.
function loanApply($loan, $state, $prin, $int)
{
    if ($state['outstanding'] > 0) {
        // Interest accrues on the balance before this month's principal (used by PRINFIRST)
        $state['accrued'] = round($state['accrued'] + $state['outstanding'] * (float) $loan['RATE'] / 1200, 2);
    }
    $state['outstanding'] = round(max($state['outstanding'] - $prin, 0), 2);
    $state['intPaid'] = round($state['intPaid'] + $int, 2);
    $state['count']++;
    return $state;
}

// Equal installment of $total, capped at what is left; the last one takes the remainder.
function loanSlice($total, $installments, $left)
{
    if ($left <= 0) {
        return 0.0;
    }
    $amount = min(round($total / max(1, (int) $installments), 2), $left);
    return ($left - $amount < 1) ? $left : $amount;
}

// array('prin' => ..., 'int' => ...) due for the next month.
function loanNextInstallment($loan, $state)
{
    $amt = (float) $loan['AMT'];
    $rate = (float) $loan['RATE'];
    $out = $state['outstanding'];
    // A monthly premium set on the loan replaces amount / tenure
    $premium = isset($loan['PREMIUM']) ? (float) $loan['PREMIUM'] : 0;
    $prin = $premium > 0 ? loanSlice($premium, 1, $out) : loanSlice($amt, $loan['NOINST'], $out);

    switch ($loan['METHOD']) {
        case 'REDUCING':
            $int = $out > 0 ? round($out * $rate / 1200, 2) : 0.0;
            break;
        case 'FLAT':
            $int = $out > 0 ? round($amt * $rate / 1200, 2) : 0.0;
            break;
        case 'PRINFIRST':
            $int = $out > 0 ? 0.0 : loanSlice($state['accrued'], $loan['INT_MONTHS'], round($state['accrued'] - $state['intPaid'], 2));
            break;
        default:
            $int = 0.0;
    }
    return array('prin' => $prin, 'int' => $int);
}

function loanIsClosed($loan, $state)
{
    if ($state['outstanding'] > 0) {
        return false;
    }
    return $loan['METHOD'] !== 'PRINFIRST' || round($state['accrued'] - $state['intPaid'], 2) <= 0;
}

/*
 * An employee's loans as they stand on a payslip: taken by the payslip month, still running
 * or deducted in it. Each row: type, taken (date), amount, prin / int deducted this month,
 * balance after this month, installments left.
 */
function loanSlipSummary($db, $empno, $sheet_id, $monthEnd)
{
    $stmt = $db->prepare("
        SELECT l.*, lt.NAME AS TYPENAME, lt.INT_HEAD
        FROM loanmast l LEFT JOIN loantype lt ON lt.CODE = l.LTYPE
        WHERE l.EMPNO = :empno AND l.AMT > 0 AND (l.DTE IS NULL OR l.DTE <= :month_end)
        ORDER BY lt.SORT, l.DTE, l.LNO");
    $stmt->execute(array(':empno' => $empno, ':month_end' => $monthEnd));
    $hist = $db->prepare("SELECT * FROM loandet WHERE LNO = :lno AND (DTE IS NULL OR DTE <= :month_end) ORDER BY SLNO");

    $rows = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $loan) {
        $hist->execute(array(':lno' => $loan['LNO'], ':month_end' => $monthEnd));
        $history = $hist->fetchAll(PDO::FETCH_ASSOC);
        $loan = loanNormalize($loan, array('INT_HEAD' => $loan['INT_HEAD']));

        $prin = $int = 0.0;
        foreach ($history as $h) {
            if ((int) $h['SHEET_ID'] === (int) $sheet_id) {
                $prin += $h['PRIN'] !== null ? (float) $h['PRIN'] : (float) $h['INSTAMT'];
                $int += (float) $h['INTAMT'];
            }
        }
        $state = loanState($loan, $history);
        $closed = loanIsClosed($loan, $state);
        if (($closed || (int) $loan['FLAG'] === 1) && $prin + $int <= 0) {
            continue; // finished before this month
        }
        $left = 0;
        foreach (loanSchedule($loan, $history) as $s) {
            if ($s['status'] === 'Pending') $left++;
        }
        $rows[] = array(
            'code' => $loan['LTYPE'], 'type' => $loan['TYPENAME'] ?: $loan['LTYPE'], 'taken' => $loan['DTE'], 'amount' => (float) $loan['AMT'],
            'prin' => $prin, 'int' => $int, 'balance' => $state['outstanding'], 'left' => $closed ? 0 : $left,
        );
    }
    return $rows;
}

/*
 * Full repayment schedule: recorded installments followed by the projected ones.
 * Each row: slno, month, prin, int, total, balance, status (Paid / Manual / Pending).
 */
function loanSchedule($loan, $history, $maxRows = 600)
{
    $rows = array();
    $state = loanState($loan, array());
    $lastDate = null;
    foreach ($history as $row) {
        $prin = $row['PRIN'] !== null ? (float) $row['PRIN'] : (float) $row['INSTAMT'];
        $int = (float) $row['INTAMT'];
        $state = loanApply($loan, $state, $prin, $int);
        $lastDate = $row['DTE'];
        $rows[] = array(
            'slno' => (int) $row['SLNO'], 'month' => $row['DTE'] ? date('M Y', strtotime($row['DTE'])) : '',
            'prin' => $prin, 'int' => $int, 'total' => round($prin + $int, 2),
            'balance' => $state['outstanding'], 'status' => !empty($row['IS_MANUAL']) ? 'Manual' : 'Paid',
        );
    }

    // Projection starts the month after the last installment, or the loan's month
    $start = $lastDate ? strtotime(date('Y-m-01', strtotime($lastDate)) . ' +1 month') : strtotime(date('Y-m-01', strtotime($loan['DTE'] ?: 'now')));
    $slno = $rows ? end($rows)['slno'] : 0;
    for ($i = 0; !loanIsClosed($loan, $state) && count($rows) < $maxRows; $i++) {
        $next = loanNextInstallment($loan, $state);
        if ($next['prin'] + $next['int'] <= 0) {
            break;
        }
        $state = loanApply($loan, $state, $next['prin'], $next['int']);
        $rows[] = array(
            'slno' => ++$slno, 'month' => date('M Y', strtotime("+$i month", $start)),
            'prin' => $next['prin'], 'int' => $next['int'], 'total' => round($next['prin'] + $next['int'], 2),
            'balance' => $state['outstanding'], 'status' => 'Pending',
        );
    }
    return $rows;
}
