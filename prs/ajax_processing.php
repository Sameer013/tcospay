<?php
/*
 * Monthly payroll processing.
 *
 * For every employee on the month's attendance sheet:
 *   - basic comes from empmast
 *   - global heads (gblall) apply to everyone
 *   - individual heads (indall) are the employee's own salary components; a head
 *     with the same name as a global one overrides it for that employee
 *   - a head is either '%' of basic or a fixed amount; a head with no value is 0
 *   - 'a' heads are earnings (trnsdet1), 'd' heads are deductions (trnsdet2)
 *
 *   gross pay       = basic + LSA + all earnings
 *   gross deduction = all deductions + advance instalment + LWOP amount (basic / days * LWOP days)
 *   net pay         = gross pay - gross deduction
 */
session_start();
header('Content-Type: application/json');

function respond($status, $msg, $extra = array())
{
    http_response_code($status);
    echo json_encode(array_merge(array('msg' => $msg, 'status' => $status), $extra));
    exit;
}

if (!isset($_SESSION['user'])) {
    respond(401, 'Please login first');
}

$selectedMonth = filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 12)));
$selectedYear = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT, array('options' => array('min_range' => 2000, 'max_range' => 2100)));
if (!$selectedMonth || !$selectedYear) {
    respond(400, 'Invalid month or year');
}

// Builds the list of heads for one employee: global heads first, then individual heads
// (an individual head replaces a global head of the same name).
function employeeHeads($globalHeads, $individualHeads)
{
    $heads = array();
    foreach (array($globalHeads, $individualHeads) as $source) {
        foreach ($source as $head) {
            $name = trim((string) $head['DESCR']);
            $type = strtolower(trim((string) $head['ALLREDNFLAG']));
            if ($name === '' || ($type !== 'a' && $type !== 'd')) {
                continue; // blank / incomplete component rows
            }
            $heads[strtoupper($name)] = array(
                'name' => $name,
                'type' => $type,
                'percent' => trim((string) $head['PRCAMTFLAG']) === '%',
                'value' => (float) $head['ALLOWANCE'], // NULL -> 0
            );
        }
    }
    return $heads;
}

// Inserts rows into a trnsdet table in chunks.
function insertLines($db, $table, $lines)
{
    foreach (array_chunk($lines, 500) as $chunk) {
        $sql = "INSERT INTO $table (TRNSNO, DESCR, AMOUNT) VALUES " . implode(',', array_fill(0, count($chunk), '(?, ?, ?)'));
        $params = array();
        foreach ($chunk as $line) {
            array_push($params, $line[0], $line[1], $line[2]);
        }
        $db->prepare($sql)->execute($params);
    }
}

