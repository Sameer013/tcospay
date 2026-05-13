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
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
function getMonthName($num)
{
    $months = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Aug',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec'
    ];

    if (isset($months[$num])) {
        return $months[$num];
    } else {
        return "Invalid month number";
    }
}
$month = getMonthName($selectedMonth);
function getDaysInMonth($month, $year)
{
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}
function checkSheetExistence($db, $selectedMonth, $selectedYear)
{

    $sql_sheet_id = "SELECT sheet_id FROM sheet WHERE mnth = $selectedMonth AND yr = $selectedYear";

    $stmt_sheet_id = $db->prepare($sql_sheet_id);
    $stmt_sheet_id->execute();
    $sheet_id = $stmt_sheet_id->fetchColumn();

    if ($sheet_id) {
        return $sheet_id;
    } else {
        echo '<script type="text/javascript">alert("No Month Data");
       // window.location.href = "index_salary.php";</script>';
        die();
    }
}

$db->exec("SET SESSION sql_mode = (SELECT REPLACE(@@SESSION.sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

$daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
$sheet_id = checkSheetExistence($db, $selectedMonth, $selectedYear);
if ($userId == 'Admin' || $userId == 'Super Admin') {
    $sql = "SELECT s.mnth, s.yr,  t.TRNSNO, t.EMPNO, e.NAME,d.DESCR DESIGNATION, e.SEFNO ESIC_NO, e.PFACNO, e.PAN, b.DESCR BANK, e.ACCNO, t.BASIC, e.PHONE1, g.GRADE, e.uanNo, MAX(CASE WHEN t1.DESCR = 'DA' THEN t1.amount END) AS DA, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount END) AS HRA, MAX(CASE WHEN t1.DESCR = 'TA' THEN t1.amount END) AS TA, MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount END) AS SPL, MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount END) AS PF, MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount END) AS ESIC, MAX(CASE WHEN t2.DESCR = 'iTax' THEN t2.amount END) AS iTax, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END) +
MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount ELSE 0 END) + MAX(CASE WHEN t1.DESCR = 'LSA' THEN t1.amount ELSE 0 END) + t.gross  AS total_earning, CONCAT(
  ROUND(
    MAX(CASE WHEN t2.DESCR = 'iTax' THEN t2.amount ELSE 0 END) +
    MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount ELSE 0 END) +
    MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount ELSE 0 END) +
    IFNULL(
      ROUND(
        (t.lwop / $daysInMonth) * 
        ((CASE WHEN t1.DESCR = 'DA' THEN t1.amount ELSE 0 END) + t.basic),
      2),
    0),
  0),
  '.00'
) AS total_deduction, t.lwop, t.adj, t.gross,  sd.cl, sd.el, sd.hdays, sd.off_days, sd.spleave, ROUND((sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays), 2) as dpaid, sd.attnd as dwork, (sd.cl + sd.spleave + sd.med_leave) as LWP, CONCAT(
  IFNULL(
    ROUND(
      (t.lwop / $daysInMonth) * 
      ((CASE WHEN t1.DESCR = 'DA' THEN t1.amount ELSE 0 END) + t.basic),
      0
    ),
    0
  ),
  '.00'
) AS lwopamt
, CONCAT(
  ROUND(
    MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END) + t.MNTHSAL,
    0
  ),
  '.00'
) AS MNTHSAL, sd.med_leave, get_advInst(t.EMPNO) AS advAmt, t.esisal, get_lsaAmt(t.EMPNO) as LSA, e.ocl, e.oml, e.oel, e.ospl, (e.ocl-sd.cl) as ccl, (e.oml-sd.med_leave) as cml, (e.oel-sd.el) as cel
FROM trnsmst t
JOIN  sheet s ON t.sheet_id = s.sheet_id
JOIN empmast e ON t.EMPNO = e.EMPNO
JOIN dsgmast d ON e.DSGCODE = d.DCODE
JOIN bankmast b ON e.BID = b.BID
JOIN grademaster g ON e.grade = g.Gradecode
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
JOIN trnsdet2 t2 ON t.TRNSNO = t2.TRNSNO
WHERE t.sheet_id=$sheet_id
GROUP BY t.TRNSNO
ORDER BY e.EMPNO";
} else {
    $sql = "SELECT s.mnth, s.yr,  t.TRNSNO, t.EMPNO, e.NAME,d.DESCR DESIGNATION, e.SEFNO ESIC_NO, e.PFACNO, e.PAN, b.DESCR BANK, e.ACCNO, t.BASIC, e.PHONE1, g.GRADE, e.uanNo, MAX(CASE WHEN t1.DESCR = 'DA' THEN t1.amount END) AS DA, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount END) AS HRA, MAX(CASE WHEN t1.DESCR = 'TA' THEN t1.amount END) AS TA, MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount END) AS SPL, MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount END) AS PF, MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount END) AS ESIC, MAX(CASE WHEN t2.DESCR = 'iTax' THEN t2.amount END) AS iTax, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END) +
MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount ELSE 0 END) + MAX(CASE WHEN t1.DESCR = 'LSA' THEN t1.amount ELSE 0 END) + t.gross  AS total_earning, CONCAT(
  ROUND(
    MAX(CASE WHEN t2.DESCR = 'iTax' THEN t2.amount ELSE 0 END) +
    MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount ELSE 0 END) +
    MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount ELSE 0 END) +
    IFNULL(
      ROUND(
        (t.lwop / $daysInMonth) * 
        ((CASE WHEN t1.DESCR = 'DA' THEN t1.amount ELSE 0 END) + t.basic),
      2),
    0),
  0),
  '.00'
) AS total_deduction, t.lwop, t.adj, t.gross,  sd.cl, sd.el, sd.hdays, sd.off_days, sd.spleave, ROUND((sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays), 2) as dpaid, sd.attnd as dwork, (sd.cl + sd.spleave + sd.med_leave) as LWP, CONCAT(
  IFNULL(
    ROUND(
      (t.lwop / $daysInMonth) * 
      ((CASE WHEN t1.DESCR = 'DA' THEN t1.amount ELSE 0 END) + t.basic),
      0
    ),
    0
  ),
  '.00'
) AS lwopamt
, CONCAT(
  ROUND(
    MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END) + t.MNTHSAL,
    0
  ),
  '.00'
) AS MNTHSAL, sd.med_leave, get_advInst(t.EMPNO) AS advAmt, t.esisal, get_lsaAmt(t.EMPNO) as LSA, e.ocl, e.oml, e.oel, e.ospl, (e.ocl-sd.cl) as ccl, (e.oml-sd.med_leave) as cml, (e.oel-sd.el) as cel
FROM trnsmst t
JOIN  sheet s ON t.sheet_id = s.sheet_id
JOIN empmast e ON t.EMPNO = e.EMPNO
JOIN dsgmast d ON e.DSGCODE = d.DCODE
JOIN bankmast b ON e.BID = b.BID
JOIN grademaster g ON e.grade = g.Gradecode
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
JOIN trnsdet2 t2 ON t.TRNSNO = t2.TRNSNO
WHERE t.sheet_id=$sheet_id AND e.EMPNO=$userId
GROUP BY t.TRNSNO";
}
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo '<script type="text/javascript">';
    echo 'alert("No data found in the database.' . $selectedMonth . ' and year: ' . $selectedYear . '");';
    //  echo 'window.location.href = "index_salary.php";';
    echo '</script>';
    die();
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
    $imagePath = 'dist/images/jpslogo.jpg';
    $pdf->Image($imagePath, 10, 15, 20, 20);
    //   $pdf->Line(10, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
    $pdf->SetFont('freesans', '', 15);
    $pdf->Cell(0, 15, "JAMSHEDPUR PUBLIC SCHOOL", 0, 1, 'C');
    $pdf->SetY($pdf->GetY() - 9);
    $pdf->SetFont('freesans', 'U', 12);
    $pdf->Cell(0, 15, "PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831017", 0, 1, 'C');
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
    $pdf->Cell(50, 5, "STATE BANK OF INDIA, GOLMURI BRANCH", 1, 0, 'C');
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

    $pdf->Cell(0, 5, "BASIC", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . $row['BASIC'], 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "LOWP", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "₹ " . $row['lwopamt'], 1, 1, 'R');

    $pdf->Cell(0, 5, "DA 4%", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " .  $row['DA'], 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "PROVIDENT FUND", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "₹ " .  $row['PF'], 1, 1, 'R');

    $pdf->Cell(0, 5, "HOUSE RENT ALLOWANCE 3%", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . (isset($row['HRA']) ? $row['HRA'] : '0.00'), 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "ESI", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "₹ " . $row['ESIC'], 1, 1, 'R');

    $pdf->Cell(0, 5, "LONG SERVICE ALLOWANCE", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . (isset($row['LSA']) ? $row['LSA'] : '0.00'), 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "INCOME TAX", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "₹ " . (!empty($row['iTax']) ? $row['iTax'] : '0.00'), 1, 1, 'R');

    $pdf->Cell(0, 5, "SPECIAL ALLOWANCE", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . (isset($row['SPL']) ? $row['SPL'] : '0.00'), 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "PROFESSIONAL TAX", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "", 1, 1, 'R');

    $pdf->Cell(0, 5, "OTHERS ALLOWANCE", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "ADVANCE", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "₹ " . (!empty($row['advAmt']) ? $row['advAmt'] : '0.00'), 1, 1, 'R');
    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "LIFE INSURANCE PREMIUM", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "", 1, 1, 'R');

    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "OTHERS", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "", 1, 1, 'R');

    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "", 1, 0, 'R');
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "", 1, 0, 'L');
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "", 1, 1, 'R');

    $pdf->Cell(0, 5, "TOTAL EARNING ( GROSS )", 1, 0, 'L', true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " . $row['total_earning'], 1, 0, 'R', true);
    $pdf->SetX(105);
    $pdf->Cell(0, 5, "TOTAL DEDUCTION", 1, 0, 'L', true);
    $pdf->SetX(170);
    $pdf->Cell(30, 5, "₹ " . $row['total_deduction'], 1, 1, 'R', true);

    $pdf->Cell(65, 5, "NET EARNING", 1, 0, 'L', true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " .  $row['MNTHSAL'], 1, 0, 'R', true);
    $pdf->Cell(95, 5, "", 1, 1, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(65, 5, "BANK PAYMENT", 1, 0, 'L', true);
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "AMOUNT", 1, 1, 'R', true);

    $pdf->SetFont('freesans', '', 9);
    $pdf->Cell(65, 5, "NET SALARY (" . $month . " - " . $selectedYear . ")", 1, 0, 'L');
    $pdf->SetX(75);
    $pdf->Cell(30, 5, "₹ " .  $row['MNTHSAL'], 1, 1, 'R');
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
    $pdf->Cell(30, 5, "₹ " . (!empty($row['iTax']) ? $row['iTax'] : '0.00'), 1, 0, 'R');
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
    $pdf->Cell(0, 5, "COSING BALANCE", 1, 0, 'L');
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

    $pdf->Cell(0, 5, "EMPLYER'S CONTRIBUTION", 1, 0, 'L');
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