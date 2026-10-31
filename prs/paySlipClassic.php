<?php
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user'])) {
    header("location:login.php");
    exit;
}
// Pay Slip (Classic): the original layout; paySlip.php is the modern one
$userId = $_SESSION['user'];
require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
require_once 'includes/org.php';
require_once "includes/payroll_report.php";
require_once 'includes/loan_calc.php';

list($selectedMonth, $selectedYear) = reportPeriod();
$month = reportMonthName($selectedMonth);
$monthEnd = date('Y-m-t', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear));
$sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

// Admins get every employee; an employee login gets only their own slip
$isAdmin = ($userId == "Admin" || $userId == "Super Admin");
$payroll = loadPayroll($db, $sheet_id, $isAdmin ? null : $userId);
$rows = $payroll["rows"];
if (empty($rows)) {
    reportStop("No data found for " . $month . " " . $selectedYear);
}
// Aadhaar is printed as XXXX XXXX 1234 (UIDAI advises masking it on documents); false prints all 12 digits
define('SLIP_MASK_AADHAAR', true);

// Aadhaar comes from migrations/2026_10_31_payslip_staff_aadhar.sql; until it runs the slip shows NA
try {
    $aadharStmt = $db->prepare("SELECT AADHAR FROM empmast WHERE EMPNO = :empno");
    $aadharStmt->execute(array(':empno' => 0));
} catch (PDOException $e) {
    $aadharStmt = null;
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// for ($index = 0; $index < count($rows); $index += 2) {
for ($index = 0; $index < count($rows); $index++) {
    $pdf->AddPage();
    $rows[$index]['AADHAR'] = null;
    if ($aadharStmt) {
        $aadharStmt->execute(array(':empno' => $rows[$index]['EMPNO']));
        $rows[$index]['AADHAR'] = $aadharStmt->fetchColumn();
    }
    $loans = loanSlipSummary($db, $rows[$index]['EMPNO'], $sheet_id, $monthEnd);
    printData($pdf, $rows[$index], $index, $selectedYear, $month, $loans);
}


function printData($pdf, $row, $index, $selectedYear, $month, $loans)
{

    $pdf->SetFillColor(220, 220, 220);
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

    $aadhaar = preg_replace('/\D/', '', (string) $row['AADHAR']);
    if (strlen($aadhaar) === 12) {
        $aadhaar = SLIP_MASK_AADHAAR ? 'XXXX XXXX ' . substr($aadhaar, 8) : implode(' ', str_split($aadhaar, 4));
    }
    $pdf->Cell(0, 5, "PAN NO. : ", 1, 0, 'L');
    $pdf->SetX(35);
    $pdf->Cell(0, 5, $row['PAN'] ?: 'NA', 1, 0, 'L');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "AADHAAR NO. : ", 1, 0, 'L');
    $pdf->SetX(140);
    $pdf->Cell(0, 5, $aadhaar !== '' ? $aadhaar : 'NA', 1, 1, 'L');
    $pdf->Ln(5);

    // Loans: PF, SPL and Comm with balance after this month's deduction; NA when there is none
    $pdf->Cell(50, 5, "LOAN", 1, 0, 'L', true);
    $pdf->Cell(45, 5, "TAKEN ON", 1, 0, 'C', true);
    $pdf->Cell(45, 5, "BALANCE", 1, 0, 'R', true);
    $pdf->Cell(50, 5, "INSTALLMENTS LEFT", 1, 1, 'C', true);
    foreach (array('PF' => 'PF LOAN', 'SPL' => 'SPL LOAN', 'COM' => 'COMM LOAN') as $code => $label) {
        $ofType = array_filter($loans, function ($l) use ($code) {
            return $l['code'] === $code;
        });
        if (!$ofType) {
            $ofType = array(null);
        }
        foreach ($ofType as $l) {
            $pdf->Cell(50, 5, $label, 1, 0, 'L');
            $pdf->Cell(45, 5, $l ? date('d-m-Y', strtotime($l['taken'])) : 'NA', 1, 0, 'C');
            $pdf->Cell(45, 5, $l ? "₹ " . number_format($l['balance'], 2, ".", "") : 'NA', 1, 0, 'R');
            $pdf->Cell(50, 5, $l ? $l['left'] : 'NA', 1, 1, 'C');
        }
    }
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

    // Total / net pay labels are bold, their amounts regular
    $pdf->SetFont('freesans', 'B', 9);
    $pdf->Cell(0, 5, "TOTAL EARNING ( GROSS )", 1, 0, "L", true);
    $pdf->SetFont('freesans', '', 9);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, $money($row["gross"]), 1, 0, "R", true);
    $pdf->SetX(105);
    $pdf->SetFont('freesans', 'B', 9);
    $pdf->Cell(0, 5, "TOTAL DEDUCTION", 1, 0, "L", true);
    $pdf->SetFont('freesans', '', 9);
    $pdf->SetX(170);
    $pdf->Cell(30, 5, $money($row["total_deduction"]), 1, 1, "R", true);

    // Net pay sits under the deductions, on the right
    $pdf->Cell(95, 5, "", 1, 0, "L");
    $pdf->SetX(105);
    $pdf->SetFont('freesans', 'B', 9);
    $pdf->Cell(65, 5, "NET PAY", 1, 0, "L", true);
    $pdf->SetFont('freesans', 'B', 9);
    $pdf->SetX(170);
    $pdf->Cell(30, 5, $money($row["netAmt"]), 1, 1, "R", true);
    $pdf->Ln(10);

    $pdf->SetFont('freesans', '', 10);
    $pdf->Cell(0, 5, "This document is electronically generated, and no signature is required.", 0, 1, 'C');
}
$pdf->Output('PaySlip_' . $month . '_' . $selectedYear . '.pdf', 'I');
