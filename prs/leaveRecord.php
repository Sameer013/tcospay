<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;


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
function MonthName($num)
{
    $dateObj = DateTime::createFromFormat('!m', $num); // '!m' ensures only the month is parsed
    return $dateObj ? $dateObj->format('F') : 'Invalid month number'; // 'F' returns the full month name
}

$month = getMonthName($selectedMonth);
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
$sheet_id = checkSheetExistence($db, $selectedMonth, $selectedYear);

$sql = "SELECT e.EMPNO, e.NAME, attnd, sd.lwop, sd.cl, sd.el, sd.hdays, sd.off_days, sd.spleave, sd.med_leave, e.ocl, e.oml, e.oel, e.ospl, (e.ocl-sd.cl) as ccl, (e.oml-sd.med_leave) as cml, (e.oel-sd.el) as cel
FROM empmast e
JOIN sheet_det sd ON e.EMPNO = sd.empno
WHERE sd.sheet_id = $sheet_id";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$monthYear = date('F, Y');
$sheet->setTitle("Leave Record " . $monthYear);

$headers = ['SL.', 'EMP CODE', 'NAME OF THE EMPLOYEE', 'CL CR', 'MED CR', 'EL CR', 'CL', 'WP', 'DL', 'MED', 'SP', 'ML', 'EL', 'WO', 'HL', 'Present', 'CL BAL', 'MED BAL', 'EL BAL', 'LT NO', 'LT/MED. ADJ.', 'SIGNATURE'];
$rowsPerPage = 25;

