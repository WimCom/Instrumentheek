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
include_once '../objects/member.php';
include_once '../PHPMailer.php';
include_once '../helper/token.php';
use PHPMailer\PHPMailer\PHPMailer;
$database = new Database();
$db = $database->getConnection();
 
$login = new login($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));

$member = new Member();
if(!empty($data->email))
{
	
	if($member->getMemberByEmail($data->email))
	{
		echo($member->Username);
		$member->PassWord = md5($data->password);
		$member->updatePassword();

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = "Instrumentheek";
		$mail->setFrom('instrumentheek@gmail.com', 'Instrumentheek VZW');
		//$mail->From = "instrumentheek@gmail.com";
		$mail->Password = "1slijpschijfje.";
		$mail->addAddress($data->email);
		$mail->Subject = "Instrumentheek - Nieuw wachtwoord";
		$mail->msgHTML('Beste, <br> U krijgt deze email omdat u een nieuw wachtwoord hebt aangevraagd. <br><br> Dit zijn uw nieuwe gegevens: <br> 
		-Gebruikersnaam: '. $member->Username .'<br>-Wachtwoord: ' . $data->password . '<br><br>Mvg,<br>Instrumentheek VZW<br>');
		if (!$mail->send()) 
		{
			$error = "Mailer Error: " . $mail->ErrorInfo;
			echo $error;
		}
		//else
		//{
			echo("true");
		//}
		
		//mail($data->email, "Nieuw wachtwoord","Uw nieuw wachtwoord is: " . $data->password);
		
	}
	else
	{
		echo("Email does not exist");
	}
}
else
{
	
	$validation = new token();
	$validation->validateToken($data->token);
	if($validation->Role == 0)
	{
		
		echo '{';
		   echo '"message": "Token invalid"';
		echo '}';
		return ;
	}
	$member->MemberId = $validation->MemberId;
	$member->PassWord = md5($data->password);
	echo($member->updatePassword());
}


?>