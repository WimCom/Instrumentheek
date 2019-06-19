<?php
include_once '../objects/member.php';
class Subscription{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $Member;
    public $SubscriptionId;
    public $StartDate;
    public $EndDate;
    public $Valid;
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	
	function getSuscriptionsByMember(){
		
		$query = "SELECT SubscriptionId,MemberID,DateStart,DateEnd,Active FROM subscriptions_lookup WHERE MemberId = :MemberID";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":MemberID", $this->Member->MemberId);
		$stmt->execute();
		$subscriptions_arr=array();
		

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			
			$subscription_item= array(
				"SubscriptionId" => $row['SubscriptionId'],
				"StartDate" => $row['DateStart'],
				"EndDate" => $row['DateEnd'],
				"Valid" => (bool)$row['Active']
			);
			
			array_push($subscriptions_arr, $subscription_item);
		}
		return $subscriptions_arr;
	}
	function addSubscription()
	{
		$query = "INSERT INTO subscriptions_lookup (MemberId,DateStart,DateEnd,Active) VALUES (:MemberId,:DateStart,:DateEnd,1)";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":MemberId", $this->Member->MemberId);
		$stmt->bindParam(":DateStart", $this->StartDate);
		$stmt->bindParam(":DateEnd", $this->EndDate);
		
		// execute query
		if($stmt->execute()){
			return "true";
		}
	 
		return "false";;   
		
		
	}
	
	function stopSubscription()
	{
		$query = "UPDATE subscriptions_lookup SET DateEnd = :DateEnd, Active = 0 WHERE SubscriptionId = :SubID";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":DateEnd", $this->EndDate);
		$stmt->bindParam(":SubID", $this->SubscriptionId);
		
		// execute query
		if($stmt->execute()){
			return "true";
		}
	 
		return "false";;   
		
		
	}
	
	function addMember(){
		$query = "INSERT INTO Members_lookup (Name,Lastname,Username,Password,Inactive) VALUES (:name,:lastName,:userName,:passWord,0)";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":name", $this->Name);
		$stmt->bindParam(":lastName", $this->LastName);
		$stmt->bindParam(":userName", $this->UserName);
		$stmt->bindParam(":passWord", $this->PassWord);
		
		// execute query
		if($stmt->execute()){
			$this->MemberId = $this->conn->lastInsertId();
			return true;
		}
	 
		return false;   
	}
}