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
        <title>Payroll - Attendance Machine</title>
        <!-- BEGIN: CSS Assets-->
        <script src="https://cdn.tailwindcss.com"></script>
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
            $amenu="transaction";
            $page="attmac";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
    <?php
        $menu_title = "Transaction";
        $currentPage = "Attendance Machine";
        include 'top.php';
    ?>
    
    <!-- Header -->
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Attendance Machine</h2>
    </div>

    <hr class="border border-primary border-3 opacity-75 mt-2 shadow mb-6 rounded">

    <!-- Company and Last Working Day -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 text-dark mb-6">
        <label class="md:col-span-2 flex items-center">Company:</label>
        <div class="md:col-span-3">
            <input type="text" class="input-box w-full" id="txt_dsgcode" name="txt_dsgcode" placeholder="Enter Company">
        </div>

        <label class="md:col-span-2 flex items-center">Last Working Day:</label>
        <div class="md:col-span-3">
            <input type="date" class="input-box w-full" id="txt_dob" name="txt_dob">
        </div>
    </div>

    <!-- Date and Report Selection -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 text-dark mb-6">
        <label class="md:col-span-2 flex items-center">Date:</label>
        <div class="md:col-span-3">
            <input type="date" class="input-box w-full" id="txt_date" name="txt_date">
        </div>

        <div class="md:col-span-5">
            <select class="p-2 text-dark w-full border rounded-md" id="attendence_report" name="attendence_report" onchange="updateRedirect()">
                <option value="" selected disabled>-- Select --</option>
                <option value="Daily attendence">Daily attendance report - I</option>
                <option value="">Daily attendance report - II</option>
                <option value="">Daily attendance report - III</option>
                <option value="">Monthly attendance report - I</option>
                <option value="">Monthly attendance report - II</option>
                <option value="Daily absent">Daily absent report</option>
                <option value="">Monthly attendance summary</option>
            </select>
        </div>

        <div class="md:col-span-3 flex items-center">
            <button type="submit" class="btn btn-outline-primary w-full md:w-40 rounded-full" onclick="redirectToPage()">Show</button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="intro-y box mx-auto p-4">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <form id="form-wizard1" class="mt-2">
                <fieldset>
                    <div class="form-card">
                        <div class="form-card mt-5">
                            <p class="text-lg font-semibold mb-4">Blank</p>
                            <div class="overflow-x-auto">
                                <table id="table1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="15%"></th>
                                            <th width="15%"></th>
                                            <th width="15%"></th>
                                            <th width="15%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
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
  function updateRedirect() {
        var selectElement = document.getElementById("attendence_report");
        var selectedValue = selectElement.options[selectElement.selectedIndex].value;

        if (selectedValue === "Daily attendence") {
            document.getElementById("form-wizard1").action = "attendence_report.php";
        } else if (selectedValue === "Daily absent") {
            document.getElementById("form-wizard1").action = "absent_report.php";
        } else {
            document.getElementById("form-wizard1").action = "default_page.html";
        }
    }

    function redirectToPage() {
        var selectedDate = document.getElementById("txt_date").value;
        var redirectUrl = document.getElementById("form-wizard1").action + "?selected_date=" + encodeURIComponent(selectedDate);
        window.location.href = redirectUrl;
    }
</script>
</html>