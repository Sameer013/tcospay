<?php
ini_set('display_errors', 0);
session_start();
$userId = $_SESSION['user'];
$requestData = $_REQUEST;
// DataTables column index => sortable column
$columns = array(
    0 => 'l.LNO',
    1 => 'e.NAME',
    2 => 'lt.NAME',
    3 => 'l.DTE',
    4 => 'l.AMT',
    5 => 'l.RATE',
    6 => 'l.METHOD',
    7 => 'l.NOINST',
    8 => 'monthly_premium',
    9 => 'balance',
    10 => 'l.FLAG',
);
$data = array();
try {
    require_once 'includes/dbconn.php';
    require_once 'includes/loan_calc.php';
    // Every loan, cleared ones included (their status says so)
    $sql = "SELECT l.*, e.NAME, lt.NAME AS TYPENAME,
            COALESCE(NULLIF(l.PREMIUM, 0), l.AMT / NULLIF(l.NOINST, 0)) AS monthly_premium,
            l.AMT - COALESCE((SELECT SUM(COALESCE(ld.PRIN, ld.INSTAMT)) FROM loandet ld WHERE ld.LNO = l.LNO), 0) AS balance
        FROM loanmast l
        LEFT JOIN empmast e ON l.empno = e.empno
        LEFT JOIN loantype lt ON l.LTYPE = lt.CODE
        WHERE 1 = 1";
    $params = array();
    if ($userId != 'Admin' && $userId != 'Super Admin') {
        $sql .= " AND l.empno = :empno";
        $params[':empno'] = $userId;
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM ($sql) b");
    $stmt->execute($params);
    $totalData = $stmt->fetchColumn();
    $totalFiltered = $totalData;

    if (!empty($requestData['search']['value'])) {
        $sql .= " AND (e.NAME LIKE :search OR lt.NAME LIKE :search2)";
        $params[':search'] = $params[':search2'] = '%' . $requestData['search']['value'] . '%';
        $stmt = $db->prepare("SELECT COUNT(*) FROM ($sql) b");
        $stmt->execute($params);
        $totalFiltered = $stmt->fetchColumn();
    }

    $orderCol = isset($requestData['order'][0]['column'], $columns[$requestData['order'][0]['column']]) ? $columns[$requestData['order'][0]['column']] : 'l.LNO';
    $orderDir = (isset($requestData['order'][0]['dir']) && strtolower($requestData['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';
    $sql .= " ORDER BY $orderCol $orderDir LIMIT " . (int) $requestData['start'] . ", " . (int) $requestData['length'];

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    $methods = loanMethods();
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
        $object->ltype     = $row['TYPENAME'] ?: $row['LTYPE'];
        $object->dte     = $row['DTE'];
        $object->descr     = $row['DESCR'];
        $object->amt     = $row['AMT'];
        $object->rate    = $row['RATE'];
        $object->method    = isset($methods[$row['METHOD']]) ? $methods[$row['METHOD']] : $row['METHOD'];
        $object->noinst    = $row['NOINST'];
        $object->premium    = number_format((float) $row['monthly_premium'], 2, '.', '');
        $object->premiumSet    = (float) $row['PREMIUM'] > 0;
        $object->balance    = number_format(max((float) $row['balance'], 0), 2, '.', '');
        $object->status    = $row['FLAG'];
        $object->action     = $action;

        array_push($objArr, $object);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
$json_data = array(
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $objArr   // total data array
);
echo json_encode($json_data);
