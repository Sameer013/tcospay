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
        <title>Payroll - Repair</title>
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
            $amenu="payroll";
            $page="repair";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "Payroll";
                $currentPage="Repair";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Repair
                </h2>
            </div>

<div class="mx-auto p-4" style="margin: 0;">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- <div class="flex justify-between">
            <div class="text-lg font-semibold">
                <h4 class="text-2xl font-bold">Employees Master</h4>
            </div>
        </div> -->

        <form id="form-wizard1" class="mt-2 p-8 text-left">
            <!-- fieldsets -->
            <fieldset>
                <div class="form-card">
                    <div class="container">
                        <!-- <div class="grid grid-cols-12">
                            <div class="col-span-6">
                                <h4 class="text-xl font-semibold mb-4">Employees Entry Form</h4>
                            </div>
                        </div> -->


                        <div class="grid grid-cols-12 text-dark mt-5">
                            <label class="col-span-2 align-self-center mb-5  flex items-center" for="txt_emp_no">Last Transaction No : </label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_emp_no" name="txt_emp_no" placeholder="Enter Last Transaction No" Readonly>
                            </div>
                        </div>

                        <div class="form-card col-span-11 text-start mt-5">
                            <div class="grid grid-cols-11 text-dark">
                                <!-- Allowances -->
                                <div class="col-span-11 mb-5">
                                    <p class="text-xl font-semibold mb-4">Transactions</p>
                                    <table id="table1" class="table table-bordered table-striped border-dark text-dark">
                                        <thead>
                                            <tr>
                                                <th width="5">Sl No</th>
                                                <th width="15">Description</th>
                                                <th width="15">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>00</td>
                                                <td>abc</td>
                                                <td>00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <center>
                        <div class="form-card col-span-11 text-start mt-5">
                            <div class="grid grid-cols-11 text-dark">
                                <!-- Allowances -->
                                <div class="col-span-5 mb-5">
                                    <p class="text-xl font-semibold mb-4">Allowances</p>
                                    <table id="table1" class="table table-bordered table-striped border-dark text-dark">
                                        <thead>
                                            <tr>
                                                <th class="w-1/6">Sl No</th>
                                                <th class="w-2/6">Description</th>
                                                <th class="w-2/6">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>00</td>
                                                <td>abc</td>
                                                <td>00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-span-1"></div>
                                <!-- Deductions -->
                                <div class="col-span-5 mb-5">
                                    <p class="text-xl font-semibold mb-4">Deductions</p>
                                    <table id="table2" class="table table-bordered table-striped border-dark text-dark">
                                        <thead>
                                            <tr>
                                                <th class="w-1/6">Sl No</th>
                                                <th class="w-2/6">Description</th>
                                                <th class="w-2/6">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>00</td>
                                                <td>abc</td>
                                                <td>00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </center>


                    </div>
                </div>
            </fieldset>
        </form>
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


            function remove_data(dcode) {
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
                            url: '../prsApi/degination/' + dcode,
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
                   var currentPageNumber = 0;
                    function updateDataTableLocally(dcode, newData) {
                        var pageInfo = dtable.page.info();
                        currentPageNumber = pageInfo.page;
                        var rowIndex = dtable.row('#' + dcode).index();
                        var cellIndex = 1;
                        dtable.cell({ row: rowIndex, column: cellIndex }).data(newData);
                        dtable.draw(false);
                    }

            $("#btn_update" ).on( "click", function() {
                const form = $("#frm_user");
                const json = convertFormToJSON(form);
                console.log(json);
                var dcode=$("#txt_dcode").val();
                $.ajax({
                    url: '../prsApi/degination/'+dcode,
                    type: 'PUT',
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function (data) {
                        if (data.status=="Ok") {
                            $("#header-footer-modal-preview").hide();
                            updateDataTableLocally(dcode, json);

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
                    url: '../prsApi/degination',
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


            function load_data(dcode){
                $("#btn_save").hide();
                $("#btn_update").show();
                $.ajax({
                    url:'../prsApi/degination/'+dcode,
                    method:"GET",
                    success:function(res){
                        $("#txt_dcode").val(res.DCODE);
                        $("#txt_descr").val(res.DESCR);
                        $("#txt_totalLeave").val(res.NODAYS);
                        $("#txt_leave").val(res.DAYSPERTIME);
                        $("#txt_catgr").val(res.CATGR);
                        $("#txt_disporder").val(res.DISPORDER);
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