<?php 

$data = array(
    "data"=> "This is test json data",  
    "message"=> "OK",
    "status"=> "200",
);  

header("Conetent-Type:application/json");

echo json_encode($data);

?>