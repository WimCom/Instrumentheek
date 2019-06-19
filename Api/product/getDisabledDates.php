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
include_once '../objects/reservation.php';
include_once '../objects/category.php';
include_once '../helper/token.php';


// get posted data
$data = json_decode(file_get_contents("php://input"));

$reservation = new Reservation();
$reservations = $reservation->getAllReservationsByProduct($data->productID);
$dates = array();
foreach($reservations as $reservation)
{
	$start = strtotime($reservation["StartDate"]);
	$end = strtotime($reservation["EndDate"]);
	while($start < $end)
	{
		array_push($dates, date("Y-m-d",$start));
		$start = strtotime('+1 day', $start);
		
	}
	
}
echo(json_encode($dates));

?>