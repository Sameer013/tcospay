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
$stmt = $db->prepare("SELECT `TRNSNO`,`DESCR`,`AMOUNT` FROM `trnsdet1`");
$stmt->execute();
$result = $stmt->fetchAll();
$stmt2 = $db->prepare("SELECT EMPNO,NAME from empmast");
$stmt2->execute();
$empnames = $stmt2->fetchAll();
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
        <title>Payroll - Payroll Generation</title>
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
        .grid-cols-11 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    #table1  th, #table1 td, #table2 th, #table2 td {
        font-size: 0.875rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .align-right {
        float: right;
    }

    .wd-200 {
        width: 240px;
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
            $page="paygen";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "Payroll";
                $currentPage="Payroll Generation";
                include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Payroll Generation
                </h2>
            </div>
          <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="intro-y col-span-12 lg:col-span-12">
                    <div class="grid grid-cols-12 text-dark mt-6">
                        <label class="col-span-1 mb-5 align-self-center flex items-center" for="relation">For Month: </label>
                        <div class="col-span-2 mb-5">
                            <select class="w-full text-dark" name="month" id="selectMonth" placeholder="Status">
                                <option value="" selected="true" disabled >Select</option>
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
                        <div class="col-span-2 mb-5">
                            <select class="w-full text-dark" name="year" id="selectYear" placeholder="Status">
                                   <?php
                                    $currentYear = date("Y");
                                    for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                    ?>
                                </select>
                        </div>
                        <div class="col-span-1"></div>

                        <div class='col-span-4 mb-5'>
                            <button type="button" id="btn_filter" class="btn btn-outline-primary rounded-full w-50" onclick="load_data(currentIndex)" disabled>Get Data</button>
                        </div>

                        <div class="align-right" id="searchdiv" style="display: none;">
                            <select name="empname" id="empname" class="wd-200 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full tom-select">
                                <option>--Select Employee--</option>
                                <?php foreach ($empnames as $empname) { ?>
                                    <option value="<?= $empname['EMPNO'] ?>"><?= $empname['EMPNO'] . ' - ' . $empname['NAME'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
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

                        <div class="grid grid-cols-12 text-dark">
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_payslip">Pay Slip No: </label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_payslip" name="txt_payslip" placeholder="Enter Pay Slip No" >
                            </div>
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_empno">Emp No:</label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_empno" name="txt_empno" placeholder="Enter Employee No." >
                            </div>
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_date">Date:</label>
                            <div class="col-span-2 mb-5">
                                <input type="date" class="p-2 text-dark w-full" id="txt_date" name="txt_date"  >
                            </div>
                        </div>

                        <div class="grid grid-cols-12 text-dark">
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_name">Emp Name: </label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_name" name="txt_name" placeholder="Enter Emp Name" >
                            </div>
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_basic">Basic Pay:</label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_basic" name="txt_basic" placeholder="Enter Basic Pay" >
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
                                            <th class="w-1/4 text-sm py-2">Sl No</th>
                                            <th class="w-1/2 text-sm py-2">Description</th>
                                            <th class="w-1/4 text-sm py-2">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table1">
                                        <!-- Your table rows go here -->
                                    </tbody>
                                </table>
                            </div>

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
                                             <tbody id="table2">

                                             </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </center>

                        <div class="grid grid-cols-12 text-dark mt-6">
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_gross">Gross: </label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_gross" name="txt_gross" placeholder="Enter Gross Amount" >
                            </div>
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_adj">Adjustment: </label>
                            <div class="col-span-2 mb-5">
                                <input type="text" class="p-2 text-dark w-full" id="txt_adj" name="txt_adj" placeholder="Enter Adjustment" >
                            </div>
                            <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_comp_id">Salary:</label>
                            <div class="col-span-2 mb-5">
                            <input type="text" class="p-2 text-dark w-full" id="txt_salary" name="txt_salary" placeholder="Enter Salary" >
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

                        <center>
                             <div class="form-group mt-6" id="navigationButtons" style="display: none;">
                                <button type="button" class="prevBtn btn mx-4 btn-outline-primary rounded-full w-20" value="Submit">Prev</button>
                                <button type="button" class="nextBtn btn mx-4 btn-outline-primary rounded-full w-20" value="Submit">Next</button>
                            </div>
                        </center>
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
  let currentIndex = 0;
document.getElementById('selectMonth').addEventListener('change', updateGetDataButtonStatus);
document.getElementById('selectYear').addEventListener('change', updateGetDataButtonStatus);

function updateGetDataButtonStatus() {
    const monthValue = document.getElementById('selectMonth').value;
    const yearValue = document.getElementById('selectYear').value;
    const getDataButton = document.getElementById('btn_filter');
    getDataButton.disabled = !(monthValue && yearValue);
}

$(document).ready(function () {
    console.log('Initial currentIndex:', currentIndex);
    load_data(currentIndex);
 $("#navigationButtons").hide();


    $(".nextBtn").click(function () {
        currentIndex++;
        load_data(currentIndex);
    });

    $(".prevBtn").click(function () {
        currentIndex--;
        load_data(currentIndex);
    });
    $("#empname").change(function () {
        var empNo = $(this).val();
        console.log('Selected Employee No:', empNo);

        load_data_by_emp(empNo);
    });
});
function load_data_by_emp(empNo) {
    var selectedMonth = document.getElementById("selectMonth").value;
    var selectedYear = document.getElementById("selectYear").value;

    $.ajax({
        url: `ajax_generation.php?empNo=${empNo}&monthYear=${selectedMonth} ${selectedYear}`,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log('Data for selected employee:', response);
            if (response && typeof response === 'object' && Array.isArray(response.data) && response.data.length > 0) {
                const data = response.data[0];

                console.log('Data is present:', data);

                $("#txt_empno").val(data.EMPNO);
                $("#txt_payslip").val(data.TRNSNO);
                $("#txt_name").val(data.NAME);
                $("#txt_date").val(data.DTE);
                $("#txt_basic").val(data.basic);
                $("#txt_gross").val(data.gross);
                $("#txt_salary").val(data.MNTHSAL);
                $("#txt_adj").val(data.adj);

                var payslipNo = data.TRNSNO;
                console.log('payslipNo:', payslipNo);

                load_allowdata(payslipNo);
                load_dedctndata(payslipNo);

                updateButtonStatus(1);
            } else {
                updateButtonStatus(0);
            }
        },
        error: function (error) {
            console.error('Error fetching data:', error);
        }
    });
}
 function updateUrlWithEmpNo(empno) {
        var currentUrl = window.location.href;
        var newUrl;

        if (currentUrl.includes("empno=")) {
            newUrl = currentUrl.replace(/empno=\d+/, `empno=${empno}`);
        } else {
            if (currentUrl.includes("?")) {
                newUrl = currentUrl + `&empno=${empno}`;
            } else {
                newUrl = currentUrl + `?empno=${empno}`;
            }
        }

        window.history.pushState({
            path: newUrl
        }, "", newUrl);
    }

$("#btn_filter").on("click", function () {
    var selectedMonth = document.getElementById("selectMonth").value;
    var selectedYear = document.getElementById("selectYear").value;
    console.log(selectedMonth);
    console.log(selectedYear);
    $.ajax({
        url: `ajax_generation.php?monthYear=${selectedMonth} ${selectedYear}`,
        type: 'GET',
        dataType: 'json',
        contentType: 'application/json',
        success: function(data) {
            if (data.data && Array.isArray(data.data) && data.data.length === 0) {
                alert("No data available for the selected month and year. \nPlease select an available month and year.");
            } else {
                $("#navigationButtons").show();
                $("#searchdiv").show();
                load_data(currentIndex);
            }
        },
    });
});
function load_data(index) {
    let selectedMonth = $('#selectMonth').val();
    let selectedYear = $('#selectYear').val();

    if (selectedMonth && selectedYear) {
        let selectedMonthYear = selectedMonth + ' ' + selectedYear;

        $.ajax({
            url: 'ajax_generation.php?monthYear=' + selectedMonthYear,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log(response);

                if (response && typeof response === 'object' && Array.isArray(response.data)) {
                    console.log('Data array is present.');

                    (function (capturedIndex) {
                        if (capturedIndex >= 0 && capturedIndex < response.data.length) {
                            const data = response.data[capturedIndex];
                            console.log('Data is present:', data);

                            $("#txt_empno").val(data.EMPNO);
                            $("#txt_payslip").val(data.TRNSNO);
                            $("#txt_name").val(data.NAME);
                            $("#txt_date").val(data.DTE);
                            $("#txt_basic").val(data.basic);
                            $("#txt_gross").val(data.gross);
                            $("#txt_salary").val(data.MNTHSAL);
                            $("#txt_adj").val(data.adj);

                            var payslipNo = data.TRNSNO;
                            console.log('payslipNo:', payslipNo);

                            load_allowdata(payslipNo);
                            load_dedctndata(payslipNo);
                              updateUrlWithEmpNo(data.EMPNO);
                            updateButtonStatus(response.data.length);
                        } else {
                            console.error('Invalid index:', capturedIndex);
                        }
                    })(index);
                } else {
                    updateButtonStatus(0);
                }
            },
            error: function (error) {
                console.error('Error fetching data:', error);
                updateButtonStatus(0);
            }
        });
    }
}

function updateButtonStatus(dataLength) {
    if (currentIndex === 0) {
        $(".prevBtn").prop("disabled", true);
    } else {
        $(".prevBtn").prop("disabled", false);
    }

    if (currentIndex === dataLength - 1 || dataLength === 0) {
        $(".nextBtn").prop("disabled", true);
    } else {
        $(".nextBtn").prop("disabled", false);
    }
}



 function loadData(tableId, ajaxUrl) {
    var payslipNo = $("#txt_payslip").val();
    $.ajax({
        type: 'GET',
        url: ajaxUrl,
        data: { payslipNo: payslipNo },
        dataType: 'json',
        success: function (response) {
            var data = response.data;
            var tbody = $(tableId + ' tbody');
            tbody.empty();

            var slno = 1;

$.each(data, function (index, item) {
    var row = "<tr>" +
        "<td>" + slno + "</td>" +
        "<td>" + item.DESCR + "</td>" +
        "<td>" + item.AMOUNT + "</td>" +
        "</tr>";

    tbody.append(row);

    slno++;
});

        },
        error: function (error) {
            console.error('Error fetching data:', error);
        }
    });
}

function load_allowdata() {
    loadData("#table1", 'ajax_allotable.php');
}

function load_dedctndata() {
    loadData("#table2", 'ajax_dedtable.php');
}

</script>
</html>