<?php
ini_set('display_errors', 0);
session_start();
if (!isset($_SESSION['user'])) {
    header('location:login.php');
    exit;
}
?>
<?php
include('includes/dbconn.php');
$stmt = $db->prepare("SELECT EMPNO,NAME from empmast");
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
    <meta name="description"
        content="Enigma admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Enigma Admin Template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="LEFT4CODE">
    <title>Payroll - General Master</title>
    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="dist/css/app.css" />
    <link rel="stylesheet" href="dist/css/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <!-- END: CSS Assets-->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
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

/* .input-box {
    width: 250px;
    height: 40px;
    padding: 10px;
    border: 1px solid #ccc;

} */

.align-right {
    float: right;
}

.wd-200 {
    width: 240px;
}

.label-left {
    text-align: right;
}

label {
    display: inline-block;
    width: 150px;
}

.image-center {
    text-align: center;
}

.image-center img {
    display: block;
    margin: 0 auto;
}

.image-button {
    position: relative;
    overflow: hidden;
}

.image-icon {
    display: block;
    width: 20%;
    padding-top: 20%;
    background-image: url('dist/images/camera.png');
    background-size: cover;
    background-position: center;
}

.image-button span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
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
        $page = "emp_master";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "General";
            $currentPage = "General Master";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    General Master
                </h2>
            </div>
            <?php
            if ($_SESSION['user'] == 'Admin') {
            ?>
            <button class="btn btn-primary shadow-md w-20 mr-2 rounded-full" onclick="add_new()" data-tw-toggle="modal"
                data-tw-target="#header-footer-modal-preview-general-view">Add
            </button>
            <?php
            }
            ?>
            <div class="align-right">
                <div>
                    <?php if ($_SESSION['user'] == 'Admin') : ?>
                    <select name="empname" id="empname"
                        class="wd-200 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full tom-select">
                        <option disabled>--Select Employee--</option>
                        <?php foreach ($empnames as $empname) : ?>
                        <option value="<?= $empname['EMPNO'] ?>"><?= $empname['EMPNO'] . ' - ' . $empname['NAME'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php else : ?>
                    <input type="hidden" id="empname" name="empname">
                    <?php endif; ?>
                </div>

            </div>

            <!-- BEGIN: Boxed Tab -->
            <div class="intro-y box mt-5">
                <div id="boxed-tab" class="p-5">
                    <div class="preview">
                        <ul class="nav nav-boxed-tabs flex flex-wrap md:flex-nowrap overflow-x-auto no-scrollbar gap-2 md:gap-4"
                            role="tablist">
                            <li id="general-tab" class="nav-item flex-1 border border-primary rounded-lg"
                                role="presentation">
                                <button class="nav-link w-full py-2 active" data-tw-toggle="pill"
                                    data-tw-target="#general-tab" type="button" role="tab" aria-controls="general-tab"
                                    aria-selected="true"> General </button>
                            </li>
                            <li id="finance-tab" class="nav-item flex-1 border border-primary rounded-lg"
                                role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#finance-tab"
                                    type="button" role="tab" aria-controls="finance-tab" aria-selected="false"> Finance
                                </button>
                            </li>
                            <!-- <li id="leave-tab" class="nav-item flex-1 border border-primary rounded-lg"
                                role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#leave-tab"
                                    type="button" role="tab" aria-controls="leave-tab" aria-selected="false"> Leave
                                </button>
                            </li>
                            <li id="loan-tab" class="nav-item flex-1 border border-primary rounded-lg"
                                role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#loan-tab"
                                    type="button" role="tab" aria-controls="loan-tab" aria-selected="false"> Loan
                                </button>
                            </li>
                            <li id="incentive-tab" class="nav-item flex-1 border border-primary rounded-lg"
                                role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill"
                                    data-tw-target="#incentive-tab" type="button" role="tab"
                                    aria-controls="incentive-tab" aria-selected="false"> Incentive </button>
                            </li> -->
                            <li id="other-tab" class="nav-item flex-1 border border-primary rounded-lg"
                                role="presentation">
                                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#other-tab"
                                    type="button" role="tab" aria-controls="other-tab" aria-selected="false"> Others
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-5">
                            <!--BEGIN: General tab ---->
                            <div id="general-tab" class="tab-pane leading-relaxed active" role="tabpanel"
                                aria-labelledby="general-tab">

                                    <!-- Employee Image -->
                                    <div class="w-full flex justify-center mb-6">
                                        <img id="employeeImage" class="h-[200px] w-[200px] rounded shadow" alt="Employee Photo" />
                                    </div>

                                    <!-- Form Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-dark">

                                        <div class="flex items-center">
                                            <label for="txt_EmpNo" class="w-1/3 font-medium">Emp No:</label>
                                            <input type="text" class="input-box w-2/3 txt_EmpNo" id="txt_EmpNo"
                                                name="txt_EmpNo" placeholder="Enter Emp No." readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_name" class="w-1/3 font-medium">Name:</label>
                                            <input type="text" class="input-box w-2/3 font-bold" id="txt_name"
                                                name="txt_name" placeholder="Enter Name" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_phone" class="w-1/3 font-medium">Phone:</label>
                                            <input type="text" class="input-box w-2/3 txt_phone" id="txt_phone"
                                                name="txt_phone" placeholder="Enter Phone" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_phone1" class="w-1/3 font-medium">Mobile:</label>
                                            <input type="text" class="input-box w-2/3 txt_phone1" id="txt_phone1"
                                                name="txt_phone1" placeholder="Enter Mobile" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_sex" class="w-1/3 font-medium">Gender:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_sex" name="txt_sex"
                                                placeholder="Enter Gender" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_address" class="w-1/3 font-medium">Address:</label>
                                            <input type="text" class="input-box w-2/3 txt_Address" id="txt_address"
                                                name="txt_address" placeholder="Enter Address" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_dsgcode" class="w-1/3 font-medium">Designation:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_dsgcode"
                                                name="txt_dsgcode" placeholder="Enter Designation" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_state" class="w-1/3 font-medium">Status:</label>
                                            <input type="text" class="input-box w-2/3 txt_State" id="txt_state"
                                                name="txt_state" placeholder="Enter Status" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_comp_id" class="w-1/3 font-medium">School:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_comp_id"
                                                name="txt_comp_id" placeholder="Enter School" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_add1" class="w-1/3 font-medium">Location:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_add1" name="txt_add1"
                                                placeholder="Enter Location" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_catcode" class="w-1/3 font-medium">Category:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_catcode"
                                                name="txt_catcode" placeholder="Enter Category" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_mar_stat" class="w-1/3 font-medium">Marital Status:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_mar_stat"
                                                name="txt_mar_stat" placeholder="Enter marital status" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_pan" class="w-1/3 font-medium">PAN No.:</label>
                                            <input type="text" class="input-box w-2/3 txt_pan" id="txt_pan"
                                                name="txt_pan" placeholder="Enter PAN Number" maxlength="10"
                                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                                                readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_dob" class="w-1/3 font-medium">Date of Birth:</label>
                                            <input type="text" class="input-box w-2/3 txt_dob" id="txt_dob"
                                                name="txt_dob" placeholder="Enter DOB" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_doj" class="w-1/3 font-medium">Date of Joining:</label>
                                            <input type="text" class="input-box w-2/3 txt_doj" id="txt_doj"
                                                name="txt_doj" placeholder="Enter Date of Joining" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_doc" class="w-1/3 font-medium">Date of Confirmation:</label>
                                            <input type="text" class="input-box w-2/3" id="txt_doc" name="txt_doc"
                                                placeholder="Enter Date of Confirmation" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label for="txt_cardno" class="w-1/3 font-medium">Card Number:</label>
                                            <input type="text" class="input-box w-2/3 txt_cardno" id="txt_cardno"
                                                name="txt_cardno" placeholder="Enter Card Number" maxlength="10"
                                                readonly>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <label for="txt_dor" class="w-1/3 font-medium">Date of Leaving:</label>
                                            <input type="text" class="input-box w-2/3 txt_dor" id="txt_dor" name="txt_dor"
                                                placeholder="Enter DOL" readonly>
                                        </div>
                                    </div>


                                <div class="col-md-12 mt-6">
                                    <div class="form-group">
                                        <center>
                                            <?php
                                            if ($_SESSION['user'] == 'Admin') {
                                            ?>
                                            <button type="button"
                                                class="prevBtn btn mx-4 btn-outline-primary rounded-full w-20"
                                                value="Submit">Prev</button>
                                            <?php
                                            }
                                            ?>
                                            <button type="button" name="next"
                                                class="btn mx-4 btn-primary rounded-full w-20"
                                                onclick="showData(getParameterByName('empno'))" data-tw-toggle="modal"
                                                data-tw-target="#header-footer-modal-preview-general-view">Edit</button>
                                            <?php
                                                if ($_SESSION['user'] == 'Admin') {
                                            ?>
                                            <button type="button"
                                                class="nextBtn btn mx-4 btn-outline-primary rounded-full w-20"
                                                value="Submit">Next</button>
                                        </center>
                                        <?php
                                        }
                                    ?>
                                    </div>
                                </div>
                            </div>
                            <!--END: General tab ---->

                            <!-- BEGIN: Finance tab -->
                            <div id="finance-tab" class="tab-pane leading-relaxed" role="tabpanel"
                                aria-labelledby="finance-tab">
                                <input id="txt_EmpNo" class="empno hidden" name="txt_EmpNo" type="hidden" readonly>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-dark">

                                    <div class="flex items-center">
                                        <label for="txt_finance_name" class="w-1/3 font-extrabold">Name:</label>
                                        <input type="text" id="txt_finance_name" name="txt_finance_name"
                                            placeholder="Enter Name" readonly
                                            class="p-2 font-extrabold border-0 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_bank" class="w-1/3">Bank Name:</label>
                                        <input type="text" id="txt_bank" name="txt_bank" placeholder="Enter Bank Name"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_accNo" class="w-1/3">Bank Account No:</label>
                                        <input type="text" id="txt_accNo" name="txt_accNo"
                                            placeholder="Enter Bank Account No" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <!-- <div class="flex items-center">
                                        <label for="txt_esic" class="w-1/3">ESIC No:</label>
                                        <input type="text" id="txt_esic" name="txt_esic" placeholder="Enter ESIC No"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div> -->

                                    <div class="flex items-center">
                                        <label for="txt_finance_pan" class="w-1/3">PAN:</label>
                                        <input type="text" id="txt_finance_pan" name="txt_finance_pan"
                                            placeholder="Enter PAN No" maxlength="10" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_basic" class="w-1/3">Basic:</label>
                                        <input type="text" id="txt_basic" name="txt_basic" placeholder="Enter Basics"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_payScale" class="w-1/3">Pay Scale:</label>
                                        <input type="text" id="txt_payScale" name="txt_payScale"
                                            placeholder="Enter Pay Scale" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_pf" class="w-1/3">PF No:</label>
                                        <input type="text" id="txt_pf" name="txt_pf" placeholder="Enter PF No" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <!-- Arrear Section Label -->
                                    <div class="col-span-full pt-3">
                                        <h3 class="font-semibold text-lg mb-2">Arrear</h3>
                                        <hr>
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_month" class="w-1/3">Month:</label>
                                        <input type="text" id="txt_month" name="txt_month" placeholder="Enter Month"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_nod" class="w-1/3">No of Days:</label>
                                        <input type="text" id="txt_nod" name="txt_nod" placeholder="Enter No. of Days"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                </div>

                                <div class="form-card mt-6 text-start">
                                    <div class="mb-4">
                                        <h5>Allowances and Deductions:</h5>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table id="allowtable" class="table table-bordered table-striped" style="width:100%"
                                            cellpadding="7">
                                            <thead>
                                                <tr>
                                                    <th class="w-1/4">Description</th>
                                                    <th class="w-1/6">% flag</th>
                                                    <th class="w-1/5">All. or Ded.</th>
                                                    <th class="w-1/10">Value</th>
                                                    <th class="w-1/5">Still Valid</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-center space-x-4">
                                    <?php if ($_SESSION['user'] == 'Admin'): ?>
                                    <button type="button"
                                        class="prevBtn btn btn-outline-primary rounded-full w-20">Prev</button>

                                    <button type="button" name="next" class="btn btn-primary rounded-full w-20"
                                        onclick="showData(getParameterByName('empno'))" data-tw-toggle="modal"
                                        data-tw-target="#header-footer-modal-preview-finance-view">Edit</button>

                                    <button type="button"
                                        class="nextBtn btn btn-outline-primary rounded-full w-20">Next</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- END: Finance tab -->


                            <!-- BEGIN: Leave tab -->
                            <div id="leave-tab" class="tab-pane leading-relaxed" role="tabpanel"
                                aria-labelledby="leave-tab">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-dark">

                                    <div class="flex items-center">
                                        <label for="txt_leave_name" class="w-1/3 font-extrabold">Name:</label>
                                        <input type="text" id="txt_leave_name" name="txt_leave_name"
                                            placeholder="Enter Name" readonly
                                            class="p-2 w-2/3 border-0 font-extrabold bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_leave" class="w-1/3">Leave Group:</label>
                                        <input type="text" id="txt_leave" name="txt_leave"
                                            placeholder="Enter Leave Group" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_Shcode" class="w-1/3">Shift:</label>
                                        <input type="text" id="txt_Shcode" name="txt_Shcode" placeholder="Shift Code"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_rotation" class="w-1/3">Shift Rotation:</label>
                                        <div class="w-2/3 flex items-center">
                                            <input type="checkbox" id="txt_rotation" name="txt_rotation" value="shift"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_weekly" class="w-1/3">Weekly Off:</label>
                                        <input type="text" id="txt_weekly" name="txt_weekly"
                                            placeholder="Enter Weekly Off" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>
                                </div>

                                <!-- Leave Summary Table -->
                                <div class="mt-6">
                                    <label class="font-bold mb-2 inline-block">Leave Summary:</label>
                                    <div class="overflow-x-auto">
                                        <table id="leaveSummaryTable"
                                            class="table table-bordered table-striped w-full md:w-1/2">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>CL</th>
                                                    <th>Earned leave</th>
                                                    <th>Med leave</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Current</td>
                                                    <td id="txt_currentCL"></td>
                                                    <td id="txt_currentEarnedLeave"></td>
                                                    <td id="txt_currentMedLeave"></td>
                                                </tr>
                                                <tr>
                                                    <td>Opening</td>
                                                    <td id="txt_openingCL"></td>
                                                    <td id="txt_openingEarnedLeave"></td>
                                                    <td id="txt_openingMedLeave"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Leave History Section -->
                                <div class="form-card text-start mt-8">
                                    <h2 class="mb-4 text-lg font-bold">Leave History:</h2>
                                    <div class="overflow-x-auto">
                                        <table id="leavetable" class="table table-bordered table-striped" style="width:100%"
                                            cellpadding="7px">
                                            <thead>
                                                <tr>
                                                    <th width="10%">Sl No</th>
                                                    <th width="20%">Date</th>
                                                    <th width="20%">From</th>
                                                    <th width="10%">To</th>
                                                    <th width="20%">Total Absent</th>
                                                    <th width="20%">Still W/O Pay</th>
                                                    <th width="20%">Leave Type</th>
                                                    <th width="20%">Sanctioned</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-md-12 mt-6">
                                    <div class="form-group">
                                        <center>
                                            <?php if ($_SESSION['user'] == 'Admin'): ?>
                                            <button type="button"
                                                class="prevBtn btn mx-4 btn-outline-primary rounded-full w-20">Prev</button>


                                            <button type="button" name="next"
                                                class="btn mx-4 btn-primary rounded-full w-20"
                                                onclick="showData(getParameterByName('empno'))" data-tw-toggle="modal"
                                                data-tw-target="#header-footer-modal-preview-leave-view">Edit</button>


                                            <button type="button"
                                                class="nextBtn btn mx-4 btn-outline-primary rounded-full w-20">Next</button>
                                            <?php endif; ?>
                                        </center>
                                    </div>
                                </div>
                            </div>
                            <!-- END: Leave tab -->


                            <!--BEGIN: Loan tab ---->
                            <div id="loan-tab" class="tab-pane leading-relaxed" role="tabpanel"
                                aria-labelledby="loan-tab">
                                <input id="txt_EmpNo" class="empno hidden" name="txt_EmpNo" type="hidden" readonly>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-dark">
                                    <div class="flex items-center">
                                        <label class="w-1/3 font-extrabold" for="txt_name">Name:</label>
                                        <input type="text" class="p-2 font-extrabold border-0 w-2/3 bg-transparent"
                                            id="txt_loan_name" name="txt_loan_name" placeholder="Enter Name" Readonly>
                                    </div>


                                </div>

                                <div class="form-card text-start mt-5">
                                    <div class="row">
                                        <div class="col-7">
                                            <h5 class="mb-4">Loan Master:</h5>
                                        </div>
                                    </div>
                                    <div class="preview">
                                        <div class="overflow-x-auto">
                                            <table id="loantable" class="table table-bordered table-striped"
                                                style="width:100%" cellpadding="7px">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Sl No</th>
                                                        <th width="15%">Date</th>
                                                        <th width="20%">Amount</th>
                                                        <th width="10%">Rate</th>
                                                        <th width="20%">Installment no</th>
                                                        <th width="20%">Status</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-6">
                                    <div class="form-group">
                                        <center>
                                            <?php
                                            if ($_SESSION['user'] == 'Admin') {
                                            ?>
                                            <button type="button"
                                                class="prevBtn btn mx-4 btn-outline-primary rounded-full w-20"
                                                value="Submit">Prev</button>

                                            <!-- <button type="button" name="next"
                                                class="btn mx-4 btn-primary rounded-full w-20"
                                                onclick="showData(getParameterByName('empno'))" data-tw-toggle="modal"
                                                data-tw-target="#header-footer-modal-preview-loan-view">Edit</button> -->

                                            <button type="button"
                                                class="nextBtn btn mx-4 btn-outline-primary rounded-full w-20"
                                                value="Submit">Next</button>
                                        </center>

                                        <?php
                                            }
                                    ?>
                                    </div>
                                </div>
                            </div>
                            <!--END: Loan tab ---->

                            <!-- BEGIN: Incentive tab -->
                            <div id="incentive-tab" class="tab-pane leading-relaxed" role="tabpanel"
                                aria-labelledby="incentive-tab">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-dark">

                                    <div class="flex items-center">
                                        <label for="txt_incentive_name" class="w-1/3 font-extrabold">Name:</label>
                                        <input type="text" id="txt_incentive_name" name="txt_incentive_name"
                                            placeholder="Enter Name" readonly
                                            class="p-2 w-2/3 border-0 font-extrabold bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_incentive_basic" class="w-1/3">Basic:</label>
                                        <input type="text" id="txt_incentive_basic" name="txt_incentive_basic"
                                            placeholder="Enter Basic" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_incentive" class="w-1/3">Incentive:</label>
                                        <input type="text" id="txt_incentive" name="txt_incentive"
                                            placeholder="Enter Incentive" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_basicIncr" class="w-1/3">Basic Increment:</label>
                                        <input type="text" id="txt_basicIncr" name="txt_basicIncr"
                                            placeholder="Enter Basic Increment" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_doi" class="w-1/3">Date of Increment:</label>
                                        <input type="text" id="txt_doi" name="txt_doi"
                                            placeholder="Enter Date of Increment" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_incrRemark" class="w-1/3">Increment Remark:</label>
                                        <input type="text" id="txt_incrRemark" name="txt_incrRemark"
                                            placeholder="Enter Increment Remark" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                </div>

                                <div class="mt-6 flex justify-center space-x-4">
                                    <?php if ($_SESSION['user'] == 'Admin'): ?>
                                    <button type="button"
                                        class="prevBtn btn btn-outline-primary rounded-full w-20">Prev</button>


                                    <button type="button" name="next" class="btn btn-primary rounded-full w-20"
                                        onclick="showData(getParameterByName('empno'))" data-tw-toggle="modal"
                                        data-tw-target="#header-footer-modal-preview-incentive-view">Edit</button>

                                    <button type="button"
                                        class="nextBtn btn btn-outline-primary rounded-full w-20">Next</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- END: Incentive tab -->


                            <!-- BEGIN: Other tab -->
                            <div id="other-tab" class="tab-pane leading-relaxed" role="tabpanel"
                                aria-labelledby="other-tab">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-dark">

                                    <div class="flex items-center">
                                        <label for="txt_other_name" class="w-1/3 font-extrabold">Name:</label>
                                        <input type="text" id="txt_other_name" name="txt_other_name"
                                            placeholder="Enter Name" readonly
                                            class="p-2 w-2/3 border-0 font-extrabold bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_relation" class="w-1/3">Relationship:</label>
                                        <input type="text" id="txt_relation" name="txt_relation"
                                            placeholder="Enter Relationship" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_gname" class="w-1/3">Father/Husband Name:</label>
                                        <input type="text" id="txt_gname" name="txt_gname"
                                            placeholder="Enter Father/Husband Name" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_edu" class="w-1/3">Edu. Qualification:</label>
                                        <input type="text" id="txt_edu" name="txt_edu"
                                            placeholder="Enter Edu. Qualification" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_subject" class="w-1/3">Subject Taught:</label>
                                        <input type="text" id="txt_subject" name="txt_subject"
                                            placeholder="Enter Subject Taught" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_email" class="w-1/3">E-mail Address:</label>
                                        <input type="text" id="txt_email" name="txt_email"
                                            placeholder="Enter E-mail Address" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_uid" class="w-1/3">UUID:</label>
                                        <input type="text" id="txt_uid" name="txt_uid"
                                            placeholder="Enter Your UUID (Aadhar No)" readonly
                                            class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_uanNo" class="w-1/3">UAN No:</label>
                                        <input type="text" id="txt_uanNo" name="txt_uanNo" placeholder="Enter UAN No"
                                            readonly class="p-2 w-2/3 bg-transparent">
                                    </div>

                                    <div class="flex items-center">
                                        <label for="txt_exper" class="w-1/3">Experience:</label>
                                        <input type="text" id="txt_exper" name="txt_exper"
                                            placeholder="Enter Experience" readonly class="p-2 w-2/3 bg-transparent">
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-center space-x-4">
                                    <?php if ($_SESSION['user'] == 'Admin'): ?>
                                    <button type="button"
                                        class="prevBtn btn btn-outline-primary rounded-full w-20">Prev</button>
                                    <?php endif; ?>

                                    <button type="button" name="next" class="btn btn-primary rounded-full w-20"
                                        onclick="showData(getParameterByName('empno'))" data-tw-toggle="modal"
                                        data-tw-target="#header-footer-modal-preview-other-view">Edit</button>

                                    <?php if ($_SESSION['user'] == 'Admin'): ?>
                                    <button type="button"
                                        class="nextBtn btn btn-outline-primary rounded-full w-20">Next</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- END: Other tab -->

                        </div>
                    </div>

                </div>


            </div>
            <!-- END: Boxed Tab -->


            <!-- BEGIN: View Modal -->
            <div class="intro-y box mt-5 hidden">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">
                        Header & Footer Modal
                    </h2>
                </div>
                <div id="header-footer-modal" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#header-footer-modal-preview-general" class="btn btn-primary">Show
                                Modal</a> </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="header-footer-modal-preview-general-view" class="modal" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog" style="width:70%;">
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
                                    <form id="general_form" class="frm_user" name="frm_user" action="" method="POST"
                                        enctype="multipart/form-data">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">

                                            <input id="txt_EmpNo" name="txt_EmpNo" type="hidden"
                                                class="txt_EmpNo form-control rounded-full" placeholder="Emp no"
                                                readonly>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label">Name: *</label>
                                                <input type="text" class="txt_Name form-control rounded-pill text-dark"
                                                    name="txt_Name" id="txt_Name" placeholder="Enter Name"
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label">School: *</label>
                                                <select name="txt_Comp_id" id="txt_Comp_id" class="form-control border px-4 py-2 text-dark">
                                                    <option value="" selected disabled>--Select School--</option>
                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT comp_id, comp_name FROM compmast");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $compid = $row['comp_id'];
                                                        $comp_name = $row['comp_name'];
                                                        echo "<option value='$compid'>$comp_name</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label " for="txt_State">Status :</label>
                                                <select class="form-control  border px-4 py-2 text-dark" name="txt_State"
                                                    id="txt_State" placeholder="state">
                                                    <!-- <option value="" selected="true" disabled> -- Status -- </option> -->
                                                    <option value="Permanent">Permanent</option>
                                                    <option value="Probation">Probation</option>
                                                    <option value="Ignore-Pay">Ignore-Pay</option>
                                                    <option value="Retired">Retired</option>
                                                </select>
                                            </div>

                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Address">Address: *</label>
                                                <input type="text"
                                                    class="form-control rounded-pill text-dark txt_Address"
                                                    name="txt_Address" id="txt_Address" placeholder="Address" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label">Location: *</label>
                                                <input type="text" class="form-control txt_add1 rounded-pill text-dark"
                                                    name="txt_Add1" id="txt_Add1" placeholder="location"
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Phone">Phone: *</label>
                                                <input type="text" class="form-control txt_phone rounded-pill text-dark"
                                                    id="txt_Phone" name="txt_Phone" placeholder="phone" maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label">Mobile No.: *</label>
                                                <input type="text"
                                                    class="form-control txt_phone1 rounded-pill text-dark"
                                                    id="txt_Phone1" name="txt_Phone1" placeholder="Contact No."
                                                    maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label">Gender:*</label>
                                                <div class="flex flex-col sm:flex-row mt-2">
                                                    <div class="form-check mr-2"> <input id="male"
                                                            class="form-check-input border-4" type="radio"
                                                            name="txt_sex" value="M"> <label for="male"
                                                            class="form-check-label">Male</label> </div>
                                                    <div class="form-check mr-2 mt-2 sm:mt-0"> <input id="female"
                                                            class="form-check-input border-4" type="radio"
                                                            name="txt_sex" value="F"> <label for="female"
                                                            class="form-check-label">Female</label> </div>
                                                </div>
                                            </div>
                                                <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label">Marital Status:*</label>
                                                <div class="flex flex-col sm:flex-row mt-2">
                                                    <div class="form-check mr-2">
                                                        <input id="married" class="form-check-input border-4"
                                                            type="radio" name="txt_Mar_stat" value="Married">
                                                        <label for="married" class="form-check-label">Married</label>
                                                    </div>
                                                    <div class="form-check mr-2 mt-2 sm:mt-0">
                                                        <input id="unmarried" class="form-check-input border-4"
                                                            type="radio" name="txt_Mar_stat" value="Unmarried">
                                                        <label for="unmarried" class="form-check-label">Unmarried</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Pan">PAN Number: *</label>
                                                <input type="text" class="form-control txt_pan rounded-pill text-dark"
                                                    id="txt_Pan" name="txt_Pan" maxlength='10'
                                                    placeholder="Enter PAN Number"
                                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Catcode">Category: *</label>
                                                <div class="form-check-inline">
                                                    <select name="txt_Catcode" id="txt_Catcode" class="form-control border px-4 py-2 text-dark">

                                                        <?php
                                                        require_once 'includes/dbconn.php';
                                                        $query = $db->query("SELECT cat_code, DESCR FROM catmast");
                                                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                            $catcode = $row['cat_code'];
                                                            $descr = $row['DESCR'];
                                                            echo "<option value='$catcode'>$descr</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Dsgcode">Degination:*</label>
                                                <select name="txt_Dsgcode" id="txt_Dsgcode" class="form-control border px-4 py-2 text-dark">
                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT DCODE, DESCR FROM dsgmast");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $dcode = $row['DCODE'];
                                                        $descr = $row['DESCR'];
                                                        echo "<option value='$dcode'>$descr</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Dob">Date of Birth:*</label>
                                                <input type="date" class="form-control rounded-pill text-dark txt_dob"
                                                    name="txt_Dob" id="txt_Dob" placeholder="Enter Date of Birth" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Doj">Date of Joining:*</label>
                                                <input type="date" class="form-control txt_doj rounded-pill text-dark"
                                                    name="txt_Doj" id="txt_Doj" placeholder="Enter  Date of Joining:" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_dor">Date of Leaving:</label>
                                                <input type="date" class="form-control rounded-pill text-dark txt_dor"
                                                    name="txt_dor" id="txt_dor" placeholder="Enter Date of Leaving:" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Doc">Date of Confirmation:*</label>
                                                <input type="date" class="form-control rounded-pill text-dark"
                                                    name="txt_Doc" for="txt_Doc" id="txt_Doc"
                                                    placeholder="Enter Date of Confirmation" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4">
                                                <label class="form-label" for="txt_Cardno">Card No:*</label>
                                                <input type="text"
                                                    class="form-control txt_cardno rounded-pill text-dark"
                                                    name="txt_Cardno" id="txt_Cardno" maxlength='10'
                                                    placeholder="Enter Card No:" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-4" id="img">
                                                <label class="form-label" for="relation">Upload Your Photo:</label>
                                                <div class="form-check mr-2"><button type="button" name="next"
                                                        class="btn rounded-pill w-60 image-button border-0"
                                                        data-tw-toggle="modal" data-tw-target="#select_model">
                                                        <span class="image-icon"></span>
                                                        .
                                                    </button>Must be less than 2 MB
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="txt_PHOTO" name="txt_PHOTO">
                                        <button type="submit" style="display: none;" id="hidden_submit_btn"></button>
                                        <input type="hidden" id="selectedImageData" name="selectedImageData" />
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                        <button id="btn_general_save" data-tw-dismiss="modal"
                                            class="btn btn-primary w-20 rounded-full"
                                            onclick="saveAndTurnOffCamera()">Save</button>
                                        <button id="btn_general_update" data-tw-dismiss="modal"
                                            class="btn btn_update btn-primary w-20 rounded-full">Update</button>
                                    </div>

                                    <!-- END: Modal Footer -->
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>

                </div>
                <!-- Model for camera -->
                <div id="use_camera" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center">
                            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#use_camera-preview"
                                class="btn btn-primary">Show Modal</a>
                        </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="camera_model" class="modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" style="width:50%;">
                                <div class="modal-content">
                                    <!-- BEGIN: Modal Header -->
                                    <div class="modal-header">
                                        <h2 class="font-bold text-base mr-auto">
                                            Take Photo
                                        </h2>
                                    </div>
                                    <hr class="border-black">
                                    <!-- END: Modal Header -->
                                    <!-- BEGIN: Modal Body -->
                                    <form id="frm_user_camera" name="frm_user_camera" class="frm_user" action=""
                                        method="POST" enctype="multipart/form-data">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <!-- Live image and capture button in the same row -->
                                            <div class="col-span-12 flex items-center">

                                                <div class="col-span-6" id="livePhoto"></div>
                                                <!-- Results container -->
                                                <div class="col-span-6 ml-6" id="results">Your captured image will
                                                    appear here...
                                                </div>

                                            </div>
                                            <!-- Capture button centered below live image -->
                                            <div class="col-span-12 flex items-center justify-center mt-4">
                                                <input type="button" class="btn btn-success" value="Capture"
                                                    id="captureBtn" name="captureBtn" accept="image/jpeg"
                                                    onclick="take_snapshot()" />
                                            </div>

                                        </div>
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full"
                                            onclick="resetwebcam()">Cancel</button>
                                        <button id="btn_save_camera" name="txt_PHOTO" data-tw-dismiss="modal"
                                            class="btn btn-primary w-20 rounded-full" onclick="resetwebcam()">
                                            Save</button>

                                    </div>
                                    <!-- END: Modal Footer -->
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>
                </div>

                <!-- End model for camera -->

                <!-- Model for Select -->
                <div id="select" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center">
                            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#select-preview"
                                class="btn btn-primary">Show Modal</a>
                        </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="select_model" class="modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" style="width:60%;">
                                <div class="modal-content">
                                    <!-- BEGIN: Modal Header -->
                                    <div class="modal-header">
                                        <h2 class="font-bold text-base mr-auto">
                                            Change Photo
                                        </h2>
                                    </div>
                                    <hr class="border-black">
                                    <!-- END: Modal Header -->
                                    <!-- BEGIN: Modal Body -->
                                    <form id="frm_user_select" class="frm_user" name="frm_user" action="" method="POST">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <!-- Image and text in the same row -->
                                            <div class="col-span-12 flex items-center flex-col">
                                                <p class="mb- text-center text-lg font-bold">Hey, help others recognize
                                                    you!</p>
                                                <div class="text-center">
                                                    <img src="./dist/images/user21012.png" alt="User Preview"
                                                        style="max-width: 50%; height: auto;" class="mx-auto block">
                                                </div>
                                                <!-- <p class="mt-6 text-center">On Payroll, we require members to use their real identities, so take or upload a photo of yourself.</p> -->
                                            </div>
                                        </div>
                                        <!-- END: Modal Body -->

                                        <div class="modal-footer flex justify-center gap-4">
                                            <button type="button" name="camera"
                                                class="btn btn-primary w-40 rounded-full" data-tw-toggle="modal"
                                                data-tw-dismiss="modal" data-tw-target="#camera_model"
                                                onclick="attachCamera()">Use Camera</button>

                                            <!-- Styled file input button -->
                                            <label for="txt_image"
                                                class="btn btn-primary w-40 rounded-full cursor-pointer">
                                                <span>Choose Photo</span>
                                                <input type="file" data-tw-dismiss="modal" class="hidden"
                                                    name="txt_image" id="txt_image" accept="image/jpeg" />
                                            </label>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>
                </div>

                <!-- End model for select -->
                <div id="header-footer-modal-finance" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#header-footer-modal-finance-preview" class="btn btn-primary">Show
                                Modal</a> </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="header-footer-modal-preview-finance-view" class="modal" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- BEGIN: Modal Header -->
                                    <div class="modal-header">
                                        <h2 class="font-bold text-base mr-auto">
                                            Edit Finance
                                        </h2>
                                    </div>
                                    <hr class="border-black">
                                    <!-- END: Modal Header -->
                                    <!-- BEGIN: Modal Body -->
                                    <form id="finance_form" class="frm_user" name="frm_user" action="" method="post">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <input id="txt_EmpNof" name="txt_EmpNof" type="hidden"
                                                class="form-control txt_EmpNo rounded-full empno" placeholder="Emp No"
                                                readonly>
                                            <div class="col-span-12 sm:col-span-6 hidden">
                                                <label class="form-label">Name: *</label>
                                                <input id="txt_Name" name="txt_Name" type="hidden"
                                                    class="txt_Name form-control" placeholder="Name" readonly
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Bank Name : *</label>
                                                <select name="txt_Bank" id="txt_Bank" class="form-control">
                                                    <option value="" selected disabled>--Select Bank Name--</option>
                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT BID, DESCR FROM bankmast");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $bid = $row['BID'];
                                                        $descr = $row['DESCR'];
                                                        echo "<option value='$bid'>$descr</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Bank Account No.: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_AccNo" id="txt_AccNo" placeholder="Bank A/c No."
                                                    maxlength="17"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>
                                            <!-- <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label " for="status">ESIC No. :</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Esic" id="txt_Esic" placeholder="ESIC No." maxlength="17"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                                            </div> -->

                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" for="txt_FinancePan">PAN: *</label>
                                                <input type="text" class="form-control txt_pan rounded-pill text-dark"
                                                    name="txt_Pan" id="txt_FinancePan" placeholder="PAN No"
                                                    maxlength="10"
                                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Basic: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Basic" id="txt_Basic" placeholder="Basic" maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Pay Scale: *</label>
                                                <input type="text"
                                                    class="form-control txt_payScale rounded-pill text-dark"
                                                    name="txt_PayScale" id="txt_payScale" placeholder="PayScale"
                                                    oninput="this.value = this.value.replace(/[^0-9\-]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">PF No.: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Pf" id="txt_Pf" placeholder="PF No." maxlength="22"
                                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Month: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Month" id="txt_Month" placeholder="Month" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">No. of Days: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Nod" id="txt_Nod" placeholder="No. of Days" />
                                            </div>


                                        </div>
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                        <button id="btn_update_finance" data-tw-dismiss="modal"
                                            class="btn_update btn btn-primary w-20 rounded-full">Update</button>
                                    </div>

                                    <!-- END: Modal Footer -->
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>
                </div>
                <div id="header-footer-modal" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#header-footer-modal-preview-leave" class="btn btn-primary">Show
                                Modal</a> </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN:Leave Modal Content -->
                        <div id="header-footer-modal-preview-leave-view" class="modal" tabindex="-1" aria-hidden="true">
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
                                    <form id="leave_form" name="frm_user" class="frm_user" action="" method="post">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <!-- <input id="txt_emp_no" name="txt_emp_no" type="hidden" class="form-control rounded-full" placeholder="Dcode" readonly> -->
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Name.: *</label>
                                                <input type="text" class="txt_Name form-control rounded-pill text-dark"
                                                    name="txt_Name" id="txt_Name" placeholder="Name" readonly />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" for="txt_LeaveGroup">Leave Group: *</label>
                                                <select name="txt_LeaveGroup" id="txt_LeaveGroup" class="form-control">

                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT DISTINCT(LeaveGroupCode), LeaveGroupDesc FROM leavegroup");
                                                    $leavegroups = $query->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($leavegroups as $row) {
                                                        ?>
                                                            <option value="<?= $row['LeaveGroupCode']?>"><?= $row['LeaveGroupDesc'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" for="txt_Rotation">Shift: </label>
                                                <select name="txt_Shcode" id="txt_Shcode" class="form-control">

                                                    <?php
                                                    require_once 'includes/dbconn.php';
                                                    $query = $db->query("SELECT ShiftCode FROM shiftmaster");
                                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $shiftcode = $row['ShiftCode'];
                                                        echo "<option value='$shiftcode'>$shiftcode</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label " for="Status">Status :</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    id="Status" name="Status" placeholder="">

                                            </div>
                                            <div class="col-span-12 sm:col-span-6 pt-2">
                                                <label class="form-label">Shift Rotation: </label>
                                                <input type="checkbox" class="" name="txt_Rotation" id="txt_Rotation"
                                                    placeholder="Shift" />
                                            </div>

                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Weekly Off: *</label>
                                                <div class="col-span-2">
                                                    <select class="text-dark" name="txt_woff" id="txt_woff"
                                                        placeholder="Select">
                                                        <option value="" selected="true" disabled>Select</option>
                                                        <option value="1">Sunday</option>
                                                        <option value="2">Monday</option>
                                                        <option value="3">Tuesday</option>
                                                        <option value="4">Wedsnday</option>
                                                        <option value="5">Thursday</option>
                                                        <option value="6">Friday</option>
                                                        <option value="7">Saturday</option>
                                                    </select>
                                                </div>
                                            </div>



                                        </div>
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                        <button id="btn_leave_update" data-tw-dismiss="modal"
                                            class="btn btn-primary w-20 rounded-full">Update</button>
                                    </div>

                                    <!-- END: Modal Footer -->
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>

                </div>
                <div id="header-footer-modal" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#header-footer-modal-preview-loan" class="btn btn-primary">Show
                                Modal</a> </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="header-footer-modal-preview-loan-view" class="modal" tabindex="-1" aria-hidden="true">
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
                                    <form id="frm_user" class="frm_user" name="frm_user" action="" method="post">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <input id="txt_dcode" name="txt_dcode" type="hidden"
                                                class="form-control rounded-full" placeholder="Dcode" readonly>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Emloyee No.: *</label>
                                                <input type="number" class="form-control rounded-pill text-dark" name=""
                                                    placeholder="Emp no" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">School: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark" name=""
                                                    placeholder="School name" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Name: *</label>
                                                <input type="text" class="txt_Name form-control rounded-pill text-dark"
                                                    name="uname" placeholder="Name"
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label " for="status">Status :</label>
                                                <select class="form-control rounded-pill text-dark" name="status"
                                                    placeholder="Status">
                                                    <option value="" selected="true" disabled> -- Status -- </option>
                                                    <option value="permanent">Permanent</option>
                                                    <option value="probation">Probation</option>
                                                    <option value="ignorepay">Ignore-Pay</option>
                                                    <option value="retired">Retired</option>
                                                </select>
                                            </div>

                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Address: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="addrss" placeholder="Address" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Location: *</label>
                                                <input type="location"
                                                    class="form-control txt_add1 txt_rounded-pill text-dark"
                                                    name="lction" placeholder="location"
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Phone: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="phone" placeholder="phone" maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Mobile No.: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="mbno" placeholder="Contact No." maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>


                                        </div>
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                        <button id="btn_loan_update" data-tw-dismiss="modal"
                                            class="btn_update btn btn-primary w-20 rounded-full">Update</button>
                                    </div>

                                    <!-- END: Modal Footer -->
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>

                </div>
                <div id="header-footer-modal" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#header-footer-modal-preview-incentive" class="btn btn-primary">Show
                                Modal</a> </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="header-footer-modal-preview-incentive-view" class="modal" tabindex="-1"
                            aria-hidden="true">
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
                                    <form id="incentive_form" class="frm_user" name="frm_user" action="" method="post">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <input id="txt_EmpNo" name="txt_EmpNo" type="hidden"
                                                class="form-control txt_EmpNo rounded-full" placeholder="Emp no"
                                                readonly>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Name: *</label>
                                                <input type="text" class="txt_Name form-control rounded-pill text-dark"
                                                    name="txt_Name" id="txt_Name" placeholder="Name" readonly
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Basic: *</label>
                                                <input type="text" class=" form-control rounded-pill text-dark"
                                                    name="txt_Name" id="txt_incentive_basic_modal" placeholder="Basic"
                                                    maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" readonly />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label " for="txt_Incentive">Incentive :</label>
                                                <input type="text" class="txt_Incentive_modal form-control rounded-pill text-dark"
                                                    name="txt_Incentive" id="txt_Incentive_modal" placeholder="Incentive "
                                                    maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')" />
                                            </div>

                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" for="txt_BasicIncr">Basic Increment: *</label>
                                                <input type="text" class="txt_BasicIncr_modal form-control rounded-pill text-dark"
                                                    name="txt_BasicIncr" id="txt_BasicIncr_modal" placeholder="Basic Increment"
                                                    maxlength='10'
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" fot="txt_Doi" >Date of Increment: *</label>
                                                <input type="date" class="txt_Doi_modal form-control rounded-pill text-dark"
                                                    name="txt_Doi" id="txt_Doi_modal" placeholder="Date of Increment" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" for="txt_IncrRemark">Increment Remark: *</label>
                                                <input type="text" class="txt_IncrRemark_modal form-control rounded-pill text-dark"
                                                    name="txt_IncrRemark" id="txt_IncrRemark_modal"
                                                    placeholder="Increment Remark" />
                                            </div>
                                        </div>
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                        <button id="btn_incentive_update" data-tw-dismiss="modal"
                                            class="btn btn-primary w-20 rounded-full">Update</button>
                                    </div>

                                    <!-- END: Modal Footer -->
                                </div>
                            </div>
                        </div>
                        <!-- END: Modal Content -->
                    </div>

                </div>
                <div id="header-footer-modal" class="p-5">
                    <div class="preview">
                        <!-- BEGIN: Modal Toggle -->
                        <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#header-footer-modal-preview-other" class="btn btn-primary">Show
                                Modal</a> </div>
                        <!-- END: Modal Toggle -->
                        <!-- BEGIN: Modal Content -->
                        <div id="header-footer-modal-preview-other-view" class="modal" tabindex="-1" aria-hidden="true">
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
                                    <form id="others_form" class="frm_user" name="frm_user" action="" method="post">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <input id="txt_EmpNo" name="txt_EmpNo" type="hidden"
                                                class="form-control txt_EmpNo rounded-full" placeholder="Name" readonly>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" for="txt_Relation_modal">Relationship: *</label>
                                                <select class="form-control rounded-pill text-dark"
                                                    name="txt_Relation" id="txt_Relation_modal">
                                                    <option value="" selected disabled>Select Relationship</option>
                                                    <option value="Father">Father</option>
                                                    <option value="Husband">Husband</option>
                                                    <option value="Mother">Mother</option>
                                                    <option value="Guardian">Guardian</option>
                                                </select>
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label" id="lbl_Gname_modal" for="txt_Gname_modal">Father/Husband Name:*</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Gname" id="txt_Gname_modal" placeholder="Enter Father/Husband Name" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Edu. Qualification: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Edu" id="txt_Edu_modal" placeholder="Edu. Qualification" />
                                            </div>

                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Subject Taught: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Subject" id="txt_Subject_modal" placeholder="Subject Taught" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Email Address: *</label>
                                                <input type="email" class="form-control rounded-pill text-dark"
                                                    name="txt_Email" id="txt_Email_modal" placeholder="Email Address" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">UID: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Uid" id="txt_Uid_modal" placeholder="UID" maxlength="12"
                                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">UAN No: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_UanNo" id="txt_UanNo_modal" placeholder="UAN No" maxlength="12"
                                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" />
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label class="form-label">Experience: *</label>
                                                <input type="text" class="form-control rounded-pill text-dark"
                                                    name="txt_Exper" id="txt_Exper_modal" placeholder="Experience" />
                                            </div>


                                        </div>
                                        <!-- END: Modal Body -->
                                    </form>
                                    <!-- BEGIN: Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal"
                                            class="btn btn-outline-secondary w-20 mr-1 rounded-full">Cancel</button>
                                        <button id="btn_other_update" data-tw-dismiss="modal"
                                            class="btn_update btn btn-primary w-20 rounded-full">Update</button>
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
        </div>
        <!-- BEGIN: JS Assets-->
        <script src="dist/js/app.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="dist/js/sweetalert2.min.js"></script>
        <!-- END: JS Assets-->
    </div>
    <?php
    include 'footer.php';
    ?>
</body>

<script>
// ─────────────────────────────────────────────
// Page Init
// ─────────────────────────────────────────────

/** Index of the currently displayed employee in the full list (used by Prev/Next). */
var currentIndex = 0;
const employeeNumbers = <?php echo json_encode(array_values(array_column($empnames, 'EMPNO'))); ?>;
let currentEmployeeData = null;
let currentEmployeeNo = null;
let leaveTableEmpno = null;
let loanTableEmpno = null;
let allowanceTableEmpno = null;

$(document).ready(function () {
    // Load the employee indicated by the URL ?empno= param, or index 0 if none.
    load_data_general(currentIndex, getParameterByName('empno'));
    updateButtonStatus();
    $('#txt_Relation_modal').on('change', function () {
        updateRelationLabel($(this).val());
    });
    // Bind Prev / Next navigation buttons.
    $(".nextBtn").click(next_data);
    $(".prevBtn").click(prev_data);
    $("[data-tw-target='#finance-tab']").on('click', function () {
        if (currentEmployeeNo) {
            loadAllowanceDataTable(currentEmployeeNo);
        }
    });
    $("[data-tw-target='#leave-tab']").on('click', function () {
        if (currentEmployeeNo) {
            loadLeaveDataTable(currentEmployeeNo);
        }
    });
    $("[data-tw-target='#loan-tab']").on('click', function () {
        if (currentEmployeeNo) {
            loadLoanDataTable(currentEmployeeNo);
        }
    });


});

function updateRelationLabel(value) {
    const relation = value || 'Father/Husband';
    $('#lbl_Gname_modal').text(relation + " Name:*");
    $('#txt_Gname_modal').attr('placeholder', "Enter " + relation + " Name");
}

$('#header-footer-modal-preview-other-view').on('hidden.tw.modal', function () {
    updateRelationLabel('');
});

function loadEmployeeBundle(empno) {
    if (!empno) return;

    $.ajax({
        url    : '../prsApi/empmast/' + empno,
        method : "GET",
        success: function (data) {
            if (!data) return;

            currentEmployeeData = data;
            currentEmployeeNo = data.EMPNO;
            updateEmployeeInformation(data);
            updateLeaveSummary(data);
            updateUrlWithEmpNo(data.EMPNO);
            populateFinanceView(data);
            populateLeaveView(data);
            populateLoanView(data);
            populateIncentiveView(data);
            populateOtherView(data);
            loadActiveTabDataTables(data.EMPNO);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("loadEmployeeBundle: " + errorThrown);
        }
    });
}

function loadActiveTabDataTables(empno) {
    if (!empno) return;

    if ($("[data-tw-target='#finance-tab']").hasClass('active')) {
        loadAllowanceDataTable(empno);
    }
    if ($("[data-tw-target='#leave-tab']").hasClass('active')) {
        loadLeaveDataTable(empno);
    }
    if ($("[data-tw-target='#loan-tab']").hasClass('active')) {
        loadLoanDataTable(empno);
    }
}


// ─────────────────────────────────────────────
// Employee Search Dropdown (Admin only)
// ─────────────────────────────────────────────

/**
 * On dropdown change, extract the EMPNO from the selected value
 * (format: "EMPNO - NAME") and reload all tab data.
 */
document.getElementById('empname').addEventListener('change', function () {
    var selectedOption = this.value;
    if (selectedOption) {
        load_search_data(selectedOption); // selectedOption IS the EMPNO (option value)
    }
});

/**
 * Fetch and display employee data for a given EMPNO (used by the search dropdown).
 * Populates the General tab view fields and updates the URL.
 *
 * @param {string|number} empno - Employee number to load.
 */
function load_search_data(empno) {
    loadEmployeeBundle(empno);
}


// ─────────────────────────────────────────────
// Modal: Add New Employee
// ─────────────────────────────────────────────

/**
 * Prepares the General modal for adding a new employee:
 * - Shows Save button, hides Update button.
 * - Resets all form fields.
 */
function add_new() {
    $("#btn_general_save").show();
    $("#img").show();
    $("#btn_general_update").hide();
    $('#frm_user1').trigger("reset");
    $('#frm_user_select').trigger("reset");
    $('#frm_user_camera').trigger("reset");
}


// ─────────────────────────────────────────────
// Utility: Form Serialization
// ─────────────────────────────────────────────

/**
 * Serializes a jQuery form into a plain JSON object.
 * Strips the "txt_" prefix from each field name for API compatibility.
 *
 * @param {jQuery} form - The form element to serialize.
 * @returns {Object} Key-value map of field name → value.
 */
function convertFormToJSON(form) {
    const array = $(form).serializeArray();
    const json  = {};
    $.each(array, function () {
        // Strip leading "txt_" prefix (e.g. "txt_Name" → "Name")
        const key = this.name.substring(this.name.indexOf("_") + 1);
        json[key] = this.value || "";
    });
    return json;
}


// ─────────────────────────────────────────────
// Utility: File Reader (Promise-based)
// ─────────────────────────────────────────────

/**
 * Reads a File object and resolves with its base64 data URI.
 *
 * @param {File} selectedImage - Image file selected by the user.
 * @returns {Promise<string>} Base64-encoded data URI.
 */
function readImageFile(selectedImage) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload  = e  => resolve(e.target.result);
        reader.onerror = err => reject(err);
        reader.readAsDataURL(selectedImage);
    });
}


