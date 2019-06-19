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

// set product property values
$product->ProductName = $data->ProductName;
$product->CreationDate = date('Y-m-d H:i:s');
$product->ProductCode = $data->ProductCode;
$product->Active = true;
$category = new Category();
$category->CategoryID = $data->CategoryID;
$product->Category = $category;
 
 
// create the product
if($product->addProduct()){
	$info = new ProductInfo();
	$info->ProductID = $product->ProductID;
	$info->Description = $data->description;
	$info->TechnicalDetails = $data->technicalDetails;
	$info->BeforeLending = $data->beforeLending;
	$info->AfterLending = $data->afterLending;
	if($info->addProductInfo())
	{	
		echo '{';
			echo '"ProductID": '. $product->ProductID ;
		echo '}';
	}
}
 
// if unable to create the product, tell the user
else{
    echo '{';
        echo '"message": "Unable to create product."';
    echo '}';
}
?>