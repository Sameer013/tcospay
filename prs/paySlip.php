<?php
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user'])) {
    header("location:login.php");
    exit;
}
// One payslip per page: employee details, attendance, earnings / deductions, running loans, net pay
$userId = $_SESSION['user'];
require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
require_once 'includes/org.php';
require_once "includes/payroll_report.php";
require_once 'includes/loan_calc.php';

// Aadhaar is printed as XXXX XXXX 1234 (UIDAI advises masking it on documents); false prints all 12 digits
define('SLIP_MASK_AADHAAR', true);
// Earnings / deductions boxes always have this many rows, so every slip has the same layout
define('SLIP_COMPONENT_ROWS', 12);
// Names printed on the slip for salary heads; the heads themselves keep their names (payroll and reports rely on them).
// Any other head ending in "Loan" (SPL Loan, Com Loan, ...) is printed as "... Loan Premium".
$SLIP_LABELS = array(
    'PF' => 'PF Contribution',
);

list($selectedMonth, $selectedYear) = reportPeriod();
$month = reportMonthName($selectedMonth);
$monthLong = date('F', mktime(0, 0, 0, $selectedMonth, 1));
$monthEnd = date('Y-m-t', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear));
$sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

// Admins get every employee; an employee login gets only their own slip
$isAdmin = ($userId == "Admin" || $userId == "Super Admin");
$payroll = loadPayroll($db, $sheet_id, $isAdmin ? null : $userId);
$rows = $payroll["rows"];
if (empty($rows)) {
    reportStop("No data found for " . $month . " " . $selectedYear);
}

// Aadhaar / joining date are only needed here; AADHAR comes from migrations/2026_10_31_payslip_staff_aadhar.sql
try {
    $empExtra = $db->prepare("SELECT AADHAR, DOJ FROM empmast WHERE EMPNO = :empno");
    $empExtra->execute(array(':empno' => 0));
} catch (PDOException $e) {
    $empExtra = $db->prepare("SELECT NULL AS AADHAR, DOJ FROM empmast WHERE EMPNO = :empno");
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(12, 12, 12);
$pdf->SetAutoPageBreak(true, 12);
$pdf->SetTitle('Payslip ' . $monthLong . ' ' . $selectedYear);

foreach ($rows as $row) {
    $empExtra->execute(array(':empno' => $row['EMPNO']));
    $row += $empExtra->fetch(PDO::FETCH_ASSOC) ?: array('AADHAR' => null, 'DOJ' => null);
    $loans = loanSlipSummary($db, $row['EMPNO'], $sheet_id, $monthEnd);
    $pdf->AddPage();
    $pdf->SetFont('freesans', '', 9);
    $pdf->writeHTML(slipHtml($row, $loans, $monthLong . ' ' . $selectedYear), true, false, true, false, '');
}
$pdf->Output('PaySlip_' . $month . '_' . $selectedYear . '.pdf', 'I');

// 1,23,456.00
function inr($amount)
{
    list($int, $dec) = explode('.', number_format(abs((float) $amount), 2, '.', ''));
    if (strlen($int) > 3) {
        $int = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int, 0, -3)) . ',' . substr($int, -3);
    }
    return ((float) $amount < 0 ? '-' : '') . $int . '.' . $dec;
}

