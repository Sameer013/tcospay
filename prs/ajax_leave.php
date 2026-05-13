<?php
$requestData = $_REQUEST;
$columns = array(
    0 => 'SLNO',
    1 => 'DTE',
    2 => 'FDATE',
    3 => 'TDATE',
    4 => 'NOL',
    5 => 'LWOP',
    6 => 'LTYPE',
    7 => 'NOOFDAYS',
    8 => 'DESCR',
    9 => 'action'
);
$data = array();
try {
    require_once 'includes/dbconn.php';
    $empnumber = $_GET['empno'];
    $sql = "SELECT COUNT(*) FROM leavedet WHERE EMPNO = :empno";
    $totalData = $db->query("SELECT COUNT(*) FROM leavedet WHERE EMPNO = '$empnumber'")->fetchColumn();
    $totalFiltered = $totalData;

    $sql = "SELECT * FROM leavedet WHERE EMPNO = :empno";
    if (!empty($requestData['search']['value'])) {
        $rd = $requestData['search']['value'];
        $sql .= " AND (FDATE LIKE '%$rd%')";
    }
    $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':empno', $empnumber, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetchAll();

    $objArr = array();
    foreach ($data as $row) {
        $object = new stdClass();
         $SLNO  = $row['slno'];

               $action = "<div class='flex justify-left items-center'>
                        <a class='flex items-center mr-5' id=\"edit-button\" href='javascript:;' onclick=\"load_edit_data('$SLNO')\" data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-leave\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"check-square\" data-lucide=\"check-square\" class=\"lucide lucide-check-square w-4 h-4 mr-1\">
                                <polyline points=\"9 11 12 14 22 4\"></polyline>
                                <path d=\"M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11\"></path>
                            </svg>
                        </a>
                        <a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data('$SLNO')\">
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"trash-2\" data-lucide=\"trash-2\" class=\"lucide lucide-trash-2 w-4 h-4 mr-1\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg></a></
                    </div>";

        $object->SLNO = $row['slno'];
        $object->DTE = $row['DTE'];
        $object->FDATE = $row['FDATE'];
        $object->TDATE = $row['TDATE'];
        $object->NOL   = $row['NOL'];
        $object->LWOP  = $row['LWOP'];
        $object->LTYPE = $row['LTYPE'];
        $object->NOOFDAYS = $row['NOOFDAYS'];
        $object->DESCR   = $row['DESCR'];
        $object->action = $action;
        array_push($objArr, $object);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    print "<br>$sql";
    die();
}
$json_data = array(
    //"sql"              => $sql,
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside, they send a number as a parameter, when they receive a response/data they first check the draw number, so we are sending the same number in draw.
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $objArr   // total data array
);
echo json_encode($json_data);
?>
