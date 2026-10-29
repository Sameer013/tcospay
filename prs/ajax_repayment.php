<?php
/*
 * Repayment schedule of one loan: recorded installments followed by the projected ones.
 */
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(array('data' => array()));
    exit;
}

try {
    require_once 'includes/dbconn.php';
    require_once 'includes/loan_calc.php';

    $stmt = $db->prepare("SELECT l.*, lt.INT_HEAD FROM loanmast l LEFT JOIN loantype lt ON l.LTYPE = lt.CODE WHERE l.LNO = :lno");
    $stmt->execute(array(':lno' => isset($_GET['lno']) ? (int) $_GET['lno'] : 0));
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);
    $rows = array();
    if ($loan) {
        $stmt = $db->prepare("SELECT * FROM loandet WHERE LNO = :lno ORDER BY SLNO");
        $stmt->execute(array(':lno' => $loan['LNO']));
        $rows = loanSchedule(loanNormalize($loan, array('INT_HEAD' => $loan['INT_HEAD'])), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    echo json_encode(array('data' => $rows));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('data' => array(), 'errmsg' => $e->getMessage()));
}
