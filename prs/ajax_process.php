<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';



try {
    include('includes/dbconn.php');

    $sheet_fun = $db->prepare("SELECT get_sheetid($selectedMonth, $selectedYear) as sheet_id");
    $sheet_fun->execute();
    $sheet_id = $sheet_fun->fetch()['sheet_id'];
    // Check if the selected month and year are available in the sheet table
    $sql_sheet_check = "SELECT COUNT(*) AS count FROM sheet WHERE mnth = :selectedMonth AND yr = :selectedYear";
    $stmt_sheet_check = $db->prepare($sql_sheet_check);
    $stmt_sheet_check->bindParam(':selectedMonth', $selectedMonth);
    $stmt_sheet_check->bindParam(':selectedYear', $selectedYear);
    $stmt_sheet_check->execute();
    $sheet_result = $stmt_sheet_check->fetch(PDO::FETCH_ASSOC);

    $response = array();

    if ($sheet_result['count'] > 0) {
        $sql_check = "SELECT COUNT(*) AS count FROM trnsmst WHERE sheet_id = :sheet_id";
        $stmt_check = $db->prepare($sql_check);
        $stmt_check->bindParam(':sheet_id', $sheet_id);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $response['status'] = 'DataExists';
        } else {
            $response['status'] = 'NoData';
        }
    } else {
        $response['status'] = 'SheetNotAvailable';
    }

} catch (PDOException $e) {
    $errorMessage = $e->getMessage();
    print "Error!: " . $errorMessage . "<br/>";
    die();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
