<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';

try {
   $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
function getMonthName($num) {
    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];

    if (isset($months[$num])) {
        return $months[$num];
    } else {
        return "Invalid month number";
    }
}
$month = getMonthName($selectedMonth);
function checkSheetExistence($db, $selectedMonth, $selectedYear) {

    $sql_sheet_id = "SELECT sheet_id FROM sheet WHERE mnth = $selectedMonth AND yr = $selectedYear";

    $stmt_sheet_id = $db->prepare($sql_sheet_id);
    $stmt_sheet_id->execute();
    $sheet_id = $stmt_sheet_id->fetchColumn();

    if ($sheet_id) {
        return $sheet_id;
    } else {
        echo '<script type="text/javascript">alert("No Month Data");
        window.location.href = "index_salary.php";</script>';
        die();
    }
}
$sheet_id = checkSheetExistence($db, $selectedMonth, $selectedYear);

$db->exec("SET SESSION sql_mode = (SELECT REPLACE(@@SESSION.sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

    $sql = "SELECT dm.designation, e.EMPNO, NAME, t.basic, t.adj, t.gross, t12.HRA, t11.DA,  t21.PF, t22.ESIC, t.MNTHSAL FROM empmast e
    JOIN designationmaster dm ON e.DSGCODE=dm.desgcode
    JOIN sheet_det sd ON e.empno=sd.empno
    JOIN trnsmst t ON e.empno=t.empno

    JOIN (SELECT t1.trnsno, amount as DA FROM trnsdet1 t1
        WHERE DESCR='DA'
        GROUP BY trnsno) as t11 ON t.TRNSNO=t11.TRNSNO
     JOIN (SELECT t1.trnsno, amount as HRA FROM trnsdet1 t1
        WHERE DESCR='HRA'
        GROUP BY trnsno) as t12 ON t.TRNSNO=t11.TRNSNO

    JOIN (SELECT t2.trnsno, amount as PF FROM trnsdet2 t2
          WHERE DESCR='PF'
          GROUP BY trnsno) as t21 ON t.TRNSNO=t21.TRNSNO

    JOIN (SELECT t2.trnsno, amount as ESIC FROM trnsdet2 t2
          WHERE DESCR='ESIC'
          GROUP BY trnsno) as t22 ON t.TRNSNO=t22.TRNSNO

    WHERE t.sheet_id= $sheet_id
    GROUP BY dm.designation, t.trnsno
    ORDER BY dm.desgcode, e.empno";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo '<script type="text/javascript">';
    echo 'alert("No data found in the database.");';
    echo 'window.location.href = "index_salary.php";';
    echo '</script>';
    die();
}
    $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();
    $pdf->SetFont('freesans', 'B', 15);
    $pdf->Cell(0, 10, "Salary Statement - $month $selectedYear", 0, 1, 'C');

    $pdf->SetFont('freesans', '', 8);

    $currentDesignation = null;
    $htmlTable = '';
    $totalBasic = 0;
    $totalAdj = 0;
    $totalGross = 0;
    $totalLwop = 0;
    $totalDa= 0;
    $totalEpf = 0;
    $totalpt = 0;
    $totalNetPay = 0;
    $totalBasicAll = 0;
    $totalAdjAll = 0;
    $totalGrossAll = 0;
    $totalLwopAll = 0;
    $totalDaAll = 0;
    $totalEpfAll = 0;
    $totalptAll = 0;
    $totalNetPayAll = 0;
    foreach ($rows as $row) {
        if ($currentDesignation !== $row['designation']) {
            if ($currentDesignation !== null) {
                $htmlTable .= '<tr>';
                $htmlTable .= '<td colspan="2">Group Total</td>';
                $htmlTable .= '<td>' . $totalBasic . '</td>';
                $htmlTable .= '<td>' . $totalAdj . '</td>';
                $htmlTable .= '<td>' . $totalGross . '</td>';
                $htmlTable .= '<td>' . $totalLwop . '</td>';
                $htmlTable .= '<td>' . $totalDa . '</td>';
                $htmlTable .= '<td>' . $totalEpf . '</td>';
                $htmlTable .= '<td>' . $totalpt . '</td>';
                $htmlTable .= '<td>' . $totalNetPay . '</td>';
                $htmlTable .= '<td></td>';
                $htmlTable .= '</tr>';
                $htmlTable .= '</tbody></table>';
                $pdf->writeHTML($htmlTable, true, false, false, false, '');

                $totalBasic = 0;
                $totalAdj = 0;
                $totalGross = 0;
                $totalLwop = 0;
                $totalDa= 0;
                $totalEpf = 0;
                $totalpt = 0;
                $totalNetPay = 0;
            }

             $currentDesignation = $row['designation'];
               $pdf->SetFont('freesans', 'B', 10);
            $pdf->Cell(0, 10, "Designation: $currentDesignation", 0, 1, 'L');
               $pdf->SetFont('freesans', '', 8);
            $htmlTable = '<table border="0.5" align="center">';
$htmlTable .= '<thead>';
$htmlTable .= '<tr>';
// $htmlTable .= '<th rowspan="2" style="background-color: #D3D3D3; text-align: center;">DESIG</th>';
$htmlTable .= '<th rowspan="2" style="background-color: #D3D3D3; text-align: center;">EMPNO</th>';
$htmlTable .= '<th rowspan="2" style="background-color: #D3D3D3; text-align: center;">NAME</th>';
$htmlTable .= '<th colspan="4" style="background-color: #F0FFF0;"></th>';
$htmlTable .= '<th colspan="1" style="background-color: #F0FFF0; text-align: center;">A</th>';
$htmlTable .= '<th colspan="2" style="background-color: #F0FFF0; text-align: center;">D</th>';
$htmlTable .= '<th colspan="2" style="background-color: #F0FFF0;"></th>';
$htmlTable .= '</tr>';
$htmlTable .= '<tr>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">BASIC</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">ADJ</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">GROSS</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">HRA</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">D.A</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">EPF</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">PROF. TAX</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">NET PAY</th>';
$htmlTable .= '<th style="background-color: #D3D3D3; text-align: center;">Employee\'s signature </th>';
$htmlTable .= '</tr>';
$htmlTable .= '</thead>';
$htmlTable .= '<tbody>';
        }
        $totalBasic += $row['basic'];
        $totalAdj += $row['adj'];
        $totalGross += $row['gross'];
        $totalLwop += $row['HRA'];
        $totalDa+= $row['DA'];
        $totalEpf += $row['PF'];
        $totalpt += 0;
        $totalNetPay += $row['MNTHSAL'];
         $totalBasicAll += $row['basic'];
    $totalAdjAll += $row['adj'];
    $totalGrossAll += $row['gross'];
    $totalLwopAll += $row['HRA'];
    $totalDaAll += $row['DA'];
    $totalEpfAll += $row['PF'];
    $totalptAll += 0;
    $totalNetPayAll += $row['MNTHSAL'];
        $htmlTable .= '<tr>';
        // $htmlTable .= '<td align="center">' . $row['designation'] . '</td>';
        $htmlTable .= '<td>' . $row['EMPNO'] . '</td>';
        $htmlTable .= '<td>' . $row['NAME'] . '</td>';
        $htmlTable .= '<td>' . $row['basic'] . '</td>';
        $htmlTable .= '<td>' . $row['adj'] . '</td>';
        $htmlTable .= '<td>' . $row['gross'] . '</td>';
        $htmlTable .= '<td>' . $row['HRA'] . '</td>';
        $htmlTable .= '<td>' . $row['DA'] . '</td>';
        $htmlTable .= '<td>' . $row['PF'] . '</td>';
        $htmlTable .= '<td></td>';
        $htmlTable .= '<td>' . $row['MNTHSAL'] . '</td>';
        $htmlTable .= '<td></td>';
        $htmlTable .= '</tr>';
    }

