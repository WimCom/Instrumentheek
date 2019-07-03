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
include_once '../objects/lending.php';
include_once '../helper/token.php';


// get posted data
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
 
// query products
$stmt = $product->read();
$num = $stmt->rowCount();

$category = new Category();
$lending = new Lending();
$info = new ProductInfo();
$product = new Product();

// check if more than 0 record found
if($num>0){
 
	// products array
	$products_arr=array();
 
	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{

		
		$product->ProductID = $row['ProductId'];
		
		//echo($row['ProductId']);
		
		$category->CategoryID = $row['CategoryID'];
		$category->getCategoryById();
		
		$category_item= array(
			"CategoryID" => $category->CategoryID,
			"CategoryCode" => $category->CategoryCode,
			"Description" => $category->Description,
			"Kind" => $category->Kind
		);
		
		$lending->Product = $product;
		
		if($lending->getActiveLendingByProduct($validation->Role))
		{
			$lending_item= array(
			"StartDate" => $lending->StartDate,
			"DueDate" => $lending->DueDate,
			"ReturnedDate" => $lending->ReturnedDate,
			"Product" => null,
			"ExtraInfo" => $lending->ExtraInfo,
			"Comments" => $lending->Comments,
			"Group" => $lending->Group
			);
		}
		else
		{
			$lending_item=null;
		}
		
		$info->ProductID = $product->ProductID;
		$info->getProductInfo();
		
		$info_item= array(
			"ProductID" => $info->ProductID,
			"Description" => $info->Description,
			"TechnicalDetails" => $info->TechnicalDetails,
			"BeforeLending" => $info->BeforeLending,
			"AfterLending" => $info->AfterLending,
			"Thumbnail" => $info->Thumbnail,
		);
		
		//echo($info->Description);
		
		$picture_arr = array();
		$picture = new Picture();
		$picture->product = $product;
		$picturel = $picture->GetPicturesByProduct();
		
		foreach($picturel as $pic)
		{
			
			$picture_item_array = array(
			"ImageID" => $pic['ImageID'],
			"AbsoluteUrl" =>$pic['AbsoluteUrl'],
			"Description" => $pic['Description'],
			);
			array_push($picture_arr, $picture_item_array);
		}
		
		
		
		$product_item= array(
			"ProductID" => $row['ProductId'],
			"ProductName" => $row['ProductName'],
			"ProductCode" => $row['ProductCode'],
			"CreationDate" => $row['CreationDate'],
			"Category" => $category_item,
			"CurrentLending" => $lending_item,
			"Info" => $info_item,
			"PictureList" => $picture_arr,
			"Status" => intval($row['Status'])
		);
		array_push($products_arr, $product_item);
		$product_item = "";
	}
	//echo(implode(" ",$products_arr));
	echo json_encode($products_arr);
}
 
else{
	echo json_encode(
		array("message" => "No products found.")
	);
}
?>