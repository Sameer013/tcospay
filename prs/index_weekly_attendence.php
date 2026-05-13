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
        <title>Payroll - Weekly Attendence</title>
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

</style>


    <!-- END: Head -->
    <body class="py-0">
                <!-- BEGIN: Mobile Menu -->
                <?php include 'mob.php' ?>
        <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
            $menu_title = "Transaction";
            $currentPage="Weekly Attendence";
            // $amenu="transaction";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $currentPage="Weekly Attendence";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                  Weekly Attendence
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12">
				<!-- <button class="btn btn-primary shadow-md mr-2 rounded-full"
				onclick="add_new()" data-tw-toggle="modal"
				data-tw-target="#header-footer-modal-preview-view">Add</button> -->
                <div class="grid grid-cols-12 text-dark mt-6">
                            <label class="col-span-1 align-self-center  flex items-center" for="month">For Month: </label>
                            <div class="col-span-1">
                                <select class="text-dark" name="month" id="month" placeholder="Select">
                                <option value="" selected="true" disabled >Select</option>
                                                    <option value="1">JAN</option>
                                                    <option value="2">FEB</option>
                                                    <option value="3">MAR</option>
                                                    <option value="4">APR</option>
                                                    <option value="5">MAY</option>
                                                    <option value="6">JUN</option>
                                                    <option value="7">JUL</option>
                                                    <option value="8">AUG</option>
                                                    <option value="9">SEP</option>
                                                    <option value="10">OCT</option>
                                                    <option value="11">NOV</option>
                                                    <option value="12">DEC</option>
                                </select>
                            </div>

                            <label class="col-span-1 align-self-center  flex items-center" for="year">For Year: </label>
                            <div class="col-span-1">
                                <select class="text-dark" name="year" id="year" placeholder="Select">
                                <option value="" selected="true" disabled >Select</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>

                                </select>
                            </div>
                            <div class="col-span-2">
                                <button type="submit" class="btn btn-outline-primary mx-4 rounded-full"  onclick="getAttendanceSheet()">Get Attendence Sheet</button>
                            </div>
                        </div>
                    <!-- BEGIN: Responsive Table -->
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
                               <div class="overflow-x-auto">
                                    <table id="table" class="table table-bordered table-striped " style="width:100%"  cellpadding="7px" >
                                            <thead>
                                            <tr>
                                                <th width="5%">SL No</th>
                                                <th width="5%">Emp No</th>
                                                <th width="20%">Name</th>
                                                <th width="5%">CL</th>
                                                <th width="5%">EL</th>
                                                <th width="5%">LWOP</th>
                                                <th width="5%">Off Days</th>
                                                <th width="5%">Sick Leaves</th>
                                                <th width="5%">Holidays</th>
                                                <th width="5%">Days Worked</th>
                                                <th width="5%">Days Paid</th>
                                                <th width="5%">Pay Mode</th>
                                                <th width="5%">Absent</th>
                                                <th width="5%">Working Days</th>
                                                <th width="5%">OT (Hr)</th>
                                                <th width="5%">Action</th>
                                            </tr>
                                            </thead>
                                    </table>
                               </div>
                            </div>

                        </div>
                    </div>
                    <!-- END: Responsive Table -->
                </div>
            </div>
        </div>
        <!-- END: Content -->

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
function getAttendanceSheet() {
    var selectedMonth = document.getElementById("month").value;
    var selectedYear = document.getElementById("year").value;

    var dtable = $('#table').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'ajax_weekly_attendence.php',
            method: 'GET',
            data: {
                month: selectedMonth,
                year: selectedYear
            },
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: "empno" },
            { data: "Name" },
            { data: "cl" },
            { data: "el" },
            { data: "lwop" },
            { data: "off_days" },
            { data: "med_leave" },
            { data: "hdays" },
            { data: "attnd" },
            { data: "adj" },
            { data: "pay_mode" },
            { data: "absent" },
            { data: "wdays" },
            { data: "othour" },
            { data: "action" }
        ],
        order: [0, "asc"],
    });
}

                function convertFormToJSON(form) {
                    const array = $(form).serializeArray();
                    const json = {};
                    $.each(array, function () {
                        key=this.name;
                        key=key.substring(key.indexOf("_") + 1);
                        json[key] = this.value || "";
                    });
                    return json;
                }

            function load_data(id){
                $("#btn_save").hide();
                $("#btn_update").show();
                $.ajax({
                    url:'../prsApi/weekly_attendence/'+id,
                    method:"GET",
                    success:function(res){
                        $("#txt_block_id").val(res.block_id);
                        $("#txt_block").val(res.block);

                    }
                });
            }

	</script>
</html>