// Rupees One Lakh Two Thousand ... Only
function amountInWords($amount)
{
    $ones = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
    $tens = array('', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
    $two = function ($n) use ($ones, $tens) {
        return $n < 20 ? $ones[$n] : trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
    };
    $three = function ($n) use ($two, $ones) {
        return trim((intdiv($n, 100) ? $ones[intdiv($n, 100)] . ' Hundred ' : '') . $two($n % 100));
    };
    list($rupees, $paise) = array_map('intval', explode('.', number_format(max((float) $amount, 0), 2, '.', '')));
    $parts = array();
    foreach (array(10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand') as $div => $name) {
        if ($rupees >= $div) {
            $parts[] = $three(intdiv($rupees, $div)) . ' ' . $name;
            $rupees %= $div;
        }
    }
    if ($rupees) {
        $parts[] = $three($rupees);
    }
    return 'Rupees ' . ($parts ? implode(' ', $parts) : 'Zero') . ($paise ? ' and ' . $two($paise) . ' Paise' : '') . ' Only';
}

function slipDate($date)
{
    return $date && $date !== '0000-00-00' ? date('d M Y', strtotime($date)) : 'NA';
}

function slipAadhaar($aadhaar)
{
    $digits = preg_replace('/\D/', '', (string) $aadhaar);
    if (strlen($digits) !== 12) {
        return $digits !== '' ? $digits : 'NA';
    }
    return SLIP_MASK_AADHAAR ? 'XXXX XXXX ' . substr($digits, 8) : implode(' ', str_split($digits, 4));
}

// Black and white: thin black rules, no fills
function slipHtml($row, $loans, $period)
{
    $e = function ($v) {
        return htmlspecialchars($v === null || trim((string) $v) === '' ? 'NA' : (string) $v, ENT_QUOTES, 'UTF-8');
    };
    $rule = 'border:0.5px solid #000000;';
    $under = 'border-bottom:0.3px solid #000000;';
    $gap = '<div style="font-size:7pt;">&nbsp;</div>';

    // Header
    $html = '<table cellpadding="0" cellspacing="0"><tr>'
        . '<td width="65%"><span style="font-size:15pt;font-weight:bold;">' . $e(ORG_NAME) . '</span><br>'
        . '<span style="font-size:9pt;">' . $e(ORG_ADDRESS) . '</span></td>'
        . '<td width="35%" align="right"><span style="font-size:13pt;font-weight:bold;">PAYSLIP</span></td>'
        . '</tr></table>'
        . '<table cellpadding="0" cellspacing="0"><tr><td style="border-bottom:1px solid #000000;font-size:4pt;">&nbsp;</td></tr></table>' . $gap;

    // Employee details: label / value pairs, two per line
    $details = array(
        array('Employee Name', $row['NAME'], 'Employee Code', $row['EMPNO']),
        array('Post', $row['DESIGNATION'], 'Grade', $row['GRADE']),
        array('Date of Joining', slipDate($row['DOJ']), 'Mobile', $row['PHONE1']),
        array('PAN', $row['PAN'], 'Aadhaar', slipAadhaar($row['AADHAR'])),
        array('Bank', $row['BANK'], 'Account No.', $row['ACCNO']),
    );
    // The pay month heads the details, where the old slip had it
    $html .= '<table cellpadding="3" cellspacing="0" style="' . $rule . '">'
        . '<tr><td colspan="4" width="100%" style="' . $under . '">Pay Period: <b>' . $e($period) . '</b></td></tr>';
    foreach ($details as $d) {
        $html .= '<tr><td width="18%" style="' . $under . '">' . $d[0] . '</td><td width="32%" style="' . $under . '"><b>' . $e($d[1]) . '</b></td>'
            . '<td width="18%" style="' . $under . '">' . $d[2] . '</td><td width="32%" style="' . $under . '"><b>' . $e($d[3]) . '</b></td></tr>';
    }
    $html .= '</table>' . $gap;

    // Loans: always PF, SPL and Comm; NA when the employee has none of that type
    $html .= '<table cellpadding="3" cellspacing="0" style="' . $rule . '">'
        . '<tr><td width="25%" style="' . $under . '"><b>Loan</b></td><td width="25%" style="' . $under . '"><b>Taken On</b></td>'
        . '<td width="25%" align="right" style="' . $under . '"><b>Balance (₹)</b></td><td width="25%" align="center" style="' . $under . '"><b>Installments Left</b></td></tr>';
    foreach (array('PF' => 'PF Loan', 'SPL' => 'SPL Loan', 'COM' => 'Comm Loan') as $code => $label) {
        $ofType = array_filter($loans, function ($l) use ($code) {
            return $l['code'] === $code;
        });
        if (!$ofType) {
            $html .= '<tr><td>' . $label . '</td><td>NA</td><td align="right">NA</td><td align="center">NA</td></tr>';
        }
        foreach ($ofType as $l) {
            $html .= '<tr><td>' . $label . '</td><td>' . slipDate($l['taken']) . '</td>'
                . '<td align="right"><b>' . inr($l['balance']) . '</b></td><td align="center"><b>' . $l['left'] . '</b></td></tr>';
        }
    }
    $html .= '</table><div style="font-size:7pt;">Balance and installments left are after this month\'s deduction.</div>' . $gap;

    // Attendance: days worked by type, then the payable days (ML / DL are not recorded, so 0)
    $worked = array('PRESENT' => $row['dwork'], 'LWOP' => $row['lwop'], 'CL' => $row['cl'], 'EL' => $row['el'],
        'MED' => $row['med_leave'], 'ML' => 0, 'DL' => 0, 'SPL' => $row['spleave'], 'WO' => $row['off_days'], 'HOLIDAY' => $row['hdays']);
    $cell = 'border:0.3px solid #000000;';
    $html .= '<table cellpadding="3" cellspacing="0" style="' . $rule . 'font-size:8.5pt;">'
        . '<tr><td colspan="' . count($worked) . '" width="86%" align="center" style="' . $cell . '"><b>WORKED</b></td>'
        . '<td width="14%" align="center" style="' . $cell . '"><b>PAYABLE</b></td></tr><tr>';
    foreach (array_keys($worked) as $label) {
        $width = in_array($label, array('PRESENT', 'HOLIDAY')) ? '10.5%' : '8.125%';
        $html .= '<td width="' . $width . '" align="center" style="' . $cell . '">' . $label . '</td>';
    }
    $html .= '<td width="14%" align="center" style="' . $cell . '">DAYS</td></tr><tr>';
    foreach ($worked as $value) {
        $html .= '<td align="center" style="' . $cell . '"><b>' . number_format((float) $value, 2) . '</b></td>';
    }
    $html .= '<td align="center" style="' . $cell . '"><b>' . number_format((float) $row['dpaid'], 2) . '</b></td></tr></table>' . $gap . $gap;

    // Earnings | gap | Deductions
    $earnings = array('Basic' => $row['BASIC']);
    if ($row['LSA'] > 0) {
        $earnings['Long Service Allowance'] = $row['LSA'];
    }
    $earnings += $row['earnings'];
    $deductions = $row['lwopamt'] > 0 ? array('LWOP' => $row['lwopamt']) : array();
    $deductions += $row['deductions'];
    // Components that came to nothing this month are left off the slip
    $nonZero = function ($amount) {
        return round((float) $amount, 2) != 0;
    };
    $earnings = array_filter($earnings, $nonZero);
    $deductions = array_filter($deductions, $nonZero);
    global $SLIP_LABELS;
    $relabel = function ($items) use ($SLIP_LABELS) {
        $out = array();
        foreach ($items as $name => $amount) {
            if (isset($SLIP_LABELS[$name])) {
                $name = $SLIP_LABELS[$name];
            } elseif (preg_match('/\bloan$/i', trim($name))) {
                $name = trim($name) . ' Premium';
            }
            $out[$name] = $amount;
        }
        return $out;
    };
    $earnings = $relabel($earnings);
    $deductions = $relabel($deductions);
    $lines = max(count($earnings), count($deductions), SLIP_COMPONENT_ROWS);

    $column = function ($title, $items, $totalLabel, $total) use ($e, $lines, $rule) {
        $t = '<table cellpadding="3" cellspacing="0" style="' . $rule . '">'
            . '<tr><td width="62%" style="border-bottom:0.8px solid #000000;"><b>' . $title . '</b></td>'
            . '<td width="38%" align="right" style="border-bottom:0.8px solid #000000;"><b>Amount (₹)</b></td></tr>';
        $names = array_keys($items);
        for ($i = 0; $i < $lines; $i++) {
            $name = isset($names[$i]) ? $names[$i] : null;
            $t .= '<tr><td>' . ($name !== null ? $e($name) : '&nbsp;') . '</td>'
                . '<td align="right">' . ($name !== null ? inr($items[$name]) : '') . '</td></tr>';
        }
        return $t . '<tr><td style="border-top:0.8px solid #000000;"><b>' . $totalLabel . '</b></td>'
            . '<td align="right" style="border-top:0.8px solid #000000;"><b>' . inr($total) . '</b></td></tr></table>';
    };
    $html .= '<table cellpadding="0" cellspacing="0"><tr>'
        . '<td width="48%">' . $column('Earnings', $earnings, 'Gross Earnings', $row['gross']) . '</td>'
        . '<td width="4%"></td>'
        . '<td width="48%">' . $column('Deductions', $deductions, 'Total Deductions', $row['total_deduction']) . '</td>'
        . '</tr></table>' . $gap . $gap;

    // Net pay, on the right
    $html .= '<table cellpadding="0" cellspacing="0"><tr><td width="48%"></td><td width="52%">'
        . '<table cellpadding="6" cellspacing="0" style="border:1px solid #000000;">'
        . '<tr><td width="45%" style="font-size:11pt;"><b>NET PAY</b></td>'
        . '<td width="55%" align="right" style="font-size:14pt;"><b>₹ ' . inr($row['netAmt']) . '</b></td></tr>'
        . '<tr><td colspan="2" style="font-size:8pt;">' . $e(amountInWords($row['netAmt'])) . '</td></tr>'
        . '</table></td></tr></table>' . $gap . $gap
        . '<div style="font-size:9pt;text-align:center;">This document is electronically generated, and no signature is required.</div>';

    return $html;
}
