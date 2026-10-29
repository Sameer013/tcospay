<?php
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user'])) {
    header("location:login.php");
    exit;
}
// create another payslip
$userId = $_SESSION['user'];
require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
require_once 'includes/org.php';
require_once "includes/payroll_report.php";

list($selectedMonth, $selectedYear) = reportPeriod();
$month = reportMonthName($selectedMonth);
$sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

// Admins get every employee; an employee login gets only their own slip
$isAdmin = ($userId == "Admin" || $userId == "Super Admin");
$payroll = loadPayroll($db, $sheet_id, $isAdmin ? null : $userId);
$rows = $payroll["rows"];
if (empty($rows)) {
    reportStop("No data found for " . $month . " " . $selectedYear);
}
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// for ($index = 0; $index < count($rows); $index += 2) {
for ($index = 0; $index < count($rows); $index++) {
    $pdf->AddPage();
    printData($pdf, $rows[$index], $index, $selectedYear, $month);
}


function printData($pdf, $row, $index, $selectedYear, $month)
{

    $pdf->SetFillColor(220, 220, 220);
    $imagePath = 'dist/images/tata1.png';
    $pdf->Image($imagePath, 10, 15, 40, 20);
    //   $pdf->Line(10, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
    $pdf->SetFont('freesans', '', 15);
    $pdf->Cell(0, 15, ORG_NAME, 0, 1, 'C');
    $pdf->SetY($pdf->GetY() - 9);
    $pdf->SetFont('freesans', '', 12);
    $pdf->Cell(0, 15, ORG_ADDRESS, 0, 1, 'C');
    $pdf->SetY($pdf->GetY() - 2);
    $pdf->SetFont('freesans', 'BU', 15);
    $pdf->Cell(0, 0, "PAYMENT SLIP", 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(0, 5, "SALARY FOR THE MONTH OF : ", 1, 0, 'L');
    $pdf->SetX(80);
    $pdf->Cell(0, 5, $month . " - " . $selectedYear, 1, 1, 'L');

    $pdf->Cell(0, 5, "NAME : ", 1, 0, 'L');
    $pdf->SetX(35);
    $pdf->Cell(0, 5, $row['NAME'], 1, 0, 'L');
    $pdf->SetX(140);
    $pdf->Cell(0, 5, "MOBILE NO : ", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(0, 5, $row['PHONE1'], 1, 1, 'L');

    $pdf->Cell(0, 5, "POST : ", 1, 0, 'L');
    $pdf->SetX(35);
    $pdf->Cell(0, 5, $row['DESIGNATION'], 1, 0, 'L');
    $pdf->SetX(80);
    $pdf->Cell(0, 5, "GRADE : ", 1, 0, 'L');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, $row['GRADE'], 1, 0, 'L');
    $pdf->SetX(140);
    $pdf->Cell(0, 5, "EMPLOYEE CODE : ", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(0, 5, $row['EMPNO'], 1, 1, 'L');

    $pdf->Cell(0, 5, "PF A/C NO. : ", 1, 0, 'L');
    $pdf->SetX(35);
    $pdf->Cell(0, 5, $row['PFACNO'], 1, 0, 'L');
    $pdf->SetX(80);
    $pdf->Cell(0, 5, "PF UAN NO. : ", 1, 0, 'L');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, $row['uanNo'], 1, 0, 'L');
    $pdf->SetX(140);
    $pdf->Cell(0, 5, "PAN NO. : ", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(0, 5, $row['PAN'], 1, 1, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('freesans', '', 8);
    $pdf->Cell(95, 5, "WORKED", 1, 0, 'C', true);
    $pdf->SetX(105);
    $pdf->Cell(20, 5, "PAYABLE", 1, 0, 'C', true);
    $pdf->SetX(125);
    $pdf->Cell(75, 5, "BANK DETAILS", 1, 1, 'C', true);

    $pdf->SetFont('freesans', '', 8);
    $pdf->Cell(15, 5, "PRESENT", 1, 0, 'C');
    $pdf->SetX(25);
    $pdf->Cell(10, 5, "LWOP", 1, 0, 'C');
    $pdf->SetX(35);
    $pdf->Cell(7, 5, "CL", 1, 0, 'C');
    $pdf->SetX(42);
    $pdf->Cell(7, 5, "EL", 1, 0, 'C');
    $pdf->SetX(49);
    $pdf->Cell(10, 5, "MED", 1, 0, 'C');
    $pdf->SetX(59);
    $pdf->Cell(8, 5, "ML", 1, 0, 'C');
    $pdf->SetX(67);
    $pdf->Cell(7, 5, "DL", 1, 0, 'C');
    $pdf->SetX(74);
    $pdf->Cell(9, 5, "SPL", 1, 0, 'C');
    $pdf->SetX(83);
    $pdf->Cell(8, 5, "WO", 1, 0, 'C');
    $pdf->SetX(91);
    $pdf->Cell(14, 5, "HOLIDAY", 1, 0, 'C');
    $pdf->SetX(105);
    $pdf->Cell(20, 5, "DAYS", 1, 0, 'C');
    $pdf->SetX(125);
    $pdf->Cell(50, 5, "BANK & BRANCH", 1, 0, 'C');
    $pdf->SetX(175);
    $pdf->Cell(25, 5, "Bank A/c No.", 1, 1, 'C');

    $pdf->SetFont('freesans', '', 8);
    $pdf->Cell(15, 5, $row['dwork'], 1, 0, 'C');
    $pdf->SetX(25);
    $pdf->Cell(10, 5, $row['lwop'], 1, 0, 'C');
    $pdf->SetX(35);
    $pdf->Cell(7, 5, $row['cl'], 1, 0, 'C');
    $pdf->SetX(42);
    $pdf->Cell(7, 5, $row['el'], 1, 0, 'C');
    $pdf->SetX(49);
    $pdf->Cell(10, 5, $row['med_leave'], 1, 0, 'C');
    $pdf->SetX(59);
    $pdf->Cell(8, 5, "0", 1, 0, 'C');
    $pdf->SetX(67);
    $pdf->Cell(7, 5, "0", 1, 0, 'C');
    $pdf->SetX(74);
    $pdf->Cell(9, 5, $row['spleave'], 1, 0, 'C');
    $pdf->SetX(83);
    $pdf->Cell(8, 5, $row['hdays'], 1, 0, 'C');
    $pdf->SetX(91);
    $pdf->Cell(14, 5, $row['off_days'], 1, 0, 'C');
    $pdf->SetX(105);
    $pdf->Cell(20, 5, $row['dpaid'], 1, 0, 'C');
    $pdf->SetX(125);
    $pdf->SetFont('freesans', '', 6);
    $pdf->Cell(50, 5, strtoupper((string) $row['BANK']), 1, 0, 'C');
    $pdf->SetX(175);
    $pdf->SetFont('freesans', '', 8);
    $pdf->Cell(25, 5, $row['ACCNO'], 1, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('freesans', 'B', 9);
    $pdf->Cell(95, 5, "EARNING", 1, 0, 'C', true);
    $pdf->SetX(105);
    $pdf->Cell(95, 5, "DEDUCTION", 1, 1, 'C', true);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(0, 5, "PARTICULARS", 1, 0, 'L', true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "AMOUNT", 1, 0, 'C', true);
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "PARTICULARS", 1, 0, 'L', true);
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "AMOUNT", 1, 1, 'C', true);

    // Earnings: basic, LSA and every allowance component; deductions: LWOP and every deduction component
    $earnings = array("BASIC" => $row["BASIC"]);
    if ($row["LSA"] > 0) {
        $earnings["LONG SERVICE ALLOWANCE"] = $row["LSA"];
    }
    foreach ($row["earnings"] as $name => $amount) {
        $earnings[$name] = $amount;
    }
    $deductions = array("LWOP" => $row["lwopamt"]);
    foreach ($row["deductions"] as $name => $amount) {
        $deductions[$name] = $amount;
    }
    $earnNames = array_keys($earnings);
    $dedNames = array_keys($deductions);
    $lineCount = max(count($earnNames), count($dedNames), 8);
    $money = function ($amount) {
        return "₹ " . number_format((float) $amount, 2, ".", "");
    };
    for ($i = 0; $i < $lineCount; $i++) {
        $e = isset($earnNames[$i]) ? $earnNames[$i] : null;
        $d = isset($dedNames[$i]) ? $dedNames[$i] : null;
        $pdf->Cell(0, 5, $e !== null ? strtoupper($e) : "", 1, 0, "L");
        $pdf->SetX(75);
        $pdf->Cell(30, 5, $e !== null ? $money($earnings[$e]) : "", 1, 0, "R");
        $pdf->SetX(105);
        $pdf->Cell(0, 5, $d !== null ? strtoupper($d) : "", 1, 0, "L");
        $pdf->SetX(170);
        $pdf->Cell(30, 5, $d !== null ? $money($deductions[$d]) : "", 1, 1, "R");
    }

    $pdf->Cell(0, 5, "TOTAL EARNING ( GROSS )", 1, 0, "L", true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, $money($row["gross"]), 1, 0, "R", true);
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "TOTAL DEDUCTION", 1, 0, "L", true);
    $pdf->SetX(170);
    $pdf->Cell(30, 5, $money($row["total_deduction"]), 1, 1, "R", true);

    $pdf->Cell(65, 5, "NET EARNING", 1, 0, "L", true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, $money($row["netAmt"]), 1, 0, "R", true);
    $pdf->Cell(95, 5, "", 1, 1, "L");
    $pdf->Ln(5);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(65, 5, "BANK PAYMENT", 1, 0, 'L', true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "AMOUNT", 1, 1, 'R', true);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(65, 5, "NET SALARY (" . $month . " - " . $selectedYear . ")", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . number_format((float) $row['netAmt'], 2, ".", ""), 1, 1, 'R');
    $pdf->Ln(5);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(65, 5, "YEAR TO DATE", 1, 0, 'L', true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "AMOUNT", 1, 0, 'C', true);
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "LEAVE RECORDS", 1, 0, 'L', true);
    $pdf->SetX(155);
    $pdf->Cell(15, 5, "CL", 1, 0, 'C', true);
    $pdf->SetX(170);
    $pdf->Cell(15, 5, "MED", 1, 0, 'C', true);
    $pdf->SetX(185);
    $pdf->Cell(15, 5, "EL", 1, 1, 'C', true);

    $pdf->Cell(0, 5, "NET EARNING", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "OPENING BALANCE", 1, 0, 'L');
    $pdf->SetX(155);
    $pdf->Cell(15, 5, $row['ocl'], 1, 0, 'C');
    $pdf->SetX(170);
    $pdf->Cell(15, 5, $row['oml'], 1, 0, 'C');
    $pdf->SetX(185);
    $pdf->Cell(15, 5, $row['oel'], 1, 1, 'C');

    $pdf->Cell(0, 5, "INCOME TAX", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . number_format(headAmount($row, 'deductions', 'iTax'), 2, ".", ""), 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "CONSUMED", 1, 0, 'L');
    $pdf->SetX(155);
    $pdf->Cell(15, 5,  $row['cl'], 1, 0, 'C');
    $pdf->SetX(170);
    $pdf->Cell(15, 5,  $row['med_leave'], 1, 0, 'C');
    $pdf->SetX(185);
    $pdf->Cell(15, 5,  $row['el'], 1, 1, 'C');

    $pdf->SetFont('freesans', 'BU', 9);
    $pdf->Cell(0, 5, "PROVIDENT FUND", 1, 0, 'L');
    $pdf->SetFont('freesans', '', 9);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "CLOSING BALANCE", 1, 0, 'L');
    $pdf->SetX(155);
    $pdf->Cell(15, 5, $row['ccl'], 1, 0, 'C');
    $pdf->SetX(170);
    $pdf->Cell(15, 5, $row['cml'], 1, 0, 'C');
    $pdf->SetX(185);
    $pdf->Cell(15, 5, $row['cel'], 1, 1, 'C');

    $pdf->Cell(0, 5, "EMPLOYEE CONTRIBUTION", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(155);
    $pdf->Cell(15, 5, "", 1, 0, 'C');
    $pdf->SetX(170);
    $pdf->Cell(15, 5, "", 1, 0, 'C');
    $pdf->SetX(185);
    $pdf->Cell(15, 5, "", 1, 1, 'C');

    $pdf->Cell(0, 5, "EMPLOYER'S CONTRIBUTION", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(155);
    $pdf->Cell(15, 5, "", 1, 0, 'C');
    $pdf->SetX(170);
    $pdf->Cell(15, 5, "", 1, 0, 'C');
    $pdf->SetX(185);
    $pdf->Cell(15, 5, "", 1, 1, 'C');

    $pdf->Cell(0, 5, "PENSION FUND", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(155);
    $pdf->Cell(15, 5, "", 1, 0, 'C');
    $pdf->SetX(170);
    $pdf->Cell(15, 5, "", 1, 0, 'C');
    $pdf->SetX(185);
    $pdf->Cell(15, 5, "", 1, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('freesans', '', 10);
    $pdf->Cell(0, 5, "This document is electronically generated, and no signature is required.", 0, 1, 'C');
}
$pdf->Output('PaySlip_' . $month . '_' . $selectedYear . '.pdf', 'I');