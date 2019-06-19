<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/member.php';
include_once '../objects/subscription.php';
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
if($validation->Role < 2 || ($validation->Role == 1 && $data->Member->MemberId != -1 ))
{
	echo '{';
       echo '"message": "Insufficient permission"';
    echo '}';
	return ;
}
if($data->Member->MemberId == -1 )
{
	$sub = new Subscription();
	$mem = new Member();
	$mem->MemberId = $validation->MemberId;
	$sub->Member = $mem;
	echo json_encode($sub->getSuscriptionsByMember());
}
else
{
	$sub = new Subscription();
	$sub->Member = $data->Member;
	echo json_encode($sub->getSuscriptionsByMember());
}
?>