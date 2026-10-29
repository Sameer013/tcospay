<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

require_once 'includes/dbconn.php';
require_once 'includes/org.php';
require_once 'includes/payroll_report.php';

list($selectedMonth, $selectedYear) = reportPeriod();
$month = reportMonthName($selectedMonth);
$sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

$payroll = loadPayroll($db, $sheet_id);
$rows = $payroll['rows'];
$earningHeads = $payroll['earningHeads'];
$deductionHeads = $payroll['deductionHeads'];

// Column layout: fixed columns, then one column per component used this month
$fixedLeft = array('SL', 'NAME', 'POST', 'GRADE', 'BANK A/C NO.', 'CL AVL', 'LWOP', 'TDP', 'BASIC', 'LSA');
$colEarnStart = count($fixedLeft) + 1;
$colGross = $colEarnStart + count($earningHeads);
$colDedStart = $colGross + 1;                       // LWOP amount, then deduction components
$colTotalDed = $colDedStart + 1 + count($deductionHeads);
$colNet = $colTotalDed + 1;
$colSign = $colNet + 1;
$col = function ($index) {
    return Coordinate::stringFromColumnIndex($index);
};
$last = $col($colSign);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Salary Data ' . $month . ' ' . $selectedYear);

// Header
$sheet->setCellValue('A1', ORG_NAME);
$sheet->setCellValue('A2', ORG_ADDRESS);
$sheet->setCellValue('A3', 'SALARY STATEMENT FOR THE MONTH OF ' . $month . ' - ' . $selectedYear);
foreach (array(1, 2, 3) as $r) {
    $sheet->mergeCells("A$r:{$last}$r");
}
$sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')->getFont()->setUnderline(true);
$sheet->getStyle('A3')->getFont()->setUnderline(true);
$sheet->getStyle('A1')->getFont()->setSize(14);
$sheet->getStyle('A2')->getFont()->setSize(10);
$sheet->getStyle('A3')->getFont()->setSize(14);

// Rows 5-6: headers. Single headers span both rows; groups have sub-headers on row 6.
foreach ($fixedLeft as $i => $title) {
    $c = $col($i + 1);
    $sheet->setCellValue($c . '5', $title);
    $sheet->mergeCells("{$c}5:{$c}6");
}
if ($earningHeads) {
    $sheet->setCellValue($col($colEarnStart) . '5', 'ALLOWANCE');
    $sheet->mergeCells($col($colEarnStart) . '5:' . $col($colGross - 1) . '5');
    foreach ($earningHeads as $i => $head) {
        $sheet->setCellValue($col($colEarnStart + $i) . '6', $head);
    }
}
$sheet->setCellValue($col($colGross) . '5', 'GROSS AMOUNT');
$sheet->mergeCells($col($colGross) . '5:' . $col($colGross) . '6');
$sheet->setCellValue($col($colDedStart) . '5', 'DEDUCTIONS');
$sheet->mergeCells($col($colDedStart) . '5:' . $col($colTotalDed - 1) . '5');
$sheet->setCellValue($col($colDedStart) . '6', 'LWOP');
foreach ($deductionHeads as $i => $head) {
    $sheet->setCellValue($col($colDedStart + 1 + $i) . '6', $head);
}
foreach (array($colTotalDed => 'TOTAL DED', $colNet => 'NET AMOUNT', $colSign => 'SIGNATURE') as $c => $title) {
    $sheet->setCellValue($col($c) . '5', $title);
    $sheet->mergeCells($col($c) . '5:' . $col($c) . '6');
}

$sheet->getStyle("A5:{$last}6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A5:{$last}6")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle("A5:{$last}6")->getAlignment()->setWrapText(true);
$sheet->getStyle("A5:{$last}6")->getFont()->setBold(true);
$sheet->getStyle("A5:{$last}6")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Column widths
$widths = array('A' => 5, 'B' => 30, 'C' => 12, 'D' => 12, 'E' => 16, 'F' => 6, 'G' => 6, 'H' => 6, 'I' => 10, 'J' => 8);
foreach ($widths as $c => $w) {
    $sheet->getColumnDimension($c)->setWidth($w);
}
for ($c = $colEarnStart; $c < $colSign; $c++) {
    $sheet->getColumnDimension($col($c))->setWidth(10);
}
$sheet->getColumnDimension($col($colSign))->setWidth(14);

// Data rows
$rowNumber = 7;
$totals = array();
$add = function ($c, $value) use (&$totals) {
    $totals[$c] = (isset($totals[$c]) ? $totals[$c] : 0) + $value;
};

foreach ($rows as $index => $row) {
    $values = array(
        1 => $index + 1, 2 => $row['NAME'], 3 => $row['DESIGNATION'], 4 => $row['GRADE'],
        5 => $row['ACCNO'], 6 => $row['cl'], 7 => $row['lwop'], 8 => $row['dpaid'],
        9 => $row['BASIC'], 10 => $row['LSA'],
    );
    foreach ($earningHeads as $i => $head) {
        $values[$colEarnStart + $i] = headAmount($row, 'earnings', $head);
    }
    $values[$colGross] = $row['gross'];
    $values[$colDedStart] = $row['lwopamt'];
    foreach ($deductionHeads as $i => $head) {
        $values[$colDedStart + 1 + $i] = headAmount($row, 'deductions', $head);
    }
    $values[$colTotalDed] = $row['total_deduction'];
    $values[$colNet] = $row['netAmt'];

    foreach ($values as $c => $value) {
        if ($c == 5) {
            // Account numbers are long digit strings; keep them as text so Excel doesn't mangle them
            $sheet->setCellValueExplicit($col($c) . $rowNumber, (string) $value, DataType::TYPE_STRING);
        } else {
            $sheet->setCellValue($col($c) . $rowNumber, $c >= 6 ? (float) $value : $value);
        }
        if ($c >= 9) {
            $add($c, (float) $value);
        }
    }
    $sheet->getRowDimension($rowNumber)->setRowHeight(22);
    $rowNumber++;
}

// Total row
$sheet->mergeCells("A$rowNumber:H$rowNumber");
$sheet->setCellValue("A$rowNumber", 'Total');
foreach ($totals as $c => $value) {
    $sheet->setCellValue($col($c) . $rowNumber, $value);
}
$sheet->getRowDimension($rowNumber)->setRowHeight(25);
$sheet->getStyle("A$rowNumber:{$last}$rowNumber")->getFont()->setBold(true);
$sheet->getStyle("A$rowNumber:{$last}$rowNumber")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->getStyle("A7:{$last}$rowNumber")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheet->getStyle("A7:{$last}$rowNumber")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

// Landscape A4, 1 page wide, narrow margins
$sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
$sheet->getPageMargins()->setTop(0.25)->setRight(0.25)->setLeft(0.25)->setBottom(0.25);

$writer = new Xlsx($spreadsheet);
$filename = 'salaryData_' . $month . '_' . $selectedYear . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
