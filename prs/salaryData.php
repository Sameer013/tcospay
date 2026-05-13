<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\PageMargins;

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

$db->exec("SET SESSION sql_mode = (SELECT REPLACE(@@SESSION.sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

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
$daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
$sheet_id = checkSheetExistence($db, $selectedMonth, $selectedYear);
// $sql = "SELECT e.uanNo, e.NAME, e.PFACNO, e.ACCNO FROM empmast e";
$sql = "SELECT s.mnth, s.yr,  t.TRNSNO, t.EMPNO, e.NAME,d.DESCR DESIGNATION, e.SEFNO ESIC_NO, e.PFACNO, e.PAN, b.DESCR BANK, e.ACCNO, t.BASIC, e.PHONE1, g.GRADE, e.uanNo, MAX(CASE WHEN t1.DESCR = 'DA' THEN t1.amount END) AS DA, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount END) AS HRA, MAX(CASE WHEN t1.DESCR = 'TA' THEN t1.amount END) AS TA, IFNULL(MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount END),0) AS SPL, IFNULL(MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount END),0) AS PF, IFNULL(MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount END),0) AS ESIC, IFNULL(MAX(CASE WHEN t2.DESCR = 'iTax' THEN t2.amount END),0) AS iTax, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END) +
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
) AS total_deduction, t.lwop, t.adj, t.gross,  sd.cl, sd.spleave, ROUND((sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays), 2) as dpaid, sd.attnd as dwork, (sd.cl + sd.spleave + sd.med_leave) as LWP, CONCAT(
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
) AS MNTHSAL, sd.med_leave, get_advInst(t.EMPNO) AS advAmt, t.esisal, get_lsaAmt(t.EMPNO) as LSA, (MAX(CASE WHEN t1.DESCR = 'DA' THEN t1.amount END) + t.BASIC) AS wages
FROM trnsmst t
JOIN  sheet s ON t.sheet_id = s.sheet_id
JOIN empmast e ON t.EMPNO = e.EMPNO
JOIN dsgmast d ON e.DSGCODE = d.DCODE
JOIN bankmast b ON e.BID = b.BID
LEFT JOIN grademaster g ON e.grade = g.Gradecode
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
JOIN trnsdet2 t2 ON t.TRNSNO = t2.TRNSNO
WHERE t.sheet_id=$sheet_id
GROUP BY t.TRNSNO";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set title of the sheet with dynamic month and year
$monthYear = date('F, Y'); // Example: November, 2024
$sheet->setTitle("Salary Data " . $monthYear);

// Header
$sheet->setCellValue('A1', 'JAMSHEDPUR PUBLIC SCHOOL');
$sheet->setCellValue('A2', 'PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831017');
$sheet->setCellValue('A3', 'SALARY STATEMENT FOR THE MONTH OF ' . $month . ' - ' . $selectedYear);

$sheet->mergeCells('A1:W1');
$sheet->mergeCells('A2:W2');
$sheet->mergeCells('A3:W3');

$sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')->getFont()->setUnderline(true);
$sheet->getStyle('A3')->getFont()->setUnderline(true);

// Set font size
$sheet->getStyle('A1')->getFont()->setSize(14);
$sheet->getStyle('A2')->getFont()->setSize(10);
$sheet->getStyle('A3')->getFont()->setSize(14);

// Row 5: Main headers
$sheet->setCellValue('A5', 'SL')
      ->setCellValue('B5', 'NAME')
      ->setCellValue('C5', 'POST / GRADE')
      ->setCellValue('E5', 'BANK A/C NO.')
      ->setCellValue('F5', 'CL AVL')
      ->setCellValue('G5', 'LWOP')
      ->setCellValue('H5', 'TDP')
      ->setCellValue('I5', 'BASIC')
      ->setCellValue('J5', 'DA 7%')
      ->setCellValue('K5', 'WAGES')
      ->setCellValue('L5', 'ALLOWANCE')
      ->setCellValue('O5', 'GROSS AMOUNT')
      ->setCellValue('P5', 'DEDUCTIONS')
      ->setCellValue('U5', 'TOTAL DED')
      ->setCellValue('V5', 'NET AMOUNT')
      ->setCellValue('W5', 'SIGNATURE');

// Subheaders for ALLOWANCE
$sheet->setCellValue('L6', 'HRA 5%')
      ->setCellValue('M6', 'LSA')
      ->setCellValue('N6', 'SPL');

// Subheaders for DEDUCTIONS
$sheet->setCellValue('P6', 'TLWP')
      ->setCellValue('Q6', 'PF')
      ->setCellValue('R6', 'ESI')
      ->setCellValue('S6', 'ITAX')
      ->setCellValue('T6', 'OTH');

