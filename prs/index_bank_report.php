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
    <title>Payroll - Report</title>
    <!-- BEGIN: CSS Assets-->
    <!-- <script src="https://cdn.tailwindcss.com"></script>  -->
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
        $amenu = "statement";
        $page = "report";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Statement";
            $currentPage = "Report";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Report
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
                                    <div class="grid grid-cols-12 text-dark mt-6">
                                        <label class="col-span-1 mb-5 align-self-center flex items-center" for="relation">For Month: </label>
                                        <div class="col-span-1 mb-5">
                                            <select class="form-select form-select-lg text-dark rounded-none border-cyan-600" name="month" placeholder="month">
                                                <option value="" selected="true" disabled>Select</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                        <div class="col-span-1"></div>

                                        <label class="col-span-1 mb-5 align-self-center flex items-center" for="relation">For Year: </label>
                                        <div class="col-span-1 mb-5">
                                            <select class="form-select form-select-lg text-dark rounded-none border-cyan-600" name="year" placeholder="year">
                                                <option value="" selected="true" disabled>Select</option>
                                                <?php
                                                $currentYear = date("Y");
                                                for ($i = $currentYear; $i >= $currentYear - 2; $i--) {
                                                    echo "<option value=\"$i\">$i</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-span-1"></div>
                                        <div class="col-span-1 mb-5 ml-5">
                                            <button type="button" class="btn btn-outline-primary mx-4 rounded-full w-20">Open</button>
                                        </div>
                                    </div>

                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-6 mt-6">
                                        <div class="form-group">
                                            <label class="form-label">Bank Reports</label>

                                            <div class="grid space-y-2">
                                                <?php
                                                if ($_SESSION['user'] == 'Admin') {
                                                ?>
                                                    <label class="max-w-xs mb-2 flex p-3 block w-64 bg-white border border-cyan-600 rounded-none text-sm">
                                                        <input type="radio" name="select" id="flexRadioDefault4" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none border-gray-700 checked:bg-blue-500 checked:border-blue-500">
                                                        <label for="flexRadioDefault4" class="form-check-label ml-2">Letter</label>
                                                    </label>
                                                    <label class="max-w-xs mb-2 flex p-3 block w-64 bg-white border border-cyan-600 rounded-none text-sm">
                                                        <input type="radio" name="select" id="flexRadioDefault6" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none border-gray-700 checked:bg-blue-500 checked:border-blue-500">
                                                        <label for="flexRadioDefault6" class="form-check-label ml-2">For Bank</label>
                                                    </label>
                                                    <label class="max-w-xs mb-2 flex p-3 block w-64 bg-white border border-cyan-600 rounded-none text-sm">
                                                        <input type="radio" name="select" id="flexRadioDefault5" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none border-gray-700 checked:bg-blue-500 checked:border-blue-500">
                                                        <label for="flexRadioDefault5" class="form-check-label ml-2">Bank Software</label>
                                                    </label>

                                                    <!-- <label for="flexRadioDefault8" class="max-w-xs mb-2 flex p-3 block w-64 bg-white border border-cyan-600 rounded-none text-sm">
                                                        <input type="radio" name="select" id="flexRadioDefault8" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none border-gray-700 checked:bg-blue-500 checked:border-blue-500">
                                                        <label for="flexRadioDefault8" class="form-check-label ml-2">Statement</label>
                                                    </label> -->
                                                <?php
                                                }
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>

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
    $(document).ready(function() {
        $('button[type="button"]').click(function() {
            var selectedRadio = $('input[name="select"]:checked').next('label').text().trim();
            var selectedMonth = $('select[name="month"]').val();
            var selectedYear = $('select[name="year"]').val();
            if (!selectedMonth) {
                alert('Please select a month and year');
                return;
            }
            var pageMapping = {
                'Leave Record': 'leaveRecord.php',
                'Salary Sheet': 'paySlipStatement.php',
                'Pay Slip': 'paySlip.php',
                'PF': 'pf.php',
                'ESIC': 'esi.php',
                'Letter': 'letter.php',
                'For Bank': 'forBank.php',
                'Bank Software': 'bankSoftware.php',
                'Statement': 'statement.php',
                'Salary Data': 'salaryData.php',

            };

            var pageUrl = pageMapping[selectedRadio];

            if (pageUrl) {
                var redirectUrl = pageUrl + '?month=' + selectedMonth + '&year=' + selectedYear;
                window.open(redirectUrl, '_blank');
            } else {
                alert('Please select a report option');
            }
        });
    });
</script>

</html>