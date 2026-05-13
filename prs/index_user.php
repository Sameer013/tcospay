<?php
    session_start();
    if ((!isset($_SESSION['user'])))
    {
        header('refresh: 1;url=login.php');
        die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
    }
    ?>


<?php
    include('includes/dbconn.php');
    $stmt = $db->prepare("SELECT UID, UNAME, PASSWD from usertb");
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
        <title>Payroll - User Manager</title>
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
            $amenu="administrative";
            $page="user";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "Administrative Tools";
                $currentPage="User Manager";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mb-2 mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    User Manager
                </h2>
            </div>
            <button class="btn btn-primary shadow-md mr-2 rounded-full w-20"
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
                                                <th ></th>
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
                                                  Add
                                                </h2>
                                            </div>
                                            <hr class="border-black">
                                            <!-- END: Modal Header -->
                                            <!-- BEGIN: Modal Body -->
											<form id="frm_user" name="frm_user" action="" method="post">
                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <input id="txt_uno" name="txt_uno" type="hidden" class="form-control rounded-full" placeholder="Uno" readonly>
                                                <!-- <div class="col-span-12 sm:col-span-6">
                                                        <label class="form-label">Login ID:*</label>
                                                        <input type="text" class="form-control rounded-pill text-dark" name="txt_UID" id="txt_UID" placeholder="Login ID" />
                                                </div> -->
                                                <div class="col-span-12 sm:col-span-6">
                                                        <label class="form-label">User Name: *</label>
                                                        <input type="text" class="form-control rounded-pill text-dark" name="txt_UNAME" id="txt_UNAME" placeholder="User name" />
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                        <label class="form-label">Password: *</label>
                                                        <input type="text" class="form-control rounded-pill text-dark" name="txt_PASSWD" id="txt_PASSWD" placeholder="Password" />
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="userType" class="form-label">User Type</label>
                                                    <select name="userType" id="userType" class="form-control Tom-select">
                                                    <!-- <option value="a1">Manager</option>
                                                    <option value="a2">Accounts</option> -->
                                                    <option value="a3">Admin</option>
                                                    <!-- <option value="">Super Admin</option> -->
                                                   </select>
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
    "ajax": {
        url: "ajax_user.php",
        type: "POST",
    },
    "columns": [

        {
            "data": null,
            "render": function (data, type, row) {
                return `
                    <div class="intro-y col-span-12 md:col-span-6">
                        <div class="box">
                            <div class="flex flex-col lg:flex-row items-center p-5">
                                <div class="w-20 h-20 lg:w-12 lg:h-12 image-fit lg:mr-1">
                                    <img alt="Midone" class="rounded-full" src="dist/images/blank.png">
                                </div>
                                <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                                    <a href="" class="font-medium">${row.UNAME}</a>
                                    <div class="text-slate-500 text-xs mt-0.5">${row.role_id === 'a1' ? 'Manager' : (row.role_id === 'a2' ? 'Account' : (row.role_id === 'a3' ? 'Admin' : ''))}</div>
                                </div>
                                <div class="flex mt-4 lg:mt-0">
                                    <button class="btn btn-primary py-1 px-2 mr-2">${row.edit}</button>
                                    <button class="btn btn-secondary py-1 px-2">${row.delete}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    ],
    "order": [0, "asc"],
});

                function load_data(UID) {
                    $("#btn_save").hide();
                    $("#btn_update").show();
                    $.ajax({
                        url: '../prsApi/usertb/'+UID,
                        method: "GET",
                        success:function(res){
                            $("#txt_uno").val(res.UID);
                            $("#txt_UNAME").val(res.UNAME);
                            $("#txt_PASSWD").val(res.PASSWD);
                            $("#txt_userType").val(res.role_id);

                        },
                        error: function(xhr, textStatus, errorThrown) {
                            console.error("Error loading data: " + errorThrown);
                        }
                    });
                }
               $("#btn_update" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                var UID=$("#txt_uno").val();
                $.ajax({
                    url: '../prsApi/usertb/'+ UID,
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

            function remove_data(UID) {
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
                            url: '../prsApi/usertb/' + UID,
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

            $("#btn_save" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                $.ajax({
                    url: '../prsApi/usertb',
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


	</script>
</html>