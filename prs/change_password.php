<?php
    session_start();
    if ((!isset($_SESSION['user'])))
    {
        header('refresh: 1;url=login.php');
        die('Please Login First...<br><br>Redirectiing in a sec to Login Page');
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $currentPassword = $_POST['password'];
        $newPassword = $_POST['newpassword'];
        $confirmPassword = $_POST['confirmpassword'];
        $userid = $_SESSION['user'];
        echo "user".$userid;
        $uid = isset($_SESSION['uID']) ? $_SESSION['uID'] : null;
        echo "uID".$uid;
        if ($newPassword == $confirmPassword) {
            include('includes/dbconn.php');

            if (isset($uid) && isset($userid)) {
                $stmt = $db->prepare("UPDATE usertb SET PASSWD = :newPassword WHERE PASSWD = :currentPassword AND UID = :uid");
                $stmt->bindParam(':uid', $uid);
                $stmt->bindParam(':newPassword', $newPassword);
                $stmt->bindParam(':currentPassword', $currentPassword);
            } else {
                $stmt_empno = $db->prepare("UPDATE empmast SET PASSWD = :newPassword WHERE PASSWD = :currentPassword AND EMPNO=:empno");
                $stmt_empno->bindParam(':empno', $userid);
                $stmt_empno->bindParam(':newPassword', $newPassword);
                $stmt_empno->bindParam(':currentPassword', $currentPassword);
            }

            // Attempt to execute the appropriate update statement and handle the result
            $success = false;
            if (isset($stmt) && $stmt->execute()) {
                $success = true;
            } elseif (isset($stmt_empno) && $stmt_empno->execute()) {
                $success = true;
            }

            if ($success) {
                $successMessage = "<div class='alert alert-success show mb-2' role='alert'>Password updated successfully!</div>";
                echo "<script>
                        alert('Password updated successfully!');
                        window.location.href = 'index.php';
                    </script>";
            } else {
                $errorMsg = isset($stmt) ? $stmt->errorInfo()[2] : (isset($stmt_empno) ? $stmt_empno->errorInfo()[2] : 'Unknown error');
                echo "<script>
                        alert('Error updating password: $errorMsg');
                    </script>";
            }
        }
        else
        {
            echo "<script>
                    alert('Password does not match.');
                </script>";
        }
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
        <title>Change Password</title>
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
    </style>

<style>
    @media (max-width: 768px) {
        .grid-cols-12 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
        }


        .badge-other {
            /* color: white; */
            color: red;
        }


        .badge-active {
            /* color: white; */
            color: green;
        }
    </style>
    
    <!-- END: Head -->
    <body class="py-5 md:py-0">
                <!-- BEGIN: Mobile Menu -->
                <?php include 'mob.php' ?>
        <!-- END: Mobile Menu -->
    <div class="flex mt-[4.7rem] md:mt-0">
        <?php
            $amenu="master";
            include 'nav.php'
        ?>
        <!-- BEGIN: Content -->
        <div class="content content--top-nav">
            <?php
                $menu_title = "Administrative Tools";
                $currentPage="Change Password";
                include 'top.php'
            ?>
            <!-- END: Top Bar -->
            <div class="intro-y flex items-center mt-8">
                    <h2 class="text-lg font-medium mr-auto">
                        Change Password
                    </h2>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 lg:col-span-12 2xl:col-span-12">
                        <!-- BEGIN: Change Password -->
                        <div class="intro-y box lg:mt-5">
                            <!-- <div class="flex items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                                <h2 class="font-medium text-base mr-auto">
                                    Change Password
                                </h2>
                            </div> -->
                            <form action="" method="post">
                                <div class="p-5">
                                    <div>
                                        <label for="change-password-form-1" class="form-label">Old Password</label>
                                        <input id="change-password-form-1" name="password" type="password" class="form-control" placeholder="Input text">
                                    </div>
                                    <div class="mt-3">
                                        <label for="change-password-form-2" class="form-label">New Password</label>
                                        <input id="change-password-form-2" type="password" name="newpassword" class="form-control" placeholder="Input text">
                                    </div>
                                    <div class="mt-3">
                                        <label for="change-password-form-3" class="form-label">Confirm New Password</label>
                                        <input id="change-password-form-3" type="password" name="confirmpassword" class="form-control" placeholder="Input text">
                                    </div>
                                    <button class="btn btn-primary mt-4">Change Password</button>
                                </div>
                            </form>
                        </div>
                        <!-- END: Change Password -->
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

</html>