// ─────────────────────────────────────────────
// Save: New Employee (POST)
// ─────────────────────────────────────────────

/**
 * Handles the Save button click:
 * - Calls saveAndTurnOffCamera() to sync the photo field.
 * - Reads the selected file or webcam image.
 * - POSTs the form as multipart/form-data to the API.
 * - On success, reloads the last employee (newly created).
 */
$("#btn_general_save").on("click", async function (event) {
    event.preventDefault();
    saveAndTurnOffCamera();

    const form     = $("#frm_user1")[0];
    const formData = new FormData(form);

    try {
        const selectedImage = $("#txt_image")[0].files[0];

        if (selectedImage) {
            // User uploaded a file: read it as base64.
            const imageData = await readImageFile(selectedImage);
            formData.set("txt_PHOTO", imageData);
        } else {
            // No file — fall back to webcam snapshot stored in hidden field.
            const webcamImageData = $("#selectedImageData").val();
            formData.set("txt_PHOTO", webcamImageData);
        }

        const response = await $.ajax({
            url        : '../prsApi/empmast',
            type       : 'POST',
            dataType   : 'json',
            data       : formData,
            processData: false,
            contentType: false,
        });

        console.log("Save response:", response);

        if (response.status === "Ok") {
            $("#header-footer-modal-preview").hide();
            loadLastEmpNo(); // Navigate to the newly created employee.
        }
    } catch (error) {
        console.error("Error processing image:", error);
    }
});


