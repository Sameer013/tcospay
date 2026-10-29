<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once 'includes/dbconn.php';
require_once 'includes/org.php';
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
$sql = "SELECT SUM(ROUND(t.netAmt, 0)) AS total
FROM trnsmst t
WHERE t.sheet_id = $sheet_id";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$monthName = MonthName($selectedMonth);
$monthYear = date('F, Y');
$sheet->setTitle("Letter " . $month.' '.$selectedYear);

$sheet->setCellValue('A1', ORG_LETTER_REF_PREFIX . orgFinancialYear($selectedMonth, $selectedYear));
$sheet->setCellValue('G1', 'DATE : ' . date('d/m/Y'));
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$sheet->setCellValue('A3', 'The Branch Manager');
$sheet->setCellValue('A4', ORG_BANK_NAME);
$sheet->setCellValue('A5', ORG_BANK_BRANCH);
$sheet->setCellValue('A6', ORG_BANK_CITY);
$sheet->mergeCells('A3:G3');
$sheet->mergeCells('A4:G4');
$sheet->mergeCells('A5:G5');
$sheet->mergeCells('A6:G6');
$sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$sheet->setCellValue('A8', 'Dear Sir,');
$sheet->setCellValue('A10', "Please find enclosed the following cheque for the employees' salary of " . ORG_NAME . " for the month of $monthName, $selectedYear along with Salary Statement to be credited in their personal account.");
$sheet->setCellValue('A11', 'SALARY STATEMENT (HASH-UTILITY) ALSO SENT THROUGH EMAIL ON ' . date('d/m/Y') . ' (Mob. No.' . ORG_CONTACT_MOBILE . ')');
$sheet->getStyle('A11:G11')->getFont()->setBold(true);
$sheet->mergeCells('A8:G8');
$sheet->mergeCells('A10:G10');
$sheet->mergeCells('A11:G11');

$sheet->getStyle('A8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

$sheet->getStyle('A8')->getAlignment()->setWrapText(true);
$sheet->getStyle('A10')->getAlignment()->setWrapText(true);
$sheet->getStyle('A11')->getAlignment()->setWrapText(true);

$sheet->getRowDimension(10)->setRowHeight(30);
$sheet->getRowDimension(11)->setRowHeight(40);
$sheet->getRowDimension(14)->setRowHeight(10);
$sheet->getRowDimension(15)->setRowHeight(25);

$columnsToAdjust = ['A', 'C', 'E', 'G'];
foreach ($columnsToAdjust as $column) {
    $sheet->getColumnDimension($column)->setWidth(15);
}

$sheet->getColumnDimension('B')->setWidth(2);
$sheet->getColumnDimension('D')->setWidth(2);
$sheet->getColumnDimension('F')->setWidth(2);


$sheet->setCellValue('A13', 'CHEQUE NO.');
$sheet->setCellValue('C13', 'DATE');
$sheet->setCellValue('E13', 'AMOUNT');
$sheet->setCellValue('G13', 'BANK');
$sheet->getStyle('A13:G13')->getFont()->setBold(true);
$sheet->getStyle('A13:G13')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A13:G13')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A13:G13')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


$sheet->setCellValue('A15', '');
$sheet->setCellValue('C15', date('d/m/Y'));
$total = $rows[0]['total'];
$sheet->setCellValue('E15', 'Rs.' . $total . '/-');
$sheet->setCellValue('G15', strtoupper(ORG_BANK_NAME . ', ' . ORG_BANK_BRANCH));
$sheet->getStyle('A15:G15')->getFont()->setBold(true);
$sheet->getStyle('A15:G15')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);



foreach (array_combine(['A', 'C', 'E', 'G'], array_pad(explode('|', ORG_SIGNATORIES), 4, '')) as $column => $title) {
    $sheet->setCellValue($column . '21', $title);
}
$sheet->getStyle('A21:G21')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

foreach (['A', 'C', 'E', 'G'] as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

$sheet->getPageSetup()->setPrintArea('A1:G25');


$writer = new Xlsx($spreadsheet);
$filename = 'letter_' . $month . '-' . $selectedYear . '.xlsx';

ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;

