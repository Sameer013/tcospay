<?php
/*
 * Shared data loading for the salary reports.
 *
 * Reads the payroll that ajax_processing.php stored totals from trnsmst and the
 * employee's own salary components from trnsdet1 (earnings) / trnsdet2 (deductions).
 * Master tables are LEFT JOINed so a missing designation, bank or grade never hides
 * an employee from a report.
 */

/*
 * ORDER OF COMPONENTS ON PAY SLIPS AND REPORTS 
 */
$REPORT_EARNING_ORDER = array('VDA','FDA', 'VDA', 'HR', 'HM', 'SA', 'SA', 'TA', 'OT Allow', 'Wash Car');
$REPORT_DEDUCTION_ORDER = array('PF', 'ESIC', 'iTax', 'PF Loan', 'PF Int', 'SPL Loan', 'SPL Int', 'Com Loan', 'Com Int', 'Advance');

// Sorts component names by the order list above.
function sortComponents($names, $order)
{
    $rank = array();
    foreach ($order as $i => $name) {
        $rank[strtoupper($name)] = $i;
    }
    usort($names, function ($a, $b) use ($rank) {
        $ra = isset($rank[strtoupper($a)]) ? $rank[strtoupper($a)] : PHP_INT_MAX;
        $rb = isset($rank[strtoupper($b)]) ? $rank[strtoupper($b)] : PHP_INT_MAX;
        return $ra !== $rb ? $ra - $rb : strcasecmp($a, $b);
    });
    return $names;
}

// Re-keys an amounts map (name => amount) in the given order.
function sortAmounts($amounts, $order)
{
    $sorted = array();
    foreach (sortComponents(array_keys($amounts), $order) as $name) {
        $sorted[$name] = $amounts[$name];
    }
    return $sorted;
}

function reportMonthName($num)
{
    return date('M', mktime(0, 0, 0, (int) $num, 1));
}

// Validated month/year from the query string, or stops with an alert.
function reportPeriod()
{
    $month = filter_var(isset($_GET['month']) ? $_GET['month'] : null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 12)));
    $year = filter_var(isset($_GET['year']) ? $_GET['year'] : null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 2000, 'max_range' => 2100)));
    if (!$month || !$year) {
        reportStop('Please select a valid month and year');
    }
    return array($month, $year);
}

function reportStop($message)
{
    echo '<script type="text/javascript">alert(' . json_encode($message) . ');</script>';
    die();
}

function reportSheetId($db, $month, $year)
{
    $stmt = $db->prepare("SELECT sheet_id FROM sheet WHERE mnth = :mnth AND yr = :yr LIMIT 1");
    $stmt->execute(array(':mnth' => $month, ':yr' => $year));
    $sheet_id = $stmt->fetchColumn();
    if (!$sheet_id) {
        reportStop('No Month Data');
    }
    return $sheet_id;
}

/*
 * Returns array(
 *   'rows'           => one row per employee, with 'earnings' and 'deductions' maps (name => amount),
 *   'earningHeads'   => earning component names used this month, in first-seen order,
 *   'deductionHeads' => deduction component names used this month, in first-seen order,
 * )
 * $empno limits it to one employee (employee login).
 */
function loadPayroll($db, $sheet_id, $empno = null)
{
    $sql = "
        SELECT t.TRNSNO, t.EMPNO, e.NAME, d.DESCR AS DESIGNATION, g.Grade AS GRADE, b.DESCR AS BANK,
            e.ACCNO, e.PFACNO, e.uanNo, e.PAN, e.SEFNO AS ESIC_NO, e.PHONE1,
            e.ocl, e.oml, e.oel, e.ospl,
            t.basic AS BASIC, t.LSA, t.lwop, t.lwopamt, t.advAmt, t.adj,
            t.gross, t.total_deduction, t.netAmt, t.pfsal, t.esisal,
            COALESCE(sd.cl, 0) AS cl, COALESCE(sd.el, 0) AS el, COALESCE(sd.med_leave, 0) AS med_leave,
            COALESCE(sd.spleave, 0) AS spleave, COALESCE(sd.hdays, 0) AS hdays, COALESCE(sd.off_days, 0) AS off_days,
            COALESCE(sd.attnd, 0) AS dwork,
            ROUND(COALESCE(sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays, 0), 2) AS dpaid,
            COALESCE(e.ocl - sd.cl, 0) AS ccl, COALESCE(e.oml - sd.med_leave, 0) AS cml, COALESCE(e.oel - sd.el, 0) AS cel
        FROM trnsmst t
        JOIN empmast e ON t.EMPNO = e.EMPNO
        LEFT JOIN dsgmast d ON e.DSGCODE = d.DCODE
        LEFT JOIN bankmast b ON e.BID = b.BID
        LEFT JOIN grademaster g ON e.grade = g.Gradecode
        LEFT JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
        WHERE t.sheet_id = :sheet_id" . ($empno !== null ? " AND t.EMPNO = :empno" : "") . "
        ORDER BY d.DESCR, t.EMPNO";
    $params = array(':sheet_id' => $sheet_id);
    if ($empno !== null) {
        $params[':empno'] = $empno;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $rows = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['earnings'] = array();
        $row['deductions'] = array();
        $rows[$row['TRNSNO']] = $row;
    }

    $earningHeads = array();
    $deductionHeads = array();
    $lines = array('trnsdet1' => 'earnings', 'trnsdet2' => 'deductions');
    foreach ($lines as $table => $key) {
        $stmt = $db->prepare("
            SELECT d.TRNSNO, d.DESCR, d.AMOUNT
            FROM $table d
            JOIN trnsmst t ON d.TRNSNO = t.TRNSNO
            WHERE t.sheet_id = :sheet_id AND TRIM(d.DESCR) != ''
            ORDER BY d.TRNSNO");
        $stmt->execute(array(':sheet_id' => $sheet_id));
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $line) {
            if (!isset($rows[$line['TRNSNO']])) {
                continue;
            }
            $rows[$line['TRNSNO']][$key][$line['DESCR']] = (float) $line['AMOUNT'];
            if ($key === 'earnings') {
                $earningHeads[$line['DESCR']] = true;
            } else {
                $deductionHeads[$line['DESCR']] = true;
            }
        }
    }

    global $REPORT_EARNING_ORDER, $REPORT_DEDUCTION_ORDER;
    foreach ($rows as &$row) {
        $row['earnings'] = sortAmounts($row['earnings'], $REPORT_EARNING_ORDER);
        $row['deductions'] = sortAmounts($row['deductions'], $REPORT_DEDUCTION_ORDER);
    }
    unset($row);

    return array(
        'rows' => array_values($rows),
        'earningHeads' => sortComponents(array_keys($earningHeads), $REPORT_EARNING_ORDER),
        'deductionHeads' => sortComponents(array_keys($deductionHeads), $REPORT_DEDUCTION_ORDER),
    );
}

// Amount of one component for a row (0 when the employee doesn't have it).
function headAmount($row, $type, $head)
{
    return isset($row[$type][$head]) ? $row[$type][$head] : 0;
}
