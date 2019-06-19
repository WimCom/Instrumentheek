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

	$member->Name = $data->Member->Name;
	$member->LastName = $data->Member->LastName;
	$member->UserName = $data->Member->UserName;
	$member->MemberId = $data->Member->MemberId;
	
	$member->updateMember();

	$memberInfo = new MemberInfo();
	$memberInfo->DateOfBirth = $data->Member->Info->DateOfBirth;
	$memberInfo->Street = $data->Member->Info->Street;
	$memberInfo->HouseNumber = $data->Member->Info->HouseNumber;
	$memberInfo->PostalNumber = $data->Member->Info->PostalNumber;
	$memberInfo->City = $data->Member->Info->City;
	$memberInfo->TelephoneNumber = $data->Member->Info->TelephoneNumber;
	$memberInfo->GsmNumber = $data->Member->Info->GsmNumber;
	$memberInfo->Email = $data->Member->Info->Email;
	$memberInfo->HasDonated = $data->Member->Info->HasDonated;
	$memberInfo->WantsToBeInformed = $data->Member->Info->WantsToBeInformed;
	$memberInfo->IncreasedCompensation = $data->Member->Info->IncreasedCompensation;
	
	if($memberInfo->updateMemberInfo($member->MemberId))
	{	
		echo $member->MemberId ;
	}
	else
	{
		echo '{';
        echo '"message": "Unable to update member."';
		echo '}';
	}
?>