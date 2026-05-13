<?php
header('Content-Type: application/json');

$requestData = $_REQUEST;
$columns = array(
    0 => 'i.EMPNO',
    1 => 'e.NAME',
    2 => 'i.DESCR',
    3 => 'i.ALLREDNFLAG',
    4 => 'i.ALLOWANCE',
    5 => 'i.PRCAMTFLAG',
    6 => 'i.ALLREDNCONTINUITY'
);

$draw = isset($requestData['draw']) ? intval($requestData['draw']) : 0;
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;
$length = isset($requestData['length']) ? intval($requestData['length']) : 10;
$orderColumnIndex = isset($requestData['order'][0]['column']) ? intval($requestData['order'][0]['column']) : 0;
$orderDir = isset($requestData['order'][0]['dir']) && strtolower($requestData['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
$searchValue = trim($requestData['search']['value'] ?? '');
$empno = trim($_GET['empno'] ?? '');

$data = array();
$totalData = 0;
$totalFiltered = 0;

try {
    require_once 'includes/dbconn.php';

    $baseSql = " FROM indall i
        JOIN empmast e ON i.EMPNO = e.EMPNO
        WHERE i.ALLOWANCE IS NOT NULL";

    $bindings = array();

    if ($empno !== '') {
        $baseSql .= " AND i.EMPNO = :empno";
        $bindings[':empno'] = $empno;
    } else {
        $baseSql .= " AND 1 = 0";
    }

    $countStmt = $db->prepare("SELECT COUNT(*)" . $baseSql);
    foreach ($bindings as $key => $value) {
        $countStmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalData = intval($countStmt->fetchColumn());
    $totalFiltered = $totalData;

    $searchSql = $baseSql;
    if ($searchValue !== '') {
        $searchSql .= " AND (i.DESCR LIKE :search OR e.NAME LIKE :search OR i.EMPNO LIKE :search)";
    }

    $filteredStmt = $db->prepare("SELECT COUNT(*)" . $searchSql);
    foreach ($bindings as $key => $value) {
        $filteredStmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    if ($searchValue !== '') {
        $filteredStmt->bindValue(':search', '%' . $searchValue . '%', PDO::PARAM_STR);
    }
    $filteredStmt->execute();
    $totalFiltered = intval($filteredStmt->fetchColumn());

    $orderColumn = $columns[$orderColumnIndex] ?? 'i.EMPNO';
    $sql = "SELECT i.*, e.NAME" . $searchSql . " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $db->prepare($sql);
    foreach ($bindings as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    if ($searchValue !== '') {
        $stmt->bindValue(':search', '%' . $searchValue . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $objArr = array();
    foreach ($rows as $row) {
        $object = new stdClass();
        $code = $row['EMPNO'];
        $descr = $row['DESCR'];
        $safeCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
        $safeDescr = htmlspecialchars($descr, ENT_QUOTES, 'UTF-8');
        $action = "<div class='flex justify-left items-center'>
            <a class='flex items-center mr-5' href='javascript:;' onclick=\"load_data('$safeCode','$safeDescr')\"
            data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"check-square\" data-lucide=\"check-square\" class=\"lucide lucide-check-square w-4 h-4 mr-1\"><polyline points=\"9 11 12 14 22 4\"></polyline><path d=\"M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11\"></path></svg></a>
            <a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data('$safeCode','$safeDescr')\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"trash-2\" data-lucide=\"trash-2\" class=\"lucide lucide-trash-2 w-4 h-4 mr-1\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg></a></div>";

        $object->code = $row['EMPNO'];
        $object->name = $row['NAME'];
        $object->descr = $row['DESCR'];
        $object->prcamtflag = $row['PRCAMTFLAG'] === '%' ? 'Yes' : 'No';
        $object->allrednflag = $row['ALLREDNFLAG'] === 'a' ? 'Allowance' : ($row['ALLREDNFLAG'] === 'd' ? 'Deduction' : '');
        $object->allowance = $row['ALLOWANCE'];
        $object->allredncountinuity = $row['ALLREDNCONTINUITY'] === 'Y' ? 'True' : 'False';
        $object->action = $action;
        $objArr[] = $object;
    }

    echo json_encode(array(
        "draw" => $draw,
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalFiltered,
        "data" => $objArr
    ));
} catch (Throwable $e) {
    echo json_encode(array(
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => array(),
        "error" => $e->getMessage()
    ));
}
?>
