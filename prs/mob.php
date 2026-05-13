<?php
    require_once "includes/dbconn.php";

    function getActiveMob($amenu,$tmenu)
    {
        if ($amenu==$tmenu) echo "menu--active";
    }
?>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
</head>
<div class="mobile-menu md:hidden" style="margin-top: -75px;">
    <div class="mobile-menu-bar">
        <a href="" class="flex mr-auto">
        <img alt="" class="h-12 rounded-lg tex-white" src="dist/images/jpslogo.png">
            <span class="xl:block text-white font-bold text-4xl ml-3 text-shadow"  style="color:#fff;text-shadow:2px 2px 2px #000">JPS Payroll</span>
        </a>
        <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i> </a>
    </div>
    <div class="scrollable">
        <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i> </a>
        <ul class="scrollable__content py-2">
            <li>
                <a href="index.php" class="menu ">
                    <div class="menu__icon"> <i data-lucide="home"></i> </div>
                    <div class="menu__title">
                        Dashboard
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="menu">
                    <div class="menu__icon"> <i data-lucide="box"></i> </div>
                    <div class="menu__title">
                        General
                        <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                    </div>
                </a>
                <ul class="">

                    <li>
                        <a href="index_general_master.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Employee Master </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_holiday.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-truck-arrow-right"></i> </div>
                            <div class="menu__title"> Holidays Master </div>
                        </a>
                    </li>
                    <?php
                        if ($_SESSION['user'] == 'Admin') {
                    ?>
                    <li>
                        <a href="index_allowance.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark"></i> </div>
                            <div class="menu__title">Allowances/Deductions</div>
                        </a>
                    </li>
                    <li>
                        <a href="index_degination.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-hotel"></i> </div>
                            <div class="menu__title">  Designation Master </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_company.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-city"></i> </div>
                            <div class="menu__title">Company Master </div>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="index_attendance.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-truck-front"></i> </div>
                            <div class="menu__title"> Attendance Machine</div>
                        </a>
                    </li> -->
                    <li>
                        <a href="index_leaveType.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-truck-fast"></i> </div>
                            <div class="menu__title">
                            Leave Type Master
                            </div>
                        </a>

                    </li>
                    <li>
                        <a href="index_leaveGroup.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-regular fa-file-lines"></i> </div>
                            <div class="menu__title">
                            Leave Group Master
                                <div class="menu__sub-icon "></i> </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_shift.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-regular fa-file-lines"></i> </div>
                            <div class="menu__title">
                            Shift Master
                                <div class="menu__sub-icon "></i> </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_allowDeduct.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-regular fa-file-lines"></i> </div>
                            <div class="menu__title">
                            Special Allowances/Deductions
                                <div class="menu__sub-icon "></i> </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_bank.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-regular fa-file-lines"></i> </div>
                            <div class="menu__title">
                            Bank Master
                                <div class="menu__sub-icon "></i> </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_category.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-regular fa-file-lines"></i> </div>
                            <div class="menu__title">
                            Category Master 
                                <div class="menu__sub-icon "></i> </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_location.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-regular fa-file-lines"></i> </div>
                            <div class="menu__title">
                            Location Master 
                                <div class="menu__sub-icon "></i> </div>
                            </div>
                        </a>
                    </li>
                                <?php
                }
            ?>
                </ul>
            </li>

            
            <li>
                <a href="javascript:;" class="menu">
                    <div class="menu__icon"> <i data-lucide="box"></i> </div>
                    <div class="menu__title">
                    Transaction
                        <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                    </div>
                </a>
                <ul class="">

                    <li>
                        <a href="index_trans_leave.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Leave </div>
                        </a>
                    </li>

                    <li>
                        <a href="index_LeaveApplication.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Leave Application</div>
                        </a>
                    </li>

                    <?php
                        if ($_SESSION['user'] == 'Admin') {
                    ?>
                    <li>
                        <a href="index_daily_attendence.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Daily Attendence </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_monthly_attendence.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Monthly Attendence </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_advPayment.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Advance Payment </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_attendanceMachine.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Attendence Machine </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_cat_shift_change.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Shift Change </div>
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
                <a href="javascript:;" class="menu">
                    <div class="menu__icon"> <i data-lucide="box"></i> </div>
                    <div class="menu__title">
                    Payroll
                        <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                    </div>
                </a>
                <ul class="">

                    <li>
                        <a href="index_payroll_processing.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Pre-Payroll Processing </div>
                        </a>
                    </li>
                    <li>
                        <a href="index_payroll_generation.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Payroll Generate </div>
                        </a>
                    </li>
                </ul>
            </li>
                <?php
                    }
                ?>

            <li>
                <a href="javascript:;" class="menu">
                    <div class="menu__icon"> <i data-lucide="box"></i> </div>
                    <div class="menu__title">
                    Statement
                        <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                    </div>
                </a>
                <ul class="">

                    <li>
                        <a href="index_salary.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Report </div>
                        </a>
                    </li>
                                    <?php
                if ($_SESSION['user'] == 'Admin') {
                ?>
                    <!-- <li>
                        <a href="index_annual_reports.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> Annual Reports </div>
                        </a>
                    </li> -->
                </ul>
            </li>

            <li>
                <a href="javascript:;" class="menu">
                    <div class="menu__icon"> <i data-lucide="box"></i> </div>
                    <div class="menu__title">
                    Administrative Tools
                        <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                    </div>
                </a>
                <ul class="">

                    <li>
                        <a href="index_user.php" class="menu">
                            <div class="menu__icon"> <i data-lucide="fa-solid fa-landmark-dome"></i> </div>
                            <div class="menu__title"> User Manager </div>
                        </a>
                    </li>
                </ul>
            </li>
    <?php
                }
    ?>
        </ul>
    </div>
</div>