<?php
require_once 'includes/dbconn.php';
$type = $_GET['type'];

$options = [];

switch ($type) {
    case 'category':
        $stmt = $db->prepare("SELECT DCODE, DESCR FROM `dsgmast` ");
        break;
    case 'individual':
        $stmt = $db->prepare("SELECT EMPNO,NAME from empmast");
        break;
    default:
        break;
}

if (isset($stmt)) {
    $stmt->execute();
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($options);
?>