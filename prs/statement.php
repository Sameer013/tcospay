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
$sheet_id = checkSheetExistence($db, $selectedMonth, $selectedYear);
$sql = "SELECT e.uanNo, e.NAME, e.PFACNO, e.ACCNO, ROUND(MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount ELSE 0 END + t.MNTHSAL), 0) AS MNTHSAL
FROM empmast e
JOIN trnsmst t on e.EMPNO=t.EMPNO
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
WHERE t.sheet_id=$sheet_id
GROUP BY t.TRNSNO";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$monthYear = $month . '  ' . $selectedYear;
$sheet->setTitle("For Bank " . $monthYear);

$sheet->setCellValue('A1', 'JAMSHEDPUR PUBLIC SCHOOL');
$sheet->setCellValue('A2', 'PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831 017');
$sheet->setCellValue('A4', 'STATEMENT OF ESI FOR THE MONTH OF ' . strtoupper($monthYear));
$sheet->mergeCells('A1:D1');
$sheet->mergeCells('A2:D2');
$sheet->mergeCells('A4:D4');

$sheet->getStyle('A1:D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$headers = ['Sl. No.', 'Bank A/C No', 'NAME', 'Amount'];
$sheet->fromArray($headers, null, 'A5');

$sheet->getStyle('A5:D5')->getFont()->setBold(true);

$sheet->getStyle('A5:D5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$rowNumber = 6;
$total = 0;

foreach ($rows as $index => $row) {
    $sheet->setCellValue('A' . $rowNumber, $index + 1);
    $sheet->setCellValue('B' . $rowNumber, $row['ACCNO']);
    $sheet->setCellValue('C' . $rowNumber, $row['NAME']);
    $sheet->setCellValue('D' . $rowNumber, $row['MNTHSAL']);

    $total += $row['MNTHSAL'];

    $rowNumber++;
}

$sheet->mergeCells('A' . $rowNumber . ':C' . $rowNumber);
$sheet->setCellValue('A' . $rowNumber, 'Total');

$sheet->getStyle('A' . $rowNumber . ':C' . $rowNumber)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A' . $rowNumber . ':C' . $rowNumber)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->setCellValue('D' . $rowNumber, $total);

$sheet->getStyle('A6:D' . ($rowNumber - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A' . $rowNumber . ':D' . $rowNumber)->getFont()->setBold(true);

foreach (range('A', $sheet->getHighestColumn()) as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}


$writer = new Xlsx($spreadsheet);


$filename = 'forBankStatement_' . date('m-Y') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: inline;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