function addHeader($sheet, $month, $selectedYear, $startRow)
{
    $sheet->setCellValue('A' . $startRow, 'JAMSHEDPUR PUBLIC SCHOOL');
    $sheet->setCellValue('A' . ($startRow + 1), 'PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831 017');
    $sheet->setCellValue('A' . ($startRow + 2), 'SUMMERY OF MONTHLY ATTENDANCE FOR THE MONTH OF '. $month . ' - '.  $selectedYear);
    $sheet->mergeCells('A' . $startRow . ':V' . $startRow);
    $sheet->mergeCells('A' . ($startRow + 1) . ':V' . ($startRow + 1));
    $sheet->mergeCells('A' . ($startRow + 2) . ':V' . ($startRow + 2));
    // $sheet->mergeCells('A' . ($startRow + 3) . ':V' . ($startRow + 3));
    $sheet->getStyle('A' . $startRow . ':V' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    // return $startRow + 4;
    return $startRow + 3;
}

$sheet->getStyle('A2')->getFont()->setUnderline(true);
$sheet->getStyle('A3')->getFont()->setUnderline(true);

// Set font size
$sheet->getStyle('A1')->getFont()->setSize(12);
$sheet->getStyle('A2')->getFont()->setSize(10);
$sheet->getStyle('A3')->getFont()->setSize(12);


function addFooter($sheet, $rowNumber)
{
    $footerStartRow = $rowNumber + 1;

    $footerText = "CL CR = Op.Casual Leave, MED CR=Op. Medical Leave, EL CR=Op. Earned Leave, CL = Casual Leave avail, WP = Without Pay, DL = Duty Leave, SP = Special Leave, ML = Maternity Leave, EL = Earned Leave, MED= Medical Leave, WO = Weekly Off, HL=Holidays, CL BAL= Closing Casual Leave, MED BAL= Closing Medical Leave, EL BAL=Closing Earned Leave";

    $cell = 'A' . $footerStartRow;

    $sheet->setCellValue($cell, $footerText);
    $sheet->mergeCells('A' . $footerStartRow . ':V' . $footerStartRow);

    $sheet->getStyle('A' . $footerStartRow . ':V' . $footerStartRow)
        ->getAlignment()
        ->setWrapText(true)
        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
        ->setVertical(Alignment::VERTICAL_TOP);

    // Optionally increase row height for better readability
    $sheet->getRowDimension($footerStartRow)->setRowHeight(30);

    return $footerStartRow + 2;
}


$sheet->getColumnDimension('A')->setWidth(5);    
$sheet->getColumnDimension('B')->setWidth(6);   
$sheet->getColumnDimension('C')->setWidth(30);   
$sheet->getColumnDimension('D')->setWidth(6);   
$sheet->getColumnDimension('E')->setWidth(6);   
$sheet->getColumnDimension('F')->setWidth(6);    
$sheet->getColumnDimension('G')->setWidth(6);    
$sheet->getColumnDimension('H')->setWidth(6);    
$sheet->getColumnDimension('I')->setWidth(6);   
$sheet->getColumnDimension('J')->setWidth(6);   
$sheet->getColumnDimension('K')->setWidth(6);   

// ALLOWANCE (L, M, N)
$sheet->getColumnDimension('L')->setWidth(6);   
$sheet->getColumnDimension('M')->setWidth(6);   
$sheet->getColumnDimension('N')->setWidth(6);   

$sheet->getColumnDimension('O')->setWidth(6);  

// DEDUCTIONS (P to T)
$sheet->getColumnDimension('P')->setWidth(8);    
$sheet->getColumnDimension('Q')->setWidth(6);    
$sheet->getColumnDimension('R')->setWidth(6);    
$sheet->getColumnDimension('S')->setWidth(6);    
$sheet->getColumnDimension('T')->setWidth(6);    

$sheet->getColumnDimension('U')->setWidth(12);   
$sheet->getColumnDimension('V')->setWidth(12); 



$currentRow = 1;
foreach ($rows as $index => $row) {
    if (($index % $rowsPerPage) === 0) {
        if ($index > 0) {
            $currentRow = addFooter($sheet, $currentRow);
        }
        $currentRow = addHeader($sheet, $month, $selectedYear, $currentRow);
        $sheet->fromArray($headers, null, 'A' . $currentRow);
        // $sheet->getStyle('A' . $currentRow . ':V' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':V' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension($currentRow)->setRowHeight(28);
        $sheet->getStyle('A' . $currentRow . ':V' . $currentRow)->getAlignment()->setWrapText(true)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $currentRow . ':V' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $currentRow++;
    } 

    $sheet->setCellValue('A' . $currentRow, $index + 1);
    $sheet->setCellValue('B' . $currentRow, $row['EMPNO']);
    $sheet->setCellValue('C' . $currentRow, $row['NAME']);
    $sheet->setCellValue('D' . $currentRow, $row['ocl']);
    $sheet->setCellValue('E' . $currentRow, $row['oml']);
    $sheet->setCellValue('F' . $currentRow, $row['oel']);
    $sheet->setCellValue('G' . $currentRow, $row['cl']);
    $sheet->setCellValue('H' . $currentRow, $row['lwop']);
    $sheet->setCellValue('I' . $currentRow, "0");
    $sheet->setCellValue('J' . $currentRow, $row['med_leave']);
    $sheet->setCellValue('K' . $currentRow, $row['spleave']);
    $sheet->setCellValue('L' . $currentRow, "0");
    $sheet->setCellValue('M' . $currentRow, $row['el']);
    $sheet->setCellValue('N' . $currentRow, $row['off_days']);
    $sheet->setCellValue('O' . $currentRow, $row['hdays']);
    $sheet->setCellValue('P' . $currentRow, $row['attnd']);
    $sheet->setCellValue('Q' . $currentRow, $row['ccl']);
    $sheet->setCellValue('R' . $currentRow, $row['cml']);
    $sheet->setCellValue('S' . $currentRow, $row['cel']);
    $sheet->setCellValue('T' . $currentRow, "0");

    // Apply border to data row
    $sheet->getStyle('A' . $currentRow . ':V' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
// Vertical center for all columns
$sheet->getStyle('A' . $currentRow . ':V' . $currentRow)
    ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

// Horizontal center for all EXCEPT column C (NAME)
$sheet->getStyle('A' . $currentRow . ':B' . $currentRow)
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->getStyle('D' . $currentRow . ':V' . $currentRow)
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Optional: explicitly set left alignment for column C
$sheet->getStyle('C' . $currentRow)
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    // Set row height
    $sheet->getRowDimension($currentRow)->setRowHeight(22);

    $currentRow++;
}

$sheet->setCellValue('A' . $currentRow, 'Total');
$sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
$sheet->getStyle('A' . $currentRow . ':V' . $currentRow)->getFont()->setBold(true);
$currentRow = addFooter($sheet, $currentRow);

// foreach (range('A', $sheet->getHighestColumn()) as $column) {
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

$writer = new Xlsx($spreadsheet);
$filename = 'leaveRecord_' . $month . '-' . $selectedYear . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;

