<?php
session_start();
if ((!isset($_SESSION['user']))) {
    header('refresh: 1;url=login.php');
    die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
}
?>


<?php
include('includes/dbconn.php');
require_once 'includes/loan_calc.php';
$stmt = $db->prepare("SELECT EMPNO,NAME from empmast");
$stmt->execute();
$empnames = $stmt->fetchAll();
$loanTypes = $db->query("SELECT * FROM loantype ORDER BY SORT, CODE")->fetchAll(PDO::FETCH_ASSOC);
$loanMethods = loanMethods();
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
    <title>Payroll - Loan</title>
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
        $amenu = "transaction";
        $page = "loanent";
        include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Transaction";
            $currentPage = "Loan Entery";
            include 'top.php'
            ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                    Loans &amp; Advances
                </h2>
            </div>
            <button class="btn btn-primary w-20 shadow-md mr-2 mt-5 mb-2 rounded-full"
                onclick="add_new()" data-tw-toggle="modal"
                data-tw-target="#header-footer-modal-preview-view">Add
            </button>
            <button class="btn btn-outline-primary shadow-md mr-2 mt-5 mb-2 rounded-full loan-admin"
                data-tw-toggle="modal" data-tw-target="#loantype-modal">Loan Types
            </button>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12 ">
                    <!-- <?php
                            if ($_SESSION['user'] == 'Admin') {
                            ?>
                        <button class="btn btn-primary w-20 shadow-md mr-2 rounded-full"
                            onclick="add_new()" data-tw-toggle="modal"
                            data-tw-target="#header-footer-modal-preview-view">Add
                        </button>
                    <?php
                            }
                    ?> -->
                    <!-- BEGIN: Responsive Table -->
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <table id="table" class="table table-bordered table-striped w-full" cellpadding="7px">
                                        <thead>
                                            <tr>
                                                <th>Loan No</th>
                                                <th>Emp Name</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Rate %</th>
                                                <th>Interest Method</th>
                                                <th>Tenure (months)</th>
                                                <th>Balance</th>
                                                <th>Status</th>
                                                <th>Action</th>
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
            <!-- <div class="mx-auto p-4" style="margin: 0;">
                <div class="bg-white shadow-lg rounded-lg p-6">

                    <form id="form-wizard1" class="mt-2 p-8 text-left">
                        <fieldset>
                            <div class="form-card">
                                <div class="container">


                                    <div class="my-5"></div>

                                    <div class="grid grid-cols-12 text-dark">
                                        <label class="col-span-2 align-self-center mb-5 flex items-center" for="txt_loanNo">Loan No: </label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class=" p-2 text-dark w-full" id="txt_loanNo" name="txt_loanNo" placeholder="Enter SL No" Readonly>
                                        </div>
                                        <div class="col-span-1"></div>
                                        <label class="col-span-1 align-self-center mb-5 flex items-center" for="txt_empno">Emp No: </label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class=" p-2 text-dark w-full" id="txt_empno" name="txt_empno" placeholder="Enter Employee No" Readonly>
                                        </div>
                                        <div class="col-span-1"></div>
                                        <label class="col-span-1 align-self-center mb-5 flex items-center" for="txt_date">Date: </label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class=" p-2 text-dark w-full" id="txt_date" name="txt_date" placeholder="Enter Date" Readonly>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 text-dark">
                                        <label class="col-span-2 align-self-center mb-5 font-extrabold  flex items-center" for="txt_empName">Employee Name: </label>
                                        <div class="col-span-3 mb-5">
                                            <input type="text" class="p-2 font-extrabold border-0 text-dark w-full" id="txt_empName" name="txt_empName" placeholder="Enter Employee Name" Readonly>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 text-dark">
                                        <label class="col-span-2 align-self-center mb-5  flex items-center" for="txt_description">Description: </label>
                                        <div class="col-span-6 mb-5">
                                            <input type="text" class="p-2 text-dark w-full" id="txt_description" name="txt_description" placeholder="Enter Description" Readonly>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 text-dark">
                                        <label class="col-span-2 align-self-center mb-5  flex items-center" for="txt_amount">Amount:</label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class="p-2 text-dark w-full" id="txt_amount" name="txt_amount" placeholder="Show Amount">
                                        </div>
                                        <div class="col-span-1"></div>
                                        <label class="col-span-1 align-self-center mb-5  flex items-center" for="txt_noi">No of Installment:</label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class="p-2 text-dark w-full" id="txt_noi" name="txt_noi" placeholder="No of Installment">
                                        </div>

                                        <label class="col-span-2 align-self-center mb-5 ml-5 flex items-center" for="txt_cleared">Cleared:</label>
                                        <div class="col-span-2 mb-5">
                                            <input type="checkbox" class="" id="txt_cleared" name="txt_cleared" style="margin-top: 15px;">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 text-dark">
                                        <label class="col-span-2 align-self-center mb-5 flex items-center" for="txt_intrestRate">Intrest Rate: </label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class=" p-2 text-dark w-full" id="txt_intrestRate" name="txt_intrestRate" placeholder="Enter Intrest Rate" Readonly>
                                        </div>
                                        <div class="col-span-2"></div>
                                        <label class="col-span-2 align-self-center mb-5 flex items-center" for="txt_intallmentMonth">Installment/Month: </label>
                                        <div class="col-span-2 mb-5">
                                            <input type="text" class=" p-2 text-dark w-full" id="txt_intallmentMonth" name="txt_intallmentMonth" placeholder="Enter Installment/month" Readonly>
                                        </div>
                                    </div>
                                    <div class="form-card text-start mt-5">
                                        <div class="row">
                                            <div class="col-7">
                                                <h5 class="mb-4">Repayment Detail:</h5>
                                            </div>
                                        </div>
                                        <div class="preview">
                                            <div class="overflow-x-auto">
                                                <table id="loantable" class="table table-bordered table-striped " style="width:100%" cellpadding="7px">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">Inst. Sl No</th>
                                                            <th width="15%">Due Date</th>
                                                            <th width="20%">Installment Amount</th>
                                                            <th width="20%">Status</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div> -->
            <!-- <center>
                <div class="form-group mt-6">
                    <button type="button" class="prevBtn btn mx-4 btn-outline-primary rounded-full w-20" value="Submit">Prev</button>
                    <button type="button" class="nextBtn btn mx-4 btn-outline-primary rounded-full w-20" value="Submit">Next</button>
                </div>
            </center> -->
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
                                    <input id="txt_lno" name="txt_lno" type="hidden" class="form-control " placeholder="Loan No" readonly>

                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_empno" class="form-label">Emp No</label>
                                        <!-- <input id="txt_empno" name="txt_empno" type="text" class="form-control " placeholder="Employee No"> -->
                                        <select name="txt_empno" id="txt_empno" class="form-control">
                                            <option value="" selected disabled>--Select Emp No--</option>
                                            <?php foreach ($empnames as $empname) { ?>
                                                <option value="<?= $empname['EMPNO'] ?>"><?= $empname['EMPNO'] . ' - ' . $empname['NAME'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_ltype" class="form-label">Loan Type</label>
                                        <select name="txt_ltype" id="txt_ltype" class="form-control">
                                            <?php foreach ($loanTypes as $type) { ?>
                                                <option value="<?= htmlspecialchars($type['CODE']) ?>"><?= htmlspecialchars($type['NAME']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_date" class="form-label">Date</label>
                                        <input id="txt_date" name="txt_date" type="date" class="form-control " placeholder="Date">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_descr" class="form-label">Description</label>
                                        <input id="txt_descr" name="txt_descr" type="text" class="form-control " placeholder="Description">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_amount" class="form-label">Amount</label>
                                        <input id="txt_amount" name="txt_amount" type="text" class="form-control " placeholder="Amount">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_cleared" class="form-label">Cleared</label><br>
                                        <input id="txt_cleared" name="txt_cleared" type="checkbox" value="0" onchange="updateCheckboxValue(this)">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="txt_nofinst" class="form-label">Tenure (months)</label>
                                        <input id="txt_nofinst" name="txt_nofinst" type="number" min="1" step="1" class="form-control" placeholder="No of monthly installments">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 interest-field">
                                        <label for="txt_method" class="form-label">Interest Method</label>
                                        <select name="txt_method" id="txt_method" class="form-control">
                                            <?php foreach ($loanMethods as $code => $label) { ?>
                                                <option value="<?= $code ?>"><?= $label ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 interest-field">
                                        <label for="txt_intRate" class="form-label">Interest Rate (% per year)</label>
                                        <input id="txt_intRate" name="txt_intRate" type="number" min="0" step="0.01" class="form-control" placeholder="Rate">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6" id="intMonthsField">
                                        <label for="txt_intMonths" class="form-label">Recover Interest Over (months)</label>
                                        <input id="txt_intMonths" name="txt_intMonths" type="number" min="1" step="1" class="form-control" placeholder="Months after principal is repaid">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label class="form-label">First Month Deduction</label>
                                        <div id="firstInst" class="form-control bg-slate-100">-</div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-12" id="paytbl">
                                        <label for="txt_paytable" class="form-label">Repayment Schedule</label>
                                        <div class="text-xs text-slate-500 mb-2">Paid rows come from processed payrolls ("Manual" = a hand-typed head was deducted instead). Pending rows are projected and update when the loan is edited.</div>
                                        <div class="preview">
                                            <div class="overflow-x-auto">
                                                <table id="paytable" class="table table-bordered table-striped" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Sl No</th>
                                                            <th>Month</th>
                                                            <th>Principal</th>
                                                            <th>Interest</th>
                                                            <th>Total</th>
                                                            <th>Balance</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                </table>
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

                <!-- BEGIN: Loan Types Modal -->
                <div id="loantype-modal" class="modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="font-bold text-base mr-auto">Loan Types</h2>
                            </div>
                            <div class="modal-body">
                                <div class="text-xs text-slate-500 mb-3">Defaults filled in when a new loan of that type is added. Changing them does not change existing loans; edit a loan to change its own method or rate.</div>
                                <div class="overflow-x-auto">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Deduction Heads</th>
                                                <th>Interest Method</th>
                                                <th>Rate % / year</th>
                                                <th>Interest Recovery (months)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($loanTypes as $type) {
                                                $hasInt = trim((string) $type['INT_HEAD']) !== ''; ?>
                                                <tr data-code="<?= htmlspecialchars($type['CODE']) ?>">
                                                    <td><?= htmlspecialchars($type['NAME']) ?></td>
                                                    <td><?= htmlspecialchars($type['PRIN_HEAD']) . ($hasInt ? ' / ' . htmlspecialchars($type['INT_HEAD']) : '') ?></td>
                                                    <td>
                                                        <select class="form-control lt-method" <?= $hasInt ? '' : 'disabled' ?>>
                                                            <?php foreach ($loanMethods as $code => $label) { ?>
                                                                <option value="<?= $code ?>" <?= $type['METHOD'] === $code ? 'selected' : '' ?>><?= $label ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" min="0" step="0.01" class="form-control lt-rate" value="<?= htmlspecialchars($type['RATE']) ?>" <?= $hasInt ? '' : 'disabled' ?>></td>
                                                    <td><input type="number" min="1" step="1" class="form-control lt-intmonths" value="<?= htmlspecialchars((string) $type['INT_MONTHS']) ?>" <?= $hasInt ? '' : 'disabled' ?>></td>
                                                    <td><?php if ($hasInt) { ?><button type="button" class="btn btn-primary btn-sm rounded-full lt-save">Save</button><?php } ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 rounded-full">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Loan Types Modal -->
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
    var paytable;
    $(document).ready(function() {
        const money = function(data) {
            return Number(data).toFixed(2);
        };
        paytable = $('#paytable').DataTable({
            "searching": false,
            "ordering": false,
            "pageLength": 12,
            "lengthChange": false,
            "ajax": {
                "url": "ajax_repayment.php",
                "type": "GET",
                "data": function(d) {
                    d.lno = $("#txt_lno").val();
                }
            },
            "columns": [{
                    "data": "slno"
                },
                {
                    "data": "month"
                },
                {
                    "data": "prin",
                    "render": money
                },
                {
                    "data": "int",
                    "render": money
                },
                {
                    "data": "total",
                    "render": money
                },
                {
                    "data": "balance",
                    "render": money
                },
                {
                    "data": "status"
                }
            ],
            "createdRow": function(row, data) {
                if (data.status === 'Pending') $(row).addClass('text-slate-500');
            }
        });

        $("#txt_ltype").on("change", function() {
            applyTypeDefaults();
            updateLoanForm();
        });
        $("#txt_method, #txt_amount, #txt_intRate, #txt_nofinst, #txt_intMonths").on("change input", updateLoanForm);

        $(".lt-save").on("click", function() {
            const tr = $(this).closest("tr");
            const code = tr.data("code");
            $.ajax({
                url: '../prsApi/loantype/' + code,
                type: 'PUT',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    method: tr.find(".lt-method").val(),
                    rate: tr.find(".lt-rate").val(),
                    intMonths: tr.find(".lt-intmonths").val()
                }),
                success: function(data) {
                    if (data.status == "Ok") {
                        const type = loanTypes.find(t => t.CODE == code);
                        type.METHOD = tr.find(".lt-method").val();
                        type.RATE = tr.find(".lt-rate").val();
                        type.INT_MONTHS = tr.find(".lt-intmonths").val();
                        Swal.fire({ title: data.msg, icon: 'success', timer: 1200, showConfirmButton: false });
                    } else {
                        Swal.fire(data.msg, '', 'error');
                    }
                }
            });
        });
    });

    var userId = <?php echo json_encode(isset($_SESSION['user']) ? $_SESSION['user'] : null); ?>;
    var isAdmin = userId === 'Admin';
    var loanTypes = <?php echo json_encode($loanTypes); ?>;
    if (!isAdmin) $(".loan-admin").hide();

    function currentType() {
        return loanTypes.find(t => t.CODE == $("#txt_ltype").val()) || {};
    }

    // New loan: take rate / method from the loan type's defaults
    function applyTypeDefaults() {
        const type = currentType();
        $("#txt_method").val(type.METHOD || 'NONE');
        $("#txt_intRate").val(type.RATE || 0);
        $("#txt_intMonths").val(type.INT_MONTHS || '');
    }

    // Shows only the fields that apply and previews the first month's deduction
    function updateLoanForm() {
        const type = currentType();
        const hasInt = !!(type.INT_HEAD && type.INT_HEAD.trim());
        $(".interest-field").toggle(hasInt);
        if (!hasInt) $("#txt_method").val('NONE');
        const method = $("#txt_method").val();
        $("#intMonthsField").toggle(hasInt && method === 'PRINFIRST');

        const amt = parseFloat($("#txt_amount").val()) || 0;
        const n = parseInt($("#txt_nofinst").val()) || 0;
        const rate = parseFloat($("#txt_intRate").val()) || 0;
        if (!amt || !n) {
            $("#firstInst").text('-');
            return;
        }
        const prin = Math.min(amt, Math.round(amt / n * 100) / 100);
        const int = (method === 'REDUCING' || method === 'FLAT') ? Math.round(amt * rate / 1200 * 100) / 100 : 0;
        let text = (type.PRIN_HEAD || 'Principal') + ' ' + prin.toFixed(2);
        if (hasInt) text += ' + ' + type.INT_HEAD + ' ' + int.toFixed(2) + ' = ' + (prin + int).toFixed(2);
        if (method === 'PRINFIRST') text += ' (interest after principal)';
        $("#firstInst").text(text);
    }
    var dtable = $('#table').DataTable({
        buttons: ['copy', 'excel', 'pdf'],
        "processing": true,
        "searching": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax_advance.php",
        },
        "columns": [{
                "data": "lno"
            },
            {
                "data": "name"
            },
            {
                "data": "ltype"
            },
            {
                "data": "dte"
            },
            {
                "data": "descr"
            },
            {
                "data": "amt"
            },
            {
                "data": "rate"
            },
            {
                "data": "method"
            },
            {
                "data": "noinst"
            },
            {
                "data": "balance",
                "orderable": false
            },
            {
                "data": "status",
                "orderable": false,
                "render": function(data, type, row) {
                    return data === 0 || data === null ? "Pending" : data === 1 ? "Cleared" : data;
                }
            },
            isAdmin ? {
                "data": "action",
                "orderable": false
            } : {
                "data": "action",
                "visible": false
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

    function updateCheckboxValue(checkbox) {
        checkbox.value = checkbox.checked ? "1" : "0";
        console.log("Checkbox value updated to:", checkbox.value);
    }

    function add_new() {
        $("#btn_save").show();
        $("#btn_update").hide();
        $("#paytbl").hide();
        $("#txt_id").removeAttr("readonly");
        $('#frm_user').trigger("reset");
        $("#txt_lno").val('');
        $("#txt_ltype").prop("disabled", false);
        applyTypeDefaults();
        updateLoanForm();
    }

    function loanFormJSON() {
        const json = convertFormToJSON($("#frm_user"));
        json.ltype = $("#txt_ltype").val(); // disabled selects are not serialized
        json.method = $("#txt_method").val();
        return json;
    }

    function saveResult(data) {
        if (data.status == "Ok") {
            dtable.draw();
        } else {
            Swal.fire(data.msg, '', 'error');
        }
    }

    $("#btn_update").on("click", function() {
        var code = $("#txt_lno").val();
        $.ajax({
            url: '../prsApi/advpayment/' + code,
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            success: saveResult,
            data: JSON.stringify(loanFormJSON())
        });
    });

    $("#btn_save").on("click", function() {
        $.ajax({
            url: '../prsApi/advpayment',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            success: saveResult,
            data: JSON.stringify(loanFormJSON())
        });
    });

    function reloadPaytable() {
        paytable.ajax.reload(null, false);
    }

    function load_data(code) {
        $("#btn_save").hide();
        $("#btn_update").show();
        $("#paytbl").show();
        $.ajax({
            url: '../prsApi/advpayment/' + code,
            method: "GET",
            success: function(res) {
                $("#txt_empno").val(res.EMPNO);
                $("#txt_empName").val(res.NAME);
                $("#txt_ltype").val(res.LTYPE).prop("disabled", true); // installments already use the type's heads
                $("#txt_date").val(res.DTE);
                $("#txt_descr").val(res.DESCR);
                $("#txt_amount").val(res.AMT);
                $("#txt_cleared").val(res.FLAG);
                $("#txt_intRate").val(res.RATE);
                $("#txt_method").val(res.METHOD);
                $("#txt_nofinst").val(res.NOINST);
                $("#txt_intMonths").val(res.INT_MONTHS || '');
                $("#txt_cleared").prop("checked", res.FLAG == 1);
                $("#txt_lno").val(res.LNO);
                updateLoanForm();
                reloadPaytable();
            }

        });
    }


    function remove_data(code) {
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
                    url: '../prsApi/advpayment/' + code,
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
</script>

</html>