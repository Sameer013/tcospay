<?php
$requestData = $_REQUEST;
$columns = array(
    0 => 'TRNSNO',
    1 => 'EMPNO',
    2 => 'gross',
    3 => 'DTE'
);
$data = array();

try {
    require_once 'includes/dbconn.php';

// One row per payslip; the earning / deduction lines are loaded separately by the page
$sql = "SELECT trst.*, em.NAME
        FROM trnsmst as trst
        LEFT JOIN empmast AS em ON trst.EMPNO=em.EMPNO
        WHERE trst.MTHYR = :monthYear";

if (isset($_GET['empNo'])) {
    $empNo = $_GET['empNo'];
    $sql .= " AND trst.EMPNO = :empNo";
}

$sql .= " ORDER BY trst.TRNSNO";

$stmt = $db->prepare($sql);
$stmt->bindParam(':monthYear', $_GET['monthYear'], PDO::PARAM_STR);

if (isset($_GET['empNo'])) {
    $stmt->bindParam(':empNo', $empNo, PDO::PARAM_INT);
}

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$json_data = array("data" => $data);
echo json_encode($json_data);


} catch (PDOException $e) {
    $json_data = array("errmsg" => $e->getMessage());
    echo json_encode($json_data);
}
?>
