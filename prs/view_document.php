<?php
session_start();
if ((!isset($_SESSION['user']))) {
    header('refresh: 1;url=login.php');
    die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll - Document</title>
</head>
<?php
require_once 'includes/dbconn.php';

if (isset($_GET['slno'])) {
    $slno = $_GET['slno'];

    try {
        $stmt = $db->prepare("SELECT DOCUMENT FROM leaveapply WHERE slno = :slno");
        $stmt->bindParam(":slno", $slno, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['DOCUMENT'])) {
            $document = $row['DOCUMENT'];

            error_log("Document Length: " . strlen($document));

            $pdfMagicNumber = substr($document, 0, 4);
            if ($pdfMagicNumber !== '%PDF') {
                error_log("Invalid PDF magic number: " . bin2hex($pdfMagicNumber));
                echo "Invalid PDF file.";
                exit;
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="document.pdf"');
            echo $document;
            exit;
        } else {
            echo "Document not found.";
            error_log("No document found for slno: $slno");
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        error_log("Database Error: " . $e->getMessage());
    }
} else {
    echo "No slno parameter provided.";
    error_log("No slno parameter provided.");
}
