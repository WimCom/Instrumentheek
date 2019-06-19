<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/category.php';
include_once '../helper/token.php';
 
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// get posted data
$data = json_decode(file_get_contents("php://input"));
$validation = new token($db);

if($validation->validateToken($data->token) == 0)
{
	echo '{';
       echo '"message": "Token invalid"';
    echo '}';
	return ;
}

// initialize object
$category = new Category();
$category->CategoryID = $data->categoryId;
//Validation of request 
 
$stmt = $category->getCategoryById();
$num = $stmt->rowCount();

if($num > 0)
{
	if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$category->CategoryCode = $row['CategoryCode'];
			$category->Description = $row['Description'];
			$category->Kind = $row['Kind'];
			
			echo json_encode($category);
		}
}
else
{
	echo '{';
       echo '"message": "Category not found"';
    echo '}';
}
?>