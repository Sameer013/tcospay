<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

if (empty($selectedMonth) || empty($selectedYear)) {
    $response = array('error' => 'Month and year are required parameters');
} else {
    try {
        include('includes/dbconn.php');

        $update_query = "UPDATE sheet_det sd
JOIN (
    SELECT
        s.sheet_id,
        atd.empno,
        SUM(CASE WHEN atd.workDur > '06:30:00' THEN TIME_TO_SEC(atd.workDur) - TIME_TO_SEC('06:30:00') ELSE 0 END ) / 3600.0 AS othour,
        COUNT(CASE WHEN atd.present = 1 THEN 1 ELSE NULL END) AS attnd,
        SUM(CASE WHEN atd.dflag = 'CL' THEN 1 ELSE 0 END) AS cl,
        SUM(CASE WHEN atd.dflag = 'MED' THEN 1 ELSE 0 END) AS med_leave,
        SUM(CASE WHEN atd.dflag = 'SPL' THEN 1 ELSE 0 END) AS spleave,
        SUM(CASE WHEN atd.dflag = 'EL' THEN 1 ELSE 0 END) AS el,
        SUM(CASE WHEN atd.dflag = 'WO' THEN 1 ELSE 0 END) AS off_days,
        (SELECT COUNT(*) as hdays FROM holidaymaster WHERE MONTH(HolidayDate) = :selectedMonth
            AND YEAR(HolidayDate) = :selectedYear
            AND Category = (SELECT CATCODE FROM empmast WHERE EMPNO = atd.empno)) as hdays
    FROM
        attnddet AS atd
    LEFT JOIN
        sheet AS s ON EXTRACT(MONTH FROM atd.dt) = s.mnth AND EXTRACT(YEAR FROM atd.dt) = s.yr
    WHERE EXTRACT(YEAR FROM atd.dt) = :selectedYear
    AND EXTRACT(MONTH FROM atd.dt) = :selectedMonth
    GROUP BY atd.empno
) AS source ON sd.sheet_id = source.sheet_id AND sd.empno = source.empno
SET
    sd.othour = source.othour,
    sd.attnd = source.attnd,
    sd.cl = source.cl,
    sd.med_leave = source.med_leave,
    sd.spleave = source.spleave,
    sd.el = source.el,
    sd.off_days = source.off_days,
    sd.hdays = source.hdays";

        $update_data = $db->prepare($update_query);
        $update_data->bindParam(':selectedYear', $selectedYear);
        $update_data->bindParam(':selectedMonth', $selectedMonth);
        $update_data->execute();

        $response = array('success' => true, 'message' => 'Data updated successfully');
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
        $response = array('error' => 'Database error: ' . $errorMessage);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
