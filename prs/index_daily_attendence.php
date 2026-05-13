<?php
    session_start();
    if ((!isset($_SESSION['user'])))
    {
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
        <title>Payroll - Daily Attendence</title>
        <!-- BEGIN: CSS Assets-->
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

    .dataTables_length select
    {
        width:60px;
    }

    .dataTables_length select {
        width: 60px;
    }

    .badge {
        display: inline-block;
        padding: 5px 10px;
        font-size: 14px;
        font-weight: bold;
        border-radius: 5px;
    }
</style>


    <!-- END: Head -->
    <body class="py-0">
                <!-- BEGIN: Mobile Menu -->
                <?php include 'mob.php' ?>
        <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
            $amenu="transaction";
            $page="daatt";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "Transaction";
                $currentPage="Daily Attendence";
                include 'top.php'
            ?>
            <div id="frm_user" class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-5">
                  Daily Attendence
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="intro-y col-span-12 lg:col-span-12">
                    <div class="grid grid-cols-12 text-dark mt-6">
                        <label class="col-span-1 mb-5 align-self-center flex items-center" for="month">For Month: </label>
                        <div class="col-span-2 mb-5">
                            <select class="w-full text-dark" id="txt_month" name="month" class="">
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
                    
                        <label class="col-span-1 mb-5 align-self-center flex items-center" for="year">For Year: </label>
                        <div class="col-span-2 mb-5">
                            <select class="w-full text-dark" id="txt_year" name="year" class="">

                            <?php
                            $currentYear = date("Y");
                            for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                echo "<option value=\"$i\">$i</option>";
                            }
                            ?>
                            </select>
                        </div>
                        
                        <div class="col-span-2">
                            <button type="button" id="btn_filter" class="btn btn-outline-primary rounded-full" onclick="getData()">Get Attendence Sheet</button>
                        </div>
                    </div>
   

				<!-- <button class="btn btn-primary shadow-md mr-2 rounded-full"
				onclick="add_new()" data-tw-toggle="modal"
				data-tw-target="#header-footer-modal-preview-view">Add</button> -->
                <div class="intro-y box mt-5">
                <div id="boxed-tab" class="p-5">
                    <div class="preview">
                        <ul class="nav nav-boxed-tabs" role="tablist">
                            <li id="cat_shift_change" class="nav-item flex-1" role="presentation">
                                <button class="nav-link w-full py-2 active" data-tw-toggle="pill"
                                    data-tw-target="#cat_shift_change" type="button" role="tab"
                                    aria-controls="cat_shift_change" aria-selected="true"> Attendance Register </button>
                            </li>
                            <li id="emp_shift_change" class="nav-item flex-1" role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill"
                                    data-tw-target="#emp_shift_change" type="button" role="tab"
                                    aria-controls="emp_shift_change" aria-selected="false"> Raw Attendance </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-5">
                            <div id="cat_shift_change" class="tab-pane leading-relaxed active" role="tabpanel"
                                aria-labelledby="cat_shift_change">
                                <div class="" id="responsive-table">
                                    <div class="preview">
                                    <div class="card-header d-flex justify-content-between">

                                        <div class="overflow-x-auto center container">
                                            <table id="table" class="table table-bordered table-striped " style="width:100%"  cellpadding="7px" >
                                                <thead>
                                                    <tr>
                                                        <th width="5%">SL No</th>
                                                        <th width="5%">Emp No</th>
                                                        <th width="20%">Emp Name</th>
                                                        <th width="8%">DT</th>
                                                        <th width="8%">In Time</th>
                                                        <th width="9%">Out Time</th>
                                                        <th width="5%">Shift</th>
                                                        <th width="5%">Present</th>
                                                        <th width="5%">LeaveAdj</th>
                                                        <th width="5%">Leave Head</th>
                                                        <th width="5%">Work Duration</th>
                                                        <th width="5%">DFLAG</th>
                                                        <th width="5%">Late</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div id="emp_shift_change" class="tab-pane leading-relaxed" role="tabpanel"
                                aria-labelledby="emp_shift_change">
                                <div class="" id="responsive-table">
                                    <div class="preview">
                                    <div class="card-header d-flex justify-content-between">

                                        <div class="overflow-x-auto center container">
                                            <table id="raw_att" class="table table-bordered table-striped " style="width:100%"  cellpadding="7px" >
                                                <thead>
                                                    <tr>
                                                        <th width="5%">SL No</th>
                                                        <th width="5%">UID</th>
                                                        <th width="20%">Emp Name</th>
                                                        <th width="8%">Date/Time</th>
                                                        <th width="8%">Mac ID</th>
                                                        <th width="9%">Date</th>
                                                        <th width="5%">MInOut</th>
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

                        </div>
                    </div>
                </div>
            </div>             
        </div>
            </div>
                </div>
            </div>
        </div>
        <!-- END: Content -->
        <!-- BEGIN: Delete Confirmation Modal -->
                <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <div class="p-5 text-center">
                                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                    <div class="text-3xl mt-5">Are you sure?</div>
                                    <div class="text-slate-500 mt-2">
                                        Do you really want to delete these records?
                                        <br>
                                        This process cannot be undone.
                                    </div>
                                </div>
                                <div class="px-5 pb-8 text-center">
                                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                    <button type="button" class="btn btn-danger w-24">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


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
  function updateDays() {
    const monthSelect = parseInt(document.getElementById('txt_month').value);
    // const monthSelect = 2;
    const yearSelect = parseInt(document.getElementById('txt_year').value);
    // const yearSelect = 2020;
    //const daySelect = document.getElementById('txt_day');

    var daysInMonth = getDaysInMonth(monthSelect, yearSelect);
    // Clear existing options
    daySelect.innerHTML = '';


    for (let i = 1; i <= daysInMonth; i++) {
      const option = document.createElement('option');
      option.value = i;
      option.textContent = i;
      daySelect.appendChild(option);
    }
}

