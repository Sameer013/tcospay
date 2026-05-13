<?php
ini_set('display_errors', 1);
    session_start();
    if (!isset($_SESSION['user']))
{
    header('location:login.php');
    exit;
}
include ('includes/dbconn.php');
$sql="SELECT COUNT(*) FROM empmast";
$stmt = $db->prepare($sql);
$stmt->execute();
$totalEmp = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) FROM empmast WHERE SEX = 'F'";
$stmt = $db->prepare($sql);
$stmt->execute();
$totalFemale = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) FROM empmast WHERE SEX = 'M'";
$stmt = $db->prepare($sql);
$stmt->execute();
$totalMale = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) AS LS FROM empmast e WHERE TIMESTAMPDIFF(YEAR, e.DOC, DATE(CONCAT(YEAR(CURDATE()), '-04-01'))) > 6;";
$stmt = $db->prepare($sql);
$stmt->execute();
$lsaRecords = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) AS LS FROM empmast e WHERE TIMESTAMPDIFF(YEAR, e.DOB, DATE(CONCAT(YEAR(CURDATE()), '-04-01'))) > 50;";
$stmt = $db->prepare($sql);
$stmt->execute();
$dobless = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) AS LS FROM empmast e WHERE TIMESTAMPDIFF(YEAR, e.DOB, DATE(CONCAT(YEAR(CURDATE()), '-04-01'))) < 40;";
$stmt = $db->prepare($sql);
$stmt->execute();
$dobbetween = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) AS LS FROM empmast e WHERE TIMESTAMPDIFF(YEAR, e.DOB, DATE(CONCAT(YEAR(CURDATE()), '-04-01'))) BETWEEN 40 AND 50;";
$stmt = $db->prepare($sql);
$stmt->execute();
$dobmore = $stmt->fetchColumn();





$stmt = $db->query("
    SELECT COUNT(*) as total, d.DESCR
    FROM empmast e
    JOIN dsgmast d on d.DCODE = e.DSGCODE
    GROUP BY e.DSGCODE
");

$labels = [];
$counts = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $labels[] = $row['DESCR'];
    $counts[] = $row['total'];
}

// Encode for safe JS embedding
$labels_json = json_encode($labels);
$counts_json = json_encode($counts);



