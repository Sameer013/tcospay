<?php
session_start();
if ((!isset($_SESSION['user']))) {
    header('refresh: 1;url=login.php');
    die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
}
?>


<?php
include('includes/dbconn.php');
$stmt = $db->prepare("SELECT EMPNO,NAME from empmast");
$stmt->execute();
$empnames = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="light">
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <link href="dist/images/logo.svg" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Enigma admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Enigma Admin Template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="LEFT4CODE">
    <title>Payroll - Leave</title>
    <!-- BEGIN: CSS Assets-->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/css/app.css" />
    <link rel="stylesheet" href="dist/css/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <!-- END: CSS Assets-->


</head>


<style>
    @media (max-width: 768px) {
        .grid-cols-12 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    .dataTables_length select {
        width: 60px;
    }

    .dataTables_length select {
        width: 60px;
    }

    .align-right {
        float: right;
    }

    .wd-200 {
        width: 240px;
    }

    .badge-active {
        color: green;
        background: url("dist/images/greendot.png") no-repeat center / 25px;
        text-align: center;
        line-height: 25px;
    }
</style>


<!-- END: Head -->

<body class="py-0">
    <!-- BEGIN: Mobile Menu -->
    <?php include 'mob.php' ?>
    <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
        $amenu = "transaction";
        $page = "leave";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Transaction";
            $currentPage = "Leave";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Leave
                </h2>
            </div>
            <button class="btn btn-primary w-20 shadow-md mr-2 mt-5 mb-2 rounded-full"
                onclick="add_new()" data-tw-toggle="modal"
                data-tw-target="#header-footer-modal-preview-view">Apply
            </button>
            <?php
            if ($_SESSION['user'] == 'Admin') {
            ?>
                <div class="align-right">
                    <select name="empname" id="empname" class="wd-200 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full tom-select">
                        <option>--Select Employee--</option>
                        <?php foreach ($empnames as $empname) { ?>
                            <option value="<?= $empname['EMPNO'] ?>"><?= $empname['EMPNO'] . ' - ' . $empname['NAME'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php
            }
            ?>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12">
                    <!-- <button class="btn btn-primary shadow-md mr-2 rounded-full"
				onclick="add_new()" data-tw-toggle="modal"
				data-tw-target="#header-footer-modal-preview-view">Add</button> -->
                    <div class="intro-y box mt-5">
                        <div id="boxed-tab" class="p-5">
                            <div class="preview">
                                <ul class="nav nav-boxed-tabs" role="tablist">
                                    <li id="leave_application" class="nav-item flex-1" role="presentation">
                                        <button class="nav-link w-full py-2 active" data-tw-toggle="pill"
                                            data-tw-target="#leave_application" type="button" role="tab"
                                            aria-controls="leave_application" aria-selected="true"> Leave Application </button>
                                    </li>
                                    <li id="attendance_record" class="nav-item flex-1" role="presentation">
                                        <button class="nav-link w-full py-2" data-tw-toggle="pill"
                                            data-tw-target="#attendance_record" type="button" role="tab"
                                            aria-controls="attendance_record" aria-selected="false">Attendance Record </button>
                                    </li>
                                    <li id="leave_history" class="nav-item flex-1" role="presentation">
                                        <button class="nav-link w-full py-2" data-tw-toggle="pill"
                                            data-tw-target="#leave_history" type="button" role="tab"
                                            aria-controls="leave_history" aria-selected="false">Leave History </button>
                                    </li>
                                </ul>

                                <div class="tab-content mt-5">
                                    <div id="leave_application" class="tab-pane leading-relaxed active" role="tabpanel"
                                        aria-labelledby="leave_application">
                                        <div id="responsive-table" class="w-full">
                                    <div class="preview">
                                        <div class="card-header">
                                        <div class="container overflow-x-auto">
                                            <form id="form-wizard1" class="mt-2 p-4 sm:p-8 text-left w-full">
                                            <fieldset>
                                                <div class="form-card">
                                                <div class="container">
                                                    <!-- Row 1 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 text-dark">
                                                    <label for="txt_slno" class="sm:col-span-2 flex items-center">SL No:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_slno" name="txt_slno" class="p-2 text-dark w-full border rounded" placeholder="Enter SL No" readonly>
                                                    </div>

                                                    <label for="txt_empno" class="sm:col-span-2 flex items-center">Emp No:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_empno" name="txt_empno" class="p-2 text-dark w-full border rounded" placeholder="Enter Employee No" readonly>
                                                    </div>
                                                    </div>

                                                    <!-- Row 2 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-4 text-dark">
                                                    <label for="txt_empName" class="sm:col-span-2 flex items-center font-semibold">Employee Name:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_empName" name="txt_empName" class="p-2 text-dark w-full border rounded font-semibold" placeholder="Enter Name" readonly>
                                                    </div>

                                                    <label for="txt_date" class="sm:col-span-2 flex items-center">Date:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_date" name="txt_date" class="p-2 text-dark w-full border rounded" placeholder="Enter Date" readonly>
                                                    </div>
                                                    </div>

                                                    <!-- Row 3 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-4 text-dark">
                                                    <label for="txt_description" class="sm:col-span-2 flex items-center">Description:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_description" name="txt_description" class="p-2 text-dark w-full border rounded" placeholder="Enter Description" readonly>
                                                    </div>
                                                    </div>

                                                    <!-- Row 4 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-4 text-dark">
                                                    <label for="txt_from" class="sm:col-span-2 flex items-center">From:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_from" name="txt_from" class="p-2 text-dark w-full border rounded" placeholder="Enter From Date">
                                                    </div>

                                                    <label for="txt_to" class="sm:col-span-2 flex items-center">To:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_to" name="txt_to" class="p-2 text-dark w-full border rounded" placeholder="Enter To Date">
                                                    </div>
                                                    </div>

                                                    <!-- Row 5 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-4 text-dark items-center">
                                                    <label for="txt_halfday" class="sm:col-span-2 flex items-center">Half Day:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="checkbox" id="txt_halfday" name="txt_halfday" class="mt-2">
                                                    </div>

                                                    <label for="txt_nofdays" class="sm:col-span-2 flex items-center">No. of Days:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_nofdays" name="txt_nofdays" class="p-2 text-dark w-full border rounded" placeholder="Enter No. of Days" readonly>
                                                    </div>
                                                    </div>

                                                    <!-- Row 6 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-4 text-dark">
                                                    <label for="txt_leave_wp" class="sm:col-span-2 flex items-center">Leave W/O Pay:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_leave_wp" name="txt_leave_wp" class="p-2 text-dark w-full border rounded" placeholder="Leave W/O Pay" readonly>
                                                    </div>

                                                    <label for="txt_leave_type" class="sm:col-span-2 flex items-center">Leave Type:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_leave_type" name="txt_leave_type" class="p-2 text-dark w-full border rounded" placeholder="Leave Type" readonly>
                                                    </div>
                                                    </div>

                                                    <!-- Row 7 -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-4 text-dark">
                                                    <label for="txt_sanctioned" class="sm:col-span-2 flex items-center">Sanctioned:</label>
                                                    <div class="sm:col-span-4">
                                                        <input type="text" id="txt_sanctioned" name="txt_sanctioned" class="p-2 text-dark w-full border rounded" placeholder="Sanctioned?" readonly>
                                                    </div>
                                                    </div>

                                                    <!-- Leave Balance -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mt-6 text-dark">
                                                    <label class="sm:col-span-2 flex items-center font-semibold">Leave Balance:</label>

                                                    <label for="txt_cl" class="sm:col-span-1 flex items-center">CL:</label>
                                                    <div class="sm:col-span-1">
                                                        <input type="text" id="txt_cl" name="txt_cl" class="p-2 text-dark w-full border rounded" placeholder="CL" readonly>
                                                    </div>

                                                    <label for="txt_el" class="sm:col-span-1 flex items-center">EL:</label>
                                                    <div class="sm:col-span-1">
                                                        <input type="text" id="txt_el" name="txt_el" class="p-2 text-dark w-full border rounded" placeholder="EL" readonly>
                                                    </div>

                                                    <label for="txt_ml" class="sm:col-span-1 flex items-center">ML:</label>
                                                    <div class="sm:col-span-1">
                                                        <input type="text" id="txt_ml" name="txt_ml" class="p-2 text-dark w-full border rounded" placeholder="ML" readonly>
                                                    </div>
                                                    </div>

                                                </div>
                                                </div>
                                            </fieldset>
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                    </div>

                                    <div id="attendance_record" class="tab-pane leading-relaxed" role="tabpanel"
                                        aria-labelledby="attendance_record">
                                        <div class="" id="responsive-table">
                                            <div class="preview">
                                                <div class="card-header d-flex justify-content-between">

                                                    <div class="overflow-x-auto center container">
                                                        <table id="attendanceRecord" class="table table-bordered table-striped " style="width:100%" cellpadding="7px">
                                                            <thead>
                                                                <tr>
                                                                    <th width="8%">Date/Time</th>
                                                                    <th width="5%">Mode</th>
                                                                    <th width="5%">Shift</th>
                                                                    <th width="5%">CMNT</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="leave_history" class="tab-pane leading-relaxed" role="tabpanel"
                                        aria-labelledby="leave_history">
                                        <div class="" id="responsive-table">
                                            <div class="preview">
                                                <div class="card-header d-flex justify-content-between">

                                                    <div class="overflow-x-auto center container">
                                                        <table id="leaveHistory" class="table table-bordered table-striped " style="width:100%" cellpadding="7px">
                                                            <thead>
                                                                <tr>
                                                                    <th width="5%">SLNO</th>
                                                                    <th width="20%">From</th>
                                                                    <th width="10%">To</th>
                                                                    <th width="20%">Description</th>
                                                                    <th width="20%">Days</th>
                                                                    <th width="20%">Action</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <?php
            if ($_SESSION['user'] == 'Admin') {
            ?>
                <center>
                    <div class="form-group mt-6">
                        <button type="button" class="prevBtn btn mx-4 btn-outline-primary rounded-full w-20" value="Submit">Prev</button>
                        <!-- <button type="submit" class="btn btn-primary mx-4 rounded-full w-20">New</button>
                                <button type="submit" class="btn btn-primary mx-4 rounded-full w-20">Save</button> -->
                        <button type="button" class="nextBtn btn mx-4 btn-outline-primary rounded-full w-20" value="Submit">Next</button>
                    </div>
                </center>
            <?php
            }
            ?>
        </div>
    </div>
    <!-- END: Content -->

    <!-- BEGIN: View Modal -->
    <div class="intro-y box mt-5 hidden">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
            <h2 class="font-medium text-base mr-auto">
                Header & Footer Modal
            </h2>
        </div>
        <div id="header-footer-modal" class="p-5">
            <div class="preview">
                <!-- BEGIN: Modal Toggle -->
                <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview" class="btn btn-primary">Show Modal</a> </div>
                <!-- END: Modal Toggle -->
                <!-- BEGIN: Modal Content -->
                <div id="header-footer-modal-preview-view" class="modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- BEGIN: Modal Header -->
                            <div class="modal-header">
                                <h2 class="font-bold text-base mr-auto">
                                    Apply Leave
                                </h2>
                            </div>
                            <hr class="border-black">
                            <!-- END: Modal Header -->

                            <!-- BEGIN: Modal Body -->
                            <form id="frm_user" name="frm_user" action="" method="post">
                                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                    <input id="txt_slno" name="txt_slno" type="hidden" class="form-control " placeholder="SL No" readonly>

                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_empno" class="form-label">Emp No</label>
                                        <!-- <input id="txt_empno" name="txt_empno" type="text" class="form-control " placeholder="Employee No"> -->
                                        <?php
                                        if ($_SESSION['user'] == 'Admin') {
                                        ?>
                                            <select name="txt_empno" id="txt_empno" class="form-control form-input w-full rounded border-gray-200 ">
                                                <option value="" selected disabled>--Select Emp No--</option>
                                                <?php foreach ($empnames as $empname) { ?>
                                                    <option value="<?= $empname['EMPNO'] ?>"><?= $empname['EMPNO'] . ' - ' . $empname['NAME'] ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php
                                        } else {
                                        ?>
                                            <input id="txt_empno" name="txt_empno" type="text" class="form-control" placeholder="" value="<?= $_SESSION['user'] ?>" Readonly>
                                        <?php
                                        }

                                        ?>

                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_date" class="form-label">Date</label>
                                        <input id="txt_date" name="txt_date" type="date" class="form-control " value="<?php echo date('Y-m-d'); ?>" placeholder="Date">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_descr" class="form-label">Reason</label>
                                        <input id="txt_descr" name="txt_descr" type="text" class="form-control " placeholder="Reason" maxlength="100">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_fromDate" class="form-label">From</label>
                                        <input id="txt_fromDate" name="txt_fromDate" type="date" class="form-control " placeholder="From Date">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_halfday" class="form-label">Half Day</label><br>
                                        <input id="txt_halfday" name="txt_halfday" type="checkbox">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_toDate" class="form-label">To</label>
                                        <input id="txt_toDate" name="txt_toDate" type="date" class="form-control " placeholder="To Date">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_nofDays" class="form-label">No of Days</label>
                                        <input id="txt_nofDays" name="txt_nofDays" type="text" class="form-control " placeholder="No of Days" Readonly>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_leave_type" class="form-label">Leave Type</label>
                                        <!-- <input id="txt_leave_type" name="txt_leave_type" type="text" class="form-control " placeholder="Leave Type"> -->
                                        <select name="txt_leave_type" id="txt_leave_type" class="form-control form-input w-full rounded border-gray-200">
                                            <option  value="" selected disabled>--Select Leave--</option>
                                            <?php
                                            $query = $db->query("SELECT LeaveName FROM leavemst");
                                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                $LeaveName = $row['LeaveName'];
                                                echo "<option value='$LeaveName'>$LeaveName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_document" class="form-label">Document</label>
                                        <input id="txt_document" name="txt_document" type="file" accept=".pdf, .jpg, .jpeg, .png, .doc, .docx" class="form-control " placeholder="Document">
                                    </div>
                                </div>
                                <!-- END: Modal Body -->
                            </form>
                            <!-- BEGIN: Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                <button id="btn_save" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full">Save</button>

                            </div>

                            <!-- END: Modal Footer -->
                        </div>
                    </div>
                </div>
                <!-- END: Modal Content -->
            </div>

        </div>
        <!-- Edit leave -->
        <div id="header-footer-modal" class="p-5">
            <div class="preview">
                <!-- BEGIN: Modal Toggle -->
                <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview" class="btn btn-primary">Show Modal</a> </div>
                <!-- END: Modal Toggle -->
                <!-- BEGIN: Modal Content -->
                <div id="header-footer-modal-preview-leave" class="modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- BEGIN: Modal Header -->
                            <div class="modal-header">
                                <h2 class="font-bold text-base mr-auto">
                                    Edit Leave Details
                                </h2>
                            </div>
                            <hr class="border-black">
                            <!-- END: Modal Header -->

                            <!-- BEGIN: Modal Body -->
                            <form id="frm_edit_user" name="frm_user" action="" method="post">
                                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                    <input id="txt_slno1" name="txt_slno1" type="hidden" class="form-control " placeholder="SL No" readonly>

                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_descr" class="form-label">Reason</label>
                                        <input id="txt_descr1" name="txt_descr1" type="text" class="form-control " placeholder="Reason" maxlength="100">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_fromDate" class="form-label">From</label>
                                        <input id="txt_fromDate1" name="txt_fromDate1" type="date" class="form-control " placeholder="From Date">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_halfday" class="form-label">Half Day</label><br>
                                        <input id="txt_halfday1" name="txt_halfday1" type="checkbox">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_toDate" class="form-label">To</label>
                                        <input id="txt_toDate1" name="txt_toDate1" type="date" class="form-control " placeholder="To Date">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_nofDays" class="form-label">No of Days</label>
                                        <input id="txt_nofDays1" name="txt_nofDays1" type="text" class="form-control " placeholder="No of Days" Readonly>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_leave_type" class="form-label">Leave Type</label>
                                        <!-- <input id="txt_leave_type" name="txt_leave_type" type="text" class="form-control " placeholder="Leave Type"> -->
                                        <select name="txt_leave_type1" id="txt_leave_type1" class="form-control">
                                            <option value="" selected disabled>--Select Shift--</option>
                                            <?php
                                            $query = $db->query("SELECT LeaveName FROM leavemst");
                                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                $LeaveName = $row['LeaveName'];
                                                echo "<option value='$LeaveName'>$LeaveName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- END: Modal Body -->
                            </form>
                            <!-- BEGIN: Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                <button id="btn_update" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full">Update</button>
                            </div>

                            <!-- END: Modal Footer -->
                        </div>
                    </div>
                </div>
                <!-- END: Modal Content -->
            </div>

        </div>
    </div>
    <!-- END: View Modal -->

    <!-- END: JS Assets-->
    <script src="dist/js/app.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="dist/js/sweetalert2.min.js"></script>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>
<script>
    var currentIndex = 0;
    var dtable1;
    $(document).ready(function() {
        load_data_tleave(currentIndex, getParameterByName('empno'));
        $(".nextBtn").click(next_data);
        $(".prevBtn").click(prev_data);
        $('#txt_fromDate, #txt_toDate').on('input', function() {
            updateNoOfDays();
        });
    });

    function updateNoOfDays() {
        var fromDate = document.getElementById('txt_fromDate').value;
        var toDate = document.getElementById('txt_toDate').value;

        var formData = new FormData();
        formData.append('fromDate', fromDate);
        formData.append('toDate', toDate);

        $.ajax({
            url: 'ajax_nod.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var jsonResponse = JSON.parse(response);
                var numberOfDays = jsonResponse.numberOfDays;

                document.getElementById('txt_nofDays').value = numberOfDays;
            },
            error: function(error) {
                console.error("Error updating number of days:", error);
            }
        });
    }

    function load_data_tleave(index, empno) {
        if (empno) {
            $.ajax({
                url: '../prsApi/leavedet/' + empno,
                method: "GET",
                success: function(data) {
                    if (data) {
                        const res = data;
                        $("#txt_slno").val(res.slno);
                        $("#txt_empno").val(res.EMPNO);
                        $("#txt_empName").val(res.NAME);
                        $("#txt_date").val(res.DTE);
                        $("#txt_description").val(res.DESCR);
                        $("#txt_from").val(res.FDATE);
                        $("#txt_to").val(res.TDATE);
                        $("#txt_halfday").val(res.HLEAVE);
                        $("#txt_nofdays").val(res.NOOFDAYS);
                        $("#txt_sanctioned").val(res.NOL);
                        $("#txt_leave_wp").val(res.LWOP);
                        $("#txt_leave_type").val(res.LTYPE);

                        loadLeaveDataTable(empno);
                        attendanceRecordData(empno);
                        load_leave(empno);

                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("Error loading data: " + errorThrown);
                }
            });
        } else {
            $.ajax({
                url: '../prsApi/leavedet',
                method: "GET",
                success: function(data) {
                    if (index >= 0 && index < data.length) {
                        const res = data[index];
                        $("#txt_slno").val(res.slno);
                        $("#txt_empno").val(res.EMPNO);
                        $("#txt_empName").val(res.NAME);
                        $("#txt_date").val(res.DTE);
                        $("#txt_description").val(res.DESCR);
                        $("#txt_from").val(res.FDATE);
                        $("#txt_to").val(res.TDATE);
                        $("#txt_halfday").val(res.HLEAVE);
                        $("#txt_nofdays").val(res.NOOFDAYS);
                        $("#txt_sanctioned").val(res.NOL);
                        $("#txt_leave_wp").val(res.LWOP);
                        $("#txt_leave_type").val(res.LTYPE);

                        loadLeaveDataTable(res.EMPNO);
                        attendanceRecordData(res.EMPNO);
                        load_leave(res.EMPNO);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("Error loading data: " + errorThrown);
                }
            });
        }
    }

    function convertFormToJSON(form) {
        const array = $(form).serializeArray();
        const json = {};
        $.each(array, function() {
            key = this.name;
            key = key.substring(key.indexOf("_") + 1);
            json[key] = this.value || "";
        });
        return json;
    }

    function load_leave(EMPNO) {
        $.ajax({
            url: '../prsApi/empmast/' + EMPNO,
            method: "GET",
            success: function(data) {
                // console.log(data);
                $("#txt_cl").val(data.ACCLEAVE);
                $("#txt_el").val(data.el);
                $("#txt_ml").val(data.MEDICAL_LEAVE);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error("Error loading data: " + errorThrown);
            }
        });
    }


    function attendanceRecordData(empno) {
        if (empno) {

            if ($.fn.DataTable.isDataTable('#attendanceRecord')) {
                $('#attendanceRecord').DataTable().destroy();
            }

            var dtable = $('#attendanceRecord').DataTable({
                buttons: ['copy', 'excel', 'pdf'],
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_attnHis.php?empno=" + empno,
                "columns": [{
                        data: "dtime"
                    },
                    {
                        data: "minout"
                    },
                    {
                        data: "shift"
                    },
                    {
                        data: "cmnt"
                    },
                ],
                "order": [1, "desc"],
                "searching": true,
            });
        }
    }

    function loadLeaveDataTable(empno) {
        if (empno) {
            if ($.fn.DataTable.isDataTable('#leaveHistory')) {
                $('#leaveHistory').DataTable().destroy();
            }
            dtable1 = $('#leaveHistory').DataTable({
                buttons: ['copy', 'excel', 'pdf'],
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_leave.php?empno=" + empno,
                "columns": [{
                        "data": "SLNO"
                    },
                    {
                        "data": "FDATE"
                    },
                    {
                        "data": "TDATE"
                    },
                    {
                        "data": "DESCR"
                    },
                    {
                        "data": "NOOFDAYS"
                    },
                    {
                        "data": "action"
                    }
                ],
                "order": [2, "desc"],
                "searching": true,
            });
        }
    }

    function add_new() {
        $("#btn_save").show();
        $("#btn_update").hide();
        $("#txt_id").removeAttr("readonly");
        $('#frm_user').trigger("reset");
    }
    var currentPageNumber = 0;

    function updateDataTableLocally(dcode, newData) {
        var pageInfo = dtable.page.info();
        currentPageNumber = pageInfo.page;
        var rowIndex = dtable.row('#' + dcode).index();
        var cellIndex = 1;
        dtable.cell({
            row: rowIndex,
            column: cellIndex
        }).data(newData);
        dtable.draw(false);
    }

    $("#btn_update").on("click", function() {
        const form = $("#frm_edit_user");
        const json = convertFormToJSON(form);
        console.log(json);
        var slno = $("#txt_slno1").val();
        $.ajax({
            url: '../prsApi/leavedet/' + slno,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    $("#header-footer-modal-preview").hide();
                    updateDataTableLocally(slno, json);

                }
                //console.log("..."+data);
            },
            data: JSON.stringify(json)
        });
    });

    $("#btn_save").on("click", function() {
        const form = $("#frm_user")[0];
        const formData = new FormData(form);

        $.ajax({
            url: '../prsApi/leavedet',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.status == "Ok") {
                    alert("Leave application submitted successfully!");
                    $("#header-footer-modal-preview").hide();
                    dtable1.draw();
                } else if (data.status == "Error") {
                    alert(`Error: ${data.msg}`);
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = "Something went wrong.";
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                alert(`Error: ${errorMsg}`);
            }
        });
    });



    function load_data(EMPNO) {
        $("#btn_save").hide();
        $("#btn_update").show();
        $.ajax({
            url: '../prsApi/leavedet/' + EMPNO,
            method: "GET",
            success: function(res) {
                $("#txt_empno").val(res.EMPNO);
                $("#txt_empName").val(res.NAME);
                $("#txt_date").val(res.DTE);
                $("#txt_descr").val(res.DESCR);
                $("#txt_fromDate").val(res.FDATE);
                $("#txt_toDate").val(res.TDATE);
                $("#txt_halfday").val(res.HLEAVE);
                $("#txt_nofDays").val(res.NOL);
                $("#txt_leave_wp").val(res.LWOP);
                $("#txt_leave_type").val(res.LTYPE);
            }
        });
    }

    function load_edit_data(SLNO) {
        $("#btn_save").hide();
        $("#btn_update").show();

        $.ajax({
            url: '../prsApi/leaveload/' + SLNO,
            method: "GET",
            success: function(res) {
                console.log("Loading Data:", res);
                $("#txt_slno1").val(res.slno);
                $("#txt_date1").val(res.DTE);
                $("#txt_descr1").val(res.DESCR);
                $("#txt_fromDate1").val(res.FDATE);
                $("#txt_toDate1").val(res.TDATE);
                $("#txt_halfday1").val(res.HLEAVE);
                $("#txt_nofDays1").val(res.NOL);
                $("#txt_leave_wp1").val(res.LWOP);
                $("#txt_leave_type1").val(res.LTYPE);
            },
            error: function(err) {
                console.error("Error loading data:", err);
            }
        });
    }

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
        var results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    document.getElementById('empname').addEventListener('change', function() {
        var selectedOption = this.value;
        if (selectedOption) {
            var empno = selectedOption.split(' - ')[0];
            console.log('Selected EMPNO:', empno);
            load_search_data(empno);
        }
    });

    function load_search_data(empno) {
        $.ajax({
            url: '../prsApi/leavedet/' + empno,
            method: "GET",

            success: function(data) {
                if (data) {
                    const res = data;
                    console.log(res.DESCR);
                    
                    console.log(`Loading Search Data ${data}`)
                    $("#txt_slno").val(res.slno);
                    $("#txt_empno").val(res.EMPNO);
                    $("#txt_empName").val(res.NAME);
                    $("#txt_date").val(res.DTE);
                    $("#txt_description").val(res.DESCR);
                    $("#txt_from").val(res.FDATE);
                    $("#txt_to").val(res.TDATE);
                    $("#txt_halfday").val(res.HLEAVE);
                    $("#txt_nofdays").val(res.NOOFDAYS);
                    $("#txt_sanctioned").val(res.NOL);
                    $("#txt_leave_wp").val(res.LWOP);
                    $("#txt_leave_type").val(res.LTYPE);

                    loadLeaveDataTable(res.EMPNO);
                    attendanceRecordData(res.EMPNO);
                    load_leave(res.EMPNO);

                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error("Error loading data: " + errorThrown);
            }
        });
    }

    function remove_data(SLNO) {
        Swal.fire({
            title: 'Are you sure to Delete?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Delete event
                $.ajax({
                    url: '../prsApi/leavedet/' + SLNO,
                    type: 'DELETE',
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function(data) {
                        if (data.status == "Ok") {
                            Swal.fire({
                                title: data.msg,
                                icon: 'success',
                            }).then((result) => {
                                dtable1.draw();
                            });
                        } else {
                            Swal.fire(data.msg, '', 'error');
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        Swal.fire('Error', 'An error occurred while deleting the data.', 'error');
                    }
                });

            } else {
                Swal.close();
            }
        });
    }

    function next_data() {
        currentIndex++;
        load_data_tleave(currentIndex);
    }

    function prev_data() {
        currentIndex--;
        load_data_tleave(currentIndex);
    }
</script>

</html>