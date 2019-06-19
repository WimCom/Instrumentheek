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
include_once '../objects/member.php';
include_once '../objects/category.php';
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
	if($validation->Role < 2)
	{
		echo '{';
		   echo '"message": "No permission to insert member"';
		echo '}';
		return ;
	}
	$member = new Member();

	$member->MemberID = $data->member->MemberId;

	
	if($member->deleteMember())
	{	
		echo "true" ;
	}
	else
	{
		echo "false";
	}
?>