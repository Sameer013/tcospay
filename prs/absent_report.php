<?php

require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
$selectedDate = isset($_GET['selected_date']) ? $_GET['selected_date'] : '';
$sql = "SELECT em.empno, em.NAME
FROM empmast AS em
LEFT JOIN attdata AS att ON em.EMPNO = att.uid AND att.dt = '$selectedDate'
WHERE att.uid IS NULL
    AND em.EMPNO NOT IN (
        SELECT ld.EMPNO
        FROM leavedet AS ld
        WHERE (
            ld.EMPNO IS NULL
            OR ('$selectedDate' >= ld.FDATE AND '$selectedDate' <= ld.TDATE)
            OR ld.FDATE IS NULL
            OR ld.TDATE IS NULL
        )
    )";

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

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->AddPage();
$pdf->SetFont('freesans', 'B', 12);
$pdf->Cell(0, 15, "Absent Report $selectedDate", 0, 1, 'C');
$pdf->Line(10, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
$pdf->SetFont('freesans', '', 10);
$pdf->SetFillColor(255, 255, 255);

$pdf->Cell(30, 8, 'SI', 0, 0, 'L', 1);
$pdf->Cell(50, 8, 'EMPNO', 0, 0, 'L', 1);
$pdf->Cell(50, 8, 'Name', 0, 1, 'L', 1);

$si = 1;
foreach ($rows as $data) {
    $empId = isset($data['empno']) ? $data['empno'] : '';
    $name = isset($data['NAME']) ? $data['NAME'] : '';

    $pdf->Cell(30, 8, $si++, 0);
    $pdf->Cell(50, 8, $empId, 0);
    $pdf->Cell(50, 8, $name, 0);
    $pdf->Ln();
}

$pdf->Output('AbsentReport.pdf', 'I');

?>
