<?php
/*
 * Bank advice letter: fills templates/advice_letter.xlsx (one sheet per bank) for the month.
 *
 * Each sheet keeps its bank, debit account, wording and employee rows from the template.
 * For every row the employee's net pay goes into AMOUNT TO BE CREDITED, matched on the
 * account number (the payroll may store it with a branch prefix, e.g. 11262001000167 for
 * 2001000167), and every "Date:" keeps the template's day with the month after the pay month.
 * The sheets' own SUM formulas give the totals.
 * Employees paid this month but missing from the template, and template rows without pay,
 * are listed on an extra CHECK sheet. New employees are added to the template by hand.
 */
session_start();
if (!isset($_SESSION['user'])) {
    header("location:login.php");
    exit;
}
require '../vendor/autoload.php';
require_once 'includes/dbconn.php';
require_once 'includes/payroll_report.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

list($selectedMonth, $selectedYear) = reportPeriod();
$month = reportMonthName($selectedMonth);
$sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

$digits = function ($v) {
    if (is_float($v) || is_int($v)) {
        $v = sprintf('%.0f', $v);
    }
    return preg_replace('/\D/', '', (string) $v);
};

// This month's net pay by account number
$stmt = $db->prepare("
    SELECT e.EMPNO, e.NAME, e.ACCNO, t.netAmt
    FROM trnsmst t JOIN empmast e ON e.EMPNO = t.EMPNO
    WHERE t.sheet_id = :sheet_id");
$stmt->execute(array(':sheet_id' => $sheet_id));
$paid = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$paid) {
    reportStop('Payroll is not processed for ' . $month . ' ' . $selectedYear);
}

// The employee whose account is the letter's account, or ends with it (branch prefix)
$findEmployee = function ($account) use ($paid, $digits) {
    if (strlen($account) < 6) {
        return null;
    }
    $hits = array_filter($paid, function ($p) use ($account, $digits) {
        $acc = $digits($p['ACCNO']);
        return $acc === $account || substr($acc, -strlen($account)) === $account;
    });
    return count($hits) === 1 ? reset($hits) : null;
};

$book = IOFactory::load(__DIR__ . '/templates/advice_letter.xlsx');

// The letter keeps the template's day of the month; month and year follow the pay month,
// moved on by this many months (salary for October is paid in November: 1)
define('LETTER_MONTHS_AFTER_PAY_MONTH', 0);
$letterMonth = mktime(0, 0, 0, $selectedMonth + LETTER_MONTHS_AFTER_PAY_MONTH, 1, $selectedYear);
$letterDate = function ($templateDay) use ($letterMonth) {
    $day = min((int) $templateDay, (int) date('t', $letterMonth)); // 31 in a 30-day month -> 30
    return sprintf('%02d-%s', $day, date('m-Y', $letterMonth));
};
$placed = array();
$problems = array();

foreach ($book->getWorksheetIterator() as $ws) {
    $lastRow = $ws->getHighestRow();
    $lastCol = Coordinate::columnIndexFromString($ws->getHighestColumn());
    $headRow = $accCol = $amtCol = null;

    for ($r = 1; $r <= $lastRow; $r++) {
        for ($c = 1; $c <= $lastCol; $c++) {
            $cell = $ws->getCell(Coordinate::stringFromColumnIndex($c) . $r);
            $v = $cell->getValue();
            if (!is_string($v)) {
                continue;
            }
            if (preg_match('/^\s*Date\s*:/i', $v)) {
                $cell->setValue(preg_replace_callback('/(\d{1,2})[-\/.]\d{1,2}[-\/.]\d{4}/', function ($m) use ($letterDate) {
                    return $letterDate($m[1]);
                }, $v));
            }
            $label = strtoupper(trim($v));
            if (strpos($label, 'AMOUNT TO BE CREDITED') !== false) {
                $headRow = $r;
                $amtCol = $c;
            } elseif (strpos($label, 'ACCOUNT NO') !== false) {
                $accCol = $c;
            }
        }
    }
    if ($headRow === null || $accCol === null) {
        $problems[] = array($ws->getTitle(), '', '', 'Sheet has no ACCOUNT NO. / AMOUNT TO BE CREDITED header; left unchanged');
        continue;
    }

    // Employee rows: from under the header to the TOTAL row
    for ($r = $headRow + 1; $r <= $lastRow; $r++) {
        $accValue = $ws->getCell(Coordinate::stringFromColumnIndex($accCol) . $r)->getValue();
        if (is_string($accValue) && strtoupper(trim($accValue)) === 'TOTAL') {
            break;
        }
        $account = $digits($accValue);
        if ($account === '') {
            continue;
        }
        $amountCell = $ws->getCell(Coordinate::stringFromColumnIndex($amtCol) . $r);
        $amountCell->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $emp = $findEmployee($account);
        if ($emp) {
            $amountCell->setValue(round((float) $emp['netAmt'], 2));
            $placed[$emp['EMPNO']] = true;
        } else {
            $amountCell->setValue(0);
            $name = $ws->getCell('B' . $r)->getValue();
            $problems[] = array($ws->getTitle(), trim((string) $name), $account, 'No payroll this month for this account; amount set to 0');
        }
    }
}

foreach ($paid as $p) {
    if (!isset($placed[$p['EMPNO']])) {
        $problems[] = array('-', $p['NAME'], $digits($p['ACCNO']), 'Paid ' . number_format((float) $p['netAmt'], 2, '.', '') . ' but not in any bank sheet; add the employee to templates/advice_letter.xlsx');
    }
}

// Anything that needs a look goes on its own sheet (not part of the letters)
if ($problems) {
    $check = $book->createSheet();
    $check->setTitle('CHECK');
    $check->fromArray(array('Sheet', 'Name', 'Account No.', 'Problem'), null, 'A1');
    $check->fromArray($problems, null, 'A2');
    $check->getStyle('A1:D1')->getFont()->setBold(true);
    foreach (array('A' => 10, 'B' => 28, 'C' => 18, 'D' => 80) as $col => $w) {
        $check->getColumnDimension($col)->setWidth($w);
    }
    $check->getStyle('C2:C' . (count($problems) + 1))->getNumberFormat()->setFormatCode('@');
}
$book->setActiveSheetIndex(0);

$filename = 'Advice_Letter_' . $month . '-' . $selectedYear . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
IOFactory::createWriter($book, 'Xlsx')->save('php://output');
exit;
