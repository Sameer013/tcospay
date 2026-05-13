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
    <title>Payroll - Leave Application</title>
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
        $page = "leaveApp";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Transaction";
            $currentPage = "Leave Application";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Leave Application
                </h2>
            </div>
            <button class="btn btn-primary w-60 shadow-md mr-2 mt-5 rounded-full"
                onclick="window.open('leaveForm.php', '_blank');">Download Leave Application
            </button>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12 ">
                    <!-- BEGIN: Responsive Table -->
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <table id="table" class="table table-bordered table-striped w-full" cellpadding="7px">
                                        <thead>
                                            <tr>
                                                <th class="w-1/12">Date</th>
                                                <th class="w-2/12">Emp Name</th>
                                                <th class="w-1/12">From Date</th>
                                                <th class="w-1/12">To Date</th>
                                                <th class="w-1/12">Leave Type</th>
                                                <th class="w-1/12">No of Days</th>
                                                <th class="w-1/12">Reason</th>
                                                <th class="w-1/12">Document</th>
                                                <th class="w-1/12">Status</th>
                                                <th class="w-1/12">Action</th>
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
                                        Leave Application
                                    </h2>
                                </div>
                                <hr class="border-black">
                                <!-- END: Modal Header -->
                                <!-- BEGIN: Modal Body -->
                                <form id="frm_user" name="frm_user" action="" method="post">
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                        <input id="txt_slno" name="txt_slno" type="hidden" class="form-control rounded-none" placeholder="slno" readonly>

                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_empName" class="form-label">Emp Name</label>
                                            <input id="txt_empName" name="txt_empName" type="text" class="form-control rounded-none" placeholder="Name">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_date" class="form-label">Date</label>
                                            <input id="txt_date" name="txt_date" type="date" class="form-control rounded-none" placeholder="Date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_fdate" class="form-label">From Date</label>
                                            <input id="txt_fdate" name="txt_fdate" type="date" class="form-control rounded-none" placeholder="Date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_tdate" class="form-label">To Date</label>
                                            <input id="txt_tdate" name="txt_tdate" type="date" class="form-control rounded-none" placeholder="Date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_desc" class="form-label">Reason</label>
                                            <input id="txt_desc" name="txt_desc" type="text" class="form-control rounded-none" placeholder="Description">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_ltype" class="form-label">Leave Type</label>
                                            <input id="txt_ltype" name="txt_ltype" type="text" class="form-control rounded-none" placeholder="Leave Type">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_nod" class="form-label">NO of Days</label>
                                            <input id="txt_nod" name="txt_nod" type="text" class="form-control rounded-none" placeholder="NO of Days">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_status" class="form-label">Status</label>
                                            <select id="txt_status" name="txt_status" class="form-control rounded-none">
                                                <option value="">-- Select Status --</option>
                                                <option value="Approve">Approve</option>
                                                <option value="Reject">Reject</option>
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
    var userId = <?php echo json_encode(isset($_SESSION['user']) ? $_SESSION['user'] : null); ?>;
    var isAdmin = userId === 'Admin';
    var dtable = $('#table').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "searching": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax_leaveApplication.php",
        },
        "columns": [{
                "data": "dte"
            },
            {
                "data": "name"
            },
            {
                "data": "fdate"
            },
            {
                "data": "tdate"
            },
            {
                "data": "ltype"
            },
            {
                "data": "nol"
            },
            {
                "data": "descr"
            },
            {
                "data": "document",
                "render": function(data, type, row) {
                    return data ? data : 'No Document';
                }
            },
            {
                "data": "status",
                "render": function(data, type, row) {
                    return data === null || data === "" ? "Pending" : data;
                }
            },
            isAdmin ? {
                "data": "action"
            } : {
                "data": "action",
                "visible": false
            }
        ],
        "order": [0, "desc"],
    });

    function add_new() {
        $("#btn_save").show();
        $("#btn_update").hide();
        $("#txt_id").removeAttr("readonly");
        $('#frm_user').trigger("reset");
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

    function load_data(empno) {
        $("#btn_save").hide();
        $("#btn_update").show();
        $.ajax({
            url: '../prsApi/leaveapply/' + empno,
            method: "GET",
            success: function(res) {
                const data = typeof res === "string" ? JSON.parse(res) : res;
                //console.log(data.Name);

                // Assign values to fields, ensuring the keys match the response
               // $("#txt_empno").val(data.EMPNO);
                $("#txt_slno").val(data.slno);
                $("#txt_empName").val(data.Name);
                $("#txt_date").val(data.DTE);
                $("#txt_fdate").val(data.FDATE);
                $("#txt_tdate").val(data.TDATE);
                $("#txt_desc").val(data.DESCR);
                $("#txt_ltype").val(data.LTYPE);
                $("#txt_nod").val(data.NOOFDAYS);
                // $("#txt_status").val(res.status);
            }
        });
    }
    $("#btn_update").on("click", function() {
        const form = $("#frm_user");
        const json = convertFormToJSON(form);
        console.log(json);
        let empno = $("#txt_slno").val();
        $.ajax({
            url: '../prsApi/leaveapply/' + empno,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    $("#header-footer-modal-preview").hide();
                    dtable.draw();
                }
                //console.log("..."+data);
            },
            data: JSON.stringify(json)
        });
    });

    function remove_data(empno) {
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
                    url: '../prsApi/advpayment/' + empno,
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
</script>

</html>