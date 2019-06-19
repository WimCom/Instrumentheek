<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once '../objects/member.php';
include_once '../PHPMailer.php';
include_once '../helper/token.php';
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
	function SendMail($mailadres, $title, $msg)
	{
		if(empty($mailadres))
		{
			return array("status" => 0, "msg" => 'No email address.');
		}
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = "qsdf";
		$mail->setFrom('instrumentheek@gmail.com', 'Instrumentheek VZW');
		//$mail->From = "instrumentheek@gmail.com";
		$mail->Password = "qsdf";
		$mail->addAddress($mailadres);
		$mail->Subject = $title;
		$mail->msgHTML($msg);
		if (!$mail->send()) 
		{
			$error = "Mailer Error: " . $mail->ErrorInfo;
			return array("status" => 0, "msg" => $error);
		}
		{
			return array("status" => 1, "msg" => "Mail sent");
		}
	}	
}
?>