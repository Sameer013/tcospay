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
    <title>Payroll - Shift Change</title>
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
        $amenu = "transaction";
        $page = "sftch";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Transaction";
            $currentPage = "Shift Change";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Shift Change
                </h2>
            </div>
            <button class="btn btn-primary w-20 shadow-md mr-2 mt-4 rounded-full" data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview-view1">Add
            </button>
            <div class="intro-y box mt-5">
                <div id="boxed-tab" class="p-5">
                    <div class="preview">
                        <ul class="nav nav-boxed-tabs" role="tablist">
                            <li id="cat_shift_change" class="nav-item flex-1" role="presentation">
                                <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#cat_shift_change" type="button" role="tab" aria-controls="cat_shift_change" aria-selected="true"> Category Shift Change </button>
                            </li>
                            <li id="emp_shift_change" class="nav-item flex-1" role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#emp_shift_change" type="button" role="tab" aria-controls="emp_shift_change" aria-selected="false"> Employee Shift Change </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-5">
                            <div id="cat_shift_change" class="tab-pane leading-relaxed active" role="tabpanel" aria-labelledby="cat_shift_change">
                                <div class="" id="responsive-table">
                                    <div class="preview">
                                        <div class="card-header d-flex justify-content-between">

                                            <div class="overflow-x-auto center container">
                                                <table id="table" class="table table-bordered table-striped " cellpadding="7px">
                                                    <thead class="text-dark">
                                                        <tr>
                                                            <th width="5%">Sl No</th>
                                                            <th width="20%">Date of Change</th>
                                                            <th width="20%">End Date</th>
                                                            <th width="20%">Category</th>
                                                            <th width="20%">Shift Description</th>
                                                            <th width="20%">Action</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="emp_shift_change" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="emp_shift_change">
                                <div class="" id="responsive-table">
                                    <div class="preview">
                                        <div class="card-header d-flex justify-content-between">

                                            <div class="overflow-x-auto center container">
                                                <table id="table1" class="table table-bordered table-striped " width="100%" cellpadding="7px">
                                                    <thead class="text-dark">
                                                        <tr>
                                                            <th>Sl No</th>
                                                            <th>Date of Change</th>
                                                            <th>EmpId</th>
                                                            <th>Employee Name</th>
                                                            <th>Shift </th>
                                                            <th>Action</th>
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
            <!-- ===================1st Model============ -->
            <div id="header-footer-modal" class="p-5">
                <div class="preview" id="cat_shift">
                    <!-- BEGIN: Modal Content -->
                    <div id="header-footer-modal-preview-view" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- BEGIN: Modal Header -->
                                <div class="modal-header">
                                    <h2 class="font-bold text-base mr-auto">
                                        Add
                                    </h2>
                                </div>
                                <hr class="border-black">

                                <!-- END: Modal Header -->
                                <!-- BEGIN: Modal Body -->
                                <form id="frm_user" name="frm_user" action="" method="post">
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                        <input id="txt_doc1" name="txt_doc1" type="hidden" class="form-control rounded-full" placeholder="Dcode" readonly>
                                        <input id="txt_cat1" name="txt_cat1" type="hidden" class="form-control rounded-full" placeholder="Dcode" readonly>

                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_doc" class="form-label">Date of Change</label>
                                            <input id="txt_doc" name="txt_doc" type="date" class="form-control rounded-full" placeholder="Date of Change">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_endDate" class="form-label">End Date</label>
                                            <input id="txt_endDate" name="txt_endDate" type="date" class="form-control rounded-full" placeholder="End Date">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_cat" class="form-label">Category</label>
                                            <div class="form-check-inline">
                                                <select name="txt_cat" id="txt_cat" class="form-control">

                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT cat_code, DESCR FROM catmast");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $catcode = $row['cat_code'];
                                                        $descr = $row['DESCR'];
                                                        echo "<option value='$catcode'>$descr</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_sDesc" class="form-label">Shift Description</label>
                                            <select name="txt_sDesc" id="txt_sDesc" class="form-control">

                                                <?php
                                                require_once 'includes/dbconn.php';
                                                $query = $db->query("SELECT ShiftCode FROM shiftmaster");
                                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                    $shiftcode = $row['ShiftCode'];
                                                    echo "<option value='$shiftcode'>$shiftcode</option>";
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
                                    <button id="btn_save" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full">Save</button>
                                    <button id="btn_update" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full">Update</button>
                                </div>

                                <!-- END: Modal Footer -->
                            </div>
                        </div>
                    </div>
                    <!-- END: Modal Content -->

                    <!-- ===================2nd Model============ -->
                    <div id="header-footer-modal" class="p-5">
                        <div class="preview" id="emp_shift">
                            <!-- BEGIN: Modal Content -->
                            <div id="header-footer-modal-preview-view1" class="modal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- BEGIN: Modal Header -->
                                        <div class="modal-header">
                                            <h2 class="font-bold text-base mr-auto">
                                                Add
                                            </h2>
                                        </div>
                                        <hr class="border-black">
                                        <!-- END: Modal Header -->
                                        <!-- BEGIN: Modal Body -->
                                        <form id="frm_user2" name="frm_user" action="" method="post">
                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                                <input id="txt_Scdte" name="txt_Scdte" type="hidden" class="form-control rounded-full" placeholder="Dcode" readonly>
                                                <input id="txt_empid" name="txt_empid" type="hidden" class="form-control rounded-full" placeholder="Dcode" readonly>


                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_empname" class="form-label">Employee Name</label>
                                                    <select name="txt_empname" id="txt_empname" class="form-control">
                                                        <option value="" selected disabled>--Select Employee Name--</option>
                                                        <?php
                                                        require_once 'includes/dbconn.php';
                                                        $query = $db->query("SELECT EMPNO, NAME FROM empmast");
                                                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                            $empno = $row['EMPNO'];
                                                            $name = $row['NAME'];
                                                            echo "<option value='$empno'>$name</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_cngedte" class="form-label">Date of Change</label>
                                                    <input id="txt_cngedte" name="txt_cngedte" type="date" class="form-control rounded" placeholder="Date of Change">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_shift" class="form-label">Shift</label>
                                                    <select name="txt_shift" id="txt_shift" class="form-control">

                                                        <?php
                                                        require_once 'includes/dbconn.php';
                                                        $query = $db->query("SELECT ShiftCode FROM shiftmaster");
                                                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                            $shiftcode = $row['ShiftCode'];
                                                            echo "<option value='$shiftcode'>$shiftcode</option>";
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
                                            <button id="btn_save1" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full">Save</button>
                                            <button id="btn_update1" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full">Update</button>
                                        </div>

                                        <!-- END: Modal Footer -->
                                    </div>
                                </div>
                            </div>
                            <!-- END: Modal Content -->
                        </div>
                    </div>
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
    var dtable;
    var dtable1;
    $(document).ready(function() {
        //function load_shift();
        $(".btn").attr("data-tw-target", "#header-footer-modal-preview-view");

    });
    dtable = $('#table').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "serverSide": true,
        "ajax": "ajax_cat_shift_change.php",
        "columns": [{
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                "data": "scdate"
            },
            {
                "data": "scedate"
            },
            {
                "data": "DESCR"
            },
            {
                "data": "ShiftCode"
            },
            {
                "data": "action"
            }
        ],
        "order": [0, "asc"],

    });

    dtable1 = $('#table1').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "serverSide": true,
        "ajax": "ajax_emp_shift_change.php",
        "columns": [{
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                "data": "scdate"
            },
            {
                "data": "Empid"
            },
            {
                "data": "NAME"
            },
            {
                "data": "ShiftCode"
            },
            {
                "data": "action"
            }
        ],
        "order": [0, "asc"],

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
    $("#btn_save").on("click", function() {
        const form = $("#frm_user");
        const json = convertFormToJSON(form);
        console.log(json);
        $.ajax({
            url: '../prsApi/catshift',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "200") {
                    $("#header-footer-modal-preview").hide();
                    dtable.draw();
                }
                //console.log("..."+data);
            },
            data: JSON.stringify(json)
        });
    });

    $("#btn_update").on("click", function() {
        const form = $("#frm_user");
        const json = convertFormToJSON(form);
        console.log(json);
        let scdate = $("#txt_doc1").val();
        let catcode = $("#txt_cat1").val();
        $.ajax({
            url: '../prsApi/catshift/' + scdate + '/' + catcode,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    $("#header-footer-modal-preview-view1").hide();
                    dtable.draw();
                }
                //console.log("..."+data);
            },
            data: JSON.stringify(json)
        });
    });

    function remove_data(scdate, catcode) {
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
                    url: '../prsApi/catshift/' + scdate + '/' + catcode,
                    type: 'DELETE',
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function(data) {
                        if (data.status == "Ok") {
                            Swal.fire({
                                title: data.msg,
                                icon: 'success',
                            }).then((result) => {
                                dtable.draw();
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

    function load_data(scdate, catcode) {
        $("#btn_save").hide();
        $("#btn_update").show();
        $.ajax({
            url: '../prsApi/catshift/' + scdate + '/' + catcode,
            method: "GET",
            success: function(data) {
                console.log(data);
                $("#txt_doc").val(data.scdate);
                $("#txt_endDate").val(data.scedate);
                $("#txt_cat").val(data.catcode);
                $("#txt_sDesc").val(data.ShiftCode);

                $("#txt_doc1").val(data.scdate);
                $("#txt_cat1").val(data.catcode);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error("Error loading data: " + errorThrown);
            }
        });
    }

    function load_data1(Empid) {
        $("#btn_save1").hide();
        $("#btn_update1").show();
        $.ajax({
            url: '../prsApi/empshift/' + Empid,
            method: "GET",
            success: function(data) {
                console.log(data);

                $("#txt_shift").val(data.ShiftCode);
                $("#txt_empname").val(data.EmpId);
                $("#txt_cngedte").val(data.scdate);

                $("#txt_scdte").val(data.scdate);
                $("#txt_empid").val(data.EmpId);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error("Error loading data: " + errorThrown);
            }
        });
    }
    $("#btn_save1").on("click", function() {
        const form = $("#frm_user2");
        const json = convertFormToJSON(form);
        console.log(json);
        $.ajax({
            url: '../prsApi/empshift',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "200") {
                    $("#header-footer-modal-preview").hide();
                    dtable1.draw();
                }
                //console.log("..."+data);
            },
            data: JSON.stringify(json)
        });
    });
    $("#btn_update1").on("click", function() {
        const form = $("#frm_user2");
        const json = convertFormToJSON(form);
        console.log(json);
        let empid = $("#txt_empid").val();
        $.ajax({
            url: '../prsApi/empshift/' + empid,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    $("#header-footer-modal-preview").hide();
                    dtable1.draw();
                }
                //console.log("..."+data);
            },
            data: JSON.stringify(json)
        });
    });

    function remove_data1(Empid) {
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
                    url: '../prsApi/empshift/' + Empid,
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
    $("#cat_shift_change").click(function() {
        console.log("cat_shift_change");
        $(".btn").attr("data-tw-target", "#header-footer-modal-preview-view");
    });
    $("#emp_shift_change").click(function() {
        $(".btn").attr("data-tw-target", "#header-footer-modal-preview-view1");

    });
    $(".btn").click(function() {
        $('#frm_user').trigger("reset");
        $("#btn_save").show();
        $("#btn_update").hide();
        $("#btn_save1").show();
        $("#btn_update1").hide();

    });
</script>

</html>