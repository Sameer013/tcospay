<?php
	$requestData= $_REQUEST;
    $leaveCode = isset($requestData['leaveCode']) ? $requestData['leaveCode'] : '';
    $columns = array(
        0 => 'LeaveGroupCode',
		1 => 'LeaveGroupDesc',
		2 => 'CarryForward',
		3 => 'Encash',
		4 => 'OverwriteWO',
		5 => 'OverwriteHL',
		6 => 'CreditType',
		7 => 'AnnQuota',
		8 => 'AttendDays',
		9 => 'LeaveCredit',
		10 => 'MonthlyQuota',
		11 => 'CreditMonthly',
		12 => 'CreditYearly',
        13 => 'action'
    );
    $data=array();
    try {
	require_once 'includes/dbconn.php';
	

	// $leaveCode = '2'; //Test Data

	// leavegroup has one row per (LeaveGroupCode, LeaveCode), so no grouping is needed
	$sql = "SELECT lm.LeaveName, lg.LeaveGroupDesc, lg.CarryForward, Encash, OverwriteWO, OverwriteHL, CreditType, AnnQuota, AttendDays, LeaveCredit, MonthlyQuota, CreditMonthly, CreditYearly,LeaveGroupCode,lg.LeaveCode FROM leavegroup lg
	JOIN leavemst lm ON lg.LeaveCode=lm.LeaveCode
	WHERE LeaveGroupCode = :leaveCode";
	$params = array(':leaveCode' => $leaveCode);
	$stmt = $db->prepare("select count(*) from ($sql) b");
	$stmt->execute($params);
	$totalData = $stmt->fetchColumn();
	$totalFiltered = $totalData;
	if( !empty($requestData['search']['value']) ) {
	$sql.=" AND (lm.LeaveName LIKE :search)";
	$params[':search'] = '%' . $requestData['search']['value'] . '%';
	$stmt = $db->prepare("select count(*) from ($sql) b");
	$stmt->execute($params);
	$totalFiltered = $stmt->fetchColumn();
	}
	$orderCol = isset($requestData['order'][0]['column'], $columns[$requestData['order'][0]['column']]) && $columns[$requestData['order'][0]['column']] !== 'action' ? $columns[$requestData['order'][0]['column']] : 'LeaveGroupCode';
	$orderDir = (isset($requestData['order'][0]['dir']) && strtolower($requestData['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';
	$sql.=" ORDER BY $orderCol $orderDir LIMIT ".(int) $requestData['start']." ,".(int) $requestData['length'];
	$stmt = $db->prepare($sql);
	$stmt->execute($params);
	$data=$stmt->fetchAll();

	$objArr = array();
	foreach ($data as $row) {

	 		$object = new stdClass();

	 	$LeaveGroupCode = $row['LeaveGroupCode'];
	 	$LeaveCode = $row['LeaveCode'];
	 	$action = "<div class='flex justify-left items-center'>


					<a class='flex items-center mr-5' id=\"edit-button\" href='javascript:;' onclick=\"load_data('$LeaveGroupCode','$LeaveCode' )\"
					data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"check-square\" data-lucide=\"check-square\" class=\"lucide lucide-check-square w-4 h-4 mr-1\"><polyline points=\"9 11 12 14 22 4\"></polyline><path d=\"M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11\"></path></svg></a>

					<a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data('$LeaveGroupCode','$LeaveCode')\">
					<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"trash-2\" data-lucide=\"trash-2\" class=\"lucide lucide-trash-2 w-4 h-4 mr-1\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg></a></div>";

	 	$object->LeaveGroupCode 	= $row['LeaveGroupCode'];
	 	$object->LeaveCode 	= $row['LeaveCode'];
		$LeaveGroupCode=$row['LeaveGroupCode'];
        $object->LeaveGroupDesc	= $row['LeaveName'];
        $object->CarryForward	= $row['CarryForward'];
        $object->Encash	= $row['Encash'];
        $object->OverwriteWO	= $row['OverwriteWO'];
        $object->OverwriteHL	= $row['OverwriteHL'];
        $object->CreditType	= $row['CreditType'];
        $object->AnnQuota	= $row['AnnQuota'];
        $object->AttendDays	= $row['AttendDays'];
        $object->LeaveCredit	= $row['LeaveCredit'];
        $object->MonthlyQuota	= $row['MonthlyQuota'];
        $object->CreditMonthly	= $row['CreditMonthly'];
        $object->CreditYearly	= $row['CreditYearly'];
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
			"data"            => $objArr,   // total data array
			"leaveCode" => $leaveCode
			);
    echo json_encode($json_data);
?>

