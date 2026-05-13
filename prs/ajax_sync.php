<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

if (empty($selectedMonth) || empty($selectedYear)) {
    $response = array('error' => 'Month and year are required parameters');
} else {
    try {
        include('includes/dbconn.php');
        $check_existing_data_sql = " SELECT COUNT(*) AS dataExists
            FROM sheet_det AS sd JOIN sheet AS s ON sd.sheet_id = s.sheet_id WHERE s.mnth = :selectedMonth AND s.yr = :selectedYear";
        $check_existing_data = $db->prepare($check_existing_data_sql);
        $check_existing_data->bindParam(':selectedMonth', $selectedMonth);
        $check_existing_data->bindParam(':selectedYear', $selectedYear);
        $check_existing_data->execute();
        $result = $check_existing_data->fetch(PDO::FETCH_ASSOC);

        if ($result['dataExists'] > 0) {
            $response = array('dataExists' => true, 'message' => 'Data already exists for the selected month and year');
        } else {
            $insert_det_sql = "INSERT INTO sheet_det (sheet_id, empno, othour,  attnd, cl, med_leave,  el, off_days, hdays, lwop)
            SELECT s.sheet_id, atd.empno,
                0 AS othour,
                COUNT(CASE WHEN atd.present = 1 AND atd.dflag IS NULL THEN 1 ELSE NULL END) AS attnd,
                SUM(CASE WHEN atd.dflag = 'CL' THEN 1 ELSE 0 END) AS cl,
                SUM(CASE WHEN atd.dflag = 'MED' THEN 1 ELSE 0 END) AS med_leave,
                SUM(CASE WHEN atd.dflag = 'EL' THEN 1 ELSE 0 END) AS el,
                SUM(CASE WHEN atd.dflag = 'WO' THEN 1 ELSE 0 END) AS off_days,
                SUM(CASE WHEN atd.dflag = 'HO' THEN 1 ELSE 0 END) AS hdays,
                COUNT(*) - SUM(CASE WHEN atd.dflag IN ('CL', 'EL', 'MED', 'WO', 'HO') THEN 1 ELSE 0 END) - COUNT(CASE WHEN atd.present = 1 AND atd.dflag IS NULL THEN 1 ELSE NULL END) AS lwop
            FROM attnddet AS atd
            LEFT JOIN sheet AS s ON EXTRACT(MONTH FROM atd.dt) = s.mnth AND EXTRACT(YEAR FROM atd.dt) = s.yr
            WHERE EXTRACT(YEAR FROM atd.dt) = :selectedYear AND EXTRACT(MONTH FROM atd.dt) = :selectedMonth
            GROUP BY atd.empno HAVING attnd > 0";

            $insert_data = $db->prepare($insert_det_sql);
            $insert_data->bindParam(':selectedYear', $selectedYear);
            $insert_data->bindParam(':selectedMonth', $selectedMonth);
            $insert_data->execute();

            // Update empmast accleave based on latemuster records just created
            $latemuster_query = "SELECT EmpCode, CL FROM latemuster
              WHERE year1 = :selectedYear AND month1 = :selectedMonth AND CL > 0";
            $stmt = $db->prepare($latemuster_query);
            $stmt->bindParam(':selectedYear', $selectedYear);
            $stmt->bindParam(':selectedMonth', $selectedMonth);
            $stmt->execute();
            $latemuster_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $update_stmt = $db->prepare("UPDATE empmast
                      SET accleave = GREATEST(accleave - :cl, 0)
                      WHERE empno = :emp_id");

            foreach ($latemuster_rows as $row) {
                $update_stmt->bindParam(':cl', $row['CL'], PDO::PARAM_INT);
                $update_stmt->bindParam(':emp_id', $row['EmpCode'], PDO::PARAM_INT);
                $update_stmt->execute();
            }

            $response = array('success' => true, 'message' => 'Inserted into sheet_det and updated empmast', 'dataExists' => false);
        }
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
        $response = array('error' => 'Database error: ' . $errorMessage);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
