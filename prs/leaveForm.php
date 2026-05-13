<?php
    ini_set('display_errors',1);
    session_start();
    if (!isset($_SESSION['user']))
    {
        header("location:login.php");
        exit;
    }
// create another payslip 
$userId=$_SESSION['user'];
require_once('tcpdf/tcpdf.php');
require_once 'includes/dbconn.php';
  
if ($userId=='Admin' || $userId=='Super Admin')
	{
$sql = "SELECT la.slno, la.EMPNO, la.DTE, la.DESCR AS REASON, la.FDATE, la.TDATE, la.NOL, la.LWOP, la.HLEAVE, la.MED, la.LEVWOPAY, la.NOOFDAYS, la.LTYPE, e.NAME, e.DSGCODE, lm.Descr AS LEAVENAME, d.DESCR
FROM leaveapply la
JOIN leavemst lm ON la.LTYPE = lm.LeaveName
JOIN empmast e ON la.EMPNO = e.EMPNO
JOIN dsgmast d ON e.DSGCODE = d.DCODE
WHERE la.STATUS is null";
    }else{
        echo 'NOT ALLOWED';
    }
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
for ($index = 0; $index < count($rows); $index += 2) {
    $pdf->AddPage();
    printData($pdf, $rows[$index], $index);

if ($index + 1 < count($rows)) {
    $pdf->SetLineStyle(['width' => 0.3, 'dash' => '2,2', 'color' => [0, 0, 0]]);
    $y = $pdf->GetY();
    $pdf->Line(10, $y, 200, $y);
    $pdf->Ln(15);

    printData($pdf, $rows[$index + 1], $index + 1);
}
}

function printData($pdf, $row, $index) {
    $pdf->SetFont('freesans', '', 13);
    $pdf->Cell(0, 0, "JAMSHEDPUR PUBLIC SCHOOL", 0, 1, 'C');
    // $pdf->SetY($pdf->GetY() - 9);
    $pdf->SetFont('freesans', '', 12);
    $pdf->Cell(0, 0, "PANCHAVATI ROAD, NEW BARIDIH, JAMSHEDPUR - 831017", 0, 1, 'C');

    
    // Reset line style to solid
    $pdf->SetLineStyle(['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]]);

    // Draw line below address
    $y = $pdf->GetY();
    $pdf->Line(10, $y, 200, $y);

    $pdf->SetFont('freesans', '', 12);
    $pdf->Cell(0, 0, "Date : " .  date('d-m-Y', strtotime($row['DTE'])), 0, 1, 'R');

    // $pdf->SetY($pdf->GetY() - 2);

    $pdf->SetFont('freesans', '', 12);
    $pdf->Cell(0, 0, "The Principal / Secretary,", 0, 1, 'L');
    $pdf->Cell(0, 0, "Jamshedpur Public School", 0, 1, 'L');
    $pdf->Cell(0, 0, "New Baridih, Jamshedpur.", 0, 1, 'L');
    $pdf->Cell(0, 15, "Respected Madam,", 0, 1, 'L');

    // $pdf->MultiCell(0, 15, "I may be granted        ___" . $row['LEAVENAME']. "___          for         ___".$row['NOOFDAYS']."___          Day/Days from   ___".$row['FDATE']."___         to      ___".$row['TDATE']."___         for reasons stated below:", 0, 'L');

    // Start of the sentence
    $text1 = 'I may be granted      ';
    $leaveName = $row['LEAVENAME'];
    $text2 = '      for     ';
    $noOfDays = $row['NOOFDAYS'];
    $text3 = '      Day/Days from   ';
    $fromDate = date('d-m-Y', strtotime($row['FDATE']));
    $text4 = '      to      ';
    $toDate = date('d-m-Y', strtotime($row['TDATE']));
    $text5 = '      for reasons stated below:';

    // Construct full sentence
    $fullLine = $text1 . $leaveName . $text2 . $noOfDays . $text3 . $fromDate . $text4 . $toDate . $text5;


    $pdf->SetFont('freesans', '', 12);
    $pdf->Write(6, $text1);

    $pdf->SetFont('freesans', 'U', 12);
    $pdf->Write(6, $leaveName);

    $pdf->SetFont('freesans', '', 12);
    $pdf->Write(6, $text2);

    $pdf->SetFont('freesans', 'U', 12);
    $pdf->Write(6, $noOfDays);

    $pdf->SetFont('freesans', '', 12);
    $pdf->Write(6, $text3);

    $pdf->SetFont('freesans', 'U', 12);
    $pdf->Write(6, $fromDate);

    $pdf->SetFont('freesans', '', 12);
    $pdf->Write(6, $text4);

    $pdf->SetFont('freesans', 'U', 12);
    $pdf->Write(6, $toDate);

    $pdf->SetFont('freesans', '', 12);
    $pdf->Write(6, $text5);

    $pdf->Ln(10);

    $pdf->SetFont('freesans', 'U', 12);
    $pdf->MultiCell(0, 15   , $row['REASON']. " ", 0, 'L');

    $pdf->SetFont('freesans', '', 12);
    $pdf->Cell(95, 6, 'Encl : 1. Written work for classes', 0, 0, 'L');
    $pdf->Cell(95, 6, 'Yours faithfully,', 0, 1, 'L');

    $pdf->Cell(95, 6, '          2. Medical Certificate / Any other', 0, 0, 'L');
    $pdf->Cell(95, 6, '', 0, 1, 'L');

    $pdf->Cell(95, 6, '', 0, 0, 'L');
    $pdf->Cell(95, 6, 'Name                  : ' . $row['NAME'], 0, 1, 'L');

    $pdf->Cell(95, 6, 'Sanctioned / Not Sanctioned', 0, 0, 'L');
    $pdf->Cell(95, 6, 'Designation        : ' . $row['DESCR'], 0, 1, 'L');

    $pdf->Cell(95, 6, '', 0, 0, 'L');
    $pdf->Cell(95, 6, 'Employee Code : ' . $row['EMPNO'], 0, 1, 'L');
    
    $pdf->Cell(95, 12, 'Principal / Secretary ________________', 0, 0, 'L');
    $pdf->Cell(95, 12, 'Signature of Employee: _______________', 0, 1, 'L');

    $pdf->Ln(10);

}
$pdf->Output('Leave Form ' . '_' . '.pdf', 'I');

?>
