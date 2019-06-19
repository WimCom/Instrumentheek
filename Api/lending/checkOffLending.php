<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate product object
include_once '../objects/lending.php';
include_once '../helper/token.php';
 
// get posted data
$data = json_decode(file_get_contents("php://input"));

$validation = new token();
$validation->validateToken($data->token);

if($validation->Role == 0)
{
	echo '{';
       echo '"message": "Token invalid"';
    echo '}';
	return ;
}

$lend = new Lending();
$lend->LendingID = $data->Lending->LendingID;
$lend->ReturnedDate = $data->Lending->ReturnedDate;
$lend->Comments = $data->Lending->Comments;

if($lend->checkOffLending())
{
	echo 'true';
}
else
{
    echo 'false';
}
?>