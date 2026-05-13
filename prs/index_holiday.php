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
        <title>Payroll - Holiday Master</title>
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
            $page="holiday_master";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "General";
                $currentPage="Holiday Master";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                  Holiday Master
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12 ">
                    <button class="btn btn-primary w-20 shadow-md mr-2 rounded-full"
                        onclick="add_new()" data-tw-toggle="modal"
                        data-tw-target="#header-footer-modal-preview-view">Add
                    </button>
                    <div class="grid grid-cols-12 text-dark mt-6">
                        <label class="col-span-1 mb-5 align-self-center flex items-center" for="month">For Month: </label>
                            <div class="col-span-1 mb-5">
                                <select class="w-full text-dark" name="month" id="month" placeholder="Select">
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

                            <label class="col-span-1 mb-5 ml-2 align-self-center flex items-center" for="year">For Year: </label>
                            <div class="col-span-1 mb-5">
                                <select class="w-full text-dark" name="year" id="year" placeholder="Select">
                                    <option value="" selected="true" disabled >Select</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-primary w-20 shadow-md mx-4 rounded-full" id="search">
                                    Search
                                </button>
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
                                                <th width="5%">S No.</th>
                                                <th width="10%">Date</th>
                                                <th width="25%">Holiday Description</th>
                                                <th width="20%">Category</th>
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
                                            <input id="txt_date1" name="txt_date1" type="hidden" class="form-control rounded-none" placeholder="Holiday Date" readonly>
                                            <input id="txt_category1" name="txt_category1" type="hidden" class="form-control rounded-none" placeholder="Category" readonly>


                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_date" class="form-label">Date</label>
                                                    <input id="txt_date" name="txt_date" type="date" class="form-control rounded-none" placeholder="Date">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_desc" class="form-label">Holiday Description</label>
                                                    <input id="txt_desc" name="txt_desc" type="text" class="form-control rounded-none" placeholder="Description">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="txt_category" class="form-label">Category</label>
                                                    <!-- <input id="txt_category" name="txt_category" type="text" class="form-control rounded-full" placeholder="Category"> -->
                                                    <select name="txt_category" id="txt_category" class="form-control">
                                                    <option value="" selected disabled>--Select Company--</option>
                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT catcode, descr FROM  category ");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $catcode = $row['catcode'];
                                                        $descr = $row['descr'];
                                                        echo "<option value='$catcode'>$descr</option>";
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
    var selectedMonth = document.getElementById("month").value;
    var selectedYear = document.getElementById("year").value;
    console.log(selectedMonth);
    console.log(selectedYear);
                var dtable = $('#table').DataTable({
                    buttons: ['copy', 'excel', 'pdf'],
                    "processing": true,
                    "searching": true,
                    "serverSide": true,
                     "ajax": {
                        "url": "ajax_holiday.php",
                    },
                    "columns": [
                         {

                            "render": function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        { "data": "HolidayDate" },
                        { "data": "HolidayDescription" },
                        { "data": "Category" },

                        { "data": "action" }
                    ],
                    "order": [0, "desc"],

                });
 $('#search').click(function()
            {
var selectedMonth = document.getElementById("month").value;
    var selectedYear = document.getElementById("year").value;
    console.log(selectedMonth);
    console.log(selectedYear);
    if ($.fn.DataTable.isDataTable('#table')) {
    $('#table').DataTable().destroy();
    }
     var dtable = $('#table').DataTable({
                    buttons: ['copy', 'excel', 'pdf'],
                    "processing": true,
                    "searching": true,
                    "serverSide": true,
                     "ajax": {
                        "url": "ajax_holiday.php",
                        "data": {
                            "month": selectedMonth,
                            "year": selectedYear
                        }
                    },
                    "columns": [
                         {

                            "render": function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        { "data": "HolidayDate" },
                        { "data": "HolidayDescription" },
                        { "data": "Category" },

                        { "data": "action" }
                    ],
                    "order": [0, "desc"],

                });
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


            function remove_data(date,category) {
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
                            url: '../prsApi/holidaymaster/'+ date + '/' + category,
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
                let date= $("#txt_date1").val();
                let category=$("#txt_category1").val();
                $.ajax({
                    url: '../prsApi/holidaymaster/'+ date + '/' + category,
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
                    url: '../prsApi/holidaymaster',
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


            function load_data(date,category){
                $("#btn_save").hide();
                $("#btn_update").show();
                $.ajax({
                    url:'../prsApi/holidaymaster/'+ date + '/' + category,
                    method:"GET",
                    success:function(res){
                        $("#txt_date").val(res.HolidayDate);
                        $("#txt_desc").val(res.HolidayDescription);
                        $("#txt_category").val(res.Category);
                        $("#txt_date1").val(res.HolidayDate);
                        $("#txt_category1").val(res.Category);
                    }
                });
            }

                $(document).ready(function () {
                    dtable.draw();
                });


	</script>
</html>