?>
<!DOCTYPE html>
<html lang="en" class="light">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <link href="dist/images/logo.svg" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Midone admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
        <meta name="keywords" content="admin template, Midone Admin Template, dashboard template, flat admin template, responsive admin template, web app">
        <meta name="author" content="LEFT4CODE">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
        <title>Dashboard</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="dist/css/app.css" />
        <!-- END: CSS Assets-->

        <style>
            @media (max-width: 768px) {
                .grid-cols-12 {
                    grid-template-columns: repeat(1, minmax(0, 1fr));
                }
            }

            #cnt{
                display: inline-block;
                margin: 0 50px 0;
            }
            #card{
                display: inline-block;
                margin:  10px;
            }
            section{
                padding: 10px;
                display: inline-block;
                margin: 10px;
                /* border-width: 2px;
                border-image: linear-gradient(150deg, blue, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)) 1;             */
            }
            #heading{
                margin: 5px
            }

        </style>
    </head>
    <!-- END: Head -->
    <body class="py-0">
        <!-- BEGIN: Mobile Menu -->
            <?php include 'mob.php' ?>
        <!-- END: Mobile Menu -->
        <div class="flex mt-[4.7rem] md:mt-0">
            <!-- BEGIN: Side Menu -->
            <?php
                $amenu="dashboard";
                include 'nav.php'
            ?>
            <!-- END: Side Menu -->
            <!-- BEGIN: Content -->
            <div class="content">
                <!-- BEGIN: Top Bar -->
                <?php
                    $menu_title = "Application";
                    $currentPage = "Dashboard";
                    include 'top.php'
                ?>
                <!-- END: Top Bar -->
                 <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">
                        Dashboard
                    </h2>
                </div>
                <!-- <div class="flex items-center justify-center">
                    <img src="./dist/images/Payroll-Banner.png" style="padding: 50px 0px 0px 0px"alt="........">
                </div> -->


          <!-- commented -->
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 2xl:col-span-12">
                        <div class="grid grid-cols-12 gap-6">
                            <!-- BEGIN: General Report -->
                            <div class="col-span-12 mt-8">
                                <!-- <div class="intro-y flex items-center h-10">
                                    <h2 class="text-lg font-medium truncate mr-5">
                                        General Report
                                    </h2>
                                </div> -->
                                <div class="grid grid-cols-12 gap-6 mt-5">
                                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                        <div class="report-box zoom-in">
                                            <div class="box p-5">
                                                <div class="flex">
                                                    <i data-lucide="users" class="report-box__icon text-primary"></i>
                                                </div>
                                                <div class="text-3xl font-medium leading-8 mt-6"><?= $totalEmp ?>
                                                </div>
                                                <div class="text-base text-slate-500 mt-1">Total Employee</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                        <div class="report-box zoom-in">
                                            <div class="box p-5">
                                                <div class="flex">
                                                    <i data-lucide="user" class="report-box__icon text-pending"></i>
                                                </div>
                                                <div class="text-3xl font-medium leading-8 mt-6"><?= $totalMale ?></div>
                                                <div class="text-base text-slate-500 mt-1">Male Employee</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                        <div class="report-box zoom-in">
                                            <div class="box p-5">
                                                <div class="flex">
                                                    <i data-lucide="user" class="report-box__icon text-warning"></i>
                                                </div>
                                                <div class="text-3xl font-medium leading-8 mt-6"><?= $totalFemale ?></div>
                                                <div class="text-base text-slate-500 mt-1">Female Employee</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                        <div class="report-box zoom-in">
                                            <div class="box p-5">
                                                <div class="flex">
                                                    <i data-lucide="user" class="report-box__icon text-success"></i>
                                                </div>
                                                <div class="text-3xl font-medium leading-8 mt-6"><?= $lsaRecords ?></div>
                                                <div class="text-base text-slate-500 mt-1">Long Service Employee</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END: General Report -->
                            <!-- BEGIN: Sales Report -->
                           <div class="col-span-12 lg:col-span-9 mt-8">
                                <div class="intro-y block sm:flex items-center h-10">
                                    <h2 class="text-lg font-medium truncate mr-5">
                                        Designation Wise Employee
                                    </h2>
                                </div>
                                <div class="intro-y box p-5 mt-12 sm:mt-5">
                                    <div class="flex flex-col md:flex-row md:items-center">
                                        <!-- Optional filters/buttons here -->
                                    </div>
                                    <div class="report-chart">
                                        <div class="h-[400px]">
                                            <canvas id="vertical-bar-chart-designation"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END: Sales Report -->
                            <!-- BEGIN: Weekly Top Seller -->
                            <div class="col-span-12 sm:col-span-3 lg:col-span-3 mt-8">
                                <div class="intro-y flex items-center h-10">
                                    <h2 class="text-lg font-medium truncate mr-5">
                                        Employee Age Category
                                    </h2>
                                </div>
                                <div class="intro-y box p-5 mt-5">
                                    <div class="mt-3">
                                        <div class="h-[213px]">
                                            <canvas id="report-pie-chart-age"></canvas>
                                        </div>
                                    </div>
                                    <div class="w-52 sm:w-auto mx-auto mt-8">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                            <span class="truncate">Less than 40 Years</span> <span class="font-medium ml-auto"><?php echo $dobless; ?></span>
                                        </div>
                                        <div class="flex items-center mt-4">
                                            <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                            <span class="truncate">Between 40 - 50 Years</span> <span class="font-medium ml-auto"><?php echo $dobbetween; ?></span>
                                        </div>
                                        <div class="flex items-center mt-4">
                                            <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                            <span class="truncate">More than 50 Years</span> <span class="font-medium ml-auto"><?php echo $dobmore; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <!-- END: Weekly Top Seller -->

                            <!-- BEGIN: General Report -->
                            <!-- <div class="col-span-12 lg:col-span-6 mt-8">
                                <div class="intro-y block sm:flex items-center h-10">
                                    <h2 class="text-lg font-medium truncate mr-5">
                                        Leave Status
                                    </h2>
                                </div>
                            </div>
                            <div class="col-span-12 grid grid-cols-12 gap-6">
                                <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                                <div class="report-box zoom-in">

                                    <div class="box p-5 zoom-in">
                                        <center>
                                        <div class="flex">
                                            <div class="flex">
                                                <div class="w-24 flex-none">
                                                <i data-lucide="user-check" class="report-box__icon text-success ml-3"></i>
                                                    <div class="text-lg font-medium truncate">15</div>
                                                    <div class="text-slate-500 mt-1">Approved</div>
                                                </div>
                                            </div>
                                            <div class="w-32 flex-none">
                                            <i data-lucide="user" class="report-box__icon text-warning ml-1"></i>
                                                <div class="text-lg font-medium truncate">15</div>
                                                <div class="text-slate-500 mt-1">Pending</div>
                                            </div>
                                            <div class="w-24 flex-none">
                                            <i data-lucide="user-x" class="report-box__icon text-danger ml-3"></i>
                                                <div class="text-lg font-medium truncate">15</div>
                                                <div class="text-slate-500 mt-1">Rejected</div>
                                            </div>
                                        </div>
                                        </center>
                                    </div>
                                </div>
                                </div>
                                <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                                    <div class="box p-5 zoom-in">
                                        <div class="flex items-center">
                                            <div class="w-2/4 flex-none">
                                                <div class="text-lg font-medium truncate">10</div>
                                                <div class="text-slate-500 mt-1">Status Pending</div>
                                            </div>
                                            <div class="flex-none ml-auto relative">
                                                <div class="w-[90px] h-[90px]">
                                                    <canvas id="report-donut-chart-2"></canvas>
                                                </div>
                                                <div class="font-medium absolute w-full h-full flex items-center justify-center top-0 left-0">45%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                                    <div class="box p-5 zoom-in">
                                        <div class="flex">
                                            <div class="text-lg font-medium truncate mr-3">Social Media</div>
                                            <div class="py-1 px-2 flex items-center rounded-full text-xs bg-slate-100 dark:bg-darkmode-400 text-slate-500 cursor-pointer ml-auto truncate">320 Followers</div>
                                        </div>
                                        <div class="mt-1">
                                            <div class="h-[58px]">
                                                <canvas class="simple-line-chart-1 -ml-1"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  -->
                            <!-- END: General Report -->

                        <!-- </div>
                    </div>
                </div> -->
               <!-- commented -->

            </div>
            <!-- END: Content -->
        </div>
        </div>
        </div>
        </div>

        <!-- BEGIN: JS Assets-->
        <script src="dist/js/app.js"></script>
        <!-- END: JS Assets-->
        <?php
            include 'footer.php';
        ?>
    </body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('report-pie-chart-age').getContext('2d');

    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["More than 50 Years", "Between 40 - 50 Years", "Less than 40 Years"],
            datasets: [{
                data: [
 <?php echo $dobmore; ?>,
                    <?php echo $dobbetween; ?>,
                    <?php echo $dobless; ?>
                ],
                backgroundColor: ['#fbbf24', '#f97316', '#3b82f6'], // yellow, orange, blue
                hoverBackgroundColor: ['#facc15', '#fb923c', '#2563eb'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });


    const ctxx = document.getElementById('vertical-bar-chart-designation').getContext('2d');

    const barChart = new Chart(ctxx, {
    type: 'bar',
    data: {
        labels: <?php echo $labels_json; ?>,
        datasets: [{
            label: 'No. of Employees',
            data: <?php echo $counts_json; ?>,
            backgroundColor: '#3b82f6',
            hoverBackgroundColor: '#2563eb',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.8,        // slightly less than 1
            categoryPercentage: 0.7,   // reasonable compactness
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: '#e5e7eb' }
            },
x: {
    offset: true,
    grid: { display: false },
    ticks: {
        autoSkip: false,
        maxRotation: 45,
        minRotation: 45,
    }
}
        },
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        }
    }
});


});

</script>
</html>