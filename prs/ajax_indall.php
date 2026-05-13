<?php
header('Content-Type: application/json');

$requestData = $_REQUEST;
$columns = [
    0 => 'DESCR',
    1 => 'PRCAMTFLAG',
    2 => 'ALLREDNFLAG',
    3 => 'ALLOWANCE',
    4 => 'ALLREDNCONTINUITY',
];

$draw = isset($requestData['draw']) ? (int) $requestData['draw'] : 0;
$objArr = [];
$totalData = 0;
$totalFiltered = 0;

try {
    require_once 'includes/dbconn.php';

    $empnumber = $_GET['empno'] ?? '';
    $orderColumnIndex = isset($requestData['order'][0]['column'])
        ? (int) $requestData['order'][0]['column']
        : 0;
    $orderColumn = $columns[$orderColumnIndex] ?? 'DESCR';
    $orderDir = strtolower($requestData['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
    $start = isset($requestData['start']) ? max(0, (int) $requestData['start']) : 0;
    $length = isset($requestData['length']) ? (int) $requestData['length'] : 10;
    $length = $length > 0 ? $length : 10;

    $baseSql = "
        SELECT ga.CODE, ga.DESCR, ga.PRCAMTFLAG, ga.ALLREDNFLAG, ga.ALLOWANCE, ga.ALLREDNCONTINUITY
        FROM gblall ga
        WHERE ga.ALLREDNCONTINUITY IN ('Y', '1', 1)

        UNION

        SELECT ia.CODE, ia.DESCR, ia.PRCAMTFLAG, ia.ALLREDNFLAG, ia.ALLOWANCE, ia.ALLREDNCONTINUITY
        FROM indall ia
        WHERE ia.ALLREDNCONTINUITY = 'Y' AND ia.EMPNO = :empno
    ";

    $countSql = "SELECT COUNT(*) FROM ($baseSql) allrows";
    $countStmt = $db->prepare($countSql);
    $countStmt->bindValue(':empno', $empnumber, PDO::PARAM_STR);
    $countStmt->execute();
    $totalData = (int) $countStmt->fetchColumn();
    $totalFiltered = $totalData;

    $sql = "$baseSql ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':empno', $empnumber, PDO::PARAM_STR);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as $row) {
        $object = new stdClass();
        $object->Desc = $row['DESCR'];
        $object->Flag = $row['PRCAMTFLAG'];
        $object->Allorded = $row['ALLREDNFLAG'];
        $object->Value = $row['ALLOWANCE'];
        $object->Stillvalid = ($row['ALLREDNCONTINUITY'] == 'Y') ? 'True' : $row['ALLREDNCONTINUITY'];
        $objArr[] = $object;
    }

    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalFiltered,
        "data" => $objArr,
    ]);
} catch (Throwable $e) {
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e->getMessage(),
    ]);
}
?>