$htmlTable .= '<tr>';
$htmlTable .= '<td colspan="2">Group Total</td>';
$htmlTable .= '<td>' . $totalBasic . '</td>';
$htmlTable .= '<td>' . $totalAdj . '</td>';
$htmlTable .= '<td>' . $totalGross . '</td>';
$htmlTable .= '<td>' . $totalLwop . '</td>';
$htmlTable .= '<td>' . $totalDa . '</td>';
$htmlTable .= '<td>' . $totalEpf . '</td>';
$htmlTable .= '<td>' . $totalpt . '</td>';
$htmlTable .= '<td>' . $totalNetPay . '</td>';
$htmlTable .= '<td></td>';
$htmlTable .= '</tr>';
$htmlTable .= '<tr>';
$htmlTable .= '<td colspan="11">&nbsp;</td>';
$htmlTable .= '<td colspan="11">&nbsp;</td>';
$htmlTable .= '<td colspan="11">&nbsp;</td>';
$htmlTable .= '<td colspan="11">&nbsp;</td>';
$htmlTable .= '</tr>';
$htmlTable .= '<tr>';
$htmlTable .= '<td colspan="2"><strong>Grand Total</strong></td>';
$htmlTable .= '<td><strong>' . $totalBasicAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalAdjAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalGrossAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalLwopAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalDaAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalEpfAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalptAll . '</strong></td>';
$htmlTable .= '<td><strong>' . $totalNetPayAll . '</strong></td>';
$htmlTable .= '<td></td>';
$htmlTable .= '</tr>';
$htmlTable .= '</tbody></table>';
$pdf->writeHTML($htmlTable, true, false, false, false, '');
    $pdf->Output('PaySlipStatement_' . $month . '_' . $selectedYear . '.pdf', 'I');


} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