try {
    include('includes/dbconn.php');

    $stmt = $db->prepare("SELECT sheet_id FROM sheet WHERE mnth = :mnth AND yr = :yr LIMIT 1");
    $stmt->execute(array(':mnth' => $selectedMonth, ':yr' => $selectedYear));
    $sheet_id = $stmt->fetchColumn();
    if (!$sheet_id) {
        respond(404, 'No Month Data', array('month' => $selectedMonth, 'year' => $selectedYear));
    }

    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
    $mnthyr = date('M', mktime(0, 0, 0, $selectedMonth, 1)) . ' ' . $selectedYear;

    // Employees on this month's sheet
    $stmt = $db->prepare("
        SELECT e.EMPNO, COALESCE(e.BASIC, 0) AS BASIC, COALESCE(sd.lwop, 0) AS LWOP,
            COALESCE(get_lsaAmt(e.EMPNO), 0) AS LSA, COALESCE(get_advInst(e.EMPNO), 0) AS ADV
        FROM sheet_det sd
        JOIN empmast e ON sd.empno = e.EMPNO
        WHERE sd.sheet_id = :sheet_id");
    $stmt->execute(array(':sheet_id' => $sheet_id));
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $globalHeads = $db->query("SELECT DESCR, PRCAMTFLAG, ALLREDNFLAG, ALLOWANCE FROM gblall ORDER BY CODE")->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        SELECT i.EMPNO, i.DESCR, i.PRCAMTFLAG, i.ALLREDNFLAG, i.ALLOWANCE
        FROM indall i
        JOIN sheet_det sd ON sd.empno = i.EMPNO AND sd.sheet_id = :sheet_id
        ORDER BY i.id");
    $stmt->execute(array(':sheet_id' => $sheet_id));
    $individualHeads = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $individualHeads[$row['EMPNO']][] = $row;
    }

    $stmt = $db->prepare("SELECT EMPNO, TRNSNO FROM trnsmst WHERE sheet_id = :sheet_id");
    $stmt->execute(array(':sheet_id' => $sheet_id));
    $trnsnoByEmp = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $db->beginTransaction();

    // One trnsmst row per employee per month
    $stmt_insert = $db->prepare("INSERT INTO trnsmst (EMPNO, sheet_id, basic, MTHYR, lwop, DTE) VALUES (:empno, :sheet_id, :basic, :mnthyr, :lwop, NOW())");
    foreach ($employees as $emp) {
        if (!isset($trnsnoByEmp[$emp['EMPNO']])) {
            $stmt_insert->execute(array(':empno' => $emp['EMPNO'], ':sheet_id' => $sheet_id, ':basic' => $emp['BASIC'], ':mnthyr' => $mnthyr, ':lwop' => $emp['LWOP']));
            $trnsnoByEmp[$emp['EMPNO']] = $db->lastInsertId();
        }
    }

    // Rebuild this month's lines so removed components don't linger
    foreach (array('trnsdet1', 'trnsdet2') as $table) {
        $db->prepare("DELETE d FROM $table d JOIN trnsmst t ON d.TRNSNO = t.TRNSNO WHERE t.sheet_id = :sheet_id")
            ->execute(array(':sheet_id' => $sheet_id));
    }

    $earningLines = array();
    $deductionLines = array();
    $stmt_update = $db->prepare("
        UPDATE trnsmst SET basic = :basic, lwop = :lwop, DTE = NOW(),
            DA = :DA, HRA = :HRA, TA = :TA, SPL = :SPL, PF = :PF, ESIC = :ESIC, iTax = :iTax,
            LSA = :LSA, lwopamt = :lwopamt, advAmt = :advAmt, pfsal = :PF,
            gross = :gross, total_earning = :gross, total_deduction = :total_deduction,
            netAmt = :net, MNTHSAL = :mnthsal
        WHERE TRNSNO = :trnsno");

    foreach ($employees as $emp) {
        $trnsno = $trnsnoByEmp[$emp['EMPNO']];
        $basic = (float) $emp['BASIC'];
        $empIndividual = isset($individualHeads[$emp['EMPNO']]) ? $individualHeads[$emp['EMPNO']] : array();

        $earnings = 0;
        $deductions = 0;
        $amounts = array();
        // foreach (employeeHeads($globalHeads, $empIndividual) as $key => $head) {
        //     $amount = round($head['percent'] ? $basic * $head['value'] / 100 : $head['value']);
        //     $amounts[$key] = $amount;
        //     if ($head['type'] === 'a') {
        //         $earnings += $amount;
        //         $earningLines[] = array($trnsno, $head['name'], $amount);
        //     } else {
        //         $deductions += $amount;
        //         $deductionLines[] = array($trnsno, $head['name'], $amount);
        //     }
        // }

        $heads = employeeHeads($globalHeads, $empIndividual);

        // Pass 1: allowances (VDA...), so their amounts are known before deductions
        foreach ($heads as $key => $head) {
            if ($head['type'] !== 'a') {
                continue;
            }
            $amount = round($head['percent'] ? $basic * $head['value'] / 100 : $head['value'], 2);
            $amounts[$key] = $amount;
            $earnings += $amount;
            $earningLines[] = array($trnsno, $head['name'], $amount);
        }

        // Pass 2: deductions. PF is a % of (basic + VDA); other % deductions stay a % of basic.
        $vda = isset($amounts['VDA']) ? $amounts['VDA'] : 0;
        foreach ($heads as $key => $head) {
            if ($head['type'] !== 'd') {
                continue;
            }
            $base = ($key === 'PF') ? $basic + $vda : $basic;
            $amount = round($head['percent'] ? $base * $head['value'] / 100 : $head['value'], 2);
            $amounts[$key] = $amount;
            $deductions += $amount;
            $deductionLines[] = array($trnsno, $head['name'], $amount);
        }

        $advance = (float) $emp['ADV'];
        if ($advance > 0) {
            $deductionLines[] = array($trnsno, 'Advance', $advance);
        }

        $lsa = (float) $emp['LSA'];
        $lwopAmt = round($basic * (float) $emp['LWOP'] / $daysInMonth, 2);
        $gross = $basic + $lsa + $earnings;
        $grossDeduction = $deductions + $advance + $lwopAmt;
        $net = $gross - $grossDeduction;

        // Fixed columns are still read by the reports; fill them from the matching heads
        $col = function ($name) use ($amounts) {
            return isset($amounts[$name]) ? $amounts[$name] : 0;
        };
        $stmt_update->execute(array(
            ':basic' => $basic, ':lwop' => $emp['LWOP'],
            ':DA' => $col('DA') ?: $col('VDA'), ':HRA' => $col('HRA'), ':TA' => $col('TA'), ':SPL' => $col('SPL'),
            ':PF' => $col('PF'), ':ESIC' => $col('ESIC'), ':iTax' => $col('ITAX'),
            ':LSA' => $lsa, ':lwopamt' => $lwopAmt, ':advAmt' => $advance,
            ':gross' => $gross, ':total_deduction' => $grossDeduction,
            ':net' => $net, ':mnthsal' => max($net, 0), // MNTHSAL is unsigned
            ':trnsno' => $trnsno,
        ));
    }

    insertLines($db, 'trnsdet1', $earningLines);
    insertLines($db, 'trnsdet2', $deductionLines);

    // Record this month's advance instalments
    $stmt_advPay = $db->prepare("CALL sp_advPayment(:empNo)");
    foreach ($employees as $emp) {
        if ($emp['ADV'] > 0) {
            $stmt_advPay->execute(array(':empNo' => $emp['EMPNO']));
            $stmt_advPay->closeCursor();
        }
    }

    $db->commit();

    respond(200, 'Querry Executed Successfully', array(
        'month' => $selectedMonth, 'year' => $selectedYear, 'employees' => count($employees),
    ));
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    respond(500, 'Query Failed: ' . $e->getMessage(), array('month' => $selectedMonth, 'year' => $selectedYear));
}