document.getElementById('txt_month').addEventListener('change', updateDays);
updateDays();
    const currentDate = new Date();
    const selectedMonth = currentDate.getMonth();
    const selectedYear = currentDate.getYear();
    const selectedDay = currentDate.getDay();

function getData() {
    const selectedMonth = $("#txt_month").val();
    const selectedYear = $("#txt_year").val();
    const selectedDay = $("#txt_day").val();
    console.log("Clicked Filter Button!");
    console.log(selectedDay + "-" + selectedMonth + "-" + selectedYear);

    $("#table").DataTable().destroy();
    $("#raw_att").DataTable().destroy();

    var dtable = $('#table').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "serverSide": true,
        ajax: {
            url: 'ajax_daily_attendence.php',
            method: 'GET',
            data: {
                month: selectedMonth,
                year: selectedYear,
                day: selectedDay,
            },
            dataSrc: 'data'
        },
        "columns": [
             {
                            "data": null,
                            "render": function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        { "data": "empno" },
                        { "data": "empname" },
                        { "data": "dt" },
                        { "data": "intime" },
                        { "data": "outtime" },
                        { "data": "shift" },
                        { "data": "present" },
                        { "data": "leaveAdj" },
                        { "data": "leaveHead" },
                        { "data": "workDur" },
                        { "data": "dflag" },
                        { "data": "late" },
        ],
        "order": [0, "asc"],
    });

    var rawtable = $('#raw_att').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "searching": true,
        "serverSide": true,
        ajax: {
            url: 'ajax_raw_attendance.php',
            method: 'GET',
            data: {
                month: selectedMonth,
                year: selectedYear,
                day: selectedDay,
            },
            dataSrc: 'data'
        },
        "columns": [
            {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
               { data: "uid" },
                { data: "name"},
                { data: "dtime"},
                { data: "macid"},
                { data: "dt"},
                { data: "minout"},
                { data: "shift" },
                { data: "cmnt"},
        ],
        "order": [3, "asc"],
    });
};

function getDaysInMonth(month, year) {
    if (month === 2 && ((year % 4 === 0 && year % 100 !== 0) || year % 400 === 0)) {
        return 29;
    }
    const daysInMonth = {
        1: 31,
        2: 28,
        3: 31,
        4: 30,
        5: 31,
        6: 30,
        7: 31,
        8: 31,
        9: 30,
        10: 31,
        11: 30,
        12: 31,
    };
    return daysInMonth[month];
}

</script>
</html>