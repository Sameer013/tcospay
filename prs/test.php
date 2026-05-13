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
        <title>Payroll - Test</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="dist/css/app.css" />
    <link rel="stylesheet" href="dist/css/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
        <!-- END: CSS Assets-->


    </head>


<style>

        .dataTables_length select
        {
            width:60px;
        }

    .dataTables_length select {
        width: 60px;
    }

    /* .progress-container {
  width: 100%;
  background-color: #f0f0f0;
  border-radius: 5px;
  height: 30px;
  margin-bottom: 20px;
  position: relative;
}

.progress-bar {
  background-color: #4caf50;
  height: 100%;
  border-radius: 5px;
  width: 0%;
  position: absolute;
  top: 0;
  left: 0;
  cursor: pointer;
} */


</style>


    <!-- END: Head -->
    <body class="py-0">
                <!-- BEGIN: Mobile Menu -->
                <?php include 'mob.php' ?>
        <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
        $amenu = "transaction";
        include 'nav.php'
          ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
            $menu_title = "Transaction";
            $currentPage = "Test";
            include 'top.php'
              ?>
            <div class="intro-y flex items-center mt-8">
                <h2 class="text-lg font-medium mr-auto">
                Test
                </h2>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">

                <div class="intro-y col-span-12 lg:col-span-12 ">
                    <!-- BEGIN: Responsive Table -->
                    <div class="intro-y box mt-5">
                        <div class="p-5" id="responsive-table">
                            <div class="preview">
<div class="progress-container relative bg-gray-200 rounded overflow-hidden h-10">
  <div class="progress-bar bg-green-500 h-full"></div>
  <div class="progress-text absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 text-white"></div>
</div>


                               </div>
                            </div>

                        </div>
                    </div>
                    <!-- END: Responsive Table -->
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
$(document).ready(function(){
    var progressBar = $(".progress-bar");
    var progressText = $(".progress-text");

    function updateProgressBarWidth(event) {
        // Calculate the width based on mouse position
        var width = (event.clientX / window.innerWidth) * 100;
        // Ensure width is within 0% to 100%
        width = Math.min(Math.max(width, 0), 100);
        // Set the width of the progress bar
        progressBar.css("width", width + "%");

        // Update progress text with selected percentage
        progressText.text(Math.round(width) + "%");

        // Update progress bar color based on percentage value
        if (width < 25) {
            progressBar.css("background-color", "#ff0000"); // Red
        } else if (width < 50) {
            progressBar.css("background-color", "#ffbf00"); // Orange
        } else if (width < 75) {
            progressBar.css("background-color", "#ffff00"); // Yellow
        } else {
            progressBar.css("background-color", "#00ff00"); // Green
        }
    }

    $(document).mousemove(function(event) {
        if (event.buttons === 1) {
            updateProgressBarWidth(event);
        }
    });

    $(document).on("touchmove", function(event) {
        event.preventDefault();
        updateProgressBarWidth(event.touches[0]);
    });
});

  </script>
</html>