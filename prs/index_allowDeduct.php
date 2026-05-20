<?php
session_start();
if ((!isset($_SESSION['user']))) {
    header('refresh: 1;url=login.php');
    die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
}
require_once 'includes/dbconn.php';
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
    <title>Payroll - Special Allowances/Deducations</title>
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
</style>


<!-- END: Head -->

<body class="py-0">
    <!-- BEGIN: Mobile Menu -->
    <?php include 'mob.php' ?>
    <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
        $amenu = "general";
        $page = "arrear";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "General";
            $currentPage = " Individual Allowance";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Individual Allowances/Deducations
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12">
                    <div class="intro-y box p-5">
                        <div class="grid grid-cols-12 gap-4 items-end">
                            <div class="col-span-12 md:col-span-5">
                                <label for="filter_empno" class="form-label">Employee</label>
                                <select name="filter_empno" id="filter_empno" class="form-control">
                                    <option value="" selected disabled>--Select Employee--</option>
                                    <?php
                                    $query = $db->query("SELECT EMPNO, NAME FROM empmast ORDER BY EMPNO");
                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                        $empno = $row['EMPNO'];
                                        $name = $row['NAME'];
                                        echo "<option value='$empno'>$empno - $name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-span-12 md:col-span-2">
                                <button class="btn btn-primary w-20 shadow-md rounded-full"
                                    onclick="return add_new()" data-tw-toggle="modal"
                                    data-tw-target="#header-footer-modal-preview-view">Add</button>
                            </div>
                        </div>
                    </div>
                    <!-- BEGIN: Responsive Table -->
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <table id="table" class="table table-bordered table-striped " style="width:100%" cellpadding="7px">
                                        <thead>
                                            <tr>
                                                <th width="5%">EMP No</th>
                                                <th width="10%">Name</th>
                                                <th width="10%">Description</th>
                                                <th width="10%">All. or Ded</th>
                                                <th width="10%">Value</th>
                                                <th width="15%">Percentage</th>
                                                <th width="10%">Still Valid</th>
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
                                        Add / Edit
                                    </h2>
                                </div>
                                <hr class="border-black">
                                <!-- END: Modal Header -->
                                <!-- BEGIN: Modal Body -->
                                <form id="frm_user" name="frm_user" action="" method="post">
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                        <input id="txt_code" name="txt_code" type="hidden" class="form-control" placeholder="CODE" readonly>

                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_empname" class="form-label">Employee Name</label>
                                            <select name="txt_empname" id="txt_empname" class="form-control">
                                                <option value="" selected disabled>--Select Employee Name--</option>
                                                <?php
                                                $query = $db->query("SELECT EMPNO, NAME FROM empmast ORDER BY EMPNO");
                                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                    $empno = $row['EMPNO'];
                                                    $name = $row['NAME'];
                                                    echo "<option value='$empno'>$empno - $name</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <!-- <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_desc" class="form-label">Description</label>
                                            <div class="flex flex-col sm:flex-row mt-0 mb-2">
                                                <input list="descOptions" name="txt_desc" id="txt_desc" class="form-control w-full" placeholder="Select or type description" style="width: 100%; height: 40px; padding: 10px;">
                                                <datalist id="descOptions">
                                                    <option value="SPL">
                                                    <option value="LSA">
                                                    <option value="Other">
                                                </datalist>
                                            </div>
                                        </div> -->
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_desc" class="form-label">Description</label>
                                            <div class="flex flex-col sm:flex-row mt-0 mb-2">
                                                <select name="txt_desc" id="txt_desc" class="form-control w-full" style="height: 40px; padding: 10px;">
                                                    <option value="" disabled selected>Select description</option>
                                                    <option value="SPL">SPL</option>
                                                    <option value="LSA">LSA</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Allowance or Deduction</label>
                                            <div class="flex flex-col sm:flex-row mt-2">
                                                <div class="form-check mr-2">
                                                    <input id="allowance" class="form-check-input border-4" type="radio" name="txt_allow" value="a" checked>
                                                    <label for="allowance" class="form-check-label">Allowance</label>
                                                </div>
                                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                                    <input id="deduction" class="form-check-input border-4" type="radio" name="txt_allow" value="d">
                                                    <label for="deduction" class="form-check-label">Deduction</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_value" class="form-label">Value</label>
                                            <input id="txt_value" name="txt_value" type="number" class="form-control" placeholder="Value">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="txt_flag" class="form-label">Percentage</label>
                                            <select id="txt_flag" name="txt_flag" class="form-control">
                                                <!-- <option value="" disabled selected>--Select--</option> -->
                                                <option value="">No</option>
                                                <option value="%">Yes</option>
                                            </select>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="form-label">Still Valid</label>
                                            <div class="flex flex-col sm:flex-row mt-2">
                                                <div class="form-check mr-2">
                                                    <input id="valid_true" class="form-check-input border-4" type="radio" name="txt_stillvalid" value="Y" checked>
                                                    <label for="valid_true" class="form-check-label">True</label>
                                                </div>
                                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                                    <input id="valid_false" class="form-check-input border-4" type="radio" name="txt_stillvalid" value="N">
                                                    <label for="valid_false" class="form-check-label">False</label>
                                                </div>
                                            </div>
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
    function getSelectedEmployee() {
        return $("#filter_empno").val() || "";
    }

    var dtable = $('#table').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "searching": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax_allowDeduct.php",
            "data": function(d) {
                d.empno = getSelectedEmployee();
            }
        },
        "columns": [{
                "data": "code"
            },
            {
                "data": "name"
            },
            {
                "data": "descr"
            },
            {
                "data": "allrednflag"
            },
            {
                "data": "allowance"
            },
            {
                "data": "prcamtflag"
            },
            {
                "data": "allredncountinuity"
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


    function remove_data(code, descr) {
        if (!getSelectedEmployee()) {
            Swal.fire('Select an employee first', '', 'warning');
            return;
        }
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
                    url: '../prsApi/indAllow/'  + descr + '/' + code,
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


    function add_new() {
        $("#btn_save").show();
        $("#btn_update").hide();
        $('#frm_user').trigger("reset");
        const empno = getSelectedEmployee();
        if (!empno) {
            Swal.fire('Select an employee first', 'Pick an employee from the filter before adding a record.', 'warning');
            return false;
        }
        $("#txt_code").val(empno);
        $("#txt_empname").val(empno);
        $("input[name='txt_allow'][value='a']").prop("checked", true);
        $("input[name='txt_stillvalid'][value='Y']").prop("checked", true);
        return true;
    }

    $("#btn_update").on("click", function() {
        const form = $("#frm_user");
        const json = convertFormToJSON(form);
        var code = $("#txt_code").val();
        var descr = $("#txt_desc").data("original-descr") || $("#txt_desc").val();
        $.ajax({
            url: '../prsApi/indAllow/' + descr + '/' + code,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    dtable.ajax.reload(null, false);
                } else {
                    Swal.fire(data.msg, '', 'error');
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                Swal.fire('Error', 'An error occurred while updating the data.', 'error');
            },
            data: JSON.stringify(json)
        });
    });

    $("#btn_save").on("click", function() {
        const empno = getSelectedEmployee();
        if (!empno) {
            Swal.fire('Select an employee first', 'Pick an employee from the filter before adding a record.', 'warning');
            return;
        }

        $("#txt_code").val(empno);
        $("#txt_empname").val(empno);

        const form = $("#frm_user");
        const json = convertFormToJSON(form);
        $.ajax({
            url: '../prsApi/indAllow',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            success: function(data) {
                if (data.status == "Ok") {
                    dtable.ajax.reload(null, false);
                } else {
                    Swal.fire(data.msg, '', 'error');
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                Swal.fire('Error', 'An error occurred while saving the data.', 'error');
            },
            data: JSON.stringify(json)
        });
    });


    function load_data(code, descr) {
        $("#btn_save").hide();
        $("#btn_update").show();
        $.ajax({
            url: '../prsApi/indAllow/' + descr + '/' + code,
            method: "GET",
            success: function(res) {
                $("#txt_code").val(res.EMPNO);
                $("#txt_empname").val(res.EMPNO);
                $("#txt_empname").prop("disabled", true);
                $("#txt_desc").val(res.DESCR).data("original-descr", res.DESCR);
                $("input[name='txt_allow'][value='" + res.ALLREDNFLAG + "']").prop("checked", true);
                $("#txt_value").val(res.ALLOWANCE);
                $("#txt_flag").val(res.PRCAMTFLAG);
                $("input[name='txt_stillvalid'][value='" + res.ALLREDNCONTINUITY + "']").prop("checked", true);
            },
            error: function() {
                Swal.fire('Error', 'Unable to load the selected record.', 'error');
            }
        });
    }

    $("#filter_empno").on("change", function() {
        dtable.ajax.reload();
    });

    $(document).ready(function() {
        dtable.draw();
    });

    $('#header-footer-modal-preview-view').on('show.tw.modal', function() {
        if ($("#btn_save").is(":visible")) {
            $("#txt_empname").prop("disabled", false);
        }
    });

    $('#header-footer-modal-preview-view').on('hidden.tw.modal', function() {
        $("#txt_empname").prop("disabled", false);
        $("#txt_desc").removeData("original-descr");
    });

    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#cimg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function populateOptions() {
        var typeSelect = document.getElementById("txt_type");
        var secondSelect = document.getElementById("load_option");

        secondSelect.innerHTML = "";

        var selectedType = typeSelect.value;
        if (selectedType === "all") {
            secondSelect.style.display = "none";
            return;
        } else {
            secondSelect.style.display = "block";
        }

        fetch("ajax_options.php?type=" + selectedType)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                data.forEach(function(option) {
                    var label, value;

                    if (selectedType === "category") {
                        label = option.DESCR;
                        value = option.DCODE;
                    } else if (selectedType === "individual") {
                        label = option.NAME;
                        value = option.EMPNO;
                    }

                    secondSelect.options.add(new Option(label, value));
                });
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }
</script>

</html>