// ─────────────────────────────────────────────
// Load: General Tab Data
// ─────────────────────────────────────────────

/**
 * Loads general employee data and populates all view fields.
 * If an EMPNO is provided, fetches that specific employee.
 * Otherwise, uses currentIndex to fetch from the full list.
 *
 * @param {number} index  - Index within the employee list (used when no EMPNO).
 * @param {string} [EMPNO] - Specific employee number to load.
 */
function load_data_general(index, EMPNO) {
    if (EMPNO) {
        loadEmployeeBundle(EMPNO);
    } else {
        if (index >= 0 && index < employeeNumbers.length) {
            loadEmployeeBundle(employeeNumbers[index]);
        }
    }
}

/**
 * Reloads general data for a specific EMPNO after an update operation.
 *
 * @param {string|number} EMPNO - Employee number to refresh.
 */
function load_updated_data(EMPNO) {
    if (EMPNO) {
        loadEmployeeBundle(EMPNO);
    }
}


// ─────────────────────────────────────────────
// DOM Updaters
// ─────────────────────────────────────────────

/**
 * Writes employee general information into the read-only view fields on all tabs.
 * Also updates the employee photo (falls back to a default avatar).
 *
 * @param {Object} res - Employee data object from the API.
 */
function updateEmployeeInformation(res) {
    $(".txt_EmpNo").val(res.EMPNO);
    $("#txt_name").val(res.NAME);
    $("#txt_comp_id").val(res.comp_name);
    $("#txt_state").val(res.STATE);
    $("#txt_address").val(res.ADDRESS);
    $("#txt_add1").val(res.loc);
    $("#txt_phone").val(res.PHONE);
    $("#txt_phone1").val(res.PHONE1);
    $("#txt_sex").val(res.SEX);
    $("#txt_mar_stat").val(res.mar_stat);
    $("#txt_pan").val(res.PAN);
    $("#txt_catcode").val(res.descr);
    $("#txt_dob").val(res.DOB);
    $("#txt_doj").val(res.DOJ);
    $("#txt_dsgcode").val(res.designation);
    $("#txt_dor").val(res.DOR);
    $("#txt_doc").val(res.DOC);
    $("#txt_cardno").val(res.cardno);

    // Photo: use base64 JPEG from API, or fall back to default avatar.
    const employeeImage = document.getElementById('employeeImage');
    employeeImage.src = res.PHOTO
        ? 'data:image/jpeg;base64,' + res.PHOTO
        : './dist/images/user1101.png';
}

