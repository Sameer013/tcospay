<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

if (empty($selectedMonth) || empty($selectedYear)) {
    $response = array('error' => 'Month and year are required parameters');
} else {
    try {
        include('includes/dbconn.php');
         $checkQuery = "SELECT get_sheetid(:selectedMonth, :selectedYear) AS sheet_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':selectedMonth', $selectedMonth);
        $checkStmt->bindParam(':selectedYear', $selectedYear);
        $checkStmt->execute();

        $response = array('success' => true, 'message' => 'Successfully checked');

    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
        $response = array('error' => 'Database error: ' . $errorMessage);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
