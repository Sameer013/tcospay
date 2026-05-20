<?php
header('Content-Type: application/json');

$requestData = $_REQUEST;
$columns = array(
    0 => 'id',
    1 => 'code',
    2 => 'descr',
    3 => 'action'
);

$draw = isset($requestData['draw']) ? intval($requestData['draw']) : 0;
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;
$length = isset($requestData['length']) ? intval($requestData['length']) : 10;
$orderColumnIndex = isset($requestData['order'][0]['column']) ? intval($requestData['order'][0]['column']) : 0;
$orderDir = isset($requestData['order'][0]['dir']) && strtolower($requestData['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
$searchValue = trim($requestData['search']['value'] ?? '');

try {
    require_once 'includes/dbconn.php';

    $baseSql = " FROM indallmast";
    $searchSql = "";
    if ($searchValue !== '') {
        $searchSql = " WHERE code LIKE :search OR descr LIKE :search";
    }

    $totalData = intval($db->query("SELECT COUNT(*) FROM indallmast")->fetchColumn());

    $countStmt = $db->prepare("SELECT COUNT(*)" . $baseSql . $searchSql);
    if ($searchValue !== '') {
        $countStmt->bindValue(':search', '%' . $searchValue . '%', PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalFiltered = intval($countStmt->fetchColumn());

    $orderColumn = $columns[$orderColumnIndex] ?? 'id';
    if ($orderColumn === 'action') {
        $orderColumn = 'id';
    }

    $sql = "SELECT id, code, descr" . $baseSql . $searchSql . " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $db->prepare($sql);
    if ($searchValue !== '') {
        $stmt->bindValue(':search', '%' . $searchValue . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();

    $objArr = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id = (int) $row['id'];
        $object = new stdClass();
        $object->id = $row['id'];
        $object->code = htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8');
        $object->descr = htmlspecialchars($row['descr'], ENT_QUOTES, 'UTF-8');
        $object->action = "<div class='flex justify-left items-center'>
            <a class='flex items-center mr-5' href='javascript:;' onclick=\"load_data($id)\"
            data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">Edit</a>
            <a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data($id)\">Delete</a>
        </div>";
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