/**
 * Populates the leave balance summary table (Current & Opening: CL / EL / Medical).
 *
 * @param {Object} res - Employee data object from the API.
 */
function updateLeaveSummary(res) {
    $("#txt_currentCL").text(res.ACCLEAVE);
    $("#txt_currentEarnedLeave").text(res.el);
    $("#txt_currentMedLeave").text(res.MEDICAL_LEAVE);
    $("#txt_openingCL").text(res.ocl);
    $("#txt_openingEarnedLeave").text(res.oel);
    $("#txt_openingMedLeave").text(res.oml);
}

function populateFinanceView(data) {
    $("#txt_finance_name").val(data.NAME);
    $("#txt_bank").val(data.DESCR);
    $("#txt_accNo").val(data.ACCNO);
    $("#txt_esic").val(data.ADDRESS1);
    $("#txt_finance_pan").val(data.PAN);
    $("#txt_basic").val(data.BASIC);
    $("#txt_payScale").val(data.PAYSCALE);
    $("#txt_pf").val(data.PFACNO);
    $("#txt_nod").val(data.DOB);
}

function populateLeaveView(data) {
    const daysOfWeek = {
        1: "Sunday", 2: "Monday", 3: "Tuesday",
        4: "Wednesday", 5: "Thursday", 6: "Friday", 7: "Saturday"
    };

    $("#txt_leave_name").val(data.NAME);
    $("#txt_leave").val(data.LeaveGroupDesc);
    $("#txt_shift").val(data.autoshift);
    $("#txt_rotation").val(data.autoshifts);
    $("#txt_weekly").val(daysOfWeek[data.woff] || "");
    $("#txt_Shcode").val(data.SHCODE);
}