// Merging for main header cells
$sheet->mergeCells('A5:A6');
$sheet->mergeCells('B5:B6');
$sheet->mergeCells('C5:D6');
$sheet->mergeCells('E5:E6');
$sheet->mergeCells('F5:F6');
$sheet->mergeCells('G5:G6');
$sheet->mergeCells('H5:H6');
$sheet->mergeCells('I5:I6');
$sheet->mergeCells('J5:J6');
$sheet->mergeCells('K5:K6');
$sheet->mergeCells('L5:N5');  // ALLOWANCE
$sheet->mergeCells('O5:O6');
$sheet->mergeCells('P5:T5');  // DEDUCTIONS
$sheet->mergeCells('U5:U6');
$sheet->mergeCells('V5:V6');
$sheet->mergeCells('W5:W6');

// Center alignment and bold
$sheet->getStyle('A5:W6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A5:W6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('A5:W6')->getAlignment()->setWrapText(true);
$sheet->getStyle('A5:W6')->getFont()->setBold(true);


// Adjusting column widths for better layout
$sheet->getColumnDimension('A')->setWidth(5);    // SL
$sheet->getColumnDimension('B')->setWidth(30);   // NAME
$sheet->getColumnDimension('C')->setWidth(12);   // POST
$sheet->getColumnDimension('D')->setWidth(14);   // GRADE
$sheet->getColumnDimension('E')->setWidth(14);   // BANK A/C NO.
$sheet->getColumnDimension('F')->setWidth(5);    // CL AVL
$sheet->getColumnDimension('G')->setWidth(6);    // LWOP
$sheet->getColumnDimension('H')->setWidth(6);    // TDP
$sheet->getColumnDimension('I')->setWidth(10);   // BASIC
$sheet->getColumnDimension('J')->setWidth(8);   // DA 7%
$sheet->getColumnDimension('K')->setWidth(8);   // WAGES

// ALLOWANCE (L, M, N)
$sheet->getColumnDimension('L')->setWidth(8);   // HRA 5%
$sheet->getColumnDimension('M')->setWidth(8);   // LSA
$sheet->getColumnDimension('N')->setWidth(8);   // SPL

$sheet->getColumnDimension('O')->setWidth(10);   // GROSS AMOUNT

// DEDUCTIONS (P to T)
$sheet->getColumnDimension('P')->setWidth(8);    // TLWP
$sheet->getColumnDimension('Q')->setWidth(8);    // PF
$sheet->getColumnDimension('R')->setWidth(8);    // ESI
$sheet->getColumnDimension('S')->setWidth(8);    // ITAX
$sheet->getColumnDimension('T')->setWidth(8);    // OTH

$sheet->getColumnDimension('U')->setWidth(10);   // TOTAL DED
$sheet->getColumnDimension('V')->setWidth(10);   // NET AMOUNT
$sheet->getColumnDimension('W')->setWidth(14);   // SIGNATURE

// Borders
$sheet->getStyle('A5:W6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Start populating the table from row 6
$rowNumber = 7;
$totals = [
    'BASIC' => 0,
    'DA' => 0,
    'wages' => 0,
    'HRA' => 0,
    'LSA' => 0,
    'SPL' => 0,
    'total_earning' => 0,
    'lwopamt' => 0,
    'PF' => 0,
    'ESIC' => 0,
    'iTax' => 0,
    'OTH' => 0,
    'total_deduction' => 0,
    'MNTHSAL' => 0,
];

foreach ($rows as $index => $row) {
    $sheet->setCellValue('A' . $rowNumber, $index + 1);
    $sheet->setCellValue('B' . $rowNumber, $row['NAME']);
    $sheet->setCellValue('C' . $rowNumber, $row['GRADE']);  
    $sheet->setCellValue('D' . $rowNumber, $row['DESIGNATION']);  
    $sheet->setCellValue('E' . $rowNumber, $row['ACCNO']);  
    $sheet->setCellValue('F' . $rowNumber, $row['cl']);  
    $sheet->setCellValue('G' . $rowNumber, $row['lwop']);  
    $sheet->setCellValue('H' . $rowNumber, $row['dpaid']);  
    $sheet->setCellValue('I' . $rowNumber, $row['BASIC']);  
    $sheet->setCellValue('J' . $rowNumber, $row['DA']);  
    $sheet->setCellValue('K' . $rowNumber, $row['wages']);  
    $sheet->setCellValue('L' . $rowNumber, $row['HRA']);  
    $sheet->setCellValue('M' . $rowNumber, $row['LSA']);  
    $sheet->setCellValue('N' . $rowNumber, $row['SPL']);  
    $sheet->setCellValue('O' . $rowNumber, $row['total_earning']); 
    $sheet->setCellValue('P' . $rowNumber, $row['lwopamt']); 
    $sheet->setCellValue('Q' . $rowNumber, $row['PF']);  
    $sheet->setCellValue('R' . $rowNumber, $row['ESIC']);  
    $sheet->setCellValue('S' . $rowNumber, $row['iTax']);  
    $sheet->setCellValue('T' . $rowNumber, 0);  
    $sheet->setCellValue('U' . $rowNumber, $row['total_deduction']);  
    $sheet->setCellValue('V' . $rowNumber, $row['MNTHSAL']);  


    $sheet->getRowDimension($rowNumber)->setRowHeight(22);

    // Accumulate totals
    $totals['BASIC'] += $row['BASIC'];
    $totals['DA'] += $row['DA'];
    $totals['wages'] += $row['wages'];
    $totals['HRA'] += $row['HRA'];
    $totals['LSA'] += $row['LSA'];
    $totals['SPL'] += $row['SPL'];
    $totals['total_earning'] += $row['total_earning'];
    $totals['lwopamt'] += $row['lwopamt'];
    $totals['PF'] += $row['PF'];
    $totals['ESIC'] += $row['ESIC'];
    $totals['iTax'] += $row['iTax'];
    $totals['total_deduction'] += $row['total_deduction'];
    $totals['MNTHSAL'] += $row['MNTHSAL'];

    $rowNumber++;
}

// Set borders
$sheet->getStyle('A7:W' . ($rowNumber))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

// Vertically center the data rows
$sheet->getStyle('A7:W' . ($rowNumber))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

// Bold total row
$sheet->getStyle('A' . $rowNumber . ':W' . $rowNumber)->getFont()->setBold(true);

// Optional: Row height for total
$sheet->getRowDimension($rowNumber)->setRowHeight(25);


// Add total row
$sheet->mergeCells('A' . $rowNumber . ':H' . $rowNumber);
$sheet->setCellValue('A' . $rowNumber, 'Total');
$sheet->setCellValue('I' . $rowNumber, $totals['BASIC']);
$sheet->setCellValue('J' . $rowNumber, $totals['DA']);
$sheet->setCellValue('K' . $rowNumber, $totals['wages']);
$sheet->setCellValue('L' . $rowNumber, $totals['HRA']);
$sheet->setCellValue('M' . $rowNumber, $totals['LSA']);
$sheet->setCellValue('N' . $rowNumber, $totals['SPL']);
$sheet->setCellValue('O' . $rowNumber, $totals['total_earning']);
$sheet->setCellValue('P' . $rowNumber, $totals['lwopamt']);
$sheet->setCellValue('Q' . $rowNumber, $totals['PF']);
$sheet->setCellValue('R' . $rowNumber, $totals['ESIC']);
$sheet->setCellValue('S' . $rowNumber, $totals['iTax']);
$sheet->setCellValue('T' . $rowNumber, 0); // OTH
$sheet->setCellValue('U' . $rowNumber, $totals['total_deduction']);
$sheet->setCellValue('V' . $rowNumber, $totals['MNTHSAL']);// Set alignment to center the text in the merged cells
$sheet->getStyle('A' . $rowNumber . ':W' . $rowNumber)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A' . $rowNumber . ':W' . $rowNumber)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->setCellValue('W' . $rowNumber, '');  // SIGNATURE
$sheet->getStyle('A' . $rowNumber . ':W' . $rowNumber)->getFont()->setBold(true);
$sheet->getStyle('A' . $rowNumber . ':W' . $rowNumber)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


// Set borders for the data rows
$sheet->getStyle('A7:W' . ($rowNumber))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A' . $rowNumber . ':W' . $rowNumber)->getFont()->setBold(true);

// Set auto column width for better readability
// foreach (range('A', 'W') as $column) {
//     $sheet->getColumnDimension($column)->setAutoSize(true);
// }

// Set page orientation to Landscape
$sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$sheet->getPageSetup()
      ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

// Fit to width (1 page wide), unlimited height
$sheet->getPageSetup()
      ->setFitToWidth(1)
      ->setFitToHeight(0);  // 0 means unlimited pages tall

// Set narrow margins (0.25 inches = ~0.64 cm)
$sheet->getPageMargins()->setTop(0.25);
$sheet->getPageMargins()->setRight(0.25);
$sheet->getPageMargins()->setLeft(0.25);
$sheet->getPageMargins()->setBottom(0.25);

// Create an Excel file writer
$writer = new Xlsx($spreadsheet);

// Define the filename
$filename = 'salaryData_' . $month . '_' . $selectedYear . '.xlsx';

// Output to browser for viewing (open in browser)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: inline;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Save the file to output stream
$writer->save('php://output');
exit;
