<?php
// sleep(2);
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

$pfceil = 20000; //Pf Ceil Value that will used for calculation change according to client requirments.
function getMonthName($num){
    $months = [ 1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec' ];

    if (isset($months[$num])) {
        return $months[$num];
    } else {
        return "Invalid month number";
    }
}
function getDaysInMonth($month, $year)
{
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

function checkArrear($db, $selectedMonth, $selectedYear)
{
    $check_vdate = $db->prepare("
        SELECT s.sheet_id FROM sheet s JOIN arrear a ON s.mnth = MONTH(a.dte) AND s.yr = YEAR(a.dte)
        WHERE MONTH(a.dte) = :mnth AND YEAR(a.dte) = :yr LIMIT 1 ");
    $check_vdate->bindParam(':mnth', $selectedMonth);
    $check_vdate->bindParam(':yr', $selectedYear);
    $check_vdate->execute();
    $end_id = $check_vdate->fetchColumn();
    return $end_id ? $end_id : 0;
}


try {
    include('includes/dbconn.php');
    // Fetching Sheet ID from selected month and year
     $sheet_fun = $db->prepare("SELECT get_sheetid($selectedMonth, $selectedYear) as sheet_id");
    $sheet_fun->execute();
    $sheet_id = $sheet_fun->fetch()['sheet_id'];

    // Returns end sheet ID if current month == end month else returns 0
    $end_id = checkArrear($db, $selectedMonth, $selectedYear);

    // $sql_empmast = "SELECT EMPNO, BASIC, STATE FROM empmast";
    $sql_empmast = "SELECT  e.EMPNO, e.BASIC, e.STATE
        FROM sheet_det sd
        JOIN empmast e on sd.empno=e.EMPNO
        JOIN sheet s on sd.sheet_id=s.sheet_id
        WHERE s.mnth = :month1 AND s.yr = :year1";
    $stmt_empmast = $db->prepare($sql_empmast);
    $stmt_empmast->bindParam(':month1', $selectedMonth);
    $stmt_empmast->bindParam(':year1', $selectedYear);  
    $stmt_empmast->execute();
    $empmast = $stmt_empmast->fetchAll();

    // Preparing lwop statment where lwop will be fetched later using the empno provided
    $sql_sheet_det = "SELECT lwop FROM `sheet_det` WHERE sheet_id = $sheet_id and empno = :empno ";
    $stmt_sheet_det = $db->prepare($sql_sheet_det);

    $stmt_insert = $db->prepare("INSERT INTO trnsmst (EMPNO, sheet_id, basic, MTHYR, lwop, DTE) VALUES (:empno, :sheet_id, COALESCE(:basic, 0), :mnthyr, :lwop, now())");

    // First Loop for Generating TRSNO.
    foreach ($empmast as $row) {
        $empno = $row['EMPNO'];
        $basic = $row['BASIC'];

        $stmt_sheet_det->bindParam(':empno', $empno);
        $stmt_sheet_det->execute();
        $lwop = $stmt_sheet_det->fetchColumn();
        $lwop = ($lwop === null || $lwop === '') ? 0.00 : $lwop;

        $month = getMonthName($selectedMonth);
        $mnthyr = $month . " " . $selectedYear;

        $sql_check_existence = "SELECT COUNT(*) FROM trnsmst WHERE EMPNO = :empno AND sheet_id = :sheet_id";
        $stmt_check_existence = $db->prepare($sql_check_existence);
        $stmt_check_existence->bindParam(':empno', $empno);
        $stmt_check_existence->bindParam(':sheet_id', $sheet_id);
        $stmt_check_existence->execute();
        $data_exists = $stmt_check_existence->fetchColumn() > 0;

        if ($data_exists) {
            $stmt_update = $db->prepare("UPDATE trnsmst SET basic = :basic, lwop = :lwop, DTE = now() WHERE EMPNO = :empno AND sheet_id = :sheet_id");
            $stmt_update->bindParam(':empno', $empno);
            $stmt_update->bindParam(':sheet_id', $sheet_id);
            $stmt_update->bindParam(':basic', $basic);
            $stmt_update->bindParam(':lwop', $lwop);
            $stmt_update->execute();
        }
        else {
            $stmt_insert->bindParam(':empno', $empno);
            $stmt_insert->bindParam(':sheet_id', $sheet_id);
            $stmt_insert->bindParam(':basic', $basic);
            $stmt_insert->bindParam(':mnthyr', $mnthyr);
            $stmt_insert->bindParam(':lwop', $lwop);
            $stmt_insert->execute();
        }
    }

    $sql_trnsmst = "SELECT TRNSNO, EMPNO, BASIC, LWOP FROM trnsmst where sheet_id = $sheet_id";
    $stmt_trnsmst = $db->prepare($sql_trnsmst);
    $stmt_trnsmst->execute();
    $trnsmst = $stmt_trnsmst->fetchAll();

    $sql_gball = "SELECT * from gblall";
    $stmt_gball = $db->prepare($sql_gball);
    $stmt_gball->execute();
    $gball = $stmt_gball->fetchAll();

    $da = 1;
    $stmt_indall = $db->prepare("SELECT * from indall WHERE EMPNO = :empno");
    $stmt_trnsdet1 = $db->prepare("INSERT INTO trnsdet1 (TRNSNO, DESCR, AMOUNT) VALUES (:trnsno, :descr, :amount)");
    $stmt_trnsdet2 = $db->prepare("INSERT INTO trnsdet2 (TRNSNO, DESCR, AMOUNT) VALUES (:trnsno, :descr, :amount)");


    // echo "Starting Calculation";
    // $stmt_sql = $db->prepare("TRUNCATE TABLE trnsdet1");
    // $stmt_sql->execute();
    // $stmt_sql = $db->prepare("TRUNCATE TABLE trnsdet2");
    // $stmt_sql->execute();

    $sql_state = "SELECT STATE FROM trnsmst t JOIN empmast e on t.EMPNO = e.EMPNO WHERE TRNSNO = :trnsno";
    $stmt_state = $db->prepare($sql_state);

    $daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
    foreach ($trnsmst as $row) {
        $trnsno = $row['TRNSNO'];
        $basic = $row['BASIC'];
        $empno = $row['EMPNO'];
        $lwopNo = $row['LWOP'];

        $stmt_state->bindParam(':trnsno', $trnsno);
        $stmt_state->execute();
        $state = $stmt_state->fetchColumn();

        foreach ($gball as $row) {
            $tmp = $row['DESCR'];
            $allrednflag = $row['ALLREDNFLAG'];
            $allowance = $row['ALLOWANCE'];

            if ($allrednflag == "a") {
                if ($tmp == "DA") {
                    $amount = round($basic * ($allowance / 100));
                    $da = $amount;
                } else {
                    $amount = round($basic * ($allowance / 100));
                }
                $stmt_insert = $stmt_trnsdet1;
            } elseif ($allrednflag == "d") {
                $lwop_amt = ($basic + $da) * $lwopNo / $daysInMonth;
                if ($tmp == "PF" && $basic > $pfceil) {
                    $pf_base = $basic + $da - $lwop_amt;
                    $amount = round($pf_base * $allowance / 100);
                } else {
                    $amount = round($pfceil * $allowance / 100);
                }
                $stmt_insert = $stmt_trnsdet2;
            }

            // Check data exists in trnsdet1 or trnsdet2
            $sql_check = "SELECT COUNT(*) FROM " . ($allrednflag == "a" ? "trnsdet1" : "trnsdet2") . " WHERE TRNSNO = :trnsno AND DESCR = :descr";
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->bindParam(':trnsno', $trnsno);
            $stmt_check->bindParam(':descr', $tmp);
            $stmt_check->execute();
            $exists = $stmt_check->fetchColumn() > 0;

            if ($exists) {
                // Update table
                $sql_update = "UPDATE " . ($allrednflag == "a" ? "trnsdet1" : "trnsdet2") . " SET AMOUNT = :amount WHERE TRNSNO = :trnsno AND DESCR = :descr";
                $stmt_update = $db->prepare($sql_update);
                $stmt_update->bindParam(':amount', $amount);
                $stmt_update->bindParam(':trnsno', $trnsno);
                $stmt_update->bindParam(':descr', $tmp);
                $stmt_update->execute();
            } else {
                // Insert in table
                $stmt_insert->bindParam(':trnsno', $trnsno);
                $stmt_insert->bindParam(':descr', $tmp);
                $stmt_insert->bindParam(':amount', $amount);
                $stmt_insert->execute();
            }
        }

        $stmt_indall->bindParam(':empno', $empno);
        $stmt_indall->execute();
        $indall_result = $stmt_indall->fetchAll(PDO::FETCH_ASSOC);

        foreach ($indall_result as $row) {
            $tmp = $row['DESCR'];
            $prcamtflag = $row['PRCAMTFLAG'];
            $allrednflag = $row['ALLREDNFLAG'];
            $allowance = $row['ALLOWANCE'];

            if ($prcamtflag == "%") {
                $amount = round($basic * $allowance / 100);
            } else {
                $amount = round($allowance);
            }

            $stmt_insert = $allrednflag == "a" ? $stmt_trnsdet1 : $stmt_trnsdet2;

            // Check data exists in trnsdet1 or trnsdet2
            $sql_check = "SELECT COUNT(*) FROM " . ($allrednflag == "a" ? "trnsdet1" : "trnsdet2") . " WHERE TRNSNO = :trnsno AND DESCR = :descr";
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->bindParam(':trnsno', $trnsno);
            $stmt_check->bindParam(':descr', $tmp);
            $stmt_check->execute();
            $exists = $stmt_check->fetchColumn() > 0;

            if ($exists) {
                // Update data
                $sql_update = "UPDATE " . ($allrednflag == "a" ? "trnsdet1" : "trnsdet2") . " SET AMOUNT = :amount WHERE TRNSNO = :trnsno AND DESCR = :descr";
                $stmt_update = $db->prepare($sql_update);
                $stmt_update->bindParam(':amount', $amount);
                $stmt_update->bindParam(':trnsno', $trnsno);
                $stmt_update->bindParam(':descr', $tmp);
                $stmt_update->execute();
            } else {
                // Insert in data
                $stmt_insert->bindParam(':trnsno', $trnsno);
                $stmt_insert->bindParam(':descr', $tmp);
                $stmt_insert->bindParam(':amount', $amount);
                $stmt_insert->execute();
            }
        }
        
        // Additional Calculation and Update Block
        $daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
        $sql_summary = "
        SELECT t.TRNSNO,
            MAX(CASE WHEN t1.DESCR='DA' THEN t1.amount END) AS DA,
            MAX(CASE WHEN t1.DESCR='HRA' THEN t1.amount END) AS HRA,
            MAX(CASE WHEN t1.DESCR='TA' THEN t1.amount END) AS TA,
            MAX(CASE WHEN t1.DESCR='SPL' THEN t1.amount END) AS SPL,
            MAX(CASE WHEN t2.DESCR='PF' THEN t2.amount END) AS PF,
            MAX(CASE WHEN t2.DESCR='ESIC' THEN t2.amount END) AS ESIC,
            MAX(CASE WHEN t2.DESCR='iTax' THEN t2.amount END) AS iTax,
            MAX(CASE WHEN t1.DESCR='HRA' THEN t1.amount ELSE 0 END) +
            MAX(CASE WHEN t1.DESCR='SPL' THEN t1.amount ELSE 0 END) +
            MAX(CASE WHEN t1.DESCR='LSA' THEN t1.amount ELSE 0 END) + t.gross AS total_earning,
            ROUND(MAX(CASE WHEN t2.DESCR='iTax' THEN t2.amount ELSE 0 END) +
            MAX(CASE WHEN t2.DESCR='PF' THEN t2.amount ELSE 0 END) +
            MAX(CASE WHEN t2.DESCR='ESIC' THEN t2.amount ELSE 0 END) +
            IFNULL(ROUND((t.lwop / :daysInMonth) * (MAX(CASE WHEN t1.DESCR='DA' THEN t1.amount ELSE 0 END) + t.basic), 2),0),0) AS total_deduction,
            ROUND(IFNULL(ROUND((t.lwop / :daysInMonth) * (MAX(CASE WHEN t1.DESCR='DA' THEN t1.amount ELSE 0 END) + t.basic),0),0),0) AS lwopamt,
            ROUND(MAX(CASE WHEN t1.DESCR='HRA' THEN t1.amount ELSE 0 END) + t.MNTHSAL,0) AS netAmt,
            get_advInst(t.EMPNO) AS advAmt,
            get_lsaAmt(t.EMPNO) AS LSA
        FROM trnsmst t
        JOIN trnsdet1 t1 ON t.TRNSNO = t1.TRNSNO
        JOIN trnsdet2 t2 ON t.TRNSNO = t2.TRNSNO
        WHERE t.sheet_id = :sheet_id
        GROUP BY t.TRNSNO";

        $stmt_summary = $db->prepare($sql_summary);
        $stmt_summary->execute([':daysInMonth'=>$daysInMonth, ':sheet_id'=>$sheet_id]);
        $summaries = $stmt_summary->fetchAll(PDO::FETCH_ASSOC);

        $update_stmt = $db->prepare("UPDATE trnsmst SET DA=:DA, HRA=:HRA, TA=:TA, SPL=:SPL, PF=:PF, ESIC=:ESIC, iTax=:iTax, total_earning=:total_earning, total_deduction=:total_deduction, lwopamt=:lwopamt, netAmt=:netAmt, advAmt=:advAmt, LSA=:LSA WHERE TRNSNO=:TRNSNO");
        foreach($summaries as $row){
            $update_stmt->execute([
                ':DA'=>$row['DA'], ':HRA'=>$row['HRA'], ':TA'=>$row['TA'], ':SPL'=>$row['SPL'], ':PF'=>$row['PF'], ':ESIC'=>$row['ESIC'], ':iTax'=>$row['iTax'],
                ':total_earning'=>$row['total_earning'], ':total_deduction'=>$row['total_deduction'], ':lwopamt'=>$row['lwopamt'], ':netAmt'=>$row['netAmt'],
                ':advAmt'=>$row['advAmt'], ':LSA'=>$row['LSA'], ':TRNSNO'=>$row['TRNSNO']
            ]);
        }
        // Additional Calculation and Update Block End

        $daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
        $stmt_update_salary = $db->prepare("CALL monthsal(:trnsno, :sheet_id, :daysInMonth, :end_id, :empno)");
        $stmt_update_salary->bindParam(':trnsno', $trnsno);
        $stmt_update_salary->bindParam(':sheet_id', $sheet_id);
        $stmt_update_salary->bindParam(':end_id', $end_id);
        $stmt_update_salary->bindParam(':empno', $empno);
        $stmt_update_salary->bindParam(':daysInMonth', $daysInMonth);
        $stmt_update_salary->execute();

        $stmt_advPay = $db->prepare("CALL sp_advPayment(:empNo)");
        $stmt_advPay->bindParam(':empNo', $empno);
        $stmt_advPay->execute();
    }

    $response = array(
        'msg' => 'Querry Executed Successfully', 'status' => 200,
        'month' => $selectedMonth, 'year' => $selectedYear,
    );
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    $errorMessage = $e->getMessage();
    if (strpos($errorMessage, 'No Month Data') !== false) {
        $response = array(
            'msg' => 'Query Failed', 'status' => 500,
            'month' => $selectedMonth, 'year' => $selectedYear,
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        print "Error!: " . $errorMessage . "<br/>";
        die();
    }
} ?>