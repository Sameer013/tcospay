<?php
ini_set('display_errors', 0);
session_start();
$userId = $_SESSION['user'];
$requestData = $_REQUEST;
$columns = array(
    0 => 'lno',
    1 => 'empno',
    2 => 'action'
);
$data = array();
try {
    require_once 'includes/dbconn.php';
    if ($userId == 'Admin' || $userId == 'Super Admin') {
        $sql = "Select l.*, e.NAME from loanmast l LEFT JOIN empmast e ON l.empno = e.empno where l.FLAG=0 or l.FLAG is null";
    } else {
        $sql = "Select l.*, e.NAME from loanmast l LEFT JOIN empmast e ON l.empno = e.empno where l.empno=$userId and l.FLAG=0";
    }
    $totalData = $db->query("SELECT COUNT(*) FROM ($sql) b")->fetchColumn();
    $totalFiltered = $totalData;

    if (!empty($requestData['search']['value'])) {
        $rd = $requestData['search']['value'];
        $sql .= " HAVING (e.NAME LIKE '%$rd%')";
        $totalFiltered = $db->query("SELECT COUNT(*) FROM ($sql) b")->fetchColumn();
    }

    $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . " LIMIT " . $requestData['start'] . " ," . $requestData['length'];

    $data = $db->query($sql)->fetchAll();


    $objArr = array();
    foreach ($data as $row) {

        $object = new stdClass();

        $code = $row['LNO'];
        $action = "<div class='flex justify-left items-center'>

					<a class='flex items-center mr-5' id=\"edit-button\" href='javascript:;' onclick=\"load_data('$code')\"
					data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"check-square\" data-lucide=\"check-square\" class=\"lucide lucide-check-square w-4 h-4 mr-1\"><polyline points=\"9 11 12 14 22 4\"></polyline><path d=\"M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11\"></path></svg></a>

					<a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data('$code')\">
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"trash-2\" data-lucide=\"trash-2\" class=\"lucide lucide-trash-2 w-4 h-4 mr-1\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg></a></div>";

        $object->lno     = $row['LNO'];
        $object->empno     = $row['EMPNO'];
        $object->name     = $row['NAME'];
        $object->dte     = $row['DTE'];
        $object->descr     = $row['DESCR'];
        $object->amt     = $row['AMT'];
        $object->rate    = $row['RATE'];
        $object->noinst    = $row['NOINST'];
        $object->status    = $row['FLAG'];
        $object->action     = $action;

        array_push($objArr, $object);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    print "<br>$sql";
    die();
}
$json_data = array(
    //"sql"			  => $sql,
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $objArr   // total data array
);
echo json_encode($json_data);
