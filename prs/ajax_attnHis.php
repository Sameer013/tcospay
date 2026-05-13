<?php
$requestData = $_REQUEST;
$columns = array(
    0 => 'uid',
    1 => 'dtime',
    2 => 'minout',
    3 => 'shift',
    4 => 'cmnt',
    5 => 'name'
);
$data = array();
try {
    require_once 'includes/dbconn.php';
    $empnumber = $_GET['empno'];
    $sql = "SELECT COUNT(*) FROM attdata as atd
            JOIN empmast AS em ON atd.uid = em.EMPNO WHERE EMPNO = :empno";
    $totalData = $db->query("SELECT COUNT(*) FROM attdata as atd
                            JOIN empmast AS em ON atd.uid = em.EMPNO WHERE EMPNO = '$empnumber'")->fetchColumn();
    $totalFiltered = $totalData;

    $sqlSearchCondition = "";
    if (!empty($requestData['search']['value'])) {
        $rd = $requestData['search']['value'];
        $sqlSearchCondition = " AND (atd.dtime LIKE '%$rd%')";
    }

    $sql = "SELECT * FROM attdata as atd
            JOIN empmast AS em ON atd.uid = em.EMPNO WHERE EMPNO = :empno";
    $sql .= $sqlSearchCondition;
    $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':empno', $empnumber, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetchAll();

    $objArr = array();
    foreach ($data as $row) {
        $object = new stdClass();

        $object->uid = $row['uid'];
        $object->dtime = $row['dtime'];
        $object->minout = $row['minout'];
        $object->shift = $row['shift'];
        $object->cmnt = $row['cmnt'];

        array_push($objArr, $object);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    print "<br>$sql";
    die();
}
$json_data = array(
    //"sql"              => $sql,
    // "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $objArr   // total data array
);
echo json_encode($json_data);
?>
