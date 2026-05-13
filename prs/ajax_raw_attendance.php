<?php
    $requestData= $_REQUEST;
	$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
	$selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
	$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
    $columns = array(
		0 => 'uid',
		1 => 'dtime',
		2 => 'macid',
		3 => 'dt',
		4 => 'minout',
		5 => 'shift',
		6 => 'cmnt',
		7 => 'name'
    );
    $data=array();
    try {
	require_once 'includes/dbconn.php';


	$selectedDate = $selectedYear . "-" . $selectedMonth . "-" . $selectedDay;
	$sql = "SELECT atd.*, em.NAME
FROM attdata AS atd
LEFT JOIN empmast AS em ON atd.uid = em.EMPNO
WHERE EXTRACT(YEAR FROM atd.dt) = :txt_year
  AND EXTRACT(MONTH FROM atd.dt) = :txt_month
  AND (em.EMPNO IS NULL OR atd.uid IS NOT NULL)";
if (!empty($requestData['search']['value'])) {
    $rd = $requestData['search']['value'];
    $sql .= " AND (em.NAME LIKE '%$rd%')";
}
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':txt_month', $selectedMonth);
    $stmt->bindParam(':txt_year', $selectedYear);

    $stmt->execute();
    $data = $stmt->fetchAll();

	$countSql = "SELECT COUNT(*) FROM attdata as atd
	LEFT JOIN empmast AS em ON atd.uid = em.EMPNO
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

    $object->uid = $row['uid'];
    $object->dtime = $row['dtime'];
    $object->macid = isset($row['macid']) ? $row['macid'] : '';
    $object->dt = $row['dt'];
    $object->name = $row['NAME'];
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
			//"sql"			  => $sql,
			// "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $objArr   // total data array
			);
    echo json_encode($json_data);
?>

