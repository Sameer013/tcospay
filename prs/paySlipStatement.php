<?php
// Salary Sheet: one table per designation, one column per salary component used this month.
require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
require_once 'includes/org.php';
require_once 'includes/payroll_report.php';

try {
    list($selectedMonth, $selectedYear) = reportPeriod();
    $month = reportMonthName($selectedMonth);
    $sheet_id = reportSheetId($db, $selectedMonth, $selectedYear);

    $payroll = loadPayroll($db, $sheet_id);
    $rows = $payroll['rows'];
    $earningHeads = $payroll['earningHeads'];
    $deductionHeads = $payroll['deductionHeads'];
    if (empty($rows)) {
        reportStop('No data found for ' . $month . ' ' . $selectedYear);
    }

    // Columns after EMPNO / NAME: key => header
    $columns = array('BASIC' => 'BASIC', 'LSA' => 'LSA');
    foreach ($earningHeads as $head) {
        $columns['e:' . $head] = $head;
    }
    $columns['gross'] = 'GROSS';
    $columns['lwopamt'] = 'LWOP';
    foreach ($deductionHeads as $head) {
        $columns['d:' . $head] = $head;
    }
    $columns['total_deduction'] = 'TOTAL DED';
    $columns['netAmt'] = 'NET PAY';

    $value = function ($row, $key) {
        if (strpos($key, 'e:') === 0) {
            return headAmount($row, 'earnings', substr($key, 2));
        }
        if (strpos($key, 'd:') === 0) {
            return headAmount($row, 'deductions', substr($key, 2));
        }
        return (float) $row[$key];
    };
    $fmt = function ($n) {
        return number_format($n, 0, '.', '');
    };

    $tableHead = '<table border="0.5" cellpadding="2" align="center"><thead><tr>'
        . '<th style="background-color:#D3D3D3;" width="25">EMP NO</th>'
        . '<th style="background-color:#D3D3D3;" width="80">NAME</th>';
    foreach ($columns as $title) {
        $tableHead .= '<th style="background-color:#D3D3D3;">' . htmlspecialchars($title) . '</th>';
    }
    $tableHead .= '<th style="background-color:#D3D3D3;" width="45">SIGNATURE</th></tr></thead><tbody>';

    $totalsRow = function ($label, $totals, $bold) use ($columns, $fmt) {
        $html = '<tr><td colspan="2">' . ($bold ? "<b>$label</b>" : $label) . '</td>';
        foreach (array_keys($columns) as $key) {
            $html .= '<td>' . ($bold ? '<b>' . $fmt($totals[$key]) . '</b>' : $fmt($totals[$key])) . '</td>';
        }
        return $html . '<td></td></tr>';
    };

    $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(8, 8, 8);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();
    $pdf->SetFont('freesans', 'B', 15);
    $pdf->Cell(0, 8, ORG_NAME, 0, 1, 'C');
    $pdf->SetFont('freesans', '', 10);
    $pdf->Cell(0, 6, ORG_ADDRESS, 0, 1, 'C');
    $pdf->SetFont('freesans', 'B', 13);
    $pdf->Cell(0, 9, "Salary Statement - $month $selectedYear", 0, 1, 'C');

    $zero = array_fill_keys(array_keys($columns), 0);
    $grand = $zero;
    $group = $zero;
    $currentDesignation = false;
    $html = '';

    foreach ($rows as $row) {
        $designation = $row['DESIGNATION'] !== null ? $row['DESIGNATION'] : 'No designation';
        if ($designation !== $currentDesignation) {
            if ($currentDesignation !== false) {
                $html .= $totalsRow('Group Total', $group, false) . '</tbody></table>';
                $pdf->writeHTML($html, true, false, false, false, '');
                $group = $zero;
            }
            $currentDesignation = $designation;
            $pdf->SetFont('freesans', 'B', 10);
            $pdf->Cell(0, 8, 'Designation: ' . $designation, 0, 1, 'L');
            $pdf->SetFont('freesans', '', 6.5);
            $html = $tableHead;
        }

        $html .= '<tr><td width="25">' . $row['EMPNO'] . '</td><td width="80" align="left">' . htmlspecialchars($row['NAME']) . '</td>';
        foreach (array_keys($columns) as $key) {
            $v = $value($row, $key);
            $group[$key] += $v;
            $grand[$key] += $v;
            $html .= '<td>' . $fmt($v) . '</td>';
        }
        $html .= '<td width="45"></td></tr>';
    }

    $html .= $totalsRow('Group Total', $group, false);
    $html .= $totalsRow('Grand Total', $grand, true);
    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Output('PaySlipStatement_' . $month . '_' . $selectedYear . '.pdf', 'I');
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
