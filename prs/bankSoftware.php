<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
$sql = "SELECT  e.ACCNO, ROUND(MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END + t.MNTHSAL), 0) AS MNTHSAL
FROM empmast e
JOIN trnsmst t ON e.EMPNO = t.EMPNO
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
WHERE t.sheet_id = $sheet_id
GROUP BY t.TRNSNO";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$monthYear = date('F, Y');
$sheet->setTitle("Bank Software " . $month.' '.$selectedYear);

$headers = ['Sl. No.', 'Bank A/C No', 'Amount'];
$sheet->fromArray($headers, null, 'A1');

$sheet->getStyle('A1:C1')->getFont()->setBold(true);

$sheet->getStyle('A1:C1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$rowNumber = 2;
//$totalAmount = 0;

foreach ($rows as $index => $row) {
    $sheet->setCellValue('A' . $rowNumber, $index + 1);
    $sheet->setCellValue('B' . $rowNumber, $row['ACCNO']);
    $sheet->setCellValue('C' . $rowNumber, $row['MNTHSAL']);

   // $totalAmount += 0;
    $rowNumber++;
}


foreach (range('A', 'C') as $column) {
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
// $sheet->getPageSetup()->setHorizontalCentered(true);
// $sheet->getPageSetup()->setVerticalCentered(true);

$writer = new Xlsx($spreadsheet);

$filename = 'BankSoftware_' . $month . '_' . $selectedYear . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: inline;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>