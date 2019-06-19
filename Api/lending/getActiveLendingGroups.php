<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/lending.php';
include_once '../objects/product.php';
include_once '../objects/member.php';
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

$group = new LendingGroup();
 
$stmt = $group->getActiveLendingGroups();
$num = $stmt->rowCount();
$lending = new Lending();

if($num>0){
	$groups_arr=array();
 
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);
		$member = new Member();
		
		if($validation->Role >= 2)
		{ 
			
			$member->MemberId = $MemberID;
			$member->getMemberByID();
			
		}
		
		$lending_item=array(
			"LendingGroupID" => $LendingGroupID,
			"Member" => $member,
			"Active" => 1,
			"Lendings" => $lending->getLendingsByGroup($LendingGroupID)
		);
 
		array_push($groups_arr, $lending_item);
	}
 
	echo json_encode($groups_arr);
}
else
{
	echo '{';
       echo '"message": "No lending active"';
    echo '}';
}

?>