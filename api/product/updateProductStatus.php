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
include_once '../objects/product.php';
include_once '../objects/category.php';
include_once '../helper/token.php';
 
$database = new Database();
$db = $database->getConnection();
 
$product = new Product($db);
 
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
       echo '"message": "No permission!"';
    echo '}';
	return ;
}


// set product property values
$product->ProductID = $data->productID;
$product->getProductByID();
if($product->Status == $data->status)
{
	exit(json_encode(array("status" => 0, "msg" => "Status was already the status")));
}
if($product->Status == 0 && $data->status > 0)
{
	//product status goes from normal to maintenance/repair
	$product->Status = $data->status;
	$product->updateProductStatus();
	//check all reservations and notice them about the maintenance/repair
	exit(json_encode(array("status" => 1, "msg" => "Status updated")));
}
if($product->Status > 0 && $data->status == 0)
{
	//product status goes from maintenance/repair to normal
	$product->Status = $data->status;
	$product->updateProductStatus();
	//check all reservations and notice that the product is available again
	exit(json_encode(array("status" => 1, "msg" => "Status updated")));
}
?>