function populateLoanView(data) {
    $("#txt_loan_name").val(data.NAME);
    $(".txt_payScale").val(data.PAYSCALE);
}

function populateIncentiveView(data) {
    $("#txt_incentive_name").val(data.NAME);
    $("#txt_incentive_basic").val(data.BASIC);
    $("#txt_incentive").val(data.incentive);
    $("#txt_incrRemark").val(data.incr_remark);
    $("#txt_basicIncr").val(data.basic_increment);
    $("#txt_doi").val(data.incr_date);
}

function populateOtherView(data) {
    $("#txt_other_name").val(data.NAME);
    $("#txt_relation").val(data.relation);
    $("#txt_gname").val(data.GNAME);
    $("#txt_edu").val(data.qual);
    $("#txt_subject").val(data.subTaught);
    $("#txt_email").val(data.EMAIL);
    $("#txt_uid").val(data.uuid);
    $("#txt_uanNo").val(data.uanNo);
    $("#txt_exper").val(data.EXP);
}


// ─────────────────────────────────────────────
// URL & Cross-Tab Sync
// ─────────────────────────────────────────────

/**
 * Updates the browser URL's ?empno= parameter without a full page reload,
 * then triggers data loading for all other tabs and DataTables.
 *
 * @param {string|number} empno - The employee number to reflect in the URL.
 */
function updateUrlWithEmpNo(empno) {
    var currentUrl = window.location.href;
    var newUrl;

    if (currentUrl.includes("empno=")) {
        newUrl = currentUrl.replace(/empno=\d+/, `empno=${empno}`);
    } else {
        newUrl = currentUrl + (currentUrl.includes("?") ? "&" : "?") + `empno=${empno}`;
    }

    window.history.pushState({ path: newUrl }, "", newUrl);
}


// ─────────────────────────────────────────────
// DataTable Loaders
// ─────────────────────────────────────────────

/**
 * Initializes (or re-initializes) the Leave History DataTable for a given employee.
 * Uses server-side processing via ajax_leave.php.
 *
 * @param {string|number} empno - Employee number.
 */
function loadLeaveDataTable(empno) {
    if (!empno) return;
    if (leaveTableEmpno === empno && $.fn.DataTable.isDataTable('#leavetable')) return;

    if ($.fn.DataTable.isDataTable('#leavetable')) {
        $('#leavetable').DataTable().destroy();
    }

    leaveTableEmpno = empno;

    $('#leavetable').DataTable({
        buttons    : ['copy', 'excel', 'pdf'],
        processing : true,
        serverSide : true,
        ajax       : "ajax_leave.php?empno=" + empno,
        columns    : [
            { data: "SLNO"     },
            { data: "DTE"      },
            { data: "FDATE"    },
            { data: "TDATE"    },
            { data: "NOL"      },
            { data: "LWOP"     },
            { data: "LTYPE"    },
            { data: "NOOFDAYS" }
        ],
        order    : [[0, "asc"]],
        searching: false,
    });
}

