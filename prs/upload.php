<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// uploading files on submit
if(isset($_POST['submit'])){

    //  uploading files
    $msg= upload_file();
    session_start();
    $_SESSION['msg']= $msg;
    header('location:upload.php');
}
function upload_file(){
    $uploadTo = "uploads/";
    $allowFileType = array('jpg','png','jpeg','gif','pdf','doc');
    $fileName = $_FILES['file']['name'];
    $tempPath=$_FILES["file"]["tmp_name"];

    $basename = basename($fileName);
    $originalPath = $uploadTo.$basename;
    $fileType = pathinfo($originalPath, PATHINFO_EXTENSION);
    if(!empty($fileName)){

       if(in_array($fileType, $allowFileType)){
         // Upload file to server
         if(move_uploaded_file($tempPath,$originalPath)){
            return $fileName." was uploaded successfully";
           // write  sql query to store image name in database

          }else{
            $error = 'File Not uploaded ! try again';
          }
      }else{
         return $fileType." file type not allowed";
      }
   }else{
    return "Please Select a file";
   }
}
?>