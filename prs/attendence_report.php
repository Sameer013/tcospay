<?php

require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
$selectedDate = isset($_GET['selected_date']) ? $_GET['selected_date'] : '';
$sql = "SELECT attn.*, em.NAME FROM `attnddet`as attn
LEFT JOIN empmast AS em ON em.EMPNO = attn.empno WHERE dt='$selectedDate'  AND attn.present='1'";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo '<script type="text/javascript">';
    echo 'alert("No data found in the database.");';
    echo 'window.location.href = "index_attendanceMachine.php";';
    echo '</script>';
    die();
}

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('freesans', 'B', 12);
$pdf->Sety(0);
$pdf->Cell(0, 15, "Attendance Report $selectedDate", 0, 1, 'C');

$pdf->SetFont('freesans', 'B', 10);
$pdf->SetFillColor(255, 255, 255);

$pdf->Cell(20, 10, 'SI', 1, 0, 'L', 1);
$pdf->Cell(20, 10, 'EMPID', 1, 0, 'L', 1);
$pdf->Cell(50, 10, 'Name', 1, 0, 'L', 1);
$pdf->Cell(30, 10, 'Coming in', 1, 0, 'L', 1);
$pdf->Cell(20, 10, 'Late', 1, 0, 'L', 1);

$pdf->Cell(50, 5, 'Last working day', 1, 0, 'C', 1);
$pdf->Ln();
$pdf->setx(150);
$pdf->Cell(25, 5, 'Going out', 1, 0, 'L', 1);
$pdf->Cell(25, 5, 'working hours', 1, 1, 'L', 1);

$si = 1;
foreach ($rows as $data) {
    $pdf->SetFont('freesans', '', 10);
    $empId = isset($data['empno']) ? $data['empno'] : '';
    $name = isset($data['NAME']) ? $data['NAME'] : '';
    $intime = isset($data['intime']) ? $data['intime'] : '';
    $outtime = isset($data['outtime']) ? $data['outtime'] : '';
    $late = isset($data['late']) ? $data['late'] : '';
    $workHour = isset($data['workDur']) ? $data['workDur'] : '';

    $pdf->Cell(20, 8, $si++, 1);
    $pdf->Cell(20, 8, $empId, 1);
    $pdf->Cell(50, 8, $name, 1);
    $pdf->Cell(30, 8, $intime, 1);
    $pdf->Cell(20, 8, $late, 1);

    $part1 = substr($outtime, 0, strlen($outtime) / 1);
    $part2 = substr($workHour, strlen($workHour) / 2);
    $pdf->Cell(25, 8, $part1, 1, 0, 'L', 1);
    $pdf->Cell(25, 8, $part2, 1, 1, 'L', 1);
}

$pdf->Output('AttendanceReport.pdf', 'I');