/**
 * Initializes (or re-initializes) the Loan History DataTable for a given employee.
 * Uses server-side processing via ajax_loan.php.
 *
 * @param {string|number} empno - Employee number.
 */
function loadLoanDataTable(empno) {
    if (!empno) return;
    if (loanTableEmpno === empno && $.fn.DataTable.isDataTable('#loantable')) return;

    if ($.fn.DataTable.isDataTable('#loantable')) {
        $('#loantable').DataTable().destroy();
    }

    loanTableEmpno = empno;

    $('#loantable').DataTable({
        buttons    : ['copy', 'excel', 'pdf'],
        processing : true,
        serverSide : true,
        ajax       : "ajax_loan.php?empno=" + empno,
        columns    : [
            { data: "LNO"    },
            { data: "DTE"    },
            { data: "AMT"    },
            { data: "RATE"   },
            { data: "NOINST" },
            { data: "FLAG"   }
        ],
        order    : [[0, "asc"]],
        searching: false,
    });
}

/**
 * Initializes (or re-initializes) the Allowances & Deductions DataTable.
 * Uses server-side processing via ajax_indall.php.
 *
 * @param {string|number} empno - Employee number.
 */
function loadAllowanceDataTable(empno) {
    if (!empno) return;
    if (allowanceTableEmpno === empno && $.fn.DataTable.isDataTable('#allowtable')) return;

    if ($.fn.DataTable.isDataTable('#allowtable')) {
        $('#allowtable').DataTable().destroy();
    }

    allowanceTableEmpno = empno;

    $('#allowtable').DataTable({
        buttons    : ['copy', 'excel', 'pdf'],
        processing : true,
        serverSide : true,
        ajax       : "ajax_indall.php?empno=" + empno,
        columns    : [
            { data: "Desc"      },
            { data: "Flag"      },
            { data: "Allorded"  },
            { data: "Value"     },
            { data: "Stillvalid"}
        ],
        order    : [[0, "asc"]],
        searching: false,
    });
}


// ─────────────────────────────────────────────
// URL Utility
// ─────────────────────────────────────────────

/**
 * Reads a named query parameter from the current URL (or a supplied URL).
 *
 * @param {string}  name - Parameter name (e.g. "empno").
 * @param {string}  [url] - URL to parse; defaults to window.location.href.
 * @returns {string|null} The decoded value, empty string, or null if not found.
 */
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex   = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
    var results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


// ─────────────────────────────────────────────
// Tab-Specific Data Loaders (view fields)
// ─────────────────────────────────────────────

/**
 * Loads and displays Finance tab read-only fields for the given EMPNO.
 * @param {string|number} EMPNO
 */
function load_data_finance(EMPNO) {
    $.ajax({
        url    : '../prsApi/empmast/' + EMPNO,
        method : "GET",
        success: function (data) {
            $("#txt_finance_name").val(data.NAME);
            $("#txt_bank").val(data.DESCR);
            $("#txt_accNo").val(data.ACCNO);
            $("#txt_esic").val(data.ADDRESS1);
            $("#txt_finance_pan").val(data.PAN);
            $("#txt_basic").val(data.BASIC);
            $("#txt_payScale").val(data.PAYSCALE);
            $("#txt_pf").val(data.PFACNO);
            $("#txt_nod").val(data.DOB); // NOTE: likely wrong field; review with API.
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("load_data_finance: " + errorThrown);
        }
    });
}

/**
 * Loads and displays Leave tab read-only fields for the given EMPNO.
 * Maps the numeric weekly-off value to its day name.
 * @param {string|number} EMPNO
 */
function load_data_leave(EMPNO) {
    // Day-of-week map: API returns 1–7.
    const daysOfWeek = {
        1: "Sunday", 2: "Monday", 3: "Tuesday",
        4: "Wednesday", 5: "Thursday", 6: "Friday", 7: "Saturday"
    };

    $.ajax({
        url    : '../prsApi/empmast/' + EMPNO,
        method : "GET",
        success: function (data) {
            $("#txt_leave_name").val(data.NAME);
            $("#txt_leave").val(data.LeaveGroupDesc);
            $("#txt_shift").val(data.autoshift);
            $("#txt_rotation").val(data.autoshifts);
            $("#txt_weekly").val(daysOfWeek[data.woff] || "");
            $("#txt_Shcode").val(data.SHCODE);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("load_data_leave: " + errorThrown);
        }
    });
}

/**
 * Loads and displays Loan tab read-only name field for the given EMPNO.
 * (Loan history details are shown in the DataTable.)
 * @param {string|number} EMPNO
 */
function load_data_loan(EMPNO) {
    $.ajax({
        url    : '../prsApi/empmast/' + EMPNO,
        method : "GET",
        success: function (data) {
            $("#txt_loan_name").val(data.NAME);
            // NOTE: Finance fields (bank, basic, etc.) duplicated here from original.
            // Consider removing if these fields don't appear on the Loan tab view.
            $(".txt_payScale").val(data.PAYSCALE);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("load_data_loan: " + errorThrown);
        }
    });
}

/**
 * Loads and displays Incentive tab read-only fields for the given EMPNO.
 * @param {string|number} EMPNO
 */
function load_data_incentive(EMPNO) {
    $.ajax({
        url    : '../prsApi/empmast/' + EMPNO,
        method : "GET",
        success: function (data) {
            $("#txt_incentive_name").val(data.NAME);
            $("#txt_incentive_basic").val(data.BASIC);
            $("#txt_incentive").val(data.incentive);
            $("#txt_incrRemark").val(data.incr_remark);
            $("#txt_basicIncr").val(data.basic_increment);
            $("#txt_doi").val(data.incr_date);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("load_data_incentive: " + errorThrown);
        }
    });
}

/**
 * Loads and displays Others tab read-only fields for the given EMPNO.
 * @param {string|number} EMPNO
 */
function load_data_other(EMPNO) {
    $.ajax({
        url    : '../prsApi/empmast/' + EMPNO,
        method : "GET",
        success: function (data) {
            $("#txt_other_name").val(data.NAME);
            $("#txt_relation").val(data.relation);
            $("#txt_gname").val(data.GNAME);
            $("#txt_edu").val(data.qual);
            $("#txt_subject").val(data.subTaught);
            $("#txt_email").val(data.EMAIL);
            $("#txt_uid").val(data.uuid);
            $("#txt_uanNo").val(data.uanNo);
            $("#txt_exper").val(data.EXP);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("load_data_other: " + errorThrown);
        }
    });
}

/**
 * Fetches the last created employee number from the API and loads their data.
 * Called after a successful Save to navigate to the new record.
 */
function loadLastEmpNo() {
    $.ajax({
        url     : '../prsApi/getLastEmpNo',
        type    : 'GET',
        dataType: 'json',
        success : function (data) {
            var lastEmpNo = data.last_emp_no;
            console.log("Last Emp No:", lastEmpNo);
            load_data_general(currentIndex, lastEmpNo);
        }
    });
}


// ─────────────────────────────────────────────
// Modal: Pre-populate for Editing
// ─────────────────────────────────────────────

/**
 * Entry point for the Edit buttons on each tab.
 * Currently delegates entirely to show_general_data which populates all modals at once.
 *
 * @param {string|number} emp - Employee number to load into the modal.
 */
function showData(emp) {
    show_general_data(emp);
}

/**
 * Fetches employee data and pre-fills ALL edit modals (General, Finance, Leave,
 * Incentive, Other) in one request, then shows the Update button.
 *
 * @param {string|number} empno - Employee number.
 */
function show_general_data(empno) {
   
    $("#btn_general_save").hide();
    $("#btn_general_update").show();

    $.ajax({
        url    : '../prsApi/empmast/' + empno,
        method : "GET",
        success: function (data) {

            // --- General Modal ---
            $(".txt_EmpNo").val(data.EMPNO);
            $(".txt_Name").val(data.NAME);
            $("#txt_Comp_id").val(data.comp_id);
            $(".txt_State").val(data.STATE);
            $(".txt_Address").val(data.ADDRESS);
            $("#txt_Add1").val(data.loc);
            $(".txt_add1").val(data.loc);
            $(".txt_phone").val(data.PHONE);
            $(".txt_phone1").val(data.PHONE1);
            $("input[name='txt_sex']").prop("checked", false);
            $("input[name='txt_sex'][value='" + data.SEX + "']").prop("checked", true);
            $("input[name='txt_Mar_stat']").prop("checked", false);
            $("input[name='txt_Mar_stat'][value='" + data.mar_stat + "']").prop("checked", true);
            $(".txt_pan").val(data.PAN);
            $("#txt_Catcode").val(data.CATCODE);
            $(".txt_dob").val(data.DOB);
            $(".txt_doj").val(data.DOJ);
            $("#txt_Dsgcode").val(data.DSGCODE);
            $(".txt_dor").val(data.DOR);
            $("#txt_Doc").val(data.DOC);
            $(".txt_cardno").val(data.cardno);
            $("#txt_PHOTO").val(data.PHOTO); // Existing photo stored as base64.

            // --- Finance Modal ---
            $("#txt_Bank").val(data.BID);
            $("#txt_AccNo").val(data.ACCNO);
            $("#txt_Esic").val(data.ADDRESS1);
            $("#txt_FinancePan").val(data.PAN);
            $("#txt_Basic").val(data.BASIC);
            $(".txt_payScale").val(data.PAYSCALE);
            $("#txt_Pf").val(data.PFACNO);

            // --- Leave Modal ---
            $("#txt_Shcode").val(data.SHCODE);
            $("#txt_Leave").val(data.LeaveGroup);
            $("#txt_Rotation").val(data.autoshifts);

            // --- Incentive Modal ---
            $("#txt_incentive_basic_modal").val(data.BASIC);
            $("#txt_IncrRemark").val(data.Aincr_remark);
            $("#txt_BasicIncr").val(data.basic_increment);
            $("#txt_Doi").val(data.incr_date);

            // --- Other Modal ---
            $("#txt_Relation_modal").val(data.relation);
            updateRelationLabel(data.relation);
            $("#txt_Gname_modal").val(data.GNAME);
            $("#txt_Edu_modal").val(data.qual);
            $("#txt_Subject_modal").val(data.subTaught);
            $("#txt_Email_modal").val(data.EMAIL);
            $("#txt_Uid_modal").val(data.uuid);
            $("#txt_UanNo_modal").val(data.uanNo);
            $("#txt_Exper_modal").val(data.EXP);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("show_general_data: " + errorThrown);
        }
    });
}


// ─────────────────────────────────────────────
// Update: Existing Employee (PUT)
// ─────────────────────────────────────────────

/**
 * Handles all "Update" button clicks (.btn_update class).
 * - Syncs the photo field.
 * - Serializes the active form to JSON.
 * - Attaches the current photo (file or webcam).
 * - PUTs the payload to the API.
 */
$(".btn_update").on("click", async function (event) {
    saveAndTurnOffCamera();

    const form  = $(this).closest(".modal-content").find("form.frm_user");
    let   json  = convertFormToJSON(form);
    const empno = form.find(".txt_EmpNo, .empno").val()
        || $("#txt_EmpNo").val()
        || getParameterByName('empno');

    if (!empno) {
        alert("Employee number is required for update");
        return;
    }

    json.EmpNo = empno;

    // Attach photo: prefer file upload, fall back to webcam snapshot.
    const selectedImage = $("#txt_image")[0].files[0];
    if (selectedImage) {
        json.txt_PHOTO = await readImageFile(selectedImage);
    } else {
        const webcamData = $("#selectedImageData").val();
        if (webcamData) json.txt_PHOTO = webcamData;
    }

    console.log("Update for EMPNO:", empno);

    $.ajax({
        url        : '../prsApi/empmast/' + empno,
        type       : 'PUT',
        dataType   : 'json',
        contentType: 'application/json',
        data       : JSON.stringify(json),
        success: function (data) {
            alert(data.msg);
            if (data.status === "Ok") {
                $("#header-footer-modal-preview").hide();
                load_updated_data(empno); // Refresh view with updated data.
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("Update failed: " + errorThrown);
        }
    });
});


// ─────────────────────────────────────────────
// Navigation: Prev / Next Employee
// ─────────────────────────────────────────────

/**
 * Updates Prev/Next button disabled states based on currentIndex
 * and the total number of employees loaded from PHP.
 */
function updateButtonStatus() {
    const totalItems = <?php echo count($empnames); ?>;
    $(".prevBtn").prop("disabled", currentIndex === 0);
    $(".nextBtn").prop("disabled", currentIndex === totalItems - 1);
}

/** Advances to the next employee in the list. */
function next_data() {
    currentIndex++;
    load_data_general(currentIndex);
    updateButtonStatus();
}

/** Goes back to the previous employee in the list. */
function prev_data() {
    currentIndex--;
    load_data_general(currentIndex);
    updateButtonStatus();
}


// ─────────────────────────────────────────────
// Webcam: Configuration & Controls
// ─────────────────────────────────────────────

// Configure Webcam.js before any camera interaction.
Webcam.set({
    width        : 320,
    height       : 320,
    image_format : 'jpeg',
    jpeg_quality : 90
});

/**
 * Attaches the webcam feed to the #livePhoto container.
 * Called when the user clicks "Use Camera" in the photo selection modal.
 */
function attachCamera() {
    Webcam.attach('#livePhoto');
}

/**
 * Captures a snapshot from the webcam:
 * - Stores the base64 data URI in #selectedImageData.
 * - Displays the preview in #results.
 */
function take_snapshot() {
    Webcam.snap(function (data_uri) {
        $("#selectedImageData").val(data_uri);
        document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
    });
}

/** Stops the webcam stream and releases the camera. */
function resetwebcam() {
    Webcam.reset();
}


// ─────────────────────────────────────────────
// Photo Sync: Finalize before Submission
// ─────────────────────────────────────────────

/**
 * Before submitting any form, ensures the hidden #txt_PHOTO field
 * contains the correct base64 image data from either:
 * 1. A file chosen via the file input (#txt_image), or
 * 2. A webcam snapshot stored in #selectedImageData.
 *
 * This is called by Save and Update handlers as well as directly
 * via onclick on the Save button.
 */
function saveAndTurnOffCamera() {
    const selectedImage = $("#txt_image")[0].files[0];

    if (selectedImage) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $("#txt_PHOTO").val(e.target.result);
        };
        reader.readAsDataURL(selectedImage);
    } else if ($("#selectedImageData").val() !== "") {
        // Use webcam-captured image if no file was selected.
        $("#txt_PHOTO").val($("#selectedImageData").val());
    }
}
</script>

</html>
