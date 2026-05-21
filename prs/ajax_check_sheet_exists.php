<?php
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : 0;
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : 0;

if (empty($selectedMonth) || empty($selectedYear)) {
    $response = array('error' => 'Month and year are required parameters');
} else {
    try {
        require_once 'includes/dbconn.php';

        // 1. Check if the sheet exists in the sheet table
        $check_sheet_sql = "SELECT sheet_id FROM sheet WHERE mnth = :month AND yr = :year LIMIT 1";
        $stmt = $db->prepare($check_sheet_sql);
        $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        $stmt->execute();
        $sheet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sheet) {
            $sheet_id = intval($sheet['sheet_id']);

            // 2. Check if payroll transactions have already been processed for this sheet
            $check_tx_sql = "SELECT COUNT(*) AS tx_count FROM trnsmst WHERE sheet_id = :sheet_id";
            $tx_stmt = $db->prepare($check_tx_sql);
            $tx_stmt->bindParam(':sheet_id', $sheet_id, PDO::PARAM_INT);
            $tx_stmt->execute();
            $tx_result = $tx_stmt->fetch(PDO::FETCH_ASSOC);
            $has_transactions = (intval($tx_result['tx_count']) > 0);

            $response = array(
                'exists' => true,
                'sheet_id' => $sheet_id,
                'has_transactions' => $has_transactions
            );
        } else {
            $response = array(
                'exists' => false
            );
        }
    } catch (PDOException $e) {
        $response = array('error' => 'Database error: ' . $e->getMessage());
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
