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
function getDaysInMonth($month, $year) {
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}
function checkSheetExistence($db, $selectedMonth, $selectedYear) {

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
if ($userId=='Admin' || $userId=='Super Admin')
	{
$sql = "SELECT s.mnth, s.yr, t.arrear, t.TRNSNO, t.EMPNO, e.NAME,d.DESCR DESIGNATION, e.SEFNO ESIC_NO, e.PFACNO, e.PAN, b.DESCR BANK, e.ACCNO, t.BASIC, MAX(CASE WHEN t1.DESCR = 'DA' THEN t1.amount END) AS DA, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount END) AS HRA, MAX(CASE WHEN t1.DESCR = 'TA' THEN t1.amount END) AS TA, MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount END) AS SPL, MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount END) AS PF, MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount END) AS ESIC, SUM(t2.amount) AS total_deduction, t.lwop, t.adj, t.gross, t.MNTHSAL, sd.cl, sd.spleave, ROUND((sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays), 2) as dpaid, sd.attnd as dwork, (sd.cl + sd.spleave + sd.med_leave) as LWP, IFNULL((ROUND((t.lwop /$daysInMonth) * (SUM(t1.Amount) + t.basic), 2)), 0) as lwopamt,  sd.med_leave
FROM trnsmst t
JOIN  sheet s ON t.sheet_id = s.sheet_id
JOIN empmast e ON t.EMPNO = e.EMPNO
JOIN dsgmast d ON e.DSGCODE = d.DCODE
JOIN bankmast b ON e.BID = b.BID
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO AND (t1.DESCR IN ('DA', 'HRA', 'TA', 'SPL'))
LEFT JOIN trnsdet2 t2 ON t.TRNSNO = t2.TRNSNO AND (t2.DESCR IN ('PF', 'ESIC'))
WHERE t.sheet_id=$sheet_id
GROUP BY t.TRNSNO";
    }else{
$sql = "SELECT s.mnth, s.yr, t.arrear, t.TRNSNO, t.EMPNO, e.NAME,d.DESCR DESIGNATION, e.SEFNO ESIC_NO, e.PFACNO, e.PAN, b.DESCR BANK, e.ACCNO, t.BASIC, MAX(CASE WHEN t1.DESCR = 'DA' THEN t1.amount END) AS DA, MAX(CASE WHEN t1.DESCR = 'HRA' THEN t1.amount END) AS HRA, MAX(CASE WHEN t1.DESCR = 'TA' THEN t1.amount END) AS TA, MAX(CASE WHEN t1.DESCR = 'SPL' THEN t1.amount END) AS SPL, MAX(CASE WHEN t2.DESCR = 'PF' THEN t2.amount END) AS PF, MAX(CASE WHEN t2.DESCR = 'ESIC' THEN t2.amount END) AS ESIC, SUM(t2.amount) AS total_deduction, t.lwop, t.adj, t.gross, t.MNTHSAL, sd.cl, sd.spleave, ROUND((sd.cl + sd.spleave + sd.off_days + sd.med_leave + sd.attnd + sd.hdays), 2) as dpaid, sd.attnd as dwork, (sd.cl + sd.spleave + sd.med_leave) as LWP, IFNULL((ROUND((t.lwop /$daysInMonth) * (SUM(t1.Amount) + t.basic), 2)), 0) as lwopamt,  sd.med_leave
FROM trnsmst t
JOIN  sheet s ON t.sheet_id = s.sheet_id
JOIN empmast e ON t.EMPNO = e.EMPNO
JOIN dsgmast d ON e.DSGCODE = d.DCODE
JOIN bankmast b ON e.BID = b.BID
JOIN sheet_det sd ON t.sheet_id = sd.sheet_id AND t.EMPNO = sd.empno
JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO AND (t1.DESCR IN ('DA', 'HRA', 'TA', 'SPL'))
LEFT JOIN trnsdet2 t2 ON t.TRNSNO = t2.TRNSNO AND (t2.DESCR IN ('PF', 'ESIC'))
WHERE t.sheet_id=$sheet_id AND e.EMPNO=$userId
GROUP BY t.TRNSNO";}
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo '<script type="text/javascript">';
    echo 'alert("No data found in the database.' . $selectedMonth . ' and year: ' . $selectedYear . '");';
  //  echo 'window.location.href = "index_salary.php";';
    echo '</script>';
    die();
}
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
for ($index = 0; $index < count($rows); $index += 2) {
    $pdf->AddPage();
    printData($pdf, $rows[$index], $index, $selectedYear, $month);

    if ($index + 1 < count($rows)) {
        $pdf->Ln(15);
        printData($pdf, $rows[$index + 1], $index + 1, $selectedYear, $month);
    }
}

function inwords($number, $true = true) {
    $no = floor($number);
    $point = number_format(number_format($number, 2, '.', '') - $no, 2, '', '');
    $digitpoint = strlen($point);
    $digit = strlen($no);
    $ones = array(0 => 'Zero', '1' => 'One', '2' => 'Two', '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six', '7' => 'Seven', '8' => 'Eight', '9' => 'Nine', '10' => 'Ten');
    $tens = array('11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fourteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen', '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty', 40 => 'Forty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninety');
    $hundred = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    $string_word = array();
    $numbers = array_reverse(str_split($no, 1));
    $i = 0;
    while ($i < $digit) {
        if ($i == 0) {
            if (!isset($numbers[2]) && !isset($numbers[1])) {
                $string_word[] = $ones[$numbers[0]];
            }
        }
        if ($i == 1) {
            $temp = intval($numbers[1] . "" . $numbers[0]);
            $ten = intval($numbers[1] . "0");
            if ($ten == 0 && $temp == 0) {
            } else if ($temp <= 10) {
                $string_word[] = $ones[$temp];
            } else if ($temp > 11 && $temp <= 20) {
                $string_word[] = $tens[$temp];
            } else if (isset($tens[$temp])) {
                $string_word[] = $tens[$temp];
            } else {
                $string_word[] = $tens[$ten] . " " . $ones[$numbers[0]];
            }
        }
        if ($i == 2) {
            if (!isset($numbers[3]) && $numbers[2] != 0) {
                $string_word[] = $ones[$numbers[2]] . " " . $hundred[1];
            }
            if (isset($numbers[3]) && $numbers[2] != 0) {
                $string_word[] = $ones[$numbers[2]] . " " . $hundred[1];
            }
        }
        if ($i == 3 || $i == 4) {
            if (isset($numbers[4])) {
                $temp = intval($numbers[4] . "" . $numbers[3]);
                $ten = intval($numbers[4] . "0");
                if ($temp == 0 && $ten == 0) {
                } else if ($temp == 10) {
                    $string_word[] = $ones[$temp] . " " . $hundred[2];
                } elseif ($temp > 10 && $temp <= 20) {
                    $string_word[] = $tens[$temp] . " " . $hundred[2];
                } else {
                    $num = ($numbers[3] == 0) ? '' : $ones[$numbers[3]];
                    $string_word[] = $tens[$ten] . " " . $num . " " . $hundred[2];
                }
            } else {
                $string_word[] = $ones[$numbers[3]] . " " . $hundred[2];
            }
            $i++;
        }
        if ($i == 5 || $i == 6) {
            if (isset($numbers[6])) {
                $temp = intval($numbers[6] . "" . $numbers[5]);
                $ten = intval($numbers[6] . "0");
                if ($numbers[5] == 0 && $numbers[6] == 0) {
                } elseif ($temp == 10) {
                    $string_word[] = $ones[$temp] . " " . $hundred[5];
                } elseif ($temp > 10 && $temp <= 20) {
                    $string_word[] = $tens[$temp] . " " . $hundred[5];
                } else {
                    $num = ($numbers[5] == 0) ? '' : $ones[$numbers[5]];
                    $tens_1 = (!isset($tens[$ten])) ? '' : $tens[$ten];
                    $string_word[] = $tens_1 . " " . $num . " " . $hundred[3];
                }
            } else {
                $string_word[] = $ones[$numbers[5]] . " " . $hundred[3];
            }
            $i++;
        }
        if ($i == 7 || $i == 8) {
            if (isset($numbers[8])) {
                $temp = intval($numbers[8] . "" . $numbers[7]);
                $ten = intval($numbers[8] . "0");
                if ($numbers[7] == 0 && $numbers[8] == 0) {
                    continue;
                } else if ($temp == 10) {
                    $string_word[] = $ones[$temp] . " " . $hundred[4];
                } elseif ($temp > 10 && $temp <= 20) {
                    $string_word[] = $tens[$temp] . " " . $hundred[4];
                } else {
                    $num = ($numbers[7] == 0) ? '' : $ones[$numbers[7]];
                    $string_word[] = $tens[$ten] . " " . $num . " " . $hundred[4];
                }
            } else {
                $string_word[] = $ones[$numbers[7]] . " " . $hundred[4];
            }
            $i++;
        }
        if ($i == 9) {
            $string_word[] = $ones[$numbers[9]] . " " . $hundred[1];
        }
        $i++;
    }
    $str = array_reverse($string_word);
    return implode(' ', $str);
}

function printData($pdf, $row, $index, $selectedYear, $month) {
    $pdf->SetFont('freesans', 'B', 12);
  $pdf->Line(10, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
    $pdf->Cell(0, 15, "Sigma eSolution Private Limited", 0, 1, 'C');
    $pdf->SetY($pdf->GetY() - 10);
    $pdf->Cell(0, 15, "JAMSHEDPUR", 0, 1, 'C');
    $pdf->SetFont('freesans', 'B', 10);
    $pdf->Cell(0, 1, "Payslip for the month of $month $selectedYear", 0, 1, 'C');
     $pdf->Ln(5);
    $pdf->SetFont('freesans', 'B', 10);
    $pdf->Cell(0, 5, "Sl NO: " .  $row['TRNSNO'], 0, 0, 'L');
    $pdf->SetX(130);
$pdf->Cell(0, 5, "Designation: " . $row['DESIGNATION'], 0, 1, 'L');
    $pdf->Cell(0, 5, "Emp. ID/Name: " . $row['EMPNO'] . '/' . $row['NAME'], 0, 0, 'L');
     $pdf->SetX(130);
    $pdf->Cell(0, 5, "ESIC No.: " . $row['ESIC_NO'], 0, 1, 'L');
    $pdf->Cell(0, 5, "PF A/C No: " . $row['PFACNO'], 0, 0, 'L');
        $pdf->SetX(130);
    $pdf->Cell(0, 5, "Bank: " . $row['BANK'], 0, 1, 'L');
$pdf->Cell(0, 5, "PAN No.: " . $row['PAN'], 0, 0, 'L');
$pdf->SetX(130);
    $pdf->Cell(0, 5, "Bank A/C No.: " . $row['ACCNO'], 0, 1, 'L');

   $pdf->Ln(2);
    $pdf->SetFont('freesans', '', 10);
$pdf->MultiCell(0, 5, "Days Worked: " . $row['dwork'] . "     CL: " . $row['cl'] . "     SPL: " . $row['spleave'] . "     LWP: " . $row['cl'] . "     LWOP: " . $row['lwop'] . "     DAYS PAID: " . $row['dpaid'] . "     SICK LEAVE: " . $row['med_leave'], 0, 'L');
   $pdf->Ln(2);
    $pdf->SetFont('freesans', 'B', 10);
$pdf->Cell(0, 5, "Earnings : ", 0, 0, 'L');
  $pdf->SetX(120);
$pdf->Cell(0, 5, "Deductions : ", 0, 1, 'L');
$pdf->Line(10, $pdf->GetY(), 90, $pdf->GetY());
 $pdf->SetX(120);

$pdf->Line(120, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
$pdf->SetFont('freesans', '', 10);
$pdf->SetX(20);
$pdf->Cell(30, 5, "Basic: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ". $row['BASIC'], 0, 0, 'R');
    $pdf->SetX(130);
 $pdf->Cell(35, 5, "EPF: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ".  $row['PF'], 0, 1, 'R');
    $pdf->SetX(20);
$pdf->Cell(30, 5, "D.A: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ".  $row['DA'], 0, 0, 'R');
$pdf->SetX(130);
$pdf->Cell(35, 5, "ESIC: ", 0, 0, 'L');
$pdf->Cell(20, 5, "₹ ". $row['ESIC'], 0, 1, 'R');
$pdf->SetX(20);
$pdf->Cell(30, 5, "HRA: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ". (isset($row['HRA']) ? $row['HRA'] : '0.00'), 0, 1, 'R');
$pdf->SetX(20);
$pdf->Cell(30, 5, "TA: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ".(isset($row['TA']) ? $row['TA'] : '0.00'), 0, 1, 'R');
$pdf->SetX(20);
$pdf->Cell(30, 5, "Arrear: ", 0, 0, 'L');
$pdf->Cell(20, 5, "₹ " . (isset($row['arrear']) ? $row['arrear'] : '0.00'), 0, 0, 'R');
    $pdf->SetFont('freesans', 'B', 10);
     $pdf->Ln(5);
    $pdf->SetX(130);
    $pdf->Cell(35, 5, "Total deductions: ", 0, 0, 'L');
$pdf->Cell(20, 5, "₹ ". $row['total_deduction'], 0, 1, 'R');
$pdf->Ln(16);

$pdf->Line(120, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
    $pdf->SetX(10);
 $pdf->Cell(30, 5, "LWOP: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ". $row['lwopamt'], 0, 0, 'R');
     $pdf->SetX(130);
     $pdf->Line(10, $pdf->GetY(), 90, $pdf->GetY());
$pdf->Cell(30, 5, "Adjustment: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ".  $row['adj'], 0, 1, 'R');
    $pdf->SetX(10);
 $pdf->Cell(30, 5, "Gross Pay: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ". $row['gross'], 0, 0, 'R');
    $pdf->SetX(130);
    $pdf->SetDrawColor(0, 0, 0);

    $pdf->SetX(130);
    $pdf->SetDrawColor(0, 0, 0);
 $pdf->Cell(30, 5, "Net Salary: ", 0, 0, 'L');
$pdf->Cell(20, 5,"₹ ".  $row['MNTHSAL'], 0, 0, 'R');
    $pdf->SetDrawColor(0, 0, 0);
 $pdf->Ln(5);
$pdf->SetX(80);
$pdf->Cell(0, 5, "(Rupees " . inwords($row['MNTHSAL']) . " Only)", 0, 1, 'L');
}
$pdf->Output('PaySlip_' . $month . '_' . $selectedYear . '.pdf', 'I');

?>
