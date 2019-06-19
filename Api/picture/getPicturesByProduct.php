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
include_once '../objects/picture.php';
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

$picture = new Picture();
$product = new Product();
$product->ProductID = $data->productID;
$picture->product = $product;
$result = $picture->GetPicturesByProduct();

if($result != null)
{
	echo json_encode($result); 
}
else
{
	echo '{}';
}

return;
?>