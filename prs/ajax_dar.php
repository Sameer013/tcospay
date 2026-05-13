<?php
    ini_set('display_errors',0);
    session_start();
    $userId=$_SESSION['user'];
    $requestData= $_REQUEST;
    $columns = array(
        0 => 'empno',
		1 => 'tdylst',
        2 => 'cmplst'
    );
    $data=array();
    try {
	require_once 'includes/dbconn.php';
	$sql = "SELECT e.NAME, e.EMPNO, e.DSGCODE, d.* FROM empmast e
LEFT JOIN dlyreport d ON e.EMPNO = d.empno AND d.dte = (SELECT MAX(dlyreport.dte) FROM dlyreport)
WHERE e.EMPNO = $userId";

$data = $db->query($sql)->fetchAll();


	$objArr = array();
	foreach ($data as $row) {

	 		$object = new stdClass();

	 	$object->empno 	= $row['EMPNO'];
	 	$object->empname 	= $row['NAME'];
        $object->dsgcode	= $row['DSGCODE'];
        $object->tdylst	= $row['tdylst'];
        $object->time	= $row['time'];
        $object->descr	= $row['descr'];
        $object->progress	= $row['progress'];

	 	array_push($objArr, $object);
	}



	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		print "<br>$sql";
		die();
	}
    $json_data = array(
			//"sql"			  => $sql,
			//"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			//"recordsTotal"    => intval( $totalData ),  // total number of records
			//"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $objArr ,  // total data array
			"user"            => $userId   // total data array
			);
    echo json_encode($json_data);
?>

