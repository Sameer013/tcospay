<?php
session_start();
ini_set('display_errors', 1);
include ('includes/dbconn.php');
if (isset($_REQUEST['login'])){
    $user = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    // Query the database for the user
    $stmt = $db->prepare("SELECT UNAME, UID, PASSWD, role_id FROM usertb WHERE UNAME=:userid AND PASSWD=:password");
    $stmt->bindParam(':userid', $user);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        switch ($admin['role_id']) {
            case 'a3':
                $_SESSION['user'] = 'Admin';
                break;
            case 'a1':
                $_SESSION['user'] = 'manager';
                break;
            case 'a2':
                $_SESSION['user'] = 'manager';
                break;
        }
        $_SESSION['uID'] = $admin['UID'];

        header('location: index.php');
        exit;
    }
    else{
        $stmt = $db->prepare("SELECT EMPNO, NAME FROM empmast WHERE EMPNO=:empno AND PASSWD=:password");
        $stmt->bindParam(':empno', $user);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user=$stmt->fetch();
        if($user){
            $_SESSION['user']=$user['EMPNO'];
            //$_SESSION['user_name'] = $data['name'];
            header('location:index.php') ;
        }
        else
        {

            echo "<script>alert('Invalid... Login!!')</script>";
        }
    }
  }
?>

<!DOCTYPE html>
<html lang="en" class="light">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <link href="dist/images/ilf.png" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Payroll - Login</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="dist/css/app.css" />
        <!-- <script src="https://cdn.tailwindcss.com"></script> -->
        
        <!-- END: CSS Assets-->
    </head>
    <style>
        /* .login::after{
            background-image:url('dist/images/school.jpg');
        } */
         
         .my-input-group{
		font-size: 1rem;
		position: relative;
		--primary: #2196f3;
	  }
        .my-input{
            all: unset;
			 color: #333;
            padding: 0.75rem 0.5rem;
            border: 1px solid #9e9e9e;
            border-radius: 5px;
            transition: 150ms
            cubic-bezier(0.4, 0, 0.2, 1);
        }
        .my-label{
            position: absolute;
            top: 1rem;
			left: 1rem;
            color: #d4d4d4;
            pointer-events: none;
            transition: 150ms
            cubic-bezier(0.4, 0, 0.2, 1);
        }
        .my-input:focus{
            border: 1px solid
            var(--primary);
        }
        .my-input:is(:focus, :valid) ~ label{
            transform: translateY(-130%) scale(0.7);
            background-color: white;
            padding-inline: 0.3rem;
            color: var(--primary);
        }
    </style>
    <!-- END: Head -->
    <body class="login absolute intro-x" style="background-image: url('dist/images/school3.jpg'); background-size: cover; background-position: center;">
    <div class="container sm:px-10" >
        <div class="block xl:grid grid-cols-2 gap-4" >
            <!-- BEGIN: Login Info -->
            <div class="hidden xl:flex flex-col min-h-screen">
                <div class="mt-8 mr-8">
                    <img alt="" class="-intro-x" style="width:35%; margin-top: 20%;  margin-left: 15%"
                        src="dist/images/jpslogo.png">
                    <div class="-intro-x text-black font-medium text-5xl leading-tight"
                        style="margin-top:50px;color:white; text-shadow: 2px 2px black;">
                        JPS Payroll System
                    </div>
                </div>
            </div>
            <!-- END: Login Info -->
            <!-- BEGIN: Login Form -->
            <form method="post">

                <!-- <span class="intro-y  text-black absolute font-medium text-3xl leading-tight" style="font-family: 'Libre Baskerville', serif;"> </span> -->
                <div class="h-screen xl:h-auto flex xl:py-0  xl:mt-0">
                    <div class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                        <div class="intro-x mt-1 py-5 flex items-center justify-center space-x-4 text-3xl font-bold leading-tight text-center rounded-t-full">
                            <div class="flex items-center justify-center xl:hidden">
                            <img alt="JPS Logo" class="w-16 h-16 mx-2" src="dist/images/jpslogo.png" />
                            <span class="text-gray-600 text-xl font-semibold" style="text-shadow: 2px 2px white;">
                                JPS Payroll System
                            </span>
                        </div>
                        </div>

                        <div class="card-body p-5 m-0" style="box-shadow:1px 5px 12px 10px rgba(184,194,230,1)">
                            <h2 class="intro-x mb-10 font-bold text-2xl xl:text-3xl text-center text-white"
                                style="background-color: #1B91BF;">
                                Login
                            </h2>
                            <div class="intro-x mt-10 my-input-group">
                                <input required type="text" class="my-input  login__input form-control py-3 px-4 block"
                                    id="username" name="username">
                                <label for="username" class="my-label">Username</label>

                            </div>
                            <div class="intro-x mt-3 my-input-group">
                                <input required type="password"
                                    class="my-input  login__input form-control py-3 px-4 block" id="password"
                                    name="password">
                                <label for="password" class="my-label">Password</label>
                            </div>
                            <div class="intro-x flex text-slate-600 dark:text-slate-500 text-xs sm:text-sm mt-4">
                                <div class="flex items-center mr-auto ">
                                    <input id="showPassword" type="checkbox" onclick="showPass()"
                                        class="form-check-input border mr-2">
                                    <label class="cursor-pointer select-none" for="showPassword">Show Password</label>
                                </div>
                            </div>
                            <div class="intro-x  text-center ">
                                <button class="btn btn-primary text-center align-top" name="login"
                                    value="login">Login</button>
                            </div>
                            <div class="mb-1  text-center font-bold absolute" style="margin: 55px; bottom: 5%">
                                <!-- <img alt="" class="-intro-x w-1/2 ml-8 mt-8" style="width:35%"  src="dist/images/payroll-system.jpg"> -->
                            </div>
                            
                        </div><div class="mb-8 py-5 mr-6 text-white text-center absolute bottom-0 leading-tight">Developed By ©
                                <a class="font-bold" href="https://www.sigmaworld.in/">Sigma eSolution Private Limited</a>, Ranchi Jharkhand
                            </div>
                    </div>
                </div>
            </form>
            
            <!-- END: Login Form -->
        </div>
        </div>
    </div>
    <!-- BEGIN: JS Assets-->
    <script src="dist/js/app.js"></script>
    <!-- END: JS Assets-->
</body>
    <script>
        function showPass() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
            }
    </script>
</html>