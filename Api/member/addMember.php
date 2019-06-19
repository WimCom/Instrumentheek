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
if($validation->Role < 2)
{
	echo '{';
       echo '"message": "No permission to insert member"';
    echo '}';
	return ;
}
$member = new Member();

$member->Name = $data->member->Name;
$member->LastName = $data->member->LastName;
$member->UserName = $data->member->UserName;
$password = random_str(8);
$member->PassWord = md5($password);

if($member->addMember())
{
	$memberInfo = new MemberInfo();
	$memberInfo->DateOfBirth = $data->member->Info->DateOfBirth;
	$memberInfo->Street = $data->member->Info->Street;
	$memberInfo->HouseNumber = $data->member->Info->HouseNumber;
	$memberInfo->PostalNumber = $data->member->Info->PostalNumber;
	$memberInfo->City = $data->member->Info->City;
	$memberInfo->TelephoneNumber = $data->member->Info->TelephoneNumber;
	$memberInfo->GsmNumber = $data->member->Info->GsmNumber;
	$memberInfo->Email = $data->member->Info->Email;
	$memberInfo->HasDonated = $data->member->Info->HasDonated;
	$memberInfo->WantsToBeInformed = $data->member->Info->WantsToBeInformed;
	$memberInfo->IncreasedCompensation = $data->member->Info->IncreasedCompensation;
	if($memberInfo->addMemberInfo($member->MemberId))
	{	
		$mailer = new Mailer();
		$result = $mailer->SendMail($memberInfo->Email, "Bevestiging lidmaatschap Instrumentheek", "Beste ".$member->Name.", <br><br>Proficiat met uw lidmaatschap bij de Instrumentheek!<br><br>Dit zijn vanaf nu uw inlog gegevens:<br>".
			"Gebruikersnaam: " .$member->UserName. "<br>Wachtwoord:  ".$password ."<br><br>Hiermee kunt u inloggen op de website van de Instrumentheek (www.instrumentheek.be) en uw account beheren.<br> ".
			"Het is tevens mogelijk om via uw account reservaties te verrichten.<br><br>Wij danken u voor uw lidmaatschap en wensen u veel klusplezier toe!<br><br>Met vriendelijke groeten,<br>Het team van de Instrumentheek");
		echo $member->MemberId ;
	}
	else
	{
		echo '{';
        echo '"message": "Unable to add member info."';
    echo '}';
	}
}
// if unable to create tell the user
else{
    echo '{';
        echo '"message": "Unable to add member."';
    echo '}';
}
function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}
?>