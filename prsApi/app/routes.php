<?php

ini_set('display_errors', 0);
session_start();
if (!isset($_SESSION['user'])) {
	header('location:login.php');
	exit;
}


use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

require_once 'db.php';


return function (App $app) {
	$burl = '/tcospay/prsApi'; //Define base directory
	$app->options("$burl/{routes:.*}", function (Request $request, Response $response) {
		// CORS Pre-Flight OPTIONS Request Handler
		return $response;
	});

	$app->get("$burl/", function (Request $request, Response $response) {
		$response->getBody()->write('Hello world!');
		return $response;
	});

	//Begin REST API for Employee Master entity
	// $app->group("$burl/empmast", function (Group $group) {
	// 	$group->get('', function (Request $request, Response $response, $args) {
	// 		try {
	// 			$userId = $_SESSION['user'];
	// 			$params = $request->getQueryParams();
	// 			$filter = '';
	// 			foreach ($params as $p => $v) {
	// 				if ($v)
	// 					switch ($p) {
	// 						case 'id';
	// 							$filter .= "$p='$v'";
	// 							break;
	// 						default;
	// 							if ($filter)
	// 								$filter .= " and ";
	// 							$filter .= "$p like '%$v%'";
	// 							break;
	// 					}
	// 			}
	// 			$db = getconn();
	// 			// $sql="select * from Empmast ";
	// 			if ($userId == 'Admin' || $userId == 'Super Admin') {
					
	// 				$sql = "SELECT em.*, bm.DESCR, cat.descr, cmp.comp_name, loc.loc, dsgc.designation
	// 				-- , lg.LeaveGroupDesc
	// 				FROM empmast AS em
	// 				LEFT JOIN bankmast AS bm ON em.BID = bm.BID
	// 				LEFT JOIN category AS cat ON em.CATCODE = cat.catcode
	// 				LEFT JOIN compmast AS cmp ON em.comp_id = cmp.comp_id
	// 				LEFT JOIN locmast AS loc ON em.loc_id = loc.loc_id
	// 				LEFT JOIN designationmaster AS dsgc ON em.DSGCODE = dsgc.desgcode
	// 				-- LEFT JOIN leavegroup AS lg ON em.LeaveGroup = lg.LeaveGroupCode
	// 				";
	// 				} else {

	// 				$sql = "SELECT em.*, bm.DESCR, cat.descr, cmp.comp_name, loc.loc, dsgc.designation, lg.LeaveGroupDesc
	// 				FROM empmast AS em
	// 				LEFT JOIN bankmast AS bm ON em.BID = bm.BID
	// 				LEFT JOIN category AS cat ON em.CATCODE = cat.catcode
	// 				LEFT JOIN compmast AS cmp ON em.comp_id = cmp.comp_id
	// 				LEFT JOIN locmast AS loc ON em.loc_id = loc.loc_id
	// 				LEFT JOIN designationmaster AS dsgc ON em.DSGCODE = dsgc.desgcode
	// 				LEFT JOIN leavegroup AS lg ON em.LeaveGroup = lg.LeaveGroupCode
	// 				WHERE em.EMPNO = $userId";
	// 			}
	// 			if ($filter)
	// 				$sql .= " where $filter";
	// 			else
	// 				$sql .= " limit 200";
	// 			$result = $db->query($sql);
	// 			$data = $result->fetchAll(PDO::FETCH_ASSOC);
	// 			foreach ($data as &$row) {
	// 				if (array_key_exists('PHOTO', $row) && $row['PHOTO'] !== null) {
	// 					$photoData = base64_encode($row['PHOTO']);
	// 					$row['PHOTO'] = $photoData;
	// 				}
	// 			}
	// 			$status = 200;
	// 		} catch (Exception $e) {
	// 			$data[] = array("errmsg" => $e->getMessage());
	// 			$status = 400;
	// 		}
	// 		$payload = json_encode($data);
	// 		$response->getBody()->write($payload);
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});
	// 	$group->get("/{EMPCODE}", function (Request $request, Response $response, $args) {
	// 		try {
	// 			$db = getconn();
	// 			// $stmt = $db->prepare('SELECT empmast.*
	// 			// FROM empmast
	// 			// WHERE EMPNO = :txt_empno');
	// 			$stmt = $db->prepare("SELECT em.*, bm.DESCR, cat.descr,cmp.comp_name,loc.loc,dsgc.designation, lg.LeaveGroupDesc
	// 			FROM empmast AS em
	// 			LEFT JOIN bankmast AS bm ON em.BID = bm.BID
	// 			LEFT JOIN category as cat on em.CATCODE = cat.catcode
	// 			LEFT JOIN compmast as cmp ON em.comp_id = cmp.comp_id
	// 			LEFT JOIN locmast as loc ON em.loc_id = loc.loc_id
	// 			LEFT JOIN designationmaster as dsgc ON em.DSGCODE = dsgc.desgcode
	// 			LEFT JOIN leavegroup as lg ON em.LeaveGroup = lg.LeaveGroupCode
	// 			WHERE em.EMPNO = :txt_empno");
	// 			$stmt->bindValue(':txt_empno', $args['EMPCODE'], PDO::PARAM_INT);
	// 			$stmt->execute();

	// 			$data = $stmt->fetch(PDO::FETCH_ASSOC);
	// 			if ($data['PHOTO'] !== null) {
	// 				$photoData = base64_encode($data['PHOTO']);
	// 				$data['PHOTO'] = $photoData;
	// 			}
	// 			$status = 200;
	// 		} catch (Exception $e) {
	// 			$data = array("errmsg" => $e->getMessage());
	// 			$status = 400;
	// 		}

	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});
	// 	$group->post('', function (Request $request, Response $response, $args) {
	// 		try {
	// 			$data = $request->getParsedBody();
	// 			$photoData = $data['txt_PHOTO'];

	// 			$dor= $data['txt_Dor'] ?? null;

	// 			if (strpos($photoData, 'data:image') === 0) {
	// 				$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			}
	// 			$sql = "INSERT INTO empmast (NAME, comp_id, STATE, ADDRESS, ADDRESS1, PHONE, PHONE1, SEX, mar_stat, PAN, CATCODE, DOB, DOJ, DSGCODE, DOR, DOC, cardno, PHOTO)
    //             VALUES (:txt_Name, :txt_Comp_id, :txt_State, :txt_Address, :txt_Add1, :txt_Phone, :txt_Phone1, :txt_Sex, :txt_Mar_stat, :txt_Pan, :txt_Catcode, :txt_Dob, :txt_Doj, :txt_Dsgcode, :txt_Dor, :txt_Doc, :txt_Cardno, :txt_PHOTO)";
	// 			$db = getconn();
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":txt_Name", $data['txt_Name']);
	// 			$stmt->bindParam(":txt_Comp_id", $data['txt_Comp_id'] ?? null, PDO::PARAM_STR);
	// 			$stmt->bindParam(":txt_State", $data['txt_State']);
	// 			$stmt->bindParam(":txt_Phone", $data['txt_Phone']);
	// 			$stmt->bindParam(":txt_Phone1", $data['txt_Phone1']);
	// 			$stmt->bindParam(":txt_Address", $data['txt_Address']);
	// 			$stmt->bindParam(":txt_Add1", $data['txt_Add1']);
	// 			$stmt->bindParam(":txt_Sex", $data['txt_Sex']);
	// 			$stmt->bindParam(":txt_Mar_stat", $data['txt_Mar_stat']);
	// 			$stmt->bindParam(":txt_Pan", $data['txt_Pan']);
	// 			$stmt->bindParam(":txt_Catcode", $data['txt_Catcode']);
	// 			$stmt->bindParam(":txt_Dob", $data['txt_Dob']);
	// 			$stmt->bindParam(":txt_Doj", $data['txt_doj']);
	// 			$stmt->bindParam(":txt_Dsgcode", $data['txt_Dsgcode']);
	// 			// $stmt->bindParam(":txt_Dor", $data['txt_Dor']);
	// 			$stmt->bindParam(':txt_Dor', $dor, $dor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
	// 			$stmt->bindParam(":txt_Doc", $data['txt_Doc']);
	// 			$stmt->bindParam(":txt_Cardno", $data['txt_Cardno']);
	// 			$stmt->bindParam(":txt_PHOTO", $photoData, PDO::PARAM_LOB);
	// 			$stmt->execute();

	// 			// Check for errors
	// 			$errorInfo = $stmt->errorInfo();
	// 			if ($errorInfo[0] !== '00000') {
	// 				// Handle the error
	// 				$response->getBody()->write(json_encode(['status' => 'Error', 'message' => 'Failed to insert data']));
	// 				return $response->withStatus(500);
	// 			}

	// 			// If no errors, return a success response
	// 			$response->getBody()->write(json_encode(['status' => 'Ok']));
	// 			return $response;
	// 		} catch (Exception $e) {
	// 			// Handle exceptions
	// 			$errorResponse = [
	// 				'status' => 'Error',
	// 				'message' => $e->getMessage()
	// 			];

	// 			$response = $response->withHeader('Content-Type', 'application/json');
	// 			$response->getBody()->write(json_encode($errorResponse));

	// 			return $response->withStatus(500);
	// 		}


	// 		// $response->getBody()->write(json_encode($data));
	// 		// 	return $response
	// 		// 			->withHeader('Content-Type', 'application/json')
	// 		// 			->withStatus($status);
	// 	});
	// 	// $group->put('/{EMPCODE}', function (Request $request, Response $response, $args) {
	// 	// 	try {

	// 	// 		$body = $request->getBody();
	// 	// 		$db = getconn();
	// 	// 		$users = $request->getParsedBody();
	// 	// 		$photoData = $users['txt_PHOTO'] ?? null;
	// 	// 		// if (strpos($photoData, 'data:image') === 0) {
	// 	// 		// 	$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 	// 		// }
	// 	// 		if (!empty($photoData) && strpos($photoData, 'data:image') === 0) {
	// 	// 			$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 	// 		}


	// 	// 		$sql = "UPDATE empmast SET NAME=:txt_Name, comp_id=:txt_Comp_id, STATE=:txt_State, ADDRESS=:txt_Address,
	// 	// 	ADDRESS1=:txt_Add1, PHONE=:txt_Phone, PHONE1=:txt_Phone1, SEX=:txt_Sex, mar_stat=:txt_Mar_stat, PAN=:txt_Pan,
	// 	// 	CATCODE=:txt_Catcode, DOB=:txt_Dob, DOJ=:txt_Doj, DSGCODE=:txt_Dsgcode, DOR=:txt_Dor, DOC=:txt_Doc,
	// 	// 	cardno=:txt_Cardno, BID=:txt_Bank, ACCNO=:txt_AccNo, BASIC=:txt_Basic, PAYSCALE=:txt_PayScale,
	// 	// 	PFACNO=:txt_Pf, incentive=:txt_Incentive, incr_remark=:txt_IncrRemark, basic_increment=:txt_BasicIncr,
	// 	// 	incr_date=:txt_Doi, relation=:txt_Relation, GNAME=:txt_Gname, qual=:txt_Edu, subTaught=:txt_Subject,
	// 	// 	EMAIL=:txt_Email, uuid=:txt_Uid, uanNo=:txt_UanNo, EXP=:txt_Exper, SHCODE=:txt_shcode, PHOTO=:txt_PHOTO WHERE EMPNO=:txt_Emp_no";
	// 	// 		$stmt = $db->prepare($sql);
	// 	// 		$stmt->bindParam(":txt_Name", $users['Name']);
	// 	// 		$stmt->bindParam(":txt_Comp_id", $users['Comp_id']);
	// 	// 		$stmt->bindParam(":txt_State", $users['State']);
	// 	// 		$stmt->bindParam(":txt_Phone", $users['Phone']);
	// 	// 		$stmt->bindParam(":txt_Phone1", $users['Phone1']);
	// 	// 		$stmt->bindParam(":txt_Address", $users['Address']);
	// 	// 		$stmt->bindParam(":txt_Add1", $users['Add1']);
	// 	// 		$stmt->bindParam(":txt_Emp_no", $users['EmpNo']);
	// 	// 		$stmt->bindParam(":txt_Sex", $users['sex']);
	// 	// 		$stmt->bindParam(":txt_Mar_stat", $users['Mar_stat']);
	// 	// 		$stmt->bindParam(":txt_Pan", $users['Pan']);
	// 	// 		$stmt->bindParam(":txt_Catcode", $users['Catcode']);
	// 	// 		$stmt->bindParam(":txt_Dob", $users['Dob']);
	// 	// 		$stmt->bindParam(":txt_Doj", $users['Doj']);
	// 	// 		$stmt->bindParam(":txt_Dsgcode", $users['Dsgcode']);
	// 	// 		$stmt->bindParam(":txt_Dor", $users['Dor']);
	// 	// 		$stmt->bindParam(":txt_Doc", $users['Doc']);
	// 	// 		$stmt->bindParam(":txt_Cardno", $users['Cardno']);
	// 	// 		$stmt->bindParam(":txt_Bank", $users['Bank']);
	// 	// 		$stmt->bindParam(":txt_AccNo", $users['AccNo']);
	// 	// 		$stmt->bindParam(":txt_Basic", $users['Basic']);
	// 	// 		$stmt->bindParam(":txt_PayScale", $users['PayScale']);
	// 	// 		$stmt->bindParam(":txt_Pf", $users['Pf']);
	// 	// 		$stmt->bindParam(":txt_Incentive", $users['Incentive']);
	// 	// 		$stmt->bindParam(":txt_IncrRemark", $users['IncrRemark']);
	// 	// 		$stmt->bindParam(":txt_BasicIncr", $users['BasicIncr']);
	// 	// 		$stmt->bindParam(":txt_Doi", $users['Doi']);
	// 	// 		$stmt->bindParam(":txt_Relation", $users['Relation']);
	// 	// 		$stmt->bindParam(":txt_Gname", $users['Gname']);
	// 	// 		$stmt->bindParam(":txt_Edu", $users['Edu']);
	// 	// 		$stmt->bindParam(":txt_Subject", $users['Subject']);
	// 	// 		$stmt->bindParam(":txt_Email", $users['Email']);
	// 	// 		$stmt->bindParam(":txt_Uid", $users['Uid']);
	// 	// 		$stmt->bindParam(":txt_UanNo", $users['UanNo']);
	// 	// 		$stmt->bindParam(":txt_Exper", $users['Exper']);
	// 	// 		$stmt->bindParam(":txt_shcode", $users['Shcode']);
	// 	// 		$stmt->bindParam(":txt_PHOTO", $photoData, PDO::PARAM_LOB);

	// 	// 		$stmt->execute();
	// 	// 		if ($stmt->rowCount() > 0)
	// 	// 			$msg = "success";
	// 	// 		else
	// 	// 			$msg = "no update";
	// 	// 		$db = null;
	// 	// 		$status = 201;
	// 	// 		$data = null;
	// 	// 		$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
	// 	// 	} catch (Exception $e) {
	// 	// 		$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
	// 	// 		$status = 200;
	// 	// 	}
	// 	// 	$response->getBody()->write(json_encode($data));
	// 	// 	return $response
	// 	// 		->withHeader('Content-Type', 'application/json')
	// 	// 		->withStatus($status);
	// 	// });

	// 	$group->put('/general/{EMPCODE}', function (Request $request, Response $response, $args) {
	// 		try {

	// 			$body = $request->getBody();
	// 			$db = getconn();
	// 			$users = $request->getParsedBody();
	// 			$photoData = $users['txt_PHOTO'] ?? null;
	// 			// if (strpos($photoData, 'data:image') === 0) {
	// 			// 	$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			// }
	// 			if (!empty($photoData) && strpos($photoData, 'data:image') === 0) {
	// 				$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			}


	// 			$sql = "UPDATE empmast SET NAME=:txt_Name, comp_id=:txt_Comp_id, STATE=:txt_State, ADDRESS=:txt_Address,
	// 		ADDRESS1=:txt_Add1, PHONE=:txt_Phone, PHONE1=:txt_Phone1, SEX=:txt_Sex, mar_stat=:txt_Mar_stat, PAN=:txt_Pan,
	// 		CATCODE=:txt_Catcode, DOB=:txt_Dob, DOJ=:txt_Doj, DSGCODE=:txt_Dsgcode, DOR=:txt_Dor, DOC=:txt_Doc,
	// 		cardno=:txt_Cardno, PHOTO=:txt_PHOTO WHERE EMPNO=:txt_Emp_no";
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":txt_Emp_no", $users['EmpNo']);
	// 			$stmt->bindParam(":txt_Name", $users['Name']);
	// 			$stmt->bindParam(":txt_Comp_id", $users['Comp_id']);
	// 			$stmt->bindParam(":txt_State", $users['State']);
	// 			$stmt->bindParam(":txt_Phone", $users['Phone']);
	// 			$stmt->bindParam(":txt_Phone1", $users['Phone1']);
	// 			$stmt->bindParam(":txt_Address", $users['Address']);
	// 			$stmt->bindParam(":txt_Add1", $users['Add1']);
	// 			$stmt->bindParam(":txt_Sex", $users['sex']);
	// 			$stmt->bindParam(":txt_Mar_stat", $users['Mar_stat']);
	// 			$stmt->bindParam(":txt_Pan", $users['Pan']);
	// 			$stmt->bindParam(":txt_Catcode", $users['Catcode']);
	// 			$stmt->bindParam(":txt_Dob", $users['Dob']);
	// 			$stmt->bindParam(":txt_Doj", $users['Doj']);
	// 			$stmt->bindParam(":txt_Dsgcode", $users['Dsgcode']);
	// 			$stmt->bindParam(":txt_Dor", $users['Dor']);
	// 			$stmt->bindParam(":txt_Doc", $users['Doc']);
	// 			$stmt->bindParam(":txt_Cardno", $users['Cardno']);
	// 			$stmt->bindParam(":txt_PHOTO", $photoData, PDO::PARAM_LOB);

	// 			$stmt->execute();
	// 			if ($stmt->rowCount() > 0)
	// 				$msg = "success";
	// 			else
	// 				$msg = "no update";
	// 			$db = null;
	// 			$status = 201;
	// 			$data = null;
	// 			$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
	// 		} catch (Exception $e) {
	// 			$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
	// 			$status = 200;
	// 		}
	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});

	// 	$group->put('/finance/{EMPCODE}', function (Request $request, Response $response, $args) {
	// 		try {

	// 			$body = $request->getBody();
	// 			$db = getconn();
	// 			$users = $request->getParsedBody();

	// 			$sql = "UPDATE empmast SET BID=:txt_Bank, ACCNO=:txt_AccNo, BASIC=:txt_Basic, PAYSCALE=:txt_PayScale,
	// 		PFACNO=:txt_Pf,  STATUS= :txt_status  WHERE EMPNO=:txt_Emp_no";
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":txt_Emp_no", $args['EMPCODE']);
	// 			$stmt->bindParam(":txt_Bank", $users['Bank']);
	// 			$stmt->bindParam(":txt_AccNo", $users['AccNo']);
	// 			$stmt->bindParam(":txt_Basic", $users['Basic']);
	// 			$stmt->bindParam(":txt_PayScale", $users['PayScale']);
	// 			$stmt->bindParam(":txt_Pf", $users['Pf']);
	// 			// $stmt->bindParam(":txt_esic", $users['Esic']);
	// 			$stmt->bindParam(":txt_status", $users['Status']);
	// 			// $stmt->bindParam(":txt_Month", $users['Month']);
	// 			// $stmt->bindParam(":txt_Nod", $users['Nod']);

	// 			$stmt->execute();
	// 			if ($stmt->rowCount() > 0)
	// 				$msg = "success";
	// 			else
	// 				$msg = "no update";
	// 			$db = null;
	// 			$status = 201;
	// 			$data = null;
	// 			$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
	// 		} catch (Exception $e) {
	// 			$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
	// 			$status = 200;
	// 		}
	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});

	// 	$group->put('/leave/{EMPCODE}', function (Request $request, Response $response, $args) {
	// 		try {

	// 			$body = $request->getBody();
	// 			$db = getconn();
	// 			$users = $request->getParsedBody();
	// 			$photoData = $users['txt_PHOTO'] ?? null;
	// 			// if (strpos($photoData, 'data:image') === 0) {
	// 			// 	$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			// }
	// 			if (!empty($photoData) && strpos($photoData, 'data:image') === 0) {
	// 				$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			}


	// 			$sql = "UPDATE empmast SET  SHCODE=:txt_shcode, woff =:txt_woff, LeaveGroup =:txt_leavegroup WHERE EMPNO=:txt_Emp_no";
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":txt_Emp_no", $args['EMPCODE']);
	// 			$stmt->bindParam(":txt_shcode", $users['Shcode']);
	// 			$stmt->bindParam(":txt_woff", $users['woff']);
	// 			$stmt->bindParam(":txt_leavegroup", $users['LeaveGroup']);

	// 			$stmt->execute();
	// 			if ($stmt->rowCount() > 0)
	// 				$msg = "success";
	// 			else
	// 				$msg = "no update";
	// 			$db = null;
	// 			$status = 201;
	// 			$data = null;
	// 			$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
	// 		} catch (Exception $e) {
	// 			$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
	// 			$status = 200;
	// 		}
	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});

	// 	$group->put('/incentive/{EMPCODE}', function (Request $request, Response $response, $args) {
	// 		try {

	// 			$body = $request->getBody();
	// 			$db = getconn();
	// 			$users = $request->getParsedBody();
	// 			$photoData = $users['txt_PHOTO'] ?? null;
	// 			// if (strpos($photoData, 'data:image') === 0) {
	// 			// 	$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			// }
	// 			if (!empty($photoData) && strpos($photoData, 'data:image') === 0) {
	// 				$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
	// 			}


	// 			$sql = "UPDATE empmast SET incentive=:txt_Incentive, incr_remark=:txt_IncrRemark, basic_increment=:txt_BasicIncr,
	// 		incr_date=:txt_Doi WHERE EMPNO=:txt_Emp_no";
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":txt_Emp_no", $args['EMPCODE']);
	// 			$stmt->bindParam(":txt_Incentive", $users['Incentive']);
	// 			$stmt->bindParam(":txt_IncrRemark", $users['IncrRemark']);
	// 			$stmt->bindParam(":txt_BasicIncr", $users['BasicIncr']);
	// 			$stmt->bindParam(":txt_Doi", $users['Doi']);

	// 			$stmt->execute();
	// 			if ($stmt->rowCount() > 0)
	// 				$msg = "success";
	// 			else
	// 				$msg = "no update";
	// 			$db = null;
	// 			$status = 201;
	// 			$data = null;
	// 			$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
	// 		} catch (Exception $e) {
	// 			$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
	// 			$status = 200;
	// 		}
	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});

	// 	$group->put('/others/{EMPCODE}', function (Request $request, Response $response, $args) {
	// 		try {

	// 			$body = $request->getBody();
	// 			$db = getconn();
	// 			$users = $request->getParsedBody();
	// 			$sql = "UPDATE empmast SET relation=:relation, GNAME=:GNAME, qual=:qual, subTaught=:subTaught, EMAIL=:EMAIL, uuid=:uuid, EXP=:EXP WHERE EMPNO=:txt_Emp_no";
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":txt_Emp_no", $args['EMPCODE']);
	// 			$stmt->bindParam(":relation", $users['Relation']);
	// 			$stmt->bindParam(":GNAME", $users['Gname']);
	// 			$stmt->bindParam(":qual", $users['Edu']);
	// 			$stmt->bindParam(":subTaught", $users['Subject']);
	// 			$stmt->bindParam(":EMAIL", $users['Email']);
	// 			$stmt->bindParam(":uuid", $users['Uid']);
	// 			// $stmt->bindParam(":uanNo", $users['UanNo']);
	// 			$stmt->bindParam(":EXP", $users['Exper']);

	// 			$stmt->execute();
	// 			if ($stmt->rowCount() > 0)
	// 				$msg = "success";
	// 			else
	// 				$msg = "no update";
	// 			$db = null;
	// 			$status = 201;
	// 			$data = null;
	// 			$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
	// 		} catch (Exception $e) {
	// 			$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
	// 			$status = 200;
	// 		}
	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});

	// 	$group->delete('/{empno}', function (Request $request, Response $response, $args) {
	// 		try {
	// 			$sql = "DELETE FROM empmast WHERE empno = :CODE";
	// 			$db = getconn();
	// 			$stmt = $db->prepare($sql);
	// 			$stmt->bindParam(":CODE", $args['empno']);
	// 			$stmt->execute();

	// 			$rowCount = $stmt->rowCount();
	// 			$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

	// 			$db = null;
	// 			$status = 201;
	// 			$data = array("status" => "Ok", "msg" => $msg);
	// 		} catch (Exception $e) {
	// 			$data = array("status" => "Error", "msg" => $e->getMessage());
	// 			$status = 200;
	// 		}

	// 		$response->getBody()->write(json_encode($data));
	// 		return $response
	// 			->withHeader('Content-Type', 'application/json')
	// 			->withStatus($status);
	// 	});

	// });
	// //End of REST API for Employee Master entity


	$app->group("$burl/empmast", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$userId = $_SESSION['user'];
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				// $sql="select * from Empmast ";
				if ($userId == 'Admin' || $userId == 'Super Admin') {
					$sql = "SELECT em.*, bm.DESCR, 
								cat.descr, 
								cmp.comp_name, 
								loc.loc, 
								dsgc.designation, 
								lg.LeaveGroupDesc
							FROM empmast AS em
							LEFT JOIN bankmast AS bm ON em.BID = bm.BID
							LEFT JOIN category AS cat ON em.CATCODE = cat.catcode
							LEFT JOIN compmast AS cmp ON em.comp_id = cmp.comp_id
							LEFT JOIN locmast AS loc ON em.loc_id = loc.loc_id
							LEFT JOIN designationmaster AS dsgc ON em.DSGCODE = dsgc.desgcode
							LEFT JOIN leavegroup AS lg ON em.LeaveGroup = lg.LeaveGroupCode
							GROUP BY 
								em.EMPNO,
								bm.DESCR,
								cat.descr,
								cmp.comp_name,
								loc.loc,
								dsgc.designation,
								lg.LeaveGroupDesc;";
				} else {
					$sql = "SELECT em.*, bm.DESCR, cat.descr, cmp.comp_name, loc.loc, dsgc.designation, lg.LeaveGroupDesc FROM empmast AS em
							LEFT JOIN bankmast AS bm ON em.BID = bm.BID
							LEFT JOIN category AS cat ON em.CATCODE = cat.catcode
							LEFT JOIN compmast AS cmp ON em.comp_id = cmp.comp_id
							LEFT JOIN locmast AS loc ON em.loc_id = loc.loc_id
							LEFT JOIN designationmaster AS dsgc ON em.DSGCODE = dsgc.desgcode
							LEFT JOIN leavegroup AS lg ON em.LeaveGroup = lg.LeaveGroupCode
							where em.EMPNO = $userId
							GROUP BY em.EMPNO,bm.DESCR,cat.descr,cmp.comp_name,loc.loc,dsgc.designation,lg.LeaveGroupDesc;";
						}
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				foreach ($data as &$row) {
					if (array_key_exists('PHOTO', $row) && $row['PHOTO'] !== null) {
						$photoData = base64_encode($row['PHOTO']);
						$row['PHOTO'] = $photoData;
					}
				}
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{EMPCODE}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				// $stmt = $db->prepare('SELECT empmast.*
				// FROM empmast
				// WHERE EMPNO = :txt_empno');
				$stmt = $db->prepare("SELECT em.*, bm.DESCR, cat.descr,cmp.comp_name,loc.loc,dsgc.designation, lg.LeaveGroupDesc
				FROM empmast AS em
				LEFT JOIN bankmast AS bm ON em.BID = bm.BID
				LEFT JOIN category as cat on em.CATCODE = cat.catcode
				LEFT JOIN compmast as cmp ON em.comp_id = cmp.comp_id
				LEFT JOIN locmast as loc ON em.loc_id = loc.loc_id
				LEFT JOIN designationmaster as dsgc ON em.DSGCODE = dsgc.desgcode
				LEFT JOIN leavegroup as lg ON em.LeaveGroup = lg.LeaveGroupCode
				WHERE em.EMPNO = :txt_empno");
				$stmt->bindValue(':txt_empno', $args['EMPCODE'], PDO::PARAM_INT);
				$stmt->execute();

				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($data['PHOTO'] !== null) {
					$photoData = base64_encode($data['PHOTO']);
					$data['PHOTO'] = $photoData;
				}
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->post('', function (Request $request, Response $response) {
    	try {
        $db    = getconn();
        $data  = $request->getParsedBody();

        // ─────────────────────────────────────────────
        // Helpers (same as PUT)
        // ─────────────────────────────────────────────
        $nullIfEmpty = fn($val) => (isset($val) && $val !== '') ? $val : null;

        $decimalOrNull = fn($val) => (isset($val) && is_numeric($val)) ? (float)$val : null;

        $dateOrNull = function ($val) {
            if (empty($val)) return null;
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $val) ? $val : null;
        };

        // ─────────────────────────────────────────────
        // Photo handling
        // ─────────────────────────────────────────────
        $photoData = $data['txt_PHOTO'] ?? null;
        if (!empty($photoData) && strpos($photoData, 'data:image') === 0) {
            $photoData = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $photoData)
            );
        }

        // ─────────────────────────────────────────────
        // Sanitize fields
        // ─────────────────────────────────────────────
        $name     = $nullIfEmpty($data['txt_Name'] ?? null);
        $compId   = $nullIfEmpty($data['txt_Comp_id'] ?? null);
        $state    = $nullIfEmpty($data['txt_State'] ?? null);
        $address  = $nullIfEmpty($data['txt_Address'] ?? null);
        $add1     = $nullIfEmpty($data['txt_Add1'] ?? null);
        $phone    = $nullIfEmpty($data['txt_Phone'] ?? null);
        $phone1   = $nullIfEmpty($data['txt_Phone1'] ?? null);
        $sex      = $nullIfEmpty($data['txt_Sex'] ?? null);
        $marStat  = $nullIfEmpty($data['txt_Mar_stat'] ?? null);
        $pan      = $nullIfEmpty($data['txt_Pan'] ?? null);
        $catcode  = $nullIfEmpty($data['txt_Catcode'] ?? null);
        $dsgcode  = $nullIfEmpty($data['txt_Dsgcode'] ?? null);
        $cardno   = $nullIfEmpty($data['txt_Cardno'] ?? null);

        $dob      = $dateOrNull($data['txt_Dob'] ?? null);
        $doj      = $dateOrNull($data['txt_Doj'] ?? null);
        $dor      = $dateOrNull($data['txt_Dor'] ?? null);
        $doc      = $dateOrNull($data['txt_Doc'] ?? null);

        // ─────────────────────────────────────────────
        // SQL
        // ─────────────────────────────────────────────
        $sql = "INSERT INTO empmast (
                    NAME, comp_id, STATE, ADDRESS, ADDRESS1,
                    PHONE, PHONE1, SEX, mar_stat, PAN,
                    CATCODE, DOB, DOJ, DSGCODE,
                    DOR, DOC, cardno, PHOTO
                ) VALUES (
                    :txt_Name, :txt_Comp_id, :txt_State, :txt_Address, :txt_Add1,
                    :txt_Phone, :txt_Phone1, :txt_Sex, :txt_Mar_stat, :txt_Pan,
                    :txt_Catcode, :txt_Dob, :txt_Doj, :txt_Dsgcode,
                    :txt_Dor, :txt_Doc, :txt_Cardno, :txt_PHOTO
                )";

        $stmt = $db->prepare($sql);

        // ─────────────────────────────────────────────
        // Bind TEXT
        // ─────────────────────────────────────────────
        $stmt->bindParam(":txt_Name", $name);
        $stmt->bindParam(":txt_Comp_id", $compId);
        $stmt->bindParam(":txt_State", $state);
        $stmt->bindParam(":txt_Address", $address);
        $stmt->bindParam(":txt_Add1", $add1);
        $stmt->bindParam(":txt_Phone", $phone);
        $stmt->bindParam(":txt_Phone1", $phone1);
        $stmt->bindParam(":txt_Sex", $sex);
        $stmt->bindParam(":txt_Mar_stat", $marStat);
        $stmt->bindParam(":txt_Pan", $pan);
        $stmt->bindParam(":txt_Catcode", $catcode);
        $stmt->bindParam(":txt_Dsgcode", $dsgcode);
        $stmt->bindParam(":txt_Cardno", $cardno);

        // ─────────────────────────────────────────────
        // Bind DATE (safe NULL handling)
        // ─────────────────────────────────────────────
        $stmt->bindValue(":txt_Dob", $dob, $dob === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Doj", $doj, $doj === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Dor", $dor, $dor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Doc", $doc, $doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        // ─────────────────────────────────────────────
        // BLOB
        // ─────────────────────────────────────────────
        $stmt->bindParam(":txt_PHOTO", $photoData, PDO::PARAM_LOB);

        $stmt->execute();

        $data = [
            "status" => "Ok",
            "msg"    => "inserted"
        ];
        $status = 201;

    } catch (Exception $e) {
        $data = [
            "status" => "Error",
            "msg"    => $e->getMessage()
        ];
        $status = 500;
    }

    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
		});
		$group->put('/{EMPCODE}', function (Request $request, Response $response, $args) {
    try {
        $db    = getconn();
        $users = $request->getParsedBody();
        if (!is_array($users)) {
            $rawBody = (string) $request->getBody();
            $jsonBody = json_decode($rawBody, true);
            $users = is_array($jsonBody) ? $jsonBody : [];
        }
 
        // ─────────────────────────────────────────────
        // Helpers
        // ─────────────────────────────────────────────
 
        /**
         * Returns NULL if the value is an empty string or not set,
         * otherwise returns the original value.
         * Used for TEXT / VARCHAR fields that allow NULL.
         *
         * @param mixed $val
         * @return mixed|null
         */
        $nullIfEmpty = fn($val) => (isset($val) && $val !== '') ? $val : null;
 
        /**
         * Returns NULL if the value is empty/non-numeric,
         * otherwise casts to float.
         * Prevents SQLSTATE 1366 "Incorrect decimal value" errors.
         *
         * @param mixed $val
         * @return float|null
         */
        $decimalOrNull = fn($val) => (isset($val) && is_numeric($val)) ? (float)$val : null;
 
        /**
         * Returns NULL if the value is empty or not a valid date string (YYYY-MM-DD).
         * Prevents invalid date errors on DATE columns.
         *
         * @param mixed $val
         * @return string|null
         */
        $dateOrNull = function ($val) {
            if (empty($val)) return null;
            // Accept YYYY-MM-DD; reject placeholder-looking values
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $val) ? $val : null;
        };
 
        $empno = $nullIfEmpty($users['EmpNo'] ?? $users['txt_EmpNo'] ?? $args['EMPCODE'] ?? null);

        if ($empno === null) {
            throw new Exception("Employee number is required for update");
        }

        $currentStmt = $db->prepare("SELECT * FROM empmast WHERE EMPNO = :txt_empno");
        $currentStmt->bindValue(':txt_empno', $empno, PDO::PARAM_INT);
        $currentStmt->execute();
        $currentRow = $currentStmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentRow) {
            throw new Exception("Employee not found for update");
        }

        $pickValue = function ($inputKey, $columnName) use ($users, $currentRow) {
            return array_key_exists($inputKey, $users) ? $users[$inputKey] : ($currentRow[$columnName] ?? null);
        };

        // ─────────────────────────────────────────────
        // Photo: strip base64 header and decode to binary
        // ─────────────────────────────────────────────
        $photoData = array_key_exists('PHOTO', $users)
            ? $users['PHOTO']
            : (array_key_exists('txt_PHOTO', $users) ? $users['txt_PHOTO'] : ($currentRow['PHOTO'] ?? null));
        if (!empty($photoData) && is_string($photoData) && strpos($photoData, 'data:image') === 0) {
            $photoData = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $photoData)
            );
        }
 
        // ─────────────────────────────────────────────
        // Sanitize incoming values
        // ─────────────────────────────────────────────
 
        // Text fields — NULL when empty rather than empty string
        $name        = $nullIfEmpty($pickValue('Name', 'NAME'));
        $compId      = $nullIfEmpty($pickValue('Comp_id', 'comp_id'));
        $state       = $nullIfEmpty($pickValue('State', 'STATE'));
        $address     = $nullIfEmpty($pickValue('Address', 'ADDRESS'));
        $add1        = $nullIfEmpty($pickValue('Add1', 'ADDRESS1'));
        $phone       = $nullIfEmpty($pickValue('Phone', 'PHONE'));
        $phone1      = $nullIfEmpty($pickValue('Phone1', 'PHONE1'));
        $sex         = $nullIfEmpty($pickValue('sex', 'SEX'));
        $marStat     = $nullIfEmpty($pickValue('Mar_stat', 'mar_stat'));
        $pan         = $nullIfEmpty($pickValue('Pan', 'PAN'));
        $catcode     = $nullIfEmpty($pickValue('Catcode', 'CATCODE'));
        $dsgcode     = $nullIfEmpty($pickValue('Dsgcode', 'DSGCODE'));
        $cardno      = $nullIfEmpty($pickValue('Cardno', 'cardno'));
        $accNo       = $nullIfEmpty($pickValue('AccNo', 'ACCNO'));
        $pfacno      = $nullIfEmpty($pickValue('Pf', 'PFACNO'));
        $incrRemark  = $nullIfEmpty($pickValue('IncrRemark', 'incr_remark'));
        $relation    = $nullIfEmpty($pickValue('Relation', 'relation'));
        $gname       = $nullIfEmpty($pickValue('Gname', 'GNAME'));
        $edu         = $nullIfEmpty($pickValue('Edu', 'qual'));
        $subject     = $nullIfEmpty($pickValue('Subject', 'subTaught'));
        $email       = $nullIfEmpty($pickValue('Email', 'EMAIL'));
        $uid         = $nullIfEmpty($pickValue('Uid', 'uuid'));
        $uanNo       = $nullIfEmpty($pickValue('UanNo', 'uanNo'));
        $exper       = $nullIfEmpty($pickValue('Exper', 'EXP'));
        $shcode      = $nullIfEmpty($pickValue('Shcode', 'SHCODE'));
 
        // Numeric/DECIMAL fields — NULL when empty to avoid SQLSTATE 1366
        $bank        = $nullIfEmpty($pickValue('Bank', 'BID')); // FK — keep as-is (int or null)
        $basic       = $decimalOrNull($pickValue('Basic', 'BASIC'));
        $payScale    = $decimalOrNull($pickValue('PayScale', 'PAYSCALE'));
        $incentive   = $decimalOrNull($pickValue('Incentive', 'incentive'));
        $basicIncr   = $decimalOrNull($pickValue('BasicIncr', 'basic_increment'));
 
        // DATE fields — NULL when empty to avoid invalid date errors
        $dob         = $dateOrNull($pickValue('Dob', 'DOB'));
        $doj         = $dateOrNull($pickValue('Doj', 'DOJ'));
        $dor         = $dateOrNull($pickValue('Dor', 'DOR'));
        $doc         = $dateOrNull($pickValue('Doc', 'DOC'));
        $doi         = $dateOrNull($pickValue('Doi', 'incr_date'));
 
        // ─────────────────────────────────────────────
        // Build & Execute Query
        // ─────────────────────────────────────────────
        $sql = "UPDATE empmast SET
                    NAME            = :txt_Name,
                    comp_id         = :txt_Comp_id,
                    STATE           = :txt_State,
                    ADDRESS         = :txt_Address,
                    ADDRESS1        = :txt_Add1,
                    PHONE           = :txt_Phone,
                    PHONE1          = :txt_Phone1,
                    SEX             = :txt_Sex,
                    mar_stat        = :txt_Mar_stat,
                    PAN             = :txt_Pan,
                    CATCODE         = :txt_Catcode,
                    DOB             = :txt_Dob,
                    DOJ             = :txt_Doj,
                    DSGCODE         = :txt_Dsgcode,
                    DOR             = :txt_Dor,
                    DOC             = :txt_Doc,
                    cardno          = :txt_Cardno,
                    BID             = :txt_Bank,
                    ACCNO           = :txt_AccNo,
                    BASIC           = :txt_Basic,
                    PAYSCALE        = :txt_PayScale,
                    PFACNO          = :txt_Pf,
                    incentive       = :txt_Incentive,
                    incr_remark     = :txt_IncrRemark,
                    basic_increment = :txt_BasicIncr,
                    incr_date       = :txt_Doi,
                    relation        = :txt_Relation,
                    GNAME           = :txt_Gname,
                    qual            = :txt_Edu,
                    subTaught       = :txt_Subject,
                    EMAIL           = :txt_Email,
                    uuid            = :txt_Uid,
                    uanNo           = :txt_UanNo,
                    EXP             = :txt_Exper,
                    SHCODE          = :txt_shcode,
                    PHOTO           = :txt_PHOTO
                WHERE EMPNO = :txt_Emp_no";
 
        $stmt = $db->prepare($sql);
 
        // Text bindings
        $stmt->bindParam(":txt_Name",      $name);
        $stmt->bindParam(":txt_Comp_id",   $compId);
        $stmt->bindParam(":txt_State",     $state);
        $stmt->bindParam(":txt_Address",   $address);
        $stmt->bindParam(":txt_Add1",      $add1);
        $stmt->bindParam(":txt_Phone",     $phone);
        $stmt->bindParam(":txt_Phone1",    $phone1);
        $stmt->bindParam(":txt_Sex",       $sex);
        $stmt->bindParam(":txt_Mar_stat",  $marStat);
        $stmt->bindParam(":txt_Pan",       $pan);
        $stmt->bindParam(":txt_Catcode",   $catcode);
        $stmt->bindParam(":txt_Dsgcode",   $dsgcode);
        $stmt->bindParam(":txt_Cardno",    $cardno);
        $stmt->bindParam(":txt_Bank",      $bank);
        $stmt->bindParam(":txt_AccNo",     $accNo);
        $stmt->bindParam(":txt_Pf",        $pfacno);
        $stmt->bindParam(":txt_IncrRemark",$incrRemark);
        $stmt->bindParam(":txt_Relation",  $relation);
        $stmt->bindParam(":txt_Gname",     $gname);
        $stmt->bindParam(":txt_Edu",       $edu);
        $stmt->bindParam(":txt_Subject",   $subject);
        $stmt->bindParam(":txt_Email",     $email);
        $stmt->bindParam(":txt_Uid",       $uid);
        $stmt->bindParam(":txt_UanNo",     $uanNo);
        $stmt->bindParam(":txt_Exper",     $exper);
        $stmt->bindParam(":txt_shcode",    $shcode);
        $stmt->bindParam(":txt_Emp_no",    $empno);
 
        // Decimal bindings — explicitly typed so PDO sends NULL not ''
        $stmt->bindValue(":txt_Basic",    $basic,    $basic    === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_PayScale", $payScale, $payScale === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Incentive",$incentive,$incentive=== null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_BasicIncr",$basicIncr,$basicIncr=== null ? PDO::PARAM_NULL : PDO::PARAM_STR);
 
        // Date bindings — explicitly typed
        $stmt->bindValue(":txt_Dob", $dob, $dob === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Doj", $doj, $doj === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Dor", $dor, $dor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Doc", $doc, $doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":txt_Doi", $doi, $doi === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
 
        // BLOB binding for photo
        $stmt->bindParam(":txt_PHOTO", $photoData, PDO::PARAM_LOB);
 
        $stmt->execute();
 
        $msg  = $stmt->rowCount() > 0 ? "success" : "no update";
        $db   = null;
        $data = ["status" => "Ok", "msg" => $msg, "item" => $empno];
        $status = 201;
 
    } catch (Exception $e) {
        $data   = ["status" => "Error", "msg" => $e->getMessage(), "item" => $sql ?? null];
        $status = 200;
    }
 
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
});

		$group->delete('/{empno}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM empmast WHERE empno = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['empno']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Employee Master entity


	// Begin REST API for last emp no entity
	$app->group("$burl/getLastEmpNo", function (Group $group) {
		$group->get("", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT MAX(EMPNO) as last_emp_no FROM empmast');
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);

				// Extract the last_emp_no from the database results
				$lastEmpNo = $data['last_emp_no'];

				// Create a response JSON array
				$responseArray = ['last_emp_no' => $lastEmpNo];

				$status = 200;
			} catch (Exception $e) {
				$responseArray = ['errmsg' => $e->getMessage()];
				$status = 400;
			}

			// Return the response as JSON
			$response->getBody()->write(json_encode($responseArray));

			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
	});
	// End of REST API for Last emp no entity


	//Begin REST API for General allowance entity
	$app->group("$burl/allowance", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from gblall";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{CODE}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM gblall WHERE CODE = :CODE');
				$stmt->bindValue(':CODE', $args['CODE'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO gblall(DESCR, PRCAMTFLAG, ALLREDNFLAG, ALLOWANCE, ALLREDNCONTINUITY)
            VALUES(:txt_desc, :txt_flag, :txt_allow, :txt_value, :txt_stillvalid)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_flag", $users['flag']);
				$stmt->bindParam(":txt_allow", $users['allow']);
				$stmt->bindParam(":txt_value", $users['value']);
				$stmt->bindParam(":txt_stillvalid", $users['stillvalid']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->put('/{CODE}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE gblall SET DESCR=:txt_desc, PRCAMTFLAG=:txt_flag, ALLREDNFLAG=:txt_allow, ALLOWANCE=:txt_value, ALLREDNCONTINUITY=:txt_stillvalid WHERE CODE=:txt_code";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_code", $users['code']);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_flag", $users['flag']);
				$stmt->bindParam(":txt_allow", $users['allow']);
				$stmt->bindParam(":txt_value", $users['value']);
				$stmt->bindParam(":txt_stillvalid", $users['stillvalid']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{CODE}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM gblall WHERE CODE = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['CODE']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Global allowance entity

	//Begin REST API for individual allowance entity
	$app->group("$burl/indAllow", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from gblall";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{descr}/{CODE}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM indall WHERE EMPNO = :CODE and DESCR = :descr');
				$stmt->bindValue(':CODE', $args['CODE'], PDO::PARAM_INT);
				$stmt->bindValue(':descr', $args['descr'], PDO::PARAM_STR);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				if (!is_array($users)) {
					$rawBody = (string) $request->getBody();
					$jsonBody = json_decode($rawBody, true);
					$users = is_array($jsonBody) ? $jsonBody : [];
				}
				$sql = "INSERT INTO indall(EMPNO, DESCR, PRCAMTFLAG, ALLREDNFLAG, ALLOWANCE, ALLREDNCONTINUITY)
            VALUES(:txt_code, :txt_desc, :txt_flag, :txt_allow, :txt_value, :txt_stillvalid)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_code", $users['empname']);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_flag", $users['flag']);
				$stmt->bindParam(":txt_allow", $users['allow']);
				$stmt->bindParam(":txt_value", $users['value']);
				$stmt->bindParam(":txt_stillvalid", $users['stillvalid']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->put('/{descr}/{CODE}', function (Request $request, Response $response, $args) {
			try {

				$db = getconn();
				$users = $request->getParsedBody();
				if (!is_array($users)) {
					$rawBody = (string) $request->getBody();
					$jsonBody = json_decode($rawBody, true);
					$users = is_array($jsonBody) ? $jsonBody : [];
				}
			$sql = "UPDATE indall SET DESCR=:txt_desc, PRCAMTFLAG=:txt_flag, ALLREDNFLAG=:txt_allow, ALLOWANCE=:txt_value, ALLREDNCONTINUITY=:txt_stillvalid WHERE EMPNO=:txt_code AND DESCR=:desc";
				$stmt = $db->prepare($sql);
				$code = $users['code'] ?? $args['CODE'];
				$stmt->bindParam(":txt_code", $code);
				$stmt->bindParam(":desc", $args['descr']);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_flag", $users['flag']);
				$stmt->bindParam(":txt_allow", $users['allow']);
				$stmt->bindParam(":txt_value", $users['value']);
				$stmt->bindParam(":txt_stillvalid", $users['stillvalid']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{descr}/{CODE}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM indall WHERE EMPNO = :CODE AND DESCR = :descr";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['CODE']);
				$stmt->bindParam(":descr", $args['descr']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
	});
	//End of REST API for individual allowance entity

	//Begin REST API for degination entity
	$app->group("$burl/degination", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from dsgmast";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{DCODE}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM dsgmast WHERE DCODE = :CODE');
				$stmt->bindValue(':CODE', $args['DCODE'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO dsgmast(DESCR, NODAYS, DAYSPERTIME, CATGR, DISPORDER)
            VALUES(:txt_descr, :txt_nodays, :txt_dayspertime, :txt_catgr, :txt_disporder)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_descr", $users['descr']);
				$stmt->bindParam(":txt_nodays", $users['totalLeave']);
				$stmt->bindParam(":txt_dayspertime", $users['leave']);
				$stmt->bindParam(":txt_catgr", $users['catgr']);
				$stmt->bindParam(":txt_disporder", $users['disporder']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->put('/{DCODE}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE dsgmast SET DESCR=:txt_descr, NODAYS=:txt_nodays, DAYSPERTIME=:txt_dayspertime, CATGR=:txt_catgr, DISPORDER=:txt_disporder WHERE DCODE=:txt_dcode";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_dcode", $users['dcode']);
				$stmt->bindParam(":txt_descr", $users['descr']);
				$stmt->bindParam(":txt_nodays", $users['totalLeave']);
				$stmt->bindParam(":txt_dayspertime", $users['leave']);
				$stmt->bindParam(":txt_catgr", $users['catgr']);
				$stmt->bindParam(":txt_disporder", $users['disporder']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{DCODE}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM dsgmast WHERE DCODE = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['DCODE']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for degination entity

	//Begin REST API for company entity
	$app->group("$burl/company", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from compmast";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{comp_id}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT comp_id, comp_name, PfEstbCode, EsiEstbCode, Add1, pf, esic, bonus FROM compmast WHERE comp_id = :CODE');
				$stmt->bindValue(':CODE', $args['comp_id'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO compmast(comp_name, PfEstbCode, EsiEstbCode, Add1, pf, esic, bonus)
            VALUES(:txt_compName, :txt_pfCode, :txt_esiCode, :txt_add1, :txt_pf, :txt_esic, :txt_bonus)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_compName", $users['txt_compName']);
				$stmt->bindParam(":txt_pfCode", $users['txt_pfCode']);
				$stmt->bindParam(":txt_esiCode", $users['txt_esiCode']);
				$stmt->bindParam(":txt_add1", $users['txt_add1']);
				$stmt->bindParam(":txt_pf", $users['txt_pf']);
				$stmt->bindParam(":txt_esic", $users['txt_esic']);
				$stmt->bindParam(":txt_bonus", $users['txt_bonus']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{comp_id}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE compmast SET comp_name=:txt_compName, PfEstbCode=:txt_pfCode, EsiEstbCode=:txt_esiCode, Add1=:txt_add1, pf=:txt_pf, esic=:txt_esic, bonus=:txt_bonus WHERE comp_id=:txt_comp_id";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_comp_id", $users['txt_comp_id']);
				$stmt->bindParam(":txt_compName", $users['txt_compName']);
				$stmt->bindParam(":txt_pfCode", $users['txt_pfCode']);
				$stmt->bindParam(":txt_esiCode", $users['txt_esiCode']);
				$stmt->bindParam(":txt_add1", $users['txt_add1']);
				$stmt->bindParam(":txt_pf", $users['txt_pf']);
				$stmt->bindParam(":txt_esic", $users['txt_esic']);
				$stmt->bindParam(":txt_bonus", $users['txt_bonus']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{comp_id}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM compmast WHERE comp_id = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['comp_id']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for company entity

	//Begin REST API for Holiday Master entity
	$app->group("$burl/holidaymaster", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from holidaymaster";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{date}/{category}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM holidaymaster WHERE `HolidayDate`=:txt_date and Category= :txt_category');
				$stmt->bindParam(":txt_date", $args['date']);
				$stmt->bindParam(":txt_category", $args['category']);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 500;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO holidaymaster(HolidayDate, HolidayDescription, Category) VALUES (:txt_date, :txt_desc,:txt_category)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_date", $users['date']);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_category", $users['category']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{date}/{category}', function (Request $request, Response $response, $args) {
			try {

				// $body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE holidaymaster SET HolidayDate=:txt_date,
			HolidayDescription=:txt_desc,
			Category= :txt_category
			 WHERE `HolidayDate`=:txt_date1 and Category= :txt_category1";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_date", $users['date']);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_category", $users['category']);
				$stmt->bindParam(":txt_date1", $args['date']);
				$stmt->bindParam(":txt_category1", $args['category']);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users, "Date" => $args['date'], "Category" => $args['category']);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 500;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{date}/{category}', function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$sql = "DELETE FROM `holidaymaster` WHERE `HolidayDate`=:txt_date1 and Category= :txt_category1";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_date1", $args['date']);
				$stmt->bindParam(":txt_category1", $args['category']);

				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Holiday Master entity

	//Begin REST API for machine entity
	$app->group("$burl/machine", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from macdb";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{mac_id}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM macdb WHERE mac_id = :CODE');
				$stmt->bindValue(':CODE', $args['mac_id'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO macdb(mac_descr, ipaddr)
            VALUES(:txt_desc, :txt_ipaddr)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_ipaddr", $users['ipaddr']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{mac_id}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE macdb SET mac_descr=:txt_desc, ipaddr=:txt_ipaddr WHERE mac_id=:txt_mac_id";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_mac_id", $users['mac_id']);
				$stmt->bindParam(":txt_desc", $users['desc']);
				$stmt->bindParam(":txt_ipaddr", $users['ipaddr']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{mac_id}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM macdb WHERE mac_id = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['mac_id']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for machine entity

	//Begin REST API for leaveType entity
	$app->group("$burl/leavetype", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from leavemst";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{leaveCode}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM leavemst WHERE leaveCode = :CODE');
				$stmt->bindValue(':CODE', $args['leaveCode'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO leavemst(LeaveName, PaidLeave, Balance)
            VALUES(:txt_leaveName, :txt_paidLeave, :txt_balance)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_leaveName", $users['leaveName']);
				$stmt->bindParam(":txt_paidLeave", $users['paidLeave']);
				$stmt->bindParam(":txt_balance", $users['balance']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{LeaveCode}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE leavemst SET LeaveName=:txt_leaveCode, PaidLeave=:txt_paidLeave, Balance=:txt_balance WHERE LeaveCode=:txt_leaveCode";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_leaveCode", $users['leaveCode']);
				$stmt->bindParam(":txt_leaveName", $users['leaveName']);
				$stmt->bindParam(":txt_paidLeave", $users['paidLeave']);
				$stmt->bindParam(":txt_balance", $users['balance']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{leaveCode}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM leavemst WHERE leaveCode = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['leaveCode']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for leaveType entity

	//Begin REST API for Shift Master entity
	$app->group("$burl/shift", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from shiftmaster";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{ShiftCode}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM shiftmaster WHERE ShiftCode = :txt_ShiftCode');
				$stmt->bindValue(':txt_ShiftCode', $args['ShiftCode']);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO shiftmaster(ShiftCode, InTime, OutTime, Night, OTduringNight, LunchTimeFrom, LunchTimeTo)
            VALUES(:txt_ShiftCode, :txt_inTime, :txt_outTime, :txt_night, :txt_otNight, :txt_lunchT_from, :txt_lunchT_to)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_ShiftCode", $users['ShiftCode']);
				$stmt->bindParam(":txt_inTime", $users['inTime']);
				$stmt->bindParam(":txt_outTime", $users['outTime']);
				$stmt->bindParam(":txt_night", $users['night']);
				$stmt->bindParam(":txt_otNight", $users['otNight']);
				$stmt->bindParam(":txt_lunchT_from", $users['lunchT_from']);
				$stmt->bindParam(":txt_lunchT_to", $users['lunchT_to']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{ShiftCode}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE shiftmaster SET ShiftCode=:txt_ShiftCode, InTime=:txt_inTime, OutTime=:txt_outTime, Night=:txt_night, OTduringNight=:txt_otNight, LunchTimeFrom=:txt_lunchT_from, LunchTimeTo=:txt_lunchT_to  WHERE ShiftCode=:txt_ShiftCode";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_ShiftCode", $users['ShiftCode']);
				$stmt->bindParam(":txt_inTime", $users['inTime']);
				$stmt->bindParam(":txt_outTime", $users['outTime']);
				$stmt->bindParam(":txt_night", $users['night']);
				$stmt->bindParam(":txt_otNight", $users['otNight']);
				$stmt->bindParam(":txt_lunchT_from", $users['lunchT_from']);
				$stmt->bindParam(":txt_lunchT_to", $users['lunchT_to']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{ShiftCode}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM shiftmaster WHERE ShiftCode = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['ShiftCode']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Shift Master entity

	//Begin REST API for Bank Master entity
	$app->group("$burl/bankmaster", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from bankmast";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{bid}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM bankmast WHERE BID = :CODE');
				$stmt->bindValue(':CODE', $args['bid'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO bankmast(DESCR)
            VALUES(:txt_bankName)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				// $stmt->bindParam(":txt_bid", $users['bid']);
				$stmt->bindParam(":txt_bankName", $users['bankName']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{bid}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE bankmast SET DESCR=:txt_bankName WHERE BID=:txt_bid";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_bankName", $users['bankName']);
				$stmt->bindParam(":txt_bid", $users['bid']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{bid}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM bankmast WHERE BID = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['bid']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Bank Master entity

	//Begin REST API for Category entity
	$app->group("$burl/category", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from catmast";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{cat_code}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM catmast WHERE cat_code = :CODE');
				$stmt->bindValue(':CODE', $args['cat_code'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO catmast(DESCR)
            VALUES(:txt_descr)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_descr", $users['descr']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{cat_code}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE catmast SET DESCR=:txt_descr WHERE cat_code=:txt_cat_code";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_descr", $users['descr']);
				$stmt->bindParam(":txt_cat_code", $users['cat_code']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{cat_code}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM catmast WHERE cat_code = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['cat_code']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Category entity

	//Begin REST API for location entity
	$app->group("$burl/location", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from locationmaster";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{Loccode}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM locationmaster WHERE Loccode = :txt_loccode');
				$stmt->bindValue(':txt_loccode', $args['Loccode'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO locationmaster(Location)
            VALUES(:txt_location)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_location", $users['location']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{loccode}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE locationmaster SET Location=:txt_location WHERE loccode=:txt_loccode";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_location", $users['location']);
				$stmt->bindParam(":txt_loccode", $users['loccode']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{Loccode}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM locationmaster WHERE Loccode = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['Loccode']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Location entity


	//Begin REST API for User Manager entity
	$app->group("$burl/usertb", function (Group $group) {

		$group->get("/{UID}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM usertb WHERE UID = :txt_uid');
				$stmt->bindValue(':txt_uid', $args['UID'], PDO::PARAM_STR);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO usertb(UID, UNAME, PASSWD, role_id )
		VALUES(:txt_UID,:txt_UNAME,:txt_PASSWD, :txt_role_id)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_UID", $users['UID']);
				$stmt->bindParam(":txt_UNAME", $users['UNAME']);
				$stmt->bindParam(":txt_PASSWD", $users['PASSWD']);
				$stmt->bindParam(":txt_role_id", $users['userType']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{UID}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE usertb SET UNAME=:txt_UNAME, PASSWD=:txt_PASSWD, role_id=:txt_role_id
		WHERE UID=:txt_UID";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_UID", $args['UID']);
				$stmt->bindParam(":txt_UNAME", $users['UNAME']);
				$stmt->bindParam(":txt_PASSWD", $users['PASSWD']);
				$stmt->bindParam(":txt_role_id", $users['userType']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{UID}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM usertb WHERE UID = :UID";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":UID", $args['UID']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for User Manager entity



	//Begin REST API for Tansaction Leave entity
	$app->group("$burl/leavedet", function (Group $group) {

		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$userId = $_SESSION['user'];
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				if ($userId == 'Admin' || $userId == 'Super Admin') {
					$sql = "SELECT l.*, e.*
		FROM leavedet l
		JOIN empmast e on l.EMPNO=e.EMPNO
		ORDER BY l.fdate DESC";
				} else {
					$sql = "SELECT l.*, e.*
		FROM leavedet l
		JOIN empmast e on l.EMPNO=e.EMPNO AND l.EMPNO=$userId
		ORDER BY l.fdate DESC";
				}
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{EMPNO}", function (Request $request, Response $response, $args) {
    try {
        $db = getconn();

        $stmt = $db->prepare('
            SELECT e.EMPNO, e.NAME, e.DCODE, e.LWP, e.NLEAVE,e.ABS,e.ACCLEAVE,CATCODE,e.PRELEAVE,e.MEDICAL_LEAVE,e.LeaveGroup,e.el,e.oel,e.ocl,e.ospl,e.oml, l.* 
            FROM empmast e
            LEFT JOIN leavedet l ON e.EMPNO = l.EMPNO
            WHERE e.EMPNO = :txt_empno
            ORDER BY l.fdate DESC
            LIMIT 1
        ');
        $stmt->bindValue(':txt_empno', $args['EMPNO'], PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $status = 200;
    } catch (Exception $e) {
        $data = array("errmsg" => $e->getMessage());
        $status = 400;
    }

    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
});


		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$uploadedFiles = $request->getUploadedFiles();
				$fileContent = null;

				if (isset($uploadedFiles['txt_document'])) {
					$file = $uploadedFiles['txt_document'];

					if ($file->getError() === UPLOAD_ERR_OK) {
						$fileContent = file_get_contents($file->getStream()->getMetadata('uri'));
					} else {
						error_log("File upload error: " . $file->getError());
					}
				}

				$users = $request->getParsedBody();
				//error_log("Parsed Body: " . print_r($users, true));
				//error_log("Uploaded Files: " . print_r($uploadedFiles, true));

				$sql = "INSERT INTO leaveapply (EMPNO, DTE, DESCR, FDATE, TDATE, NOL, LTYPE, NOOFDAYS, DOCUMENT)
                VALUES (:txt_empno, :txt_date, :txt_desc, :txt_fromDate, :txt_toDate, :txt_nofDays, :txt_leave_type, :txt_nofDays, :txt_document)";

				$db = getconn();
				$stmt = $db->prepare($sql);

				$stmt->bindParam(":txt_empno", $users['txt_empno']);
				$stmt->bindParam(":txt_date", $users['txt_date']);
				$stmt->bindParam(":txt_desc", $users['txt_descr']);
				$stmt->bindParam(":txt_fromDate", $users['txt_fromDate']);
				$stmt->bindParam(":txt_toDate", $users['txt_toDate']);
				$stmt->bindParam(":txt_nofDays", $users['txt_nofDays']);
				$stmt->bindParam(":txt_leave_type", $users['txt_leave_type']);

				if ($fileContent !== null) {
					$stmt->bindParam(":txt_document", $fileContent, PDO::PARAM_LOB);
				} else {
					$stmt->bindValue(":txt_document", null, PDO::PARAM_NULL);
				}

				$stmt->execute();

				if ($stmt->rowCount() > 0) {
					$data = [
						"status" => "Ok",
						"msg" => "Inserted successfully",
						"item" => $users,
						"document_uploaded" => $fileContent ? "Yes" : "No"
					];
				} else {
					$errorInfo = $stmt->errorInfo();
					$msg = $errorInfo[2] ?? "Leave Application not applied";
					$data = [
						"status" => "Error",
						"msg" => $msg
					];
				}

				$db = null;


				$status = 201;
			} catch (Exception $e) {
				$data = [
					"status" => "Error",
					"msg" => $e->getMessage()
				];
				$status = 500;
			}

			$response->getBody()->write(json_encode($data));
			return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
		});


		$group->put('/{slno}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE leavedet SET DESCR=:txt_descr1, FDATE=:txt_fromDate1, TDATE=:txt_toDate1,  NOL=:txt_nofDays1,  LTYPE=:txt_leave_type1, NOOFDAYS=:txt_nofDays1  WHERE SLNO=:txt_slno1";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_slno1", $users['slno1']);
				$stmt->bindParam(":txt_descr1", $users['descr1']);
				$stmt->bindParam(":txt_fromDate1", $users['fromDate1']);
				$stmt->bindParam(":txt_toDate1", $users['toDate1']);
				$stmt->bindParam(":txt_nofDays1", $users['nofDays1']);
				$stmt->bindParam(":txt_leave_type1", $users['leave_type1']);
				$stmt->bindParam("::txt_nofDays1", $users['nofDays1']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{SLNO}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM leavedet WHERE SLNO = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['SLNO']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Tansaction Leave entity

	//Begin REST API for Tansaction Leave entity
	$app->group("$burl/leaveload", function (Group $group) {

		$group->get("/{SLNO}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT l.*, e.*
			FROM leavedet l
			JOIN empmast e ON l.EMPNO = e.EMPNO
			WHERE l.SLNO = :txt_empno
			');
				$stmt->bindValue(':txt_empno', $args['SLNO'], PDO::PARAM_INT);
				$stmt->execute();

				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
	});
	// END FOR leave load
	//Begin REST API for Tansaction Leave application
	$app->group("$burl/leaveapply", function (Group $group) {

		$group->get("/{EMPNO}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT l.slno, l.EMPNO, Name, l.DTE, l.DESCR, l.FDATE, l.TDATE, l.NOL, l.LWOP, l.HLEAVE, l.MED, l.LEVWOPAY, l.NOOFDAYS, l.LTYPE, l.STATUS, e.EMPNO
			FROM leaveapply l
			JOIN empmast e on l.EMPNO=e.EMPNO
			WHERE l.slno = :txt_empno');
				$stmt->bindValue(':txt_empno', $args['EMPNO'], PDO::PARAM_STR);
				$stmt->execute();

				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus($status);
		});

		$group->put('/{EMPNO}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE leaveapply SET DTE=:txt_date, DESCR=:txt_descr, FDATE=:txt_fdate, TDATE=:txt_fdate,
		        LTYPE=:txt_ltype, STATUS=:txt_status  WHERE slno=:txt_slno";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_date", $users['date']);
				$stmt->bindParam(":txt_fdate", $users['fdate']);
				$stmt->bindParam(":txt_tdate", $users['tdate']);
				$stmt->bindParam(":txt_descr", $users['descr']);
				$stmt->bindParam(":txt_ltype", $users['ltype']);
				//$stmt->bindParam(":txt_nod", $users['Address']);
				$stmt->bindParam(":txt_status", $users['status']);
				$stmt->bindParam(":txt_slno", $args['EMPNO']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus($status);
		});
	});
	// END FOR leave Application

	//Start of Datatable Route
	$app->group("$burl/attdata", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = true;

				$db = getconn();
				$sql = "SELECT atd.*, em.NAME
		FROM attnddet as atd
		LEFT JOIN empmast AS em ON atd.empno=em.EMPNO
		ORDER BY atd.dt DESC LIMIT 100";
				// if ($filter)
				// 	$sql.=" where $filter";
				// else $sql.=" limit 200";
				$result = $db->query($sql);
				$totalData = $db->query("select count(*) from ($sql) b")->fetchColumn();
				$totalFiltered = $totalData;
				$data = array(
					"recordsTotal" => intval($totalData),
					"recordsFiltered" => intval($totalFiltered),
					"data" => $result->fetchAll(PDO::FETCH_ASSOC)
				);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
	});
	//End of REST API for Tansaction loan entity

	//Begin REST API for Monthly Attendance loan entity
	$app->group("$burl/attdmonth", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "SELECT *
		FROM sheet_det";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{EMPNO}/{SHEET_ID}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT  sd.empno, e.Name, cl, sd.el, lwop, off_days, med_leave, hdays, attnd, adj, pay_mode, (cl+sd.el+lwop+med_leave+spleave) as absent, (cl+off_days+med_leave+ attnd+hdays+spleave) as dpaid,  wdays, othour, sd.sheet_id
			FROM sheet_det sd
			JOIN empmast e on sd.empno=e.EMPNO
			JOIN sheet s on sd.sheet_id=s.sheet_id
			WHERE e.EMPNO= :txt_empno AND sd.sheet_id = :txt_sheet_id');
				$stmt->bindValue(':txt_empno', $args['EMPNO']);
				$stmt->bindValue(':txt_sheet_id', $args['SHEET_ID']);
				$stmt->execute();

				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->put('/{EMPNO}/{SHEET_ID}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE sheet_det SET
		--  sheet_id=:txt_sheet_id,
		 empno=:txt_empno,
		 cl=:txt_cl,
		 el=:txt_el,
		 lwop=:txt_lwop,
		 off_days=:txt_off_days,
		 med_leave=:txt_med_leave,
		 hdays=:txt_hdays,
		 attnd=:txt_attnd,
		 pay_mode=:txt_pay_mode,
		 wdays=:txt_wdays
		--  arrear=:txt_arrear,
		--  iTax=:txt_iTax
		 WHERE empno=:txt_empno and sheet_id=:txt_sheetId";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_empno", $args['EMPNO']);
				$stmt->bindParam(":txt_sheetId", $args['SHEET_ID']);
				$stmt->bindParam(":txt_cl", $users['cl']);
				$stmt->bindParam(":txt_el", $users['el']);
				$stmt->bindParam(":txt_lwop", $users['lwop']);
				$stmt->bindParam(":txt_off_days", $users['off_days']);
				$stmt->bindParam(":txt_med_leave", $users['med_leave']);
				$stmt->bindParam(":txt_hdays", $users['hdays']);
				$stmt->bindParam(":txt_attnd", $users['attnd']);
			   // $stmt->bindParam(":txt_dpaid", $users['dpaid']);
				$stmt->bindParam(":txt_pay_mode", $users['pay_mode']);
				// $stmt->bindParam(":txt_absent", $users['absent']);
				$stmt->bindParam(":txt_wdays", $users['wdays']);
				// $stmt->bindParam(":txt_arrear", $users['splPay']);
				// $stmt->bindParam(":txt_iTax", $users['iTax']);
				$stmt->bindParam(":txt_sheet_id", $users['sheet_id']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{empno}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM sheet_det WHERE empno = :empno";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":empno", $args['empno']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Monthly Attendance loan entity

	//Begin REST API for Payroll Processing loan entity
	$app->group("$burl/payroll", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "SELECT trst.*, em.NAME
			FROM trnsmst as trst
			LEFT JOIN empmast AS em ON trst.EMPNO=em.EMPNO
			WHERE trst.EMPNO=:txt_empno";

				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{EMPNO}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT trst.*, em.NAME
			FROM trnsmst as trst
			LEFT JOIN empmast AS em ON trst.EMPNO=em.EMPNO
			WHERE trst.EMPNO=:txt_empno ');
				$stmt->bindValue(':txt_empno', $args['EMPNO']);
				$stmt->execute();

				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO sheet_det(empno, cl, el, lwop, off_days, med_leave, hdays, attnd, adj, pay_mode, absent, wdays, othour )
		VALUES(:txt_empno,:txt_cl,:txt_el, :txt_lwop, :txt_offdays, :txt_sickleave, :txt_hdays, :txt_dwork, :txt_dpaid, :txt_paymode, :txt_absent, :txt_wdays, :txt_ot)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_empno", $users['empno']);
				$stmt->bindParam(":txt_cl", $users['cl']);
				$stmt->bindParam(":txt_el", $users['el']);
				$stmt->bindParam(":txt_lwop", $users['lwop']);
				$stmt->bindParam(":txt_offdays", $users['offdays']);
				$stmt->bindParam(":txt_sickleave", $users['sickleave']);
				$stmt->bindParam(":txt_hdays", $users['hdays']);
				$stmt->bindParam(":txt_dwork", $users['dwork']);
				$stmt->bindParam(":txt_dpaid", $users['dpaid']);
				$stmt->bindParam(":txt_paymode", $users['paymode']);
				$stmt->bindParam(":txt_absent", $users['absent']);
				$stmt->bindParam(":txt_wdays", $users['wdays']);
				$stmt->bindParam(":txt_ot", $users['ot']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{EMPNO}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE sheet_det SET
		 empno=:txt_empno,
		 cl=:txt_cl,
		 el=:txt_el,
		 lwop=:txt_lwop,
		 off_days=:txt_offdays,
		 med_leave=:txt_sickleave,
		 hdays=:txt_hdays,
		 attnd=:txt_dwork,
		 adj=:txt_dpaid,
		 pay_mode=:txt_paymode,
		 wdays=:txt_wdays,
		 othour=:txt_ot
		 WHERE empno=:txt_empno";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_empno", $users['empno']);
				$stmt->bindParam(":txt_cl", $users['cl']);
				$stmt->bindParam(":txt_el", $users['el']);
				$stmt->bindParam(":txt_lwop", $users['lwop']);
				$stmt->bindParam(":txt_offdays", $users['offdays']);
				$stmt->bindParam(":txt_sickleave", $users['sickleave']);
				$stmt->bindParam(":txt_hdays", $users['hdays']);
				$stmt->bindParam(":txt_dwork", $users['dwork']);
				$stmt->bindParam(":txt_dpaid", $users['adj']);
				$stmt->bindParam(":txt_paymode", $users['paymode']);
				// $stmt->bindParam(":txt_absent", $users['absent']);
				$stmt->bindParam(":txt_wdays", $users['wdays']);
				$stmt->bindParam(":txt_ot", $users['ot']);

				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{empno}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM sheet_det WHERE empno = :empno";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":empno", $args['empno']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Payroll Processing loan entity

	//Begin REST API for Cat Shift Change entity
	$app->group("$burl/catshift", function (Group $group) {
		// $group->get('', function (Request $request, Response $response, $args) {
		// try {
		// 	$params = $request->getQueryParams();
		// 	$filter='';
		// 	foreach($params as $p=>$v) {
		// 		if ($v)
		// 			switch($p)
		// 				{
		// 				case 'id';
		// 					$filter.="$p='$v'";
		// 					break;
		// 				default;
		// 					if ($filter) $filter.=" and ";
		// 					$filter.="$p like '%$v%'";
		// 				break;
		// 			}
		// 	  }
		// 	$db=getconn();
		// 	$sql="select * from catshiftchange";
		// 	if ($filter)
		// 		$sql.=" where $filter";
		// 	else $sql.=" limit 200";
		// 	$result=$db->query($sql);
		// 	$data=$result->fetchAll(PDO::FETCH_ASSOC);
		// 	$status=200;
		// 	} catch(Exception $e) {
		// 		$data[]=array("errmsg"=>$e->getMessage());$status=400;
		// 	}
		// 	$payload = json_encode($data);
		// 	$response->getBody()->write($payload);
		// 	return $response
		// 			->withHeader('Content-Type', 'application/json')
		// 			->withStatus($status);
		// });
		$group->get("/{scdate}/{catcode}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM catshiftchange WHERE catcode=:txt_catcode AND scdate=:txt_scdate');
				$stmt->bindValue(':txt_catcode', $args['catcode']);
				$stmt->bindValue(':txt_scdate', $args['scdate']);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 500;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO catshiftchange(catcode, scdate, ShiftCode, scedate) VALUES (:txt_catcode,:txt_scdate,:txt_shiftcode,:txt_scedate)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_catcode", $users['cat']);
				$stmt->bindParam(":txt_scdate", $users['doc']);
				$stmt->bindParam(":txt_shiftcode", $users['sDesc']);
				$stmt->bindParam(":txt_scedate", $users['endDate']);

				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				//$data=array("item"=>$users);
				$data = array("status" => $status, "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{scdate}/{catcode}', function (Request $request, Response $response, $args) {
			try {

				// $body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE catshiftchange SET catcode=:txt_catcode, scdate=:txt_scdate,ShiftCode=:txt_shiftcode, scedate=:txt_scedate
		WHERE catcode=:txt_catcode1 AND scdate=:txt_scdate1";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_catcode", $users['cat']);
				$stmt->bindParam(":txt_scdate", $users['doc']);
				$stmt->bindParam(":txt_shiftcode", $users['sDesc']);
				$stmt->bindParam(":txt_scedate", $users['endDate']);
				$stmt->bindValue(':txt_catcode1', $args['catcode']);
				$stmt->bindValue(':txt_scdate1', $args['scdate']);

				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users, "Date" => $args['scdate'], "Category" => $args['catcode']);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql, "Scdate" => $args['scdate'], "Catcode" => $args['catcode']);
				$status = 500;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{scdate}/{catcode}', function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$sql = "DELETE FROM `catshiftchange` WHERE catcode=:txt_catcode AND scdate=:txt_scdate";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':txt_catcode', $args['catcode']);
				$stmt->bindValue(':txt_scdate', $args['scdate']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Cat Shift Change entity

	//Begin REST API for LeaveGroup entity
	$app->group("$burl/leaveGroup", function (Group $group) {
		$group->get("/{leaveGrpCode}/{leaveCode}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT lm.LeaveName, lg.CarryForward, Encash, OverwriteWO, OverwriteHL, CreditType, AnnQuota, AttendDays, LeaveCredit, MonthlyQuota, CreditMonthly, CreditYearly,LeaveGroupCode,lg.LeaveCode FROM leavegroup lg
			JOIN leavemst lm ON lg.LeaveCode=lm.LeaveCode
			WHERE lg.LeaveCode = :txt_leaveCode AND LeaveGroupCode = :txt_leaveGrpCode');
				$stmt->bindValue(':txt_leaveCode', $args['leaveCode']);
				$stmt->bindValue(':txt_leaveGrpCode', $args['leaveGrpCode']);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 500;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO `leavegroup`
		(`LeaveGroupCode`, `LeaveCode`, `CarryForward`, `Encash`, `OverwriteWO`, `OverwriteHL`, `CreditYearly`, `CreditMonthly`, `CreditType`, `AnnQuota`, `AttendDays`, `LeaveCredit`, `MonthlyQuota`) VALUES
		(:txt_LeaveGroupCode,:txt_LeaveCode,:txt_CarryForward,:txt_Encash,:txt_OverwriteWO,:txt_OverwriteHL,:txt_CreditYearly,:txt_CreditMonthly,:txt_CreditType,:txt_AnnQuota,:txt_AttendDays,:txt_LeaveCredit,:txt_MonthlyQuota)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_LeaveGroupCode", $users['leaveGrpCode']);
				$stmt->bindParam(":txt_LeaveCode", $users['leaveDesc']);
				$stmt->bindParam(":txt_CarryForward", $users['carryForw']);
				$stmt->bindParam(":txt_Encash", $users['encash']);
				$stmt->bindParam(":txt_OverwriteWO", $users['OvrWO']);
				$stmt->bindParam(":txt_OverwriteHL", $users['OvrHL']);
				$stmt->bindParam(":txt_CreditType", $users['credType']);
				$stmt->bindParam(":txt_AnnQuota", $users['annQuota']);
				$stmt->bindParam(":txt_AttendDays", $users['attndDays']);
				$stmt->bindParam(":txt_LeaveCredit", $users['leaveCred']);
				$stmt->bindParam(":txt_MonthlyQuota", $users['mnthQuota']);
				$stmt->bindParam(":txt_CreditMonthly", $users['credMnth']);
				$stmt->bindParam(":txt_CreditYearly", $users['credYr']);


				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				//$data=array("item"=>$users);
				$data = array("status" => $status, "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 500;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{leaveGrpCode}/{leaveCode}', function (Request $request, Response $response, $args) {
			try {

				// $body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE leavegroup SET
		 LeaveCode=:txt_LeaveCode,
		 CarryForward=:txt_CarryForward,
		 Encash=:txt_Encash,
		 OverwriteWO=:txt_OverwriteWO,
		 OverwriteHL=:txt_OverwriteHL,
		 CreditType=:txt_CreditType,
		 AnnQuota=:txt_AnnQuota,
		 AttendDays=:txt_AttendDays,
		 LeaveCredit=:txt_LeaveCredit,
		 MonthlyQuota=:txt_MonthlyQuota,
		 CreditMonthly=:txt_CreditMonthly,
		 CreditYearly=:txt_CreditYearly
		WHERE LeaveCode=:txt_leaveCode1 AND LeaveGroupCode=:txt_leaveGrpCode";

				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_LeaveCode", $users['leaveDesc']);
				$stmt->bindParam(":txt_CarryForward", $users['carryForw']);
				$stmt->bindParam(":txt_Encash", $users['encash']);
				$stmt->bindParam(":txt_OverwriteWO", $users['OvrWO']);
				$stmt->bindParam(":txt_OverwriteHL", $users['OvrHL']);
				$stmt->bindParam(":txt_CreditType", $users['credType']);
				$stmt->bindParam(":txt_AnnQuota", $users['annQuota']);
				$stmt->bindParam(":txt_AttendDays", $users['attndDays']);
				$stmt->bindParam(":txt_LeaveCredit", $users['leaveCred']);
				$stmt->bindParam(":txt_MonthlyQuota", $users['mnthQuota']);
				$stmt->bindParam(":txt_CreditMonthly", $users['credMnth']);
				$stmt->bindParam(":txt_CreditYearly", $users['credYr']);

				$stmt->bindValue(':txt_leaveCode1', $args['leaveCode']);
				$stmt->bindValue(':txt_leaveGrpCode', $args['leaveGrpCode']);

				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 500;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{leaveGrpCode}/{leaveCode}', function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$sql = "DELETE FROM `leavegroup` WHERE LeaveCode=:txt_leaveCode1 AND LeaveGroupCode=:txt_leaveGrpCode";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':txt_leaveCode1', $args['leaveCode']);
				$stmt->bindValue(':txt_leaveGrpCode', $args['leaveGrpCode']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for LeaveGroup entity
//Begin REST API for emp Shift Change entity
	$app->group("$burl/empshift", function (Group $group) {
		$group->get("/{Empid}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT sc.*, c.NAME FROM shiftchange sc
			JOIN empmast c on sc.Empid=c.EMPNO WHERE EmpId=:txt_empid');
				$stmt->bindValue(':txt_empid', $args['Empid']);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 500;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO shiftchange(EmpId, scdate, ShiftCode) VALUES (:txt_empid,:txt_scdate,:txt_shiftcode)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_empid", $users['empname']);
				$stmt->bindParam(":txt_scdate", $users['cngedte']);
				$stmt->bindParam(":txt_shiftcode", $users['shift']);

				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				//$data=array("item"=>$users);
				$data = array("status" => $status, "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{empid}', function (Request $request, Response $response, $args) {
			try {

				// $body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE shiftchange SET ShiftCode=:txt_shiftcode, scdate=:txt_scdate
		WHERE EmpId=:txt_empid";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_shiftcode", $users['shift']);
				$stmt->bindParam(":txt_scdate", $users['cngedte']);
				$stmt->bindValue(':txt_empid', $args['empid']);
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					$msg = "success";
					$status = 200;
				} else {
					$msg = "no update";
					$status = 201;
				}
				$db = null;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql, "Scdate" => $args['scdate'], "Catcode" => $args['catcode']);
				$status = 500;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{Empid}', function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$sql = "DELETE FROM `shiftchange` WHERE Empid=:txt_empid";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':txt_empid', $args['Empid']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for empShift Change entity
//Begin REST API for Advance Payment entity
	$app->group("$burl/advpayment", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from bankmast";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{code}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				$stmt = $db->prepare('SELECT * FROM loanmast WHERE LNO = :CODE');
				$stmt->bindValue(':CODE', $args['code'], PDO::PARAM_INT);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				$users = $request->getParsedBody();
				$sql = "INSERT INTO loanmast(EMPNO, DTE, DESCR, AMT, RATE, NOINST, FLAG)
            VALUES(:txt_empno, :txt_date, :txt_descr, :txt_amount, :txt_intRate, :txt_nofinst, :txt_descr)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_empno", $users['empno']);
				$stmt->bindParam(":txt_date", $users['date']);
				$stmt->bindParam(":txt_amount", $users['amount']);
				$stmt->bindParam(":txt_descr", $users['descr']);
				$stmt->bindParam(":txt_intRate", $users['intRate']);
				$stmt->bindParam(":txt_nofinst", $users['nofinst']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				//$data=array("item"=>$users);
				$data = array("status" => "Ok", "msg" => "Inserted sucessfully", "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->put('/{code}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE loanmast SET dte=:txt_date, amt=:txt_amount, descr=:txt_descr, rate=:txt_intRate, noinst=:txt_nofinst, FLAG=:txt_cleared WHERE lno=:txt_lno";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_lno", $users['lno']);
				$stmt->bindParam(":txt_date", $users['date']);
				$stmt->bindParam(":txt_amount", $users['amount']);
				$stmt->bindParam(":txt_descr", $users['descr']);
				//$stmt->bindParam(":txt_empno", $users['empno']);
				$stmt->bindParam(":txt_intRate", $users['intRate']);
				$stmt->bindParam(":txt_nofinst", $users['nofinst']);
				$stmt->bindParam(":txt_cleared", $users['cleared']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{code}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM loanmast WHERE LNO = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['code']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Adv Payment entity
     //Begin REST API for Daily Report entity
	$app->group("$burl/dlyreport", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				$sql = "select * from bankmast";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
$group->get("/{empno}/{dte}", function (Request $request, Response $response, $args) {
    try {
        $db = getconn();
        $stmt = $db->prepare('SELECT d.*, dm.designation, e.NAME, e.PHOTO FROM dlyreport d JOIN empmast e ON d.empno = e.EMPNO left join designationmaster dm on e.DCODE =dm.desgcode WHERE d.empno = :CODE AND d.dte = :dte');
        $stmt->bindValue(':CODE', $args['empno'], PDO::PARAM_INT);
        $stmt->bindValue(':dte', $args['dte'], PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as &$row) {
            if (isset($row['PHOTO'])) {
                $row['PHOTO'] = base64_encode($row['PHOTO']);
            }
        }

        $status = 200;
    } catch (Exception $e) {
        $data = array("errmsg" => $e->getMessage());
        $status = 400;
    }

    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
});


	$group->post('', function (Request $request, Response $response, $args) {
    try {
        $users = $request->getParsedBody();

        $db = getconn();

   // Assuming $users contains the data
$tomorrowList = $users['nextList'];
$completeList = $users['completeList'];

// Prepare the SQL statement outside the loop for better performance
$sql = "INSERT INTO dlyreport(empno, dte, title, tdylst, cmplst, progress, tym, descr) VALUES(:txt_empno, :txt_dte, :txt_title, :tomorrow, :complete, :progress, :time, :descr)";
$stmt = $db->prepare($sql);
$stmt->bindParam(":txt_empno", $users['empno']);
$stmt->bindParam(":txt_dte", $users['dte']);
$stmt->bindParam(":txt_title", $users['title']);

foreach ($completeList['inputs'] as $index => $completeItem) {
    $progress = isset($completeList['progress'][$index]) ? $completeList['progress'][$index] : '';
    $time = isset($completeList['time'][$index]) ? $completeList['time'][$index] : '';
    $descr = isset($completeList['descr'][$index]) ? $completeList['descr'][$index] : '';

    $stmt->bindValue(":tomorrow", null);
    $stmt->bindParam(":complete", $completeItem);
    $stmt->bindParam(":progress", $progress);
    $stmt->bindParam(":time", $time);
    $stmt->bindParam(":descr", $descr);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $msg = "success";
    } else {
        $msg = "no update";
    }
}
foreach ($tomorrowList['inputs'] as $index => $tomorrowItem) {
    $progress = isset($tomorrowList['progress'][$index]) ? $tomorrowList['progress'][$index] : '';
    $time = isset($tomorrowList['time'][$index]) ? $tomorrowList['time'][$index] : '';
    $descr = isset($tomorrowList['descr'][$index]) ? $tomorrowList['descr'][$index] : '';
    if (!empty($tomorrowItem)) {
        $stmt->bindParam(":tomorrow", $tomorrowItem);
        $stmt->bindValue(":complete", null);
        $stmt->bindParam(":progress", $progress);
        $stmt->bindParam(":time", $time);
        $stmt->bindParam(":descr", $descr);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $msg = "success";
        } else {
            $msg = "no update";
        }
    }
}



        $db = null;
        $status = 201;
        $data = array("status" => "Ok", "msg" => "Inserted successfully", "item" => $users);
    } catch (Exception $e) {
        $data = array("status" => "Error", "msg" => $e->getMessage());
        $status = 200;
    }

    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
});

		$group->put('/{empno}', function (Request $request, Response $response, $args) {
			try {

				$body = $request->getBody();
				$db = getconn();
				$users = $request->getParsedBody();
				$sql = "UPDATE tradv SET dt=:txt_dt, amt=:txt_amt, descr=:txt_descr WHERE empno=:txt_empno";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_empno", $users['empno']);
				$stmt->bindParam(":txt_dt", $users['date']);
				$stmt->bindParam(":txt_amt", $users['amt']);
				$stmt->bindParam(":txt_descr", $users['desc']);
				$stmt->execute();
				if ($stmt->rowCount() > 0)
					$msg = "success";
				else
					$msg = "no update";
				$db = null;
				$status = 201;
				$data = null;
				$data = array("status" => "Ok", "msg" => $msg, "item" => $users);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage(), "item" => $sql);
				$status = 200;
			}
			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->delete('/{empno}', function (Request $request, Response $response, $args) {
			try {
				$sql = "DELETE FROM tradv WHERE empno = :CODE";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":CODE", $args['empno']);
				$stmt->execute();

				$rowCount = $stmt->rowCount();
				$msg = ($rowCount > 0) ? "Deleted successfully" : "No deletion";

				$db = null;
				$status = 201;
				$data = array("status" => "Ok", "msg" => $msg);
			} catch (Exception $e) {
				$data = array("status" => "Error", "msg" => $e->getMessage());
				$status = 200;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

	});
	//End of REST API for Daily Report entity
	//Begin REST API for test Master entity
	$app->group("$burl/testmaster", function (Group $group) {
		$group->get('', function (Request $request, Response $response, $args) {
			try {
				$params = $request->getQueryParams();
				$filter = '';
				foreach ($params as $p => $v) {
					if ($v)
						switch ($p) {
							case 'id';
								$filter .= "$p='$v'";
								break;
							default;
								if ($filter)
									$filter .= " and ";
								$filter .= "$p like '%$v%'";
								break;
						}
				}
				$db = getconn();
				// $sql="select * from Empmast ";
				$sql = "SELECT em.*, bm.DESCR, cat.descr,cmp.comp_name,loc.loc,dsgc.designation,lg.LeaveGroupDesc
			FROM empmast AS em
			LEFT JOIN bankmast AS bm ON em.BID = bm.BID
			LEFT JOIN category as cat on em.CATCODE = cat.catcode
			LEFT JOIN compmast as cmp ON em.comp_id = cmp.comp_id
			LEFT JOIN locmast as loc ON em.loc_id = loc.loc_id
			LEFT JOIN designationmaster as dsgc ON em.DSGCODE = dsgc.desgcode
			LEFT JOIN leavegroup as lg ON em.LeaveGroup = lg.LeaveGroupCode Group BY em.EMPNO
			";
				if ($filter)
					$sql .= " where $filter";
				else
					$sql .= " limit 200";
				$result = $db->query($sql);
				$data = $result->fetchAll(PDO::FETCH_ASSOC);
				foreach ($data as &$row) {
					if (array_key_exists('PHOTO', $row) && $row['PHOTO'] !== null) {
						$photoData = base64_encode($row['PHOTO']);
						$row['PHOTO'] = $photoData;
					}
				}
				$status = 200;
			} catch (Exception $e) {
				$data[] = array("errmsg" => $e->getMessage());
				$status = 400;
			}
			$payload = json_encode($data);
			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});
		$group->get("/{bid}", function (Request $request, Response $response, $args) {
			try {
				$db = getconn();
				// $stmt = $db->prepare('SELECT empmast.*
				// FROM empmast
				// WHERE EMPNO = :txt_empno');
				$stmt = $db->prepare("SELECT em.*, bm.DESCR, cat.descr,cmp.comp_name,loc.loc,dsgc.designation, lg.LeaveGroupDesc
				FROM empmast AS em
				LEFT JOIN bankmast AS bm ON em.BID = bm.BID
				LEFT JOIN category as cat on em.CATCODE = cat.catcode
				LEFT JOIN compmast as cmp ON em.comp_id = cmp.comp_id
				LEFT JOIN locmast as loc ON em.loc_id = loc.loc_id
				LEFT JOIN designationmaster as dsgc ON em.DSGCODE = dsgc.desgcode
				LEFT JOIN leavegroup as lg ON em.LeaveGroup = lg.LeaveGroupCode
				WHERE em.EMPNO = :txt_empno");
				$stmt->bindValue(':txt_empno', $args['EMPCODE'], PDO::PARAM_INT);
				$stmt->execute();

				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($data['PHOTO'] !== null) {
					$photoData = base64_encode($data['PHOTO']);
					$data['PHOTO'] = $photoData;
				}
				$status = 200;
			} catch (Exception $e) {
				$data = array("errmsg" => $e->getMessage());
				$status = 400;
			}

			$response->getBody()->write(json_encode($data));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($status);
		});

		$group->post('', function (Request $request, Response $response, $args) {
			try {
				// Retrieve data from the request body
				$data = $request->getParsedBody();

				// Decode the base64 image data
				// $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['txt_PHOTO']));
				$photoData = $data['txt_PHOTO'];

				// Check if data needs to be decoded
				if (strpos($photoData, 'data:image') === 0) {
					// Data is in base64 format, decode it
					$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
				}

				// Prepare and execute the SQL query
				$sql = "INSERT INTO test (testname, code, photo)
        VALUES (:txt_testName, :txt_code, :txt_PHOTO)";
				$db = getconn();
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":txt_testName", $data['txt_testName']);
				$stmt->bindParam(":txt_code", $data['txt_code']);
				$stmt->bindParam(":txt_PHOTO", $photoData, PDO::PARAM_LOB);
				$stmt->execute();


				// Check for errors
				$errorInfo = $stmt->errorInfo();
				if ($errorInfo[0] !== '00000') {
					// Handle the error
					$response->getBody()->write(json_encode(['status' => 'Error', 'message' => 'Failed to insert data']));
					return $response->withStatus(500);
				}

				// If no errors, return a success response
				$response->getBody()->write(json_encode(['status' => 'Ok']));
				return $response;
			} catch (Exception $e) {
				// Handle exceptions
				// $response = $response->withJson(['status' => 'Error', 'message' => $e->getMessage()], 500);
				return $response;
			}
		});


	});
	//End of REST API for test Master entity

};
