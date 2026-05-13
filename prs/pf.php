<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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
$daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
$sheet_id = checkSheetExistence($db, $selectedMonth, $selectedYear);

    $sql = "SELECT e.uanNo, e.NAME, t.gross as gross, ROUND(t.gross - get_lwopAmt(t.TRNSNO, $daysInMonth), 0) as EPFgross, CASE WHEN e.DOB <= DATE_SUB(CURDATE(), INTERVAL 58 YEAR) THEN 0  ELSE 15000 END AS Epswages, '15000' as EDLIwages, t.pfsal,CASE WHEN e.DOB <= DATE_SUB(CURDATE(), INTERVAL 58 YEAR) THEN 0  ELSE 1250 END AS EpsCount, CASE WHEN e.DOB <= DATE_SUB(CURDATE(), INTERVAL 58 YEAR) THEN t.pfsal  ELSE t.pfsal-1250 END AS EpfDiff, t.lwop as ncpdays, 0 as adv
FROM empmast e
JOIN trnsmst t ON e.EMPNO = t.EMPNO
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
WHERE t.sheet_id = $sheet_id";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Set title
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("PF". date('m-Y'));

// Headers
$headers = ['UAN', 'NAME', 'Gross Wages', 'EPF Wages','EPS Wages', 'EDLI Wages', 'EPF Cont.', 'EPS Cont.', 'EPF EPS Diff', 'NCP Days', 'Ref. Of Advance'];
$sheet->fromArray($headers, null, 'A1');

// Force UAN column as text
$sheet->getStyle('A')->getNumberFormat()->setFormatCode('@');

$rowNumber = 2;
foreach ($rows as $row) {
    $sheet->setCellValueExplicit('A' . $rowNumber, $row['uanNo'], DataType::TYPE_STRING);
    $col = 'B';
    foreach (array_slice($row, 1) as $value) {
        $sheet->setCellValue($col . $rowNumber, $value);
        $col++;
    }
    $rowNumber++;
}

// Auto size columns
foreach (range('A', $sheet->getHighestColumn()) as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);

$filename = 'pf_' .  $selectedMonth . ' and year: ' . $selectedYear . '.xlsx';

// Output to browser for download
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="' . $filename . '"');
// header('Cache-Control: max-age=0');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: inline;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
