<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
include_once '../objects/login.php';
 
$database = new Database();
$db = $database->getConnection();
 
$login = new login($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
$login->Username = $data->username;
$login->Password = $data->password;
 
// create the product
if($login->validate()){
	$login->login();
    echo '{';
        echo '"sessionId" : "'. $login->SessionId .'",';
		echo '"role" : "'. $login->Role .'"';
	    //echo "good";
    echo '}';
}
 
else{
    echo '{';
       echo '"sessionId" : "Login failed"';
    echo '}';
}
?>