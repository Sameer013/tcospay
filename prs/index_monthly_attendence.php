<?php
session_start();
if ((!isset($_SESSION['user']))) {
    header('refresh: 1;url=login.php');
    die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
}
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
    <title>Payroll - Monthly Attendence</title>
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
</style>


<!-- END: Head -->

<body class="py-0">
    <!-- BEGIN: Mobile Menu -->
    <?php include 'mob.php' ?>
    <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
        $menu_title = "Transaction";
        $currentPage = "Monthly Attendence";
        $amenu = "transaction";
        $page = "mnatt";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
    <?php
    $currentPage = "Monthly Attendance";
    include 'top.php';
    ?>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Monthly Attendance
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12">
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-dark mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
                    <label for="month" class="whitespace-nowrap">For Month:</label>
                    <select class="text-dark p-2 border rounded-md w-full sm:w-40" name="month" id="month">
                        <option value="" selected disabled>Select Month</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
                    <label for="year" class="whitespace-nowrap">For Year:</label>
                    <select class="text-dark p-2 border rounded-md w-full sm:w-32" name="year" id="year">
                        <option value="" selected disabled>Select Year</option>
                        <?php
                        $currentYear = date("Y");
                        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-outline-primary px-4 py-2 rounded-md" id="getAttendanceSheetBtn">
                        Get Attendance Sheet
                    </button>
                    <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-md" id="syncData">
                        Synchronize Data
                    </button>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="intro-y box mt-5">
                <div class="p-5" id="responsive-table">
                    <div class="preview overflow-x-auto">
                        <table id="table" class="table table-bordered table-striped min-w-[1000px] w-full" cellpadding="7px">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">SL No</th>
                                    <th class="whitespace-nowrap">Emp No</th>
                                    <th class="whitespace-nowrap">Name</th>
                                    <th class="whitespace-nowrap">CL</th>
                                    <th class="whitespace-nowrap">EL</th>
                                    <th class="whitespace-nowrap">LWOP</th>
                                    <th class="whitespace-nowrap">Off Days</th>
                                    <th class="whitespace-nowrap">Sick Leaves</th>
                                    <th class="whitespace-nowrap">Holidays</th>
                                    <th class="whitespace-nowrap">Days Worked</th>
                                    <th class="whitespace-nowrap">Days Paid</th>
                                    <th class="whitespace-nowrap">Pay Mode</th>
                                    <th class="whitespace-nowrap">Absent</th>
                                    <th class="whitespace-nowrap">Working Days</th>
                                    <th class="whitespace-nowrap">Action</th>
                                </tr>
                            </thead>
                    
                        </table>
                    </div>
                </div>
            </div>
            <!-- END Responsive Table -->
        </div>
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
                                        Edit
                                    </h2>
                                </div>
                                <hr class="border-black">
                                <!-- END: Modal Header -->
                                <!-- BEGIN: Modal Body -->
                                <form id="frm_user" class="frm_user" name="frm_user" action="" method="post">
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">


                                        <div class="col-span-12 sm:col-span-6 hidden">
                                            <label class="form-label">Name: *</label>
                                            <input id="txt_Name" name="txt_Name" type="hidden" class="txt_Name form-control" placeholder="Name" readonly>
                                        </div>
                                        <!-- <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Bank Name : *</label>
                                                <select name="txt_Bank" id="txt_Bank" class="form-control">
                                                    <option value="" selected disabled>--Select Bank Name--</option>
                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT BID, DESCR FROM bankmast");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $bid = $row['BID'];
                                                        $descr = $row['DESCR'];
                                                        echo "<option value='$bid'>$descr</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div> -->
                                        <div class="col-span-12 sm:col-span-6 hidden">
                                            <label class="form-label">Sheet ID: </label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_sheet_id" id="txt_sheet_id" placeholder="Sheet ID" readonly />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Emp No: </label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_empno" id="txt_empno" placeholder="Employee No." readonly />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Sheet Id: </label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_sheetId" id="txt_sheetId" placeholder="Sheet Id." readonly />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Emp Name:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_empname" id="txt_empname" placeholder="Employee Name" readonly />

                                        </div>

                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">CL:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_cl" id="txt_cl" placeholder="CL" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">EL:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_el" id="txt_el" placeholder="EL" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">LWOP:</label>
                                            <input type="number" class="form-control rounded-pill text-dark" name="txt_lwop" id="txt_lwop" placeholder="LWOP" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Off Days:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_off_days" id="txt_off_days" placeholder="Off Days" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Sick Leaves:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_med_leave" id="txt_med_leave" placeholder="Sick leave" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Holidays:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_hdays" id="txt_hdays" placeholder="Hoildays" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Days Worked:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_attnd" id="txt_attnd" placeholder="Days Worked" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Days Paid:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_dpaid" id="txt_dpaid" placeholder="Days Paid" readonly />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Pay Mode:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_pay_mode" id="txt_pay_mode" placeholder="Pay Mode" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6 hidden">
                                            <label class="form-label">Absent:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_absent" id="txt_absent" placeholder="Absent" readonly />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Working Days:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_wdays" id="txt_wdays" placeholder="Working Days" />
                                        </div>
                                        <!-- <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">SPL:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_splPay" id="txt_splPay" placeholder="SPL" />
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">iTax:</label>
                                            <input type="text" class="form-control rounded-pill text-dark" name="txt_iTax" id="txt_iTax" placeholder="iTax" />
                                        </div> -->

                                    </div>
                                    <!-- END: Modal Body -->
                                </form>
                                <!-- BEGIN: Modal Footer -->
                                <div class="modal-footer">
                                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                    <button id="btn_update" data-tw-dismiss="modal" class="btn_update btn btn-primary w-20 rounded-full">Update</button>
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
    var dtable;

    function initializeDataTable() {
        var selectedMonth = document.getElementById("month").value;
        var selectedYear = document.getElementById("year").value;

        if (!selectedMonth || !selectedYear) {
            alert("Please select both month and year.");
            return;
        }

        if ($.fn.DataTable.isDataTable('#table')) {
            $('#table').DataTable().destroy();
        }

        dtable = $('#table').DataTable({
            buttons: ['copy', 'excel', 'pdf'],
            "processing": true,
            "searching": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax_monthly_attendence.php",
                "data": {
                    "month": selectedMonth,
                    "year": selectedYear
                }
            },
            "columns": [{

                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },{ data: "empno"},
                { data: "Name"},
                { data: "cl" },
                { data: "el"},
                { data: "lwop"},
                { data: "off_days" },
                { data: "med_leave"},
                { data: "hdays"},
                { data: "attnd"},
                { data: "dpaid"},
                { data: "pay_mode"},
                { data: "absent"},
                { data: "wdays"},
                // {
                //     data: "arrear"
                // },
                // {
                //     data: "iTax"
                // },
                {
                    data: "action",
                    defaultContent: ""
                }
            ],
            "order": [0, "asc"],
        });
    }

    function sheet() {
        var selectedMonth = document.getElementById("month").value;
        var selectedYear = document.getElementById("year").value;

        $.ajax({
            url: `ajax_sheet.php?month=${selectedMonth}&year=${selectedYear}`,
            type: 'GET',
            dataType: 'json',
            contentType: 'application/json',
            success: function(response) {
                console.log("AJAX Response:", response);
            },
            error: function() {
                alert("Error in AJAX.");
            },
        });
    }

    function syncSheet() {
        var selectedMonth = document.getElementById("month").value;
        var selectedYear = document.getElementById("year").value;

        $.ajax({
            url: `ajax_sync.php?month=${selectedMonth}&year=${selectedYear}`,
            type: 'GET',
            dataType: 'json',
            contentType: 'application/json',
            success: function(response) {
                console.log("AJAX Response:", response);

                if (response.dataExists) {
                    if (confirm("Data already exists. Do you want to regenerate?")) {
                        regenerateData();
                    } else {
                        console.log("Regeneration canceled by the user.");
                    }
                } else {
                    initializeDataTable();
                }
            },
            error: function() {
                alert("Error in AJAX.");
            },
        });
    }

    function regenerateData() {
        console.log("Regenerating data...");

        var selectedMonth = document.getElementById("month").value;
        var selectedYear = document.getElementById("year").value;

        $.ajax({
            url: `ajax_regenerate.php?month=${selectedMonth}&year=${selectedYear}`,
            type: 'GET',
            dataType: 'json',
            contentType: 'application/json',
            success: function(response) {
                console.log("Regeneration AJAX Response:", response);

                if (response.success) {

                    initializeDataTable();
                } else {
                    // Handle the update failure
                    console.log("Update failed:", response.error);
                }
            },
            error: function() {
                alert("Error in AJAX.");
            },
        });
    }

    $(document).ready(function() {
        $('#getAttendanceSheetBtn').on('click', function() {
            initializeDataTable();
        });
        $('#syncData').on('click', function() {
            sheet();
            syncSheet();
        });
    });

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


    function load_data(CODE, SHEET_ID) {
        $("#btn_save").hide();
        $("#btn_update").show();
        console.log(CODE);
        console.log(SHEET_ID);
        $.ajax({
            url: '../prsApi/attdmonth/' + CODE + '/' + SHEET_ID,
            method: "GET",
            success: function(res) {
                $("#txt_sheet_id").val(res.sheet_id);
                $("#txt_empno").val(res.empno);
                $("#txt_sheetId").val(res.sheet_id);
                $("#txt_empname").val(res.Name);
                $("#txt_cl").val(res.cl);
                $("#txt_el").val(res.el);
                $("#txt_lwop").val(res.lwop);
                $("#txt_off_days").val(res.off_days);
                $("#txt_med_leave").val(res.med_leave);
                $("#txt_hdays").val(res.hdays);
                $("#txt_attnd").val(res.attnd);
                $("#txt_dpaid").val(res.dpaid);
                $("#txt_pay_mode").val(res.pay_mode);
                $("#txt_absent").val(res.absent);
                $("#txt_wdays").val(res.wdays);
                // $("#txt_splPay").val(res.arrear);
                // $("#txt_iTax").val(res.iTax);
            }
        });
    }

    function updateDataTableLocally(CODE, newData) {
        var pageInfo = dtable.page.info();
        currentPageNumber = pageInfo.page;

        var rowIndex = dtable.row('#' + CODE).index();
        dtable.row(rowIndex).data(newData);
        dtable.draw(false);
    }

    $("#btn_update").on("click", function() {
        const form = $("#frm_user");
        const json = convertFormToJSON(form);
        console.log(json);

        var CODE = $("#txt_empno").val();
        var SHEET_ID = $("#txt_sheetId").val();
        console.log(CODE);
        console.log(SHEET_ID);

        $.ajax({
            url: '../prsApi/attdmonth/' + CODE + '/' + SHEET_ID,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    $("#header-footer-modal-preview").hide();
                    //   console.log('UPDATE Worked!');
                    //  updateDataTableLocally(CODE, json);
                }
            },
            data: JSON.stringify(json)
        });
    });
</script>

</html>