<?php
$selectedMonth = isset($_POST['month']) ? intval($_POST['month']) : 0;
$selectedYear = isset($_POST['year']) ? intval($_POST['year']) : 0;
$regenerate = isset($_POST['regenerate']) ? intval($_POST['regenerate']) : 0;

if (empty($selectedMonth) || empty($selectedYear)) {
    $response = array('success' => false, 'error' => 'Month and year are required parameters');
} else {
    try {
        require_once 'includes/dbconn.php';

        // Start transaction for safe database updates
        $db->beginTransaction();

        // 1. If regenerate is requested, perform safety checks and clean up existing data
        if ($regenerate === 1) {
            // Find the existing sheet_id
            $find_sheet_sql = "SELECT sheet_id FROM sheet WHERE mnth = :month AND yr = :year LIMIT 1";
            $find_stmt = $db->prepare($find_sheet_sql);
            $find_stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
            $find_stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
            $find_stmt->execute();
            $sheet = $find_stmt->fetch(PDO::FETCH_ASSOC);

            if ($sheet) {
                $sheet_id = intval($sheet['sheet_id']);

                // Security check: Verify payroll transactions are not processed
                $check_tx_sql = "SELECT COUNT(*) AS tx_count FROM trnsmst WHERE sheet_id = :sheet_id";
                $tx_stmt = $db->prepare($check_tx_sql);
                $tx_stmt->bindParam(':sheet_id', $sheet_id, PDO::PARAM_INT);
                $tx_stmt->execute();
                $tx_result = $tx_stmt->fetch(PDO::FETCH_ASSOC);
                $tx_count = intval($tx_result['tx_count']);

                if ($tx_count > 0) {
                    throw new Exception("Cannot regenerate sheet because payroll transactions have already been processed for this month.");
                }

                /*
                // FUTURE IMPLEMENTATION: Restoring or adjusting accrued leaves (accleave) in empmast on regeneration
                // -----------------------------------------------------------------------------------------------
                // When a sheet is regenerated, you may want to restore the leaves deducted from the employees'
                // accrued balances during the month before resetting their attendance details to standard days.
                //
                // For example:
                // 1. Fetch any deducted CL/EL leaves for the month from the current sheet details:
                //    $leave_query = "SELECT empno, SUM(cl) as total_cl, SUM(el) as total_el FROM sheet_det WHERE sheet_id = :sheet_id GROUP BY empno";
                //    $leave_stmt = $db->prepare($leave_query);
                //    $leave_stmt->bindParam(':sheet_id', $sheet_id, PDO::PARAM_INT);
                //    $leave_stmt->execute();
                //    $leaves_to_restore = $leave_stmt->fetchAll(PDO::FETCH_ASSOC);
                //
                // 2. Loop through and credit back the leaves in empmast:
                //    $restore_stmt = $db->prepare("UPDATE empmast SET accleave = accleave + :cl_to_restore WHERE empno = :empno");
                //    foreach ($leaves_to_restore as $row) {
                //        if (isset($row['total_cl']) && $row['total_cl'] > 0) {
                //            $restore_stmt->bindParam(':cl_to_restore', $row['total_cl']);
                //            $restore_stmt->bindParam(':empno', $row['empno'], PDO::PARAM_INT);
                //            $restore_stmt->execute();
                //        }
                //    }
                // -----------------------------------------------------------------------------------------------
                */

                // Delete details and main sheet entry
                $delete_det_sql = "DELETE FROM sheet_det WHERE sheet_id = :sheet_id";
                $del_det_stmt = $db->prepare($delete_det_sql);
                $del_det_stmt->bindParam(':sheet_id', $sheet_id, PDO::PARAM_INT);
                $del_det_stmt->execute();

                $delete_sheet_sql = "DELETE FROM sheet WHERE sheet_id = :sheet_id";
                $del_sheet_stmt = $db->prepare($delete_sheet_sql);
                $del_sheet_stmt->bindParam(':sheet_id', $sheet_id, PDO::PARAM_INT);
                $del_sheet_stmt->execute();
            }
        }

        // 2. Call the stored procedure sp_gensheet(month, year) to generate the sheet structure
        $procedure_sql = "CALL sp_gensheet(:month, :year)";
        $proc_stmt = $db->prepare($procedure_sql);
        $proc_stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        $proc_stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        $proc_stmt->execute();

        // Commit transaction
        $db->commit();

        $response = array(
            'success' => true,
            'message' => $regenerate === 1 ? 'Sheet successfully regenerated' : 'Sheet successfully created'
        );
    } catch (Exception $e) {
        // Rollback on any failure
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        $response = array(
            'success' => false,
            'error' => $e->getMessage()
        );
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
