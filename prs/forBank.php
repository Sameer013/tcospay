<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

$sql = "SELECT e.EMPNO, e.NAME, e.ACCNO, ROUND(MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END + t.MNTHSAL), 0) AS MNTHSAL
FROM empmast e
JOIN trnsmst t ON e.EMPNO = t.EMPNO
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
WHERE t.SHEET_ID = $sheet_id
GROUP BY t.TRNSNO";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$monthYear = date('F, Y');
$sheet->setTitle("For Bank " . $monthYear);

$headers = ['Sl. No.', 'Bank A/C No', 'NAME', 'Amount'];
$rowsPerPage = 38;

function addHeader($sheet, $month, $selectedYear, $startRow)
{
    $sheet->setCellValue('A' . $startRow, 'JAMSHEDPUR PUBLIC SCHOOL');
    $sheet->setCellValue('A' . ($startRow + 1), 'PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831 017');
    $sheet->setCellValue('A' . ($startRow + 2), 'SALARY STATEMENT FOR THE MONTH OF '. $month . ' - '.  $selectedYear);
    $sheet->mergeCells('A' . $startRow . ':D' . $startRow);
    $sheet->mergeCells('A' . ($startRow + 1) . ':D' . ($startRow + 1));
    $sheet->mergeCells('A' . ($startRow + 2) . ':D' . ($startRow + 2));
    $sheet->getStyle('A' . $startRow . ':D' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    return $startRow + 4;
}

$sheet->getStyle('A2')->getFont()->setUnderline(true);
$sheet->getStyle('A3')->getFont()->setUnderline(true);

// Set font size
$sheet->getStyle('A1')->getFont()->setSize(14);
$sheet->getStyle('A2')->getFont()->setSize(10);
$sheet->getStyle('A3')->getFont()->setSize(14);


function addFooter($sheet, $rowNumber)
{
    $footerStartRow = $rowNumber + 3;
    $footerText = 'PRINCIPAL           CHAIRPERSON              SECRETARY                 TREASURER';
    $sheet->setCellValue('A' . $footerStartRow, $footerText);
    $sheet->mergeCells('A' . $footerStartRow . ':D' . $footerStartRow);
    $sheet->getStyle('A' . $footerStartRow . ':D' . $footerStartRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    return $footerStartRow + 5;
}

$currentRow = 1;
$totalMNTHSAL = 0;
foreach ($rows as $index => $row) {
    if (($index % $rowsPerPage) === 0) {
        if ($index > 0) {
            $currentRow = addFooter($sheet, $currentRow);
        }
        $currentRow = addHeader($sheet, $month, $selectedYear, $currentRow);
        $sheet->fromArray($headers, null, 'A' . $currentRow);
        $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $currentRow++;
    }

    $sheet->setCellValue('A' . $currentRow, $index + 1);
    $sheet->setCellValue('B' . $currentRow, $row['ACCNO']);
    $sheet->setCellValue('C' . $currentRow, $row['NAME']);
    $sheet->setCellValue('D' . $currentRow, $row['MNTHSAL']);

    $totalMNTHSAL += floatval(preg_replace('/[^\d.]+/', '', $row['MNTHSAL']));

    $currentRow++;
}

$sheet->setCellValue('A' . $currentRow, 'Total');
$sheet->setCellValue('D' . $currentRow, $totalMNTHSAL);
$sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
$sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
$currentRow = addFooter($sheet, $currentRow);

foreach (range('A', $sheet->getHighestColumn()) as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Fit to width (1 page wide), unlimited height
$sheet->getPageSetup()
      ->setFitToWidth(1)
      ->setFitToHeight(0);  // 0 means unlimited pages tall

// Set narrow margins (0.25 inches = ~0.64 cm)
$sheet->getPageMargins()->setTop(0.25);
$sheet->getPageMargins()->setRight(0.25);
$sheet->getPageMargins()->setLeft(0.25);
$sheet->getPageMargins()->setBottom(0.25);

// Center on page when printing
$sheet->getPageSetup()->setHorizontalCentered(true);
$sheet->getPageSetup()->setVerticalCentered(true);

$writer = new Xlsx($spreadsheet);
$filename = 'forBank_' . $month . '-' . $selectedYear . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;

