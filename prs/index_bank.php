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
        <title>Payroll - Bank Master</title>
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
            $page="bank_master";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "General";
                $currentPage="Bank Master";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                  Bank Master
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12 ">
                    <button class="btn btn-primary shadow-md mr-2 w-20 rounded-full"
                        onclick="add_new()" data-tw-toggle="modal"
                        data-tw-target="#header-footer-modal-preview-view">Add
                    </button>
                    <!-- BEGIN: Responsive Table -->
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
                               <div class="overflow-x-auto">
                                    <table id="table" class="table table-bordered table-striped " style="width:100%"  cellpadding="7px" >
                                            <thead>
                                            <tr>
                                                 <th width="5%">Bank ID</th>
                                                <th width="10%">Bank Name</th>
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
                                            <input id="txt_bid" name="txt_bid" type="hidden" class="form-control rounded" placeholder="BID" readonly>

                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_bankName" class="form-label">Bank Name</label>
                                                    <input id="txt_bankName" name="txt_bankName" type="text" class="form-control rounded" placeholder="Bank Name">
                                                </div>

                                            </div>
                                            <!-- END: Modal Body -->
											</form>
                                            <!-- BEGIN: Modal Footer -->
                                            <div class="modal-footer">
                                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                                <button id="btn_save" data-tw-dismiss="modal" class="btn btn-primary w-20 rounded-full" >Save</button>
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
                var dtable = $('#table').DataTable({
                    buttons: ['copy', 'excel', 'pdf'],
                    "processing": true,
                    "searching": true,
                    "serverSide": true,
                    "ajax": "ajax_bank.php",
                    "columns": [
                        { "data": "BID" },
                        { "data": "DESCR" },
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
                    if (result.isConfirmed) {
                        // Delete event
                        $.ajax({
                            url: '../prsApi/bankmaster/' + id,
                            type: 'DELETE',
                            dataType: 'json',
                            contentType: 'application/json',
                            success: function (data) {
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
                            error: function (xhr, textStatus, errorThrown) {
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
                    $("#txt_id").removeAttr("readonly");
                    $('#frm_user').trigger("reset");
                }

            $("#btn_update" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                var id=$("#txt_block_id").val();
                $.ajax({
                    url: '../prsApi/bankmaster/'+id,
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

            $("#btn_save" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                $.ajax({
                    url: '../prsApi/bankmaster',
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


            function load_data(id){
                $("#btn_save").hide();
                $("#btn_update").show();
                $.ajax({
                    url:'../prsApi/bankmaster/'+id,
                    method:"GET",
                    success:function(res){
                        $("#txt_bid").val(res.BID);
                        $("#txt_bankName").val(res.DESCR);

                    }
                });
            }

                $(document).ready(function () {
                    dtable.draw();
                });

                function displayImg(input,_this) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#cimg').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                   }
               }
	</script>
</html>