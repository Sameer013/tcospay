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

$sql = "SELECT
    e.EMPNO,
    e.SEFNO,
    e.NAME,
    FORMAT(ROUND(sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays), 2) AS dpaid,
    FORMAT(ROUND(t.gross), 2) AS gross,
    FORMAT(ROUND(t.esisal), 2) AS esisal,
    FORMAT(ROUND(t.gross * 3.25 / 100), 2) AS esiEmp,
    FORMAT(ROUND(ROUND(t.esisal) + ROUND(t.gross * 3.25 / 100)), 2) AS total
FROM empmast e
JOIN trnsmst t ON e.EMPNO = t.EMPNO
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
WHERE t.sheet_id = $sheet_id AND t.esisal != 0";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$monthYear = date('F, Y');
$sheet->setTitle("ESIC " . $monthYear);


$sheet->setCellValue('A1', 'JAMSHEDPUR PUBLIC SCHOOL');
$sheet->setCellValue('A2', 'PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831 017');
$sheet->setCellValue('A4', 'STATEMENT OF ESIC FOR THE MONTH OF ' . $month . ' - '.  $selectedYear);
$sheet->mergeCells('A1:I1');
$sheet->mergeCells('A2:I2');
$sheet->mergeCells('A4:I4');

$sheet->getStyle('A2')->getFont()->setUnderline(true);
$sheet->getStyle('A4')->getFont()->setUnderline(true);

$sheet->getRowDimension(3)->setRowHeight(10);
$sheet->getRowDimension(5)->setRowHeight(20);

// Set font size
$sheet->getStyle('A1')->getFont()->setSize(14);
$sheet->getStyle('A2')->getFont()->setSize(10);
$sheet->getStyle('A3')->getFont()->setSize(14);


$sheet->getStyle('A1:H4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


$headers = ['Sl. No.', 'ESIC No', 'NAME', 'TDP', 'Gross', 'ACT. Gross Paid', 'ESIEMP', 'ESIEMPR', 'Total'];
$sheet->fromArray($headers, null, 'A5');


$sheet->getStyle('A5:I5')->getFont()->setBold(true);

$sheet->getStyle('A5:I5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$rowNumber = 6;
$totalTDP = 0;
$totalGross = 0;
$totalActGrossPaid = 0;
$totalESIEMP = 0;
$totalESIEMPR = 0;
$total = 0;

foreach ($rows as $index => $row) {
    $sheet->setCellValue('A' . $rowNumber, $index + 1);
    $sheet->setCellValue('B' . $rowNumber, $row['SEFNO']);
    $sheet->setCellValue('C' . $rowNumber, $row['NAME']);

    $sheet->setCellValue('D' . $rowNumber, $row['dpaid']);
    $sheet->setCellValue('E' . $rowNumber, $row['gross']);
    $sheet->setCellValue('F' . $rowNumber, $row['gross']);
    $sheet->setCellValue('G' . $rowNumber, $row['esisal']);
    $sheet->setCellValue('H' . $rowNumber, $row['esiEmp']);
    $sheet->setCellValue('I' . $rowNumber, $row['total']);

    $totalTDP += floatval(preg_replace('/[^\d.]+/', '',$row['dpaid']));
    $totalGross += floatval(preg_replace('/[^\d.]+/', '', $row['gross']));
    $totalActGrossPaid += floatval(preg_replace('/[^\d.]+/', '', $row['gross']));
    $totalESIEMP += floatval(preg_replace('/[^\d.]+/', '',$row['esisal']));
    $totalESIEMPR += floatval(preg_replace('/[^\d.]+/', '',$row['esiEmp']));
    $total += floatval(preg_replace('/[^\d.]+/', '', $row['total']));

    $rowNumber++;
}

$sheet->mergeCells('A' . $rowNumber . ':C' . $rowNumber);
$sheet->setCellValue('A' . $rowNumber, 'Total');
$sheet->getStyle('A' . $rowNumber . ':C' . $rowNumber)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A' . $rowNumber . ':C' . $rowNumber)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->setCellValue('D' . $rowNumber, number_format($totalTDP, 2));
$sheet->setCellValue('E' . $rowNumber, number_format($totalGross, 2));
$sheet->setCellValue('F' . $rowNumber, number_format($totalActGrossPaid, 2));
$sheet->setCellValue('G' . $rowNumber, number_format($totalESIEMP, 2));
$sheet->setCellValue('H' . $rowNumber, number_format($totalESIEMPR, 2));
$sheet->setCellValue('I' . $rowNumber, number_format($total, 2));
$sheet->getStyle('D' . $rowNumber . ':I' . $rowNumber)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('D' . $rowNumber . ':I' . $rowNumber)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A' . $rowNumber . ':I' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A' . $rowNumber . ':I' . $rowNumber)->getFont()->setBold(true);
foreach (range('A', $sheet->getHighestColumn()) as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}
$writer = new Xlsx($spreadsheet);

$filename = 'esic_' . $selectedMonth . '- ' . $selectedYear . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: inline;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
