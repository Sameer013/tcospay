<?php
$requestData = $_REQUEST;
$data = array();

try {
    require_once 'includes/dbconn.php';

    $payslipNo = $_GET['payslipNo'];

    $columns = array('TRNSNO', 'DESCR', 'AMOUNT');

    $totalData = $db->query("SELECT COUNT(*) FROM trnsdet1 WHERE TRNSNO = '$payslipNo'")->fetchColumn();
    $totalFiltered = $totalData;

    $sql = "SELECT TRNSNO, DESCR, AMOUNT FROM trnsdet1 WHERE TRNSNO = :payslipNo";

    if (isset($requestData['order'][0]['column'])) {
        $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
    }

    if (isset($requestData['start']) && isset($requestData['length'])) {
        $sql .= " LIMIT " . $requestData['start'] . ", " . $requestData['length'];
    }

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':payslipNo', $payslipNo, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $objArr = array();
    foreach ($data as $row) {
        $objArr[] = $row;
    }

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    print "<br>$sql";
    die();
}

$json_data = array(
    "draw"            => isset($requestData['draw']) ? intval($requestData['draw']) : 0,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $objArr
);
echo json_encode($json_data);
?>
