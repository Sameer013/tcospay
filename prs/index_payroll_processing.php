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
    <title>Payroll - Payroll Processing</title>
    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="dist/css/app.css" />
    <link rel="stylesheet" href="dist/css/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <!-- END: CSS Assets-->
</head>
<!-- END: Head -->
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

<body class="py-0">
    <!-- BEGIN: Mobile Menu -->
    <?php include 'mob.php' ?>
    <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
        $amenu = "payroll";
        $page = "paypro";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Payroll";
            $currentPage = "Payroll Processing";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Pre-Payroll Processing
                </h2>
            </div>

            <div class="mx-auto p-4" style="margin: 0;">
                <div class="bg-white shadow-lg rounded-lg p-6">

                    <form id="form-wizard1" class="mt-2 p-8 text-left">
                        <!-- fieldsets -->
                        <fieldset>
                            <div class="form-card">
                                <div class="container">
                                    <div class="grid grid-cols-12 text-gray-800 mt-6 gap-4">
                                        <label class="col-span-2 flex items-center font-medium" for="month">For Month:</label>
                                        <div class="col-span-4">
                                            <select
                                                class="w-full text-gray-700 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                name="month"
                                                id="month"
                                                placeholder="Select"
                                                onchange="checkSelection()">
                                                <option value="" selected disabled>Select</option>
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
                                        <label class="col-span-2 flex items-center font-medium" for="year">For Year:</label>
                                        <div class="col-span-4">
                                            <select
                                                class="w-full text-gray-700 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                name="year"
                                                id="year"
                                                placeholder="Select"
                                                onchange="checkSelection()">
                                                <option value="" selected disabled>Select</option>
                                                <?php
                                                $currentYear = date("Y");
                                                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                                    echo "<option value=\"$i\">$i</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="progress rounded-full" style="margin: 10% 15%; height: 30px; width:70%">
                                        <div class="progress-bar rounded-full" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%
                                        </div>
                                    </div>


                                    <center>
                                        <div class="form-group mt-4 d-flex justify-content-center align-items-center">
                                            <button
                                                type="button"
                                                id="start_btn"
                                                class="btn btn-success rounded-pill px-4 py-2 shadow-lg disabled opacity-50 mr-6"
                                                disabled>
                                                Start
                                            </button>
                                            <button
                                                type="submit"
                                                id="cancel_btn"
                                                class="btn btn-danger rounded-pill px-4 py-2 shadow-lg ms-5">
                                                Cancel
                                            </button>
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
    include 'footer.php';  //footer
    ?>
</body>
<script>
    let inProgress = false;
    let lastSelectedMonth = '';
    let lastSelectedYear = '';

    function animateProgressBar() {
        const progressBar = document.querySelector('.progress-bar');
        var width = 0;
        var value = 0;
        const duration = 1000;

        const interval = 10;
        const increment = (100 / (duration / interval));

        const updateProgress = () => {
            width += increment;
            value += increment;
            progressBar.style.width = width + '%';
            progressBar.textContent = Math.round(value) + '%';
            if (width >= 100) {
                clearInterval(progressInterval);
                alert("Processing completed successfully!");
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
            }
        };

        const progressInterval = setInterval(updateProgress, interval);
    }

    function checkSelection() {
        var month = document.getElementById("month").value;
        var year = document.getElementById("year").value;
        document.getElementById("start_btn").disabled = !(month && year) || (month === lastSelectedMonth && year === lastSelectedYear) || inProgress;
    }

    $("#start_btn").on("click", function() {
        if (!inProgress) {
            inProgress = true;
            checkSelection();

            var selectedMonth = document.getElementById("month").value;
            var selectedYear = document.getElementById("year").value;

            $.ajax({
                url: `ajax_process.php?month=${selectedMonth}&year=${selectedYear}`,
                type: 'GET',
                dataType: 'json',
                contentType: 'application/json',
                success: function(data) {
                    if (data.status === "SheetNotAvailable") {
                        alert("No data available for the selected month and year. \nPlease select an available month and year.");
                        document.getElementById("start_btn").disabled = false;
                        inProgress = false;
                        checkSelection();
                    } else if (data.status === "DataExists") {
                        var confirmRegenerate = confirm("Data is already available for the selected month and year. Do you want to regenerate?");
                        console.log("User clicked: ", confirmRegenerate);

                        if (confirmRegenerate) {
                            console.log("User confirmed regeneration");
                            $.ajax({
                                url: `ajax_processing.php?month=${selectedMonth}&year=${selectedYear}`,
                                type: 'GET',
                                dataType: 'json',
                                contentType: 'application/json',
                                success: function(preprocessingData) {
                                    console.log("Preprocessing exists AJAX Response:", preprocessingData);
                                    animateProgressBar();
                                },
                                error: function() {
                                    alert("Error in preprocessing AJAX request.");
                                },
                                complete: function() {
                                    document.getElementById("start_btn").disabled = false;
                                    lastSelectedMonth = selectedMonth;
                                    lastSelectedYear = selectedYear;
                                    checkSelection();
                                    inProgress = false;
                                }
                            });
                        } else {
                            console.log("User cancelled regeneration");
                            document.getElementById("start_btn").disabled = false;
                            inProgress = false;
                            checkSelection();
                        }
                    } else if (data.status === "NoData") {
                        $.ajax({
                            url: `ajax_processing.php?month=${selectedMonth}&year=${selectedYear}`,
                            type: 'GET',
                            dataType: 'json',
                            contentType: 'application/json',
                            success: function(processData) {
                                animateProgressBar();
                                console.log("Processing AJAX Response:", processData);
                            },
                            error: function() {
                                alert("Error in processing AJAX request.");
                            },
                            complete: function() {
                                document.getElementById("start_btn").disabled = false;
                                lastSelectedMonth = selectedMonth;
                                lastSelectedYear = selectedYear;
                                checkSelection();
                                inProgress = false;
                            }
                        });
                    }
                },
                error: function() {
                    alert("Error in checking sheet availability.");
                    document.getElementById("start_btn").disabled = false;
                    inProgress = false;
                    checkSelection();
                }
            });
        }
    });
</script>

</html>