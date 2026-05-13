<?php
$requestData = $_REQUEST;
$data = array();

try {
    require_once 'includes/dbconn.php';

    $fromDate = new DateTime($_POST['fromDate']);
    $toDate = new DateTime($_POST['toDate']);

    $daysDiff = $toDate->diff($fromDate)->days;

    $currentDate = clone $fromDate;
    for ($i = 0; $i <= $daysDiff; $i++) {
        $formattedDate = $currentDate->format('Y-m-d');

        $stmt = $db->prepare("
            SELECT COUNT(*) AS excludeCount
            FROM holidaymaster
            WHERE HolidayDate = :formattedDate
        ");
        $stmt->bindParam(':formattedDate', $formattedDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentDate->format('N') != 7 && $result['excludeCount'] == 0) {
  
            $data[] = $formattedDate;
        }

        $currentDate->modify('+1 day');
    }

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

// Add the calculated number of days to the JSON response
$json_data = array(
    "numberOfDays" => count($data), // Use the count of processed days
    "processedDates" => $data,      // Include the processed dates in the response
);

echo json_encode($json_data);
?>
