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
        <title>Payroll - Leave Group Master</title>
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

    .badge-other {
        color: red;
        background: url("dist/images/reddot.png") no-repeat center / 25px;
        text-align: center;
        line-height: 25px;
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
            $amenu="general";
            $page="lg_master";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "General";
                $currentPage="Leave Group Master";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                  Leave Group Master
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12">
				<button class="btn btn-primary  w-20 shadow-md mr-2 rounded-full"
				onclick="add_new()" data-tw-toggle="modal"
				data-tw-target="#header-footer-modal-preview-view">Add</button>
                <div class="grid grid-cols-12 text-dark mt-6">
                <label class="col-span-1 align-self-center flex items-center" for="leaveCode">Select: </label>
                <div class="col-span-2">
                             <select class="text-dark form-control rounded" name="leaveCode" id="leaveCode" placeholder="Description">
                                <?php
                                require_once 'includes/dbconn.php';
                                $query = $db->query("SELECT DISTINCT LeaveGroupCode, LeaveGroupDesc FROM leavegroup");
                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                    $leavecode = $row['LeaveGroupCode'];
                                    $lname1 = $row['LeaveGroupDesc'];
                                    echo "<option value='$leavecode'>$lname1</option>";
                                }
                                ?>
                                </select>
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
                                                 <th width="5%">Leave Description</th>
                                                <th width="10%">Carryforward</th>
                                                <th width="15%">Encash</th>
                                                <th width="15%">Overwrite WO</th>
                                                <th width="15%">Overwrite HL</th>
                                                <th width="15%">Credit Type</th>
                                                <th width="15%">AnnQuota</th>
                                                <th width="15%">Attend Days</th>
                                                <th width="15%">Leave Credit</th>
                                                <th width="15%">Monthly Quota</th>
                                                <th width="15%">Credit Monthly</th>
                                                <th width="15%">Credit Yearly</th>
                                                <th width="10%">Action</th>
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
                <!-- END: Delete Confirmation Modal -->
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
                                                  Add
                                                </h2>
                                            </div>
                                            <hr class="border-black">
                                            <!-- END: Modal Header -->
                                            <!-- BEGIN: Modal Body -->
											<form id="frm_user" name="frm_user" action="" method="post">
                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <input id="txt_leaveGrpCode" name="txt_leaveGrpCode" type="hidden" class="form-control rounded" placeholder="txt_leaveGrpCode" readonly>

                                            <input id="txt_leaveCode" name="txt_leaveCode" type="hidden" class="form-control rounded" placeholder="txt_leaveCode" readonly>

                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_leaveDesc" class="form-label">Leave Description</label>
                                                    <!-- <input id="txt_leaveDesc" name="txt_leaveDesc" type="text" class="form-control rounded" placeholder="Description"> -->
                      <select class="text-dark form-control rounded" name="txt_leaveDesc" id="txt_leaveDesc" placeholder="Description" readonly>
                                <?php
                                require_once 'includes/dbconn.php';
                                $query = $db->query("SELECT  LeaveCode, LeaveName FROM leavemst");
                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                    $lcode1 = $row['LeaveCode'];
                                    $lname1 = $row['LeaveName'];
                                    echo "<option value='$lcode1'>$lname1</option>";
                                }
                                ?>
                                </select>

                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_carryForw" class="form-label">Carry Forward</label>
                                                    <input id="txt_carryForw" name="txt_carryForw" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_encash" class="form-label">Encash</label>
                                                    <input id="txt_encash" name="txt_encash" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_OvrWO" class="form-label">Overwrite WO</label>
                                                    <input id="txt_OvrWO" name="txt_OvrWO" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_OvrHL" class="form-label">Overwrite HL</label>
                                                    <input id="txt_OvrHL" name="txt_OvrHL" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_credType" class="form-label">Credit Type</label>
                                                    <input id="txt_credType" name="txt_credType" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_annQuota" class="form-label">Annual Quota</label>
                                                    <input id="txt_annQuota" name="txt_annQuota" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_attndDays" class="form-label">Attend Days</label>
                                                    <input id="txt_attndDays" name="txt_attndDays" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_leaveCred" class="form-label">Leave Credit</label>
                                                    <input id="txt_leaveCred" name="txt_leaveCred" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_mnthQuota" class="form-label">Monthly Quota</label>
                                                    <input id="txt_mnthQuota" name="txt_mnthQuota" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_credMnth" class="form-label">Credit Monthly</label>
                                                    <input id="txt_credMnth" name="txt_credMnth" type="text" class="form-control rounded" placeholder="">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_credYr" class="form-label">Credit Yearly</label>
                                                    <input id="txt_credYr" name="txt_credYr" type="text" class="form-control rounded" placeholder="">
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
        var selectedLeaveCode = $("#leaveCode").val();
        var dtable;
                dtable = $('#table').DataTable({
                    buttons: ['copy', 'excel', 'pdf'],
                    "processing": true,
                    "searching": true,
                    "serverSide": true,
                    "ajax": {
                        "url":"ajax_leaveGroup.php",
                        "type":"GET",
                        "data": { "leaveCode": selectedLeaveCode }
                    }
                    ,
                    "columns": [
                        { "data": "LeaveGroupDesc" },
                        { "data": "CarryForward",
                            "render": function (data, type, row) {
                            return data === 1 ? "YES" : "NO";
                            }
                        },
                        { "data": "Encash",
                            "render": function (data, type, row) {
                            return data === 1 ? "YES" : "NO";
                            }
                        },
                        { "data": "OverwriteWO",
                            "render": function (data, type, row) {
                            return data === 1 ? "YES" : "NO";
                            }
                        },
                        { "data": "OverwriteHL",
                            "render": function (data, type, row) {
                            return data === 1 ? "YES" : "NO";
                            }
                        },
                        { "data": "CreditType" },
                        { "data": "AnnQuota" },
                        { "data": "AttendDays" },
                        { "data": "LeaveCredit" },
                        { "data": "MonthlyQuota" },
                        { "data": "CreditMonthly" },
                        { "data": "CreditYearly",
                            "render": function (data, type, row) {
                            return data === 1 ? "YES" : "NO";
                            }
                        },
                        { "data": "action" }
                    ],
                    "order": [0, "asc"],

                });


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



                function add_new() {
                    $("#btn_save").show();
                    $("#btn_update").hide();
                    $("#txt_id").removeAttr("readonly");
                    $('#frm_user').trigger("reset");
                }


                $("#leaveCode").change(function(){
                    selectedLeaveCode = $("#leaveCode").val();
                    console.log(selectedLeaveCode)
                    dtable.settings()[0].ajax.data = { "leaveCode": selectedLeaveCode };
                    dtable.ajax.reload();
                })
                function load_data(scdate,catcode) {
                    $("#btn_save").hide();
                    $("#btn_update").show();
                    $.ajax({
                        url: '../prsApi/leaveGroup/' + scdate + '/' + catcode,
                        method: "GET",
                        success: function(data) {
                            console.log(data);
                            $("#txt_leaveGrpCode").val(data.LeaveGroupCode);
                            $("#txt_leaveCode").val(data.LeaveCode);
                            $("#txt_leaveDesc").val(data.LeaveCode);
                            $("#txt_carryForw").val(data.CarryForward);
                            $("#txt_encash").val(data.Encash);
                            $("#txt_OvrWO").val(data.OverwriteWO);
                            $("#txt_OvrHL").val(data.OverwriteHL);
                            $("#txt_credType").val(data.CreditType);
                            $("#txt_annQuota").val(data.AnnQuota);
                            $("#txt_attndDays").val(data.AttendDays);
                            $("#txt_leaveCred").val(data.LeaveCredit);
                            $("#txt_mnthQuota").val(data.MonthlyQuota);
                            $("#txt_credMnth").val(data.CreditMonthly);
                            $("#txt_credYr").val(data.CreditYearly);

                        },
                        error: function(xhr, textStatus, errorThrown) {
                            console.error("Error loading data: " + errorThrown);
                        }
                        });
                }

                $("#btn_save").click(function() {
                $('#frm_user').trigger("reset");
                $("#btn_save").show();
                $("#btn_update").hide();
                $("#btn_save1").show();
                $("#btn_update1").hide();

                });

            function remove_data(scdate,catcode) {
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
                    url: '../prsApi/leaveGroup/' + scdate + '/' + catcode,
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
    $("#btn_save" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                $.ajax({
                    url: '../prsApi/leaveGroup',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function (data)
                    {
                        if (data.status=="Ok") {
                            $("#header-footer-modal-preview").hide();
                            dtable.draw();
                        }
                        //console.log("..."+data);
                    },
                    data: JSON.stringify(json)
                });
            });

            $("#btn_update" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                let leaveGrpCode= $("#txt_leaveGrpCode").val();
                let leaveCode=$("#txt_leaveCode").val();
                $.ajax({
                    url: '../prsApi/leaveGroup/'+ leaveGrpCode + '/' + leaveCode,
                    type: 'PUT',
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function (data) {
                        if (data.status=="Ok") {
                            $("#header-footer-modal-preview").hide();
                            dtable.draw();
                        }
                        //console.log("..."+data);
                    },
                    data: JSON.stringify(json)
                });
            });

                $(document).ready(function () {
                    console.log(selectedLeaveCode)
                    dtable.draw();
                });

	</script>
</html>`