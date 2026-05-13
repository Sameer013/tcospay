<?php
    $requestData= $_REQUEST;
    $columns = array(
        0 => 'LNO',
		1 => 'DTE',
		2 => 'DESCR',
		3 => 'AMT',
        4 => 'RATE',
        5 => 'NOINST',
        6 => 'FLAG',
    );
    $data=array();
    try {
	require_once 'includes/dbconn.php';
$empnumber = $_GET['empno'];
$sql = "SELECT COUNT(*) FROM loanmast WHERE EMPNO = :empno";
$totalData = $db->query("SELECT COUNT(*) FROM loanmast WHERE EMPNO = '$empnumber'")->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT * FROM loanmast WHERE EMPNO = :empno";
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";

$stmt = $db->prepare($sql);
$stmt->bindParam(':empno', $empnumber, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetchAll();


	$objArr = array();
	foreach ($data as $row) {

	 		$object = new stdClass();
	 	$EMPNO = $row['EMPNO'];
	 	$object->LNO 	= $row['LNO'];
        $object->DTE	= $row['DTE'];
	 	$object->DESCR	= $row['DESCR'];
	 	$object->AMT	= $row['AMT'];
	 	$object->RATE	= $row['RATE'];
	 	$object->NOINST	= $row['NOINST'];
	 	$object->FLAG	= $row['FLAG'];
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

