<?php
/*
 * Salary Sheet (portrait A4): salary components as rows, employees as columns.
 * Earnings (to Gross), deductions (to Total Deductions), Net Pay and a signature row;
 * a Total column follows the last employee. Components that are zero for everyone this
 * month are left out. More employees than fit across a page continue on the next page.
 */
require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
require_once 'includes/org.php';
require_once 'includes/payroll_report.php';

// Employee columns per page
define('SHEET_EMPLOYEES_PER_PAGE', 5);

try {
    list($selectedMonth, $selectedYear) = reportPeriod();
    $month = reportMonthName($selectedMonth);
    $sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

    $payroll = loadPayroll($db, $sheet_id);
    $rows = $payroll['rows'];
    if (empty($rows)) {
        reportStop('No data found for ' . $month . ' ' . $selectedYear);
    }

    // key => label; e: / d: keys are salary heads
    $earningRows = array('BASIC' => 'Basic', 'LSA' => 'LSA');
    foreach ($payroll['earningHeads'] as $head) {
        $earningRows['e:' . $head] = $head;
    }
    $deductionRows = array('lwopamt' => 'LWOP');
    foreach ($payroll['deductionHeads'] as $head) {
        $deductionRows['d:' . $head] = $head;
    }

    $value = function ($row, $key) {
        if (strpos($key, 'e:') === 0) {
            return headAmount($row, 'earnings', substr($key, 2));
        }
        if (strpos($key, 'd:') === 0) {
            return headAmount($row, 'deductions', substr($key, 2));
        }
        return (float) $row[$key];
    };

    // Drop components that are zero for every employee (Basic always stays)
    $used = function ($list) use ($rows, $value) {
        return array_filter($list, function ($key) use ($rows, $value) {
            if ($key === 'BASIC') {
                return true;
            }
            foreach ($rows as $row) {
                if (round($value($row, $key), 2) != 0) {
                    return true;
                }
            }
            return false;
        }, ARRAY_FILTER_USE_KEY);
    };
    $earningRows = $used($earningRows);
    $deductionRows = $used($deductionRows);

    $fmt = function ($n) {
        return number_format($n, 0, '.', '');
    };
    $esc = function ($s) {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    };

    // Grand totals per row key
    $keys = array_merge(array_keys($earningRows), array_keys($deductionRows), array('gross', 'total_deduction', 'netAmt'));
    $totals = array_fill_keys($keys, 0);
    foreach ($rows as $row) {
        foreach ($keys as $key) {
            $totals[$key] += $value($row, $key);
        }
    }

    $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(8, 8, 8);
    $pdf->SetAutoPageBreak(true, 10);

    $cell = 'border:0.3px solid #000000;';
    $shade = 'background-color:#E6E6E6;';
    $chunks = array_chunk($rows, SHEET_EMPLOYEES_PER_PAGE);
    foreach ($chunks as $c => $chunk) {
        $withTotal = ($c === count($chunks) - 1);
        $cols = count($chunk) + ($withTotal ? 1 : 0);
        $wLabel = 16;
        $wCol = round((100 - $wLabel) / max($cols, 1), 3);

        $pdf->AddPage();
        $pdf->SetFont('freesans', 'B', 14);
        $pdf->Cell(0, 7, ORG_NAME, 0, 1, 'C');
        $pdf->SetFont('freesans', '', 9);
        $pdf->Cell(0, 5, ORG_ADDRESS, 0, 1, 'C');
        $pdf->SetFont('freesans', 'B', 12);
        $pdf->Cell(0, 8, "Salary Statement - $month $selectedYear" . (count($chunks) > 1 ? ' (Page ' . ($c + 1) . ' of ' . count($chunks) . ')' : ''), 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont('freesans', '', 7.5);

        // One table row: label, then one cell per employee (+ total)
        $line = function ($label, $cells, $total, $style = '', $align = 'right') use ($cell, $wLabel, $wCol, $withTotal) {
            $t = '<tr><td width="' . $wLabel . '%" style="' . $cell . $style . '">' . $label . '</td>';
            foreach ($cells as $v) {
                $t .= '<td width="' . $wCol . '%" align="' . $align . '" style="' . $cell . $style . '">' . $v . '</td>';
            }
            if ($withTotal) {
                $t .= '<td width="' . $wCol . '%" align="' . $align . '" style="' . $cell . $style . 'font-weight:bold;">' . $total . '</td>';
            }
            return $t . '</tr>';
        };
        $amounts = function ($key) use ($chunk, $value, $fmt) {
            return array_map(function ($row) use ($key, $value, $fmt) {
                return $fmt($value($row, $key));
            }, $chunk);
        };
        $section = function ($title) use ($cell, $shade, $cols, $wLabel) {
            return '<tr><td colspan="' . ($cols + 1) . '" style="' . $cell . $shade . 'font-weight:bold;">' . $title . '</td></tr>';
        };

        $html = '<table cellpadding="2.5" cellspacing="0">';
        $html .= $line('<b>EMP NO</b>', array_map(function ($r) { return '<b>' . $r['EMPNO'] . '</b>'; }, $chunk), '<b>TOTAL</b>', $shade, 'center');
        $html .= $line('<b>NAME</b>', array_map(function ($r) use ($esc) { return $esc($r['NAME']); }, $chunk), '', $shade, 'center');

        $html .= $section('EARNINGS');
        foreach ($earningRows as $key => $label) {
            $html .= $line($esc($label), $amounts($key), $fmt($totals[$key]));
        }
        $html .= $line('<b>GROSS</b>', array_map(function ($v) { return "<b>$v</b>"; }, $amounts('gross')), $fmt($totals['gross']), 'font-weight:bold;');

        $html .= $section('DEDUCTIONS');
        foreach ($deductionRows as $key => $label) {
            $html .= $line($esc($label), $amounts($key), $fmt($totals[$key]));
        }
        $html .= $line('<b>TOTAL DEDUCTIONS</b>', array_map(function ($v) { return "<b>$v</b>"; }, $amounts('total_deduction')), $fmt($totals['total_deduction']), 'font-weight:bold;');

        $html .= $line('<b>NET PAY</b>', array_map(function ($v) { return "<b>$v</b>"; }, $amounts('netAmt')), $fmt($totals['netAmt']), $shade . 'font-weight:bold;');
        $html .= $line('<b>SIGNATURE</b><br><br><br>', array_fill(0, count($chunk), ''), '');
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, false, false, '');
    }

    $pdf->Output('PaySlipStatement_' . $month . '_' . $selectedYear . '.pdf', 'I');
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
