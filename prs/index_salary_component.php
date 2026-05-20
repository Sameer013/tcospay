<?php
session_start();
if ((!isset($_SESSION['user']))) {
    header('refresh: 1;url=login.php');
    die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
}
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8">
    <link href="dist/images/logo.svg" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Payroll salary component master">
    <meta name="keywords" content="payroll, salary component master">
    <meta name="author" content="TCOS">
    <title>Payroll - Salary Component Master</title>
    <link rel="stylesheet" href="dist/css/app.css" />
    <link rel="stylesheet" href="dist/css/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
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
</style>

<body class="py-0">
    <?php include 'mob.php' ?>
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
        $amenu = "general";
        $page = "salary_component";
        include 'nav.php'
        ?>
        <div class="content content--top-nav">
            <?php
            $menu_title = "General";
            $currentPage = "Salary Component Master";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">Salary Component Master</h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="intro-y col-span-12 lg:col-span-12">
                    <button class="btn btn-primary w-20 shadow-md mr-2 rounded-full" onclick="add_new()"
                        data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview-view">Add</button>
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <table id="table" class="table table-bordered table-striped" style="width:100%"
                                        cellpadding="7px">
                                        <thead>
                                            <tr>
                                                <th width="10%">ID</th>
                                                <th width="20%">Code</th>
                                                <th width="50%">Description</th>
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

        <div class="intro-y box mt-5 hidden">
            <div id="header-footer-modal" class="p-5">
                <div class="preview">
                    <div id="header-footer-modal-preview-view" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="font-bold text-base mr-auto">Add / Edit</h2>
                                </div>
                                <hr class="border-black">
                                <form id="frm_user" name="frm_user" action="" method="post">
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                        <input id="txt_id" name="txt_id" type="hidden" class="form-control">
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_code" class="form-label">Code</label>
                                            <input id="txt_code" name="txt_code" type="text" maxlength="10"
                                                class="form-control" placeholder="Code">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_descr" class="form-label">Description</label>
                                            <input id="txt_descr" name="txt_descr" type="text" maxlength="50"
                                                class="form-control" placeholder="Description">
                                        </div>
                                    </div>
                                </form>
                                <div class="modal-footer">
                                    <button type="button" data-tw-dismiss="modal"
                                        class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                    <button id="btn_save" data-tw-dismiss="modal"
                                        class="btn btn-primary w-20 rounded-full">Save</button>
                                    <button id="btn_update" data-tw-dismiss="modal"
                                        class="btn btn-primary w-20 rounded-full">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="dist/js/app.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="dist/js/sweetalert2.min.js"></script>
    </div>
    <?php include 'footer.php'; ?>
</body>

<script>
var dtable = $('#table').DataTable({
    buttons: ['copy', 'excel', 'pdf'],
    processing: true,
    searching: true,
    serverSide: true,
    ajax: "ajax_salary_component.php",
    columns: [
        { data: "id" },
        { data: "code" },
        { data: "descr" },
        { data: "action", orderable: false, searchable: false }
    ],
    order: [0, "asc"],
});

function convertFormToJSON(form) {
    const array = $(form).serializeArray();
    const json = {};
    $.each(array, function() {
        let key = this.name;
        key = key.substring(key.indexOf("_") + 1);
        json[key] = this.value || "";
    });
    return json;
}

function add_new() {
    $("#btn_save").show();
    $("#btn_update").hide();
    $('#frm_user').trigger("reset");
}

function remove_data(id) {
    Swal.fire({
        title: 'Are you sure to Delete?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (!result.isConfirmed) {
            Swal.close();
            return;
        }

        $.ajax({
            url: '../prsApi/salaryComponent/' + id,
            type: 'DELETE',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status === "Ok") {
                    Swal.fire({ title: data.msg, icon: 'success' }).then(() => dtable.draw());
                } else {
                    Swal.fire(data.msg, '', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred while deleting the data.', 'error');
            }
        });
    });
}

$("#btn_save").on("click", function() {
    const json = convertFormToJSON($("#frm_user"));
    $.ajax({
        url: '../prsApi/salaryComponent',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(json),
        success: function(data) {
            if (data.status === "Ok") {
                dtable.draw();
            } else {
                Swal.fire(data.msg, '', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'An error occurred while saving the data.', 'error');
        }
    });
});

$("#btn_update").on("click", function() {
    const json = convertFormToJSON($("#frm_user"));
    const id = $("#txt_id").val();
    $.ajax({
        url: '../prsApi/salaryComponent/' + id,
        type: 'PUT',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(json),
        success: function(data) {
            if (data.status === "Ok") {
                dtable.draw();
            } else {
                Swal.fire(data.msg, '', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'An error occurred while updating the data.', 'error');
        }
    });
});

function load_data(id) {
    $("#btn_save").hide();
    $("#btn_update").show();
    $.ajax({
        url: '../prsApi/salaryComponent/' + id,
        method: "GET",
        success: function(res) {
            $("#txt_id").val(res.id);
            $("#txt_code").val(res.code);
            $("#txt_descr").val(res.descr);
        },
        error: function() {
            Swal.fire('Error', 'Unable to load the selected component.', 'error');
        }
    });
}
</script>

</html>
