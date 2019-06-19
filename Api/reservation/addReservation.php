<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/reservation.php';
include_once '../objects/product.php';
include_once '../objects/member.php';
include_once '../helper/token.php';
include_once '../helper/mailer.php';


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
if($validation->Role < 2 && $data->MemberId != -1)
{
	if($validation->MemberId)
	echo '{';
       echo '"message": "No permission"';
    echo '}';
	return ;
}

$reservation = new Reservation();
$reservation->StartDate = $data->StartDate;
$reservation->EndDate = $data->EndDate;
$member = new Member();
if($data->MemberId != -1)
{
	$member->MemberId = $data->MemberId;
}
else
{
	$member->MemberId = $validation->MemberId;
}

$reservation->Member = $member;
$member->getMemberByID();
//echo($member->Info["Email"]);
$product = new Product();
$product->ProductID = $data->ProductID;
$product->getProductByID();
$reservation->Product = $product;
$reservation->Comments = $data->Comments;

if($reservation->addReservation())
{
	$mailer = new Mailer();
	$result = $mailer->SendMail($member->Info["Email"], "Reservatie", "Beste, <br>Bevesting van uw reservatie van ". $reservation->StartDate." tot ". $reservation->EndDate . ", voor volgend toestel:<br>".
	 $product->ProductName. "<br>Bedankt,<br>Instrumentheek VZW");
	if($result["status"] == 1)
	{
		exit(json_encode(array("status" => 1, "msg" => "Reservation added.")));
	}
	else
	{
		exit(json_encode(array("status" => 0, "msg" => $result["msg"])));
	}	
}
else
{
	exit(json_encode(array("status" => 1, "msg" => "Failed to add reservation.")));
}
?>