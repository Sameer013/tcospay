<?php
function getActive($amenu, $tmenu)
{
    if ($amenu == $tmenu) echo "side-menu--active";
}
function getActiveTab($amenu, $bmenu)
{
    if ($amenu == $bmenu) echo "side-menu__sub-open";
}
function getActivePage($apage, $bpage)
{
    if ($apage == $bpage) echo "side-menu--active";
}
?>
<style>
    .side-menu:hover {
        color: #fff;
    }

    .side-menu__title:hover {
        color: #fff;
    }
</style>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<nav class="side-nav">
    <div class="logo" style="padding: 20px; text-align: justify; border-color: white; border-radius: 10px; border-width:0px; width:180px; ">
        <a href="index.php" class="intro-x flex col items-center">
            <img alt="" class="px-0 rounded-lg text-white" style="height: 60%; width: 60%" src="dist/images/tcoslogo.png" />
            <div>
                <span class="hidden xl:block font-bold text-4xl ml-3 text-shadow" style="color:#fff;text-shadow:2px 2px 2px #000">TCOS</span>
                <span class="hidden xl:block font-bold text-xl ml-3 text-shadow" style="color:#fff;text-shadow:2px 2px 2px #000">Payroll</span>
            </div>
        </a>
    </div>
    <div class="side-nav__devider"></div>
    <ul>
        <li>
            <a href="index.php" class="side-menu <?php getActive($amenu, "dashboard"); ?>">
                <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                <div class="side-menu__title">
                    Dashboard
                    <div class="side-menu__sub-icon transform rotate-180"> </div>
                </div>
            </a>
            <ul class="side-menu__sub-open"><!-- This class is responsible for opening the submenu in the General tab  -->
            </ul>
        <li>
            <a href="javascript:;" class="side-menu <?php getActive($amenu, "general"); ?>">
                <div class="side-menu__icon"> <i data-lucide="box" class="fa-solid fa-user"></i> </div>
                <div class="side-menu__title ">
                    General
                    <div class="side-menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>

            <ul class="<?php getActiveTab($amenu, "general"); ?>">
                <li>
                    <a href="index_general_master.php" class="side-menu <?php getActivePage($page, "emp_master"); ?> ">
                        <div class="side-menu__icon"><i class="fa-solid fa-user"></i></div>
                        <div class="side-menu__title">Employee Master</div>
                    </a>
                </li>
                <li>
                    <a href="index_holiday.php" class="side-menu <?php getActivePage($page, "holiday_master"); ?>">
                        <div class="side-menu__icon"> <i class="fa-regular fa-file-lines"></i></div>
                        <div class="side-menu__title"> Holidays Master </div>
                    </a>
                </li>

                <?php
                if ($_SESSION['user'] == 'Admin') {
                ?>

                    <li>
                        <a href="index_allowance.php" class="side-menu <?php getActivePage($page, "all_ded"); ?> ">
                            <div class="side-menu__icon"><i class="fa-solid fa-plus"></i></div>
                            <div class="side-menu__title">Allowances/Deductions </div>
                        </a>
                    </li>
                     <li>
                        <a href="index_allowDeduct.php" class="side-menu <?php getActivePage($page, "arrear"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-wallet"></i></div>
                            <div class="side-menu__title">Individual Allowances/Deductions </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_degination.php" class="side-menu <?php getActivePage($page, "designatopn"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-pen-nib"></i></div>
                            <div class="side-menu__title"> Designation Master</div>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="index_company.php" class="side-menu <?php getActivePage($page, "company_master"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-city"></i></div>
                            <div class="side-menu__title"> Company Master </div>
                        </a>
                    </li> -->
                    <!-- <li>
                        <a href="index_attendance.php" class="side-menu <?php getActivePage($page, "attd_machine"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-clipboard-user"></i></div>
                            <div class="side-menu__title">Attendance Machine </div>
                        </a>
                    </li> -->
                    <!-- <li>
                        <a href="index_leaveType.php" class="side-menu <?php getActivePage($page, "lt_master"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i></div>
                            <div class="side-menu__title"> Leave Type Master </div>
                        </a>
                    </li> -->
                    <!-- <li>
                        <a href="index_leaveGroup.php" class="side-menu <?php getActivePage($page, "lg_master"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-person-walking-arrow-right"></i></div>
                            <div class="side-menu__title">Leave Group Master </div>
                        </a>
                    </li> -->
                    <!-- <li>
                        <a href="index_shift.php" class="side-menu <?php getActivePage($page, "shift_master"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-person-booth"></i></div>
                            <div class="side-menu__title">Shift Master </div>
                        </a>
                    </li> -->
                   
                    <li>
                        <a href="index_bank.php" class="side-menu <?php getActivePage($page, "bank_master"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-city"></i></div>
                            <div class="side-menu__title">Bank Master </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_category.php" class="side-menu <?php getActivePage($page, "cat_master"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-table-columns"></i></div>
                            <div class="side-menu__title">Category Master </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_location.php" class="side-menu <?php getActivePage($page, "loc_master"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="side-menu__title">Location Master </div>
                        </a>
                    </li>

                <?php
                }
                ?>

            </ul>
        </li>
        <li>
            <a href="javascript:;" class="side-menu <?php getActive($amenu, "transaction"); ?>">
                <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                <div class="side-menu__title">
                    Transaction
                    <div class="side-menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>
            <ul class="<?php getActiveTab($amenu, "transaction"); ?>">
                <li>
                    <a href="index_trans_leave.php" class="side-menu <?php getActive($page, "leave"); ?>">
                        <div class="side-menu__icon"><i class="fa-solid fa-person-walking-arrow-right"></i></div>
                        <div class="side-menu__title">Leave </div>
                    </a>
                </li>
                <li>
                    <a href="index_LeaveApplication.php" class="side-menu <?php getActive($page, "leaveApp"); ?>">
                        <div class="side-menu__icon"> <i class="fa-solid fa-piggy-bank"></i></div>
                        <div class="side-menu__title">Leave Application </div>
                    </a>
                </li>

                <?php
                if ($_SESSION['user'] == 'Admin') {
                ?>

                    <li>
                        <a href="index_daily_attendence.php" class="side-menu <?php getActive($page, "daatt"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-clipboard-user"></i></div>
                            <div class="side-menu__title">Daily Attendence </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_monthly_attendence.php" class="side-menu <?php getActive($page, "mnatt"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-address-book"></i></div>
                            <div class="side-menu__title">Monthly Attendence</div>
                        </a>
                    </li>
                    <li>
                        <a href="index_loan_entry.php" class="side-menu <?php getActive($page, "loanent"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-money-bill-wave"></i></div>
                            <div class="side-menu__title">Advance Payment</div>
                        </a>
                    </li>
                    <li>
                        <a href="index_attendanceMachine.php" class="side-menu <?php getActive($page, "attmac"); ?>">
                            <div class="side-menu__icon"> <i class="fa-solid fa-clipboard-user"></i></div>
                            <div class="side-menu__title">Attendence Machine </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_cat_shift_change.php" class="side-menu <?php getActive($page, "sftch"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-person-booth"></i></i></div>
                            <div class="side-menu__title">Shift Change </div>
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </li>

        <?php
        if ($_SESSION['user'] == 'Admin') {
        ?>

            <li>
                <a href="javascript:;" class="side-menu <?php getActive($amenu, "payroll"); ?>">
                    <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                    <div class="side-menu__title">
                        Payroll
                        <div class="side-menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                    </div>
                </a>
                <ul class="<?php getActiveTab($amenu, "payroll"); ?>">
                    <li>
                        <a href="index_payroll_processing.php" class="side-menu <?php getActive($page, "paypro"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-microchip"></i></div>
                            <div class="side-menu__title">Pre-Payroll Processing</div>
                        </a>
                    </li>
                    <li>
                        <a href="index_payroll_generation.php" class="side-menu <?php getActive($page, "paygen"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-dna"></i></div>
                            <div class="side-menu__title">Payroll Generate</div>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="index_repair.php" class="side-menu <?php getActive($page, "repair"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-hotel"></i></div>
                            <div class="side-menu__title">Repair</div>
                        </a>
                    </li> -->
                </ul>
            </li>

        <?php
        }
        ?>

        <li>
            <a href="javascript:;" class="side-menu <?php getActive($amenu, "statement"); ?>">
                <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                <div class="side-menu__title">
                    Statement
                    <div class="side-menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>
            <ul class="<?php getActiveTab($amenu, "statement"); ?>">
                <li>
                    <a href="index_salary.php" class="side-menu <?php getActive($page, "salary"); ?>">
                        <div class="side-menu__icon"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div class="side-menu__title">Salary Reports</div>
                    </a>
                </li>

                <?php
                if ($_SESSION['user'] == 'Admin') {
                ?>

                    <!-- <li>
                        <a href="index_bank_report.php" class="side-menu <?php getActive($page, "bank"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-landmark-dome"></i></div>
                            <div class="side-menu__title">Bank Reports</div>
                        </a>
                    </li> -->
                    <!-- <li>
                        <a href="index_leave_report.php" class="side-menu <?php getActive($page, "annrpt"); ?>">
                            <div class="side-menu__icon"><i class="fa-solid fa-file-export"></i></div>
                            <div class="side-menu__title">Leave Reports</div>
                        </a>
                    </li> -->
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="side-menu <?php getActive($amenu, "administrative"); ?>">
                <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                <div class="side-menu__title">
                    Administrative Tools
                    <div class="side-menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>
            <ul class="<?php getActiveTab($amenu, "administrative"); ?>">
                <li>
                    <a href="index_user.php" class="side-menu <?php getActive($page, "user"); ?>">
                        <div class="side-menu__icon"><i class="fa-solid fa-user-tie"></i></div>
                        <div class="side-menu__title">User Manager</div>
                    </a>
                </li>

            </ul>
        </li>

    <?php
                }
    ?>

    </ul>
    <div class="side-nav__devider"></div>
</nav>