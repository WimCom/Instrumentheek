<?php
include_once '../objects/member.php';
include_once '../objects/product.php';
include_once '../config/database.php';
class Reservation{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $ReservationID;
    public $StartDate;
    public $EndDate;
    public $Member; 
    public $Product; 
    public $Comments;
    public $Active;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	function getAllActiveReservations(){
		// select all query
		$query = "SELECT ReservationID,MemberID,ProductID,StartDate,EndDate,Comments FROM reservation_lookup WHERE Active = 1 AND EndDate >= CURDATE()";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		$reservation_arr=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$member = new Member();
			$member->MemberId = $row['MemberID'];
			$member->getMemberByID();
			$member_arr = array(
				"MemberId" => $member->MemberId,
				"Name" => $member->Name,
				"LastName" => $member->LastName,
				"UserName" => $member->UserName,
			);
			$product = new Product();
			$product->ProductID = $row['ProductID'];
			$product->getProductByID();
			$product_arr = array(
				"ProductID" => $product->ProductID,
				"ProductName" => $product->ProductName,
				"ProductCode" => $product->ProductCode,
			);
			 
			$reservation= array(
				"ReservationID" => $row['ReservationID'],
				"StartDate" => $row['StartDate'],
				"EndDate" => $row['EndDate'],
				"Comments" => $row['Comments'],
				"Member" => $member_arr,
				"Product" => $product_arr,
			);
			array_push($reservation_arr, $reservation);
		}
		return $reservation_arr;
	}
	
	function getAllReservationsByMember($memberID){
		// select all query
		$query = "SELECT ReservationID,ProductID,StartDate,EndDate,Comments FROM reservation_lookup WHERE Active = 1 AND MemberID = :memberID AND EndDate >= CURDATE()";
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":memberID", $memberID);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		$reservation_arr=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$product = new Product();
			$product->ProductID = $row['ProductID'];
			$product->getProductByID();
			$product_arr = array(
				"ProductID" => $product->ProductID,
				"ProductName" => $product->ProductName,
				"ProductCode" => $product->ProductCode,
			);
			
			 
			$reservation= array(
				"ReservationID" => $row['ReservationID'],
				"StartDate" => $row['StartDate'],
				"EndDate" => $row['EndDate'],
				"Comments" => $row['Comments'],
				"Product" => $product_arr,
			);
			array_push($reservation_arr, $reservation);
		}
		return $reservation_arr;
	}
	
	function getAllReservationsByProduct($productID){
		// select all query
		$query = "SELECT ReservationID, MemberId,StartDate,EndDate,Comments FROM reservation_lookup WHERE Active = 1 AND ProductID = :productID AND EndDate >= CURDATE()";
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":productID", $productID);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		$reservation_arr=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$member = new Member();
			$member->MemberId = $row['MemberId'];
			$member->getMemberByID();
			$member_arr = array(
				"MemberId" => $member->MemberId,
				"Name" => $member->Name,
				"LastName" => $member->LastName,
				"UserName" => $member->UserName,
			);
			 
			$reservation= array(
				"ReservationID" => $row['ReservationID'],
				"StartDate" => $row['StartDate'],
				"EndDate" => $row['EndDate'],
				"Comments" => $row['Comments'],
				"Member" => $member_arr,
			);
			array_push($reservation_arr, $reservation);
		}
		return $reservation_arr;
	}
	
	function addReservation(){
		
		// select all query
		$query = "INSERT INTO reservation_lookup (MemberID,ProductID,StartDate,EndDate,Comments,Active) ".
                "VALUES (:memberID,:productID,:startDate,:endDate,:comments,1)";

		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":memberID", $this->Member->MemberId);
		$stmt->bindParam(":productID", $this->Product->ProductID);
		$stmt->bindParam(":startDate", $this->StartDate);
		$stmt->bindParam(":endDate", $this->EndDate);
		$stmt->bindParam(":comments", $this->Comments);
		 
		// execute query
		if($stmt->execute())
		{
			
			return true;
		}
		return false;
	}
	function cancelReservation($reservationId)
	{
		$query = "UPDATE reservation_lookup SET Active = 0 WHERE ReservationID = :reservationID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":reservationID", $reservationId);

		// execute query
		if($stmt->execute())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}