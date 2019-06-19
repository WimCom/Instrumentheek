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
		   echo '"message": "No permission to delete a product"';
		echo '}';
		return ;
	}
	$product = new Product();

	$product->ProductID = $data->Product->ProductID;

	
	if($product->deleteProduct())
	{	
		echo "true" ;
	}
	else
	{
		echo "false";
	}

?>