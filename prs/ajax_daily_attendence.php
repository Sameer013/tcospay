<?php
$requestData = $_REQUEST;
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
$columns = array(
    0 => 'empno',
    1 => 'dt',
    2 => 'intime',
    3 => 'outtime',
    4 => 'shift',
    5 => 'present',
    6 => 'leaveAdj',
    7 => 'leaveHead',
    8 => 'workDur',
    9 => 'dflag',
    10 => 'late',
    11 => 'empname'
);
$data = array();

try {
    require_once 'includes/dbconn.php';


$checkExistingSql = "SELECT COUNT(*) FROM attnddet as atd WHERE EXTRACT(YEAR FROM atd.dt) = :selectedYear AND EXTRACT(MONTH FROM atd.dt) = :selectedMonth";
$checkExistingStmt = $db->prepare($checkExistingSql);
$checkExistingStmt->bindParam(':selectedYear', $selectedYear, PDO::PARAM_STR);
$checkExistingStmt->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
$checkExistingStmt->execute();
$existingDataCount = $checkExistingStmt->fetchColumn();



    if ($existingDataCount == 0) {
    $insertSql = "INSERT INTO attnddet (empno, dt, intime, outtime, workDur, present, shift, late, dflag)
                      WITH RECURSIVE DateSeries AS (
                        SELECT
                          CAST(CONCAT(:selectedYear, '-', LPAD(:selectedMonth, 2, '0'), '-01') AS DATE) AS date
                        UNION ALL
                        SELECT date + INTERVAL 1 DAY
                        FROM DateSeries
                        WHERE date + INTERVAL 1 DAY <= LAST_DAY(CAST(CONCAT(:selectedYear, '-', LPAD(:selectedMonth, 2, '0'), '-01') AS DATE))
                      )
SELECT
    e.EMPNO,
    ds.date AS dt,
    MIN(CAST(a.dtime AS TIME)) AS intime,
    MAX(CAST(a.dtime AS TIME)) AS outtime,
    TIMEDIFF(MAX(CAST(a.dtime AS TIME)), MIN(CAST(a.dtime AS TIME))) AS WorkDur,
    CASE WHEN MIN(CAST(a.dtime AS TIME)) IS NOT NULL AND MAX(CAST(a.dtime AS TIME)) IS NOT NULL THEN 1 ELSE 0 END AS present,
    a.shift,
    a.cmnt AS late,
    CASE
        WHEN FIND_IN_SET(DAYOFWEEK(ds.date), e.woff) > 0 THEN 'WO'
        WHEN hm.HolidayDate IS NOT NULL THEN 'HO'
        ELSE COALESCE(ld.LTYPE, hm.HolidayDescription)
    END AS dflag
FROM
    DateSeries ds
LEFT JOIN
    empmast e ON 1=1
LEFT JOIN
    attdata a ON e.EMPNO = a.uid
              AND ds.date = CAST(a.dtime AS DATE)
              AND (a.minout = 'in' OR a.minout = 'out')
LEFT JOIN
    leavedet ld ON e.EMPNO = ld.EMPNO
               AND ds.date BETWEEN ld.FDATE AND ld.TDATE
LEFT JOIN
    holidaymaster hm ON ds.date = hm.HolidayDate
        AND hm.Category = e.catcode
GROUP BY
    ds.date, e.EMPNO";

        $stmtInsert = $db->prepare($insertSql);
        $stmtInsert->bindParam(':selectedYear', $selectedYear, PDO::PARAM_STR);
        $stmtInsert->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
        $stmtInsert->execute();
    }

    $sql = "SELECT atd.*, em.NAME
        FROM attnddet as atd
        LEFT JOIN empmast AS em ON atd.empno=em.EMPNO
        WHERE EXTRACT(YEAR FROM atd.dt) = :txt_year
  AND EXTRACT(MONTH FROM atd.dt) = :txt_month";
if (!empty($requestData['search']['value'])) {
    $rd = $requestData['search']['value'];
    $sql .= " AND (em.NAME LIKE '%$rd%')";
}
    if (isset($requestData['order'][0]['column']) && isset($requestData['order'][0]['dir'])) {
        $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
    }

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':txt_month', $selectedMonth);
    $stmt->bindParam(':txt_year', $selectedYear);
    $stmt->execute();
    $data = $stmt->fetchAll();

    $countSql = "SELECT COUNT(*) FROM attnddet as atd
        LEFT JOIN empmast AS em ON atd.empno=em.EMPNO
         WHERE EXTRACT(YEAR FROM atd.dt) = :txt_year1
  AND EXTRACT(MONTH FROM atd.dt) = :txt_month1";

    $countStmt = $db->prepare($countSql);
    $countStmt->bindParam(':txt_month1', $selectedMonth);
    $countStmt->bindParam(':txt_year1', $selectedYear);
    $countStmt->execute();
    $totalData = $countStmt->fetchColumn();
    $totalFiltered = $totalData;

    $objArr = array();

    foreach ($data as $row) {
        $object = new stdClass();
        $object->empno 	= $row['empno'];
        $empno=$row['empno'];
        $object->empname	= $row['NAME'];
        $object->dt	= $row['dt'];
        $object->intime	= $row['intime'];
        $object->outtime	= $row['outtime'];
        $object->shift	= $row['shift'];
        $object->present	= $row['present'];
        $object->leaveAdj	= $row['leaveAdj'];
        $object->leaveHead	= $row['leaveHead'];
        $object->workDur	= $row['workDur'];
        $object->dflag	= $row['dflag'];
        $object->late	= $row['late'];

        array_push($objArr, $object);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
$status = ($totalData > 0) ? "DataAvailable" : "";

$json_data = array(
    "status" => $status,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $objArr
);
echo json_encode($json_data);
?>
