<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
$requestData = $_REQUEST;
$columns = array(
    0 => 'empno',
    1 => 'Name',
    2 => 'cl',
    3 => 'el',
    4 => 'lwop',
    5 => 'off_days',
    6 => 'med_leave',
    7 => 'hdays',
    8 => 'attnd',
    9 => 'adj',
    10 => 'pay_mode',
    11 => 'absent',
    12 => 'wdays',
    13 => 'othour',
    14 => 'action'
);
$data = array();
try {
    require_once 'includes/dbconn.php';

    $sql = "SELECT  sd.empno, e.Name, cl, sd.el, lwop, off_days, med_leave, hdays, attnd, adj, pay_mode, (cl+sd.el+lwop+med_leave+spleave) as absent, wdays, othour
        FROM sheet_det sd
        JOIN empmast e on sd.empno=e.EMPNO
        JOIN sheet s on sd.sheet_id=s.sheet_id
        WHERE s.mnth = :month1 AND s.yr = :year1";
 $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':month1', $selectedMonth);
    $stmt->bindParam(':year1', $selectedYear);
    $stmt->execute();
    $data = $stmt->fetchAll();

    $countSql = "SELECT COUNT(*) FROM sheet_det sd
        JOIN empmast e on sd.empno=e.EMPNO
        JOIN sheet s on sd.sheet_id=s.sheet_id
        WHERE s.mnth = :month2 AND s.yr = :year2";
    $countStmt = $db->prepare($countSql);
    $countStmt->bindParam(':month2', $selectedMonth);
    $countStmt->bindParam(':year2', $selectedYear);
    $countStmt->execute();
    $totalData = $countStmt->fetchColumn();

    $totalFiltered = $totalData;

    $objArr = array();
    foreach ($data as $row) {
        $object = new stdClass();

        $empno = $row['empno'];
        $action = "<div class='flex justify-left items-center'>
                        <a class='flex items-center mr-5' id=\"edit-button\" href='javascript:;' onclick=\"load_data('$empno')\"
                            data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"check-square\" data-lucide=\"check-square\" class=\"lucide lucide-check-square w-4 h-4 mr-1\">
                                <polyline points=\"9 11 12 14 22 4\"></polyline>
                                <path d=\"M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11\"></path>
                            </svg>
                        </a>
                        <a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data('$empno')\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"trash-2\" data-lucide=\"trash-2\" class=\"lucide lucide-trash-2 w-4 h-4 mr-1\">
                                <polyline points=\"3 6 5 6 21 6\"></polyline>
                                <path d=\"M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2\"></path>
                                <line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line>
                                <line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line>
                            </svg>
                        </a>
                    </div>";

        $object->empno  = $row['empno'];
        $empno = $row['empno'];
        $object->Name   = $row['Name'];
        $object->cl     = $row['cl'];
        $object->el     = $row['el'];
        $object->lwop   = $row['lwop'];
        $object->off_days   = $row['off_days'];
        $object->med_leave  = $row['med_leave'];
        $object->hdays  = $row['hdays'];
        $object->attnd  = $row['attnd'];
        $object->adj    = $row['adj'];
        $object->pay_mode   = $row['pay_mode'];
        $object->absent = $row['absent'];
        $object->wdays  = $row['wdays'];
        $object->othour = $row['othour'];
        $object->action = $action;

        array_push($objArr, $object);
    }

    $json_data = array(
			//"sql"			  => $sql,
			// "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $objArr   // total data array
			);

    echo json_encode($json_data);

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
