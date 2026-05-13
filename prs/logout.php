<?php
    session_start();
    unset($_SESSION['user']);
    session_destroy();
    echo "<script>alert('Logged Out Successfully!!')</script>";
    header('refresh:0;url=index.php');
    die('');
?>