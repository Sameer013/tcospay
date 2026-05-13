<?php
    ini_set('display_errors',0);
    session_start();
    $userId=$_SESSION['user'];
    $requestData= $_REQUEST;
    $columns = array(
	0 => 'ta_claim_id',
	1 => 'created_on',
	2 => 'emp_id',
	3 => 'name',
	4 => 'travel_plan',
	5 => 'manager_remarks',
	6 => 'admin_remarks',
	7 => 'accounts_remarks',
	8 => 'provisional_pay',
	9 => 'pay_adj',
	10 => 'action'
);
$data=array();
    try {
	require_once 'includes/dbconn.php';
    $selectedOption = $_GET['selectedOption'];
    if ($userId=='Admin' || $userId=='manager')
	{

	$sql = "SELECT  tm.*, empmast.name
    FROM ta_master1 as tm
    LEFT JOIN empmast ON tm.emp_id = empmast.EMPNO WHERE closed_ta_claim='0'";
    }
	else
	{
    $sql = "SELECT  tm.*, empmast.name
    FROM ta_master1 as tm
    LEFT JOIN empmast ON tm.emp_id = empmast.EMPNO WHERE tm.emp_id=$userId AND tm.closed_ta_claim = $selectedOption";
	}
	$totalData = $db->query("select count(*) from ($sql) b")->fetchColumn();
	$totalFiltered = $totalData;
	if( !empty($requestData['search']['value']) ) {
	$rd=$requestData['search']['value'];
	$sql.=" where (empmast.name LIKE '%$rd%')";
	$totalFiltered = $db->query("select count(*) from ($sql) b")->fetchColumn();
	}

	$data=$db->query($sql)->fetchAll();

	$objArr = array();
	foreach ($data as $row) {

	 		$object = new stdClass();

	 	$ID = $row['ta_claim_id'];
	 	$action = "<div class='flex justify-left items-center'>

					<a class='flex items-center mr-5' id=\"viewBtn\" href='javascript:;' onclick=\"load_data('$ID')\"
					data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"lucide lucide-eye\"><path d=\"M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z\"></path><circle cx=\"12\" cy=\"12\" r=\"3\"></circle></svg>
					</a>

					<!-- <a class='flex items-center mr-5'   href='./index_ta_master.php' title='View TA Details'>
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"lucide lucide-eye\"><path d=\"M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z\"></path><circle cx=\"12\" cy=\"12\" r=\"3\"></circle></svg></a>-->

				<div>";

	 	$object->ta_claim_id 	= $row['ta_claim_id'];
	 	$object->created_on 	= $row['created_on'];
	 	$object->emp_id 	= $row['emp_id'];
	 	$object->name	= $row['name'];
        $object->travel_plan	= $row['travel_plan'];
	 	$object->manager_remarks 	= $row['manager_remarks'];
	 	$object->admin_remarks 	= $row['admin_remarks'];
	 	$object->accounts_remarks 	= $row['accounts_remarks'];
	 	$object->provisional_pay 	= $row['provisional_pay'];
	 	$object->pay_adj 	= $row['pay_adjust'];
	 	// $closed_ta_claim_value = $row['closed_ta_claim'] == 1 ? "True" : "False";
	 	// $object->closed_ta_claim 	= $closed_ta_claim_value;

	 	$object->action 	= $action;

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

