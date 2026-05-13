<?php
    $requestData= $_REQUEST;
    $columns = array(
        0 => 'CODE',
		1 => 'DESCR',
		2 => 'PRCAMTFLAG',
		3 => 'ALLREDNFLAG',
        4 => 'ALLOWANCE',
        5 => 'ALLREDNCONTINUITY'
    );
    $data=array();
    try {
	require_once 'includes/dbconn.php';
$empnumber = $_GET['empno'];
$sql = "SELECT COUNT(*) FROM indall WHERE EMPNO = :empno";
$totalData = $db->query("SELECT COUNT(*) FROM indall WHERE EMPNO = '$empnumber'")->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT ga.CODE, ga.DESCR, ga.PRCAMTFLAG,ga.ALLREDNFLAG, ga.ALLOWANCE, ga.ALLREDNCONTINUITY
FROM gblall ga
WHERE ga.ALLREDNCONTINUITY = true

UNION

SELECT ia.code, ia.DESCR, ia.PRCAMTFLAG, ia.ALLREDNFLAG, ia.ALLOWANCE, ia.ALLREDNCONTINUITY
FROM indall ia
WHERE ia.ALLREDNCONTINUITY = 'Y' AND ia.EMPNO = :empno";
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";

$stmt = $db->prepare($sql);
$stmt->bindParam(':empno', $empnumber, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetchAll();


	$objArr = array();
	foreach ($data as $row) {

	 		$object = new stdClass();
	 	$object->Code 	= $row['CODE'];
        $object->Desc	= $row['DESCR'];
	 	$object->Flag	= $row['PRCAMTFLAG'];
	 	$object->Allorded	= $row['ALLREDNFLAG'];
	 	$object->Value	= $row['ALLOWANCE'];
	 	$object->Stillvalid	= ($row['ALLREDNCONTINUITY'] == 'Y') ? 'True' : $row['ALLREDNCONTINUITY'];
	 	array_push($objArr, $object);
	}



	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		print "<br>$sql";
		die();
	}
    $json_data = array(
			//"sql"			  => $sql,
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $objArr   // total data array
			);
    echo json_encode($json_data);
?>

