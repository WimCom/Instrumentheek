<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/product.php';
include_once '../objects/category.php';
include_once '../helper/token.php';

$isUser = false;
$data = json_decode(file_get_contents("php://input"));
$validation = new token();
if(!EMPTY($data->token))
{
	$validation->validateToken($data->token);

	if($validation->Role == 0)
	{
		echo '{';
		   echo '"message": "Token invalid"';
		echo '}';
		return ;
	}
	else
	{
		$isUser = true;
	}
}
else
{
	$isUser = false;
}

// initialize object
$product = new Product();

$product->ProductID = $data->productID;

if($product->getProductByID())
{
	echo json_encode($product);
}
else
{
	echo json_encode(
		array("message" => "Product not found.")
	);
}
 

?>