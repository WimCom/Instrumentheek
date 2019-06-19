<?php
include_once '../objects/member.php';
class Lending{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $LendingID;
    public $StartDate;
    public $DueDate;
    public $ReturnedDate; 
    public $Product; 
    public $ExtraInfo;
    public $Comments;
    public $Active;
    public $Group;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	function getActiveLendingByProduct($Role){
		//echo($this->Product->ProductID);
		// select all query
		$query = "SELECT ll.lendingID, ll.StartDate,ll.DueDate, ll.ExtraInfo, ll.Comments, lg.LendingGroupID, lg.MemberID
                FROM lendings_lookup ll
                LEFT OUTER JOIN lendings_group lg
                ON ll.LendingGroupID = lg.LendingGroupID
                WHERE ll.ProductID = :ProductID
                AND ll.Active = 1";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":ProductID", $this->Product->ProductID);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		if($num > 0) 
		{
			if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$this->LendingID = $row['lendingID'];
					$this->StartDate = $row['StartDate'];
					$this->DueDate = $row['DueDate'];
					$this->ExtraInfo = $row['Comments'];
					$this->Comments = $row['ExtraInfo'];
					$this->Active = 1;
					
					$group = new LendingGroup();
					$group->LendingGroupID = $row['LendingGroupID'];
					$group->Active = true;
					if($Role >= 2)
					{
						$member = new Member();
						$member->MemberId = $row['MemberID'];
						$member->getMemberByID();
						$group->Member = $member;
					}
					$this->Group = $group;
					
					return true;
				}
		}
		else
		{ 
			return false;
		}

	}
	function getAllLendingsByProduct($productId){
		//echo($this->Product->ProductID);
		// select all query
		$query = "SELECT LendingID, pl.ProductID, ProductName, ProductCode, StartDate, DueDate, ReturnedDate, ExtraInfo, Comments, ll.Active, ml.Name, ml.Lastname
                FROM lendings_lookup ll
                INNER JOIN products_lookup pl
                ON ll.ProductID = pl.ProductId
                INNER JOIN lendings_group lg
                ON ll.LendingGroupID = lg.LendingGroupID
                INNER JOIN members_lookup ml
                ON lg.MemberID = ml.MemberID
                WHERE ll.ProductID = :ProductID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":ProductID", $productId);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		$lending_arr=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$product= array(
			"ProductID" => $row['ProductID'],
			"ProductName" => $row['ProductName'],
			"ProductCode" => $row['ProductCode'],
			);
                        $member = array(
                        "Name" => $row['Name'],
                        "LastName" => $row['Lastname'],
                        );
                        $group = array(
                        "Member" => $member
                        );
			$lending= array(
				"LendingID" => $row['LendingID'],
				"StartDate" => $row['StartDate'],
				"DueDate" => $row['DueDate'],
				"ReturnedDate" => $row['ReturnedDate'],
				"Product" => $product,
				"ExtraInfo" => $row['ExtraInfo'],
				"Comments" => $row['Comments'],
                                "Group" => $group,
				"Active" => (bool)$row['Active']
			);
			array_push($lending_arr, $lending);
		}
		return $lending_arr;
	}
	function getLendingsByGroup($groupId){
		//echo($this->Product->ProductID);
		// select all query
		$query = "SELECT LendingID, pl.ProductID, ProductName, ProductCode, StartDate, DueDate, ReturnedDate, ExtraInfo, Comments, ll.Active, pi.Description, pi.TechnicalDetails, pi.BeforeLending, pi.AfterLending
                FROM lendings_lookup ll
                INNER JOIN products_lookup pl
                ON ll.ProductID = pl.ProductId
				INNER JOIN products_info pi
				on ll.ProductID = pi.ProductID
                WHERE ll.LendingGroupID = :LendingGroupID";
				
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":LendingGroupID", $groupId);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		$lending_arr=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$product_info = array(
			"Description" => $row['Description'],
			"TechnicalDetails" => $row['TechnicalDetails'],
			"BeforeLending" => $row['BeforeLending'],
			"AfterLending" => $row['AfterLending'],
			);
			$product= array(
			"ProductID" => $row['ProductID'],
			"ProductName" => $row['ProductName'],
			"ProductCode" => $row['ProductCode'],
			"Info" => $product_info,
			);
			
			
			$lending= array(
				"LendingID" => $row['LendingID'],
				"StartDate" => $row['StartDate'],
				"DueDate" => $row['DueDate'],
				"ReturnedDate" => $row['ReturnedDate'],
				"Product" => $product,
				"ExtraInfo" => $row['ExtraInfo'],
				"Comments" => $row['Comments'],
				"Active" => (bool)$row['Active']
			);
			array_push($lending_arr, $lending);
		}
		return $lending_arr;
	}
	function getLendingsByMember($memberId){
		//echo($this->Product->ProductID);
		// select all query
		$query = "SELECT LendingID, pl.ProductID, ProductName, ProductCode, StartDate, DueDate, ReturnedDate, ExtraInfo, Comments, ll.Active
                FROM lendings_lookup ll
                INNER JOIN lendings_group lg
                ON ll.LendingGroupID = lg.LendingGroupID
                INNER JOIN products_lookup pl
                ON ll.ProductID = pl.ProductId
                WHERE lg.MemberID = :MemberID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":MemberID", $memberId);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();

		$lending_arr=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$product= array(
			"ProductID" => $row['ProductID'],
			"ProductName" => $row['ProductName'],
			"ProductCode" => $row['ProductCode'],
			);
			$lending= array(
				"LendingID" => $row['LendingID'],
				"StartDate" => $row['StartDate'],
				"DueDate" => $row['DueDate'],
				"ReturnedDate" => $row['ReturnedDate'],
				"Product" => $product,
				"ExtraInfo" => $row['ExtraInfo'],
				"Comments" => $row['Comments'],
				"Active" => (bool)$row['Active']
			);
			array_push($lending_arr, $lending);
		}
		return $lending_arr;
	}
	function addLendingToGroup($LendingGroupID){
		
		// select all query
		$query = "INSERT INTO lendings_lookup (LendingGroupID,ProductID,StartDate,DueDate,ReturnedDate,ExtraInfo,Comments,Active)" .
                "VALUES (:LendingGroupID,:ProductID,:StartDate,:DueDate,NULL,:ExtraInfo,:Comments,1)";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":LendingGroupID", $LendingGroupID);
		$stmt->bindParam(":ProductID", $this->Product->ProductID);
		$stmt->bindParam(":StartDate", $this->StartDate);
		$stmt->bindParam(":DueDate", $this->DueDate);
		$stmt->bindParam(":ExtraInfo", $this->ExtraInfo);
		$stmt->bindParam(":Comments", $this->Comments);
	
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
	function checkOffLending()
	{
		
		// select all query
		$query = "UPDATE lendings_lookup SET Active = 0, ReturnedDate = :ReturnedDate, Comments = :Comments WHERE LendingID = :LendingID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":ReturnedDate", $this->ReturnedDate);
		$stmt->bindParam(":Comments", $this->Comments);
		$stmt->bindParam(":LendingID", $this->LendingID);
	
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
	function extendLending()
	{
		
		// select all query
		$query = "UPDATE lendings_lookup SET DueDate = :DueDate WHERE LendingID = :LendingID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":DueDate", $this->DueDate);
		$stmt->bindParam(":LendingID", $this->LendingID);
	
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
class LendingGroup
{
     // database connection and table name
    private $conn;
     // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
    // object properties
    public $LendingGroupID;
    public $Member;
    public $Active;
    public $Status;
	public $Lendings;
	
	function getActiveLendingGroups()
	{
		// select all query
		$query = "SELECT LendingGroupID,MemberID FROM lendings_group WHERE Active = 1";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		// execute query
		$stmt->execute();
	 
		return $stmt;
	}
	function addLendingGroup()
	{
		// select all query
		$query = "INSERT INTO lendings_group (MemberID,Active)" .
            " VALUES (:MemberID,1)";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":MemberID", $this->Member->MemberId);
		if($stmt->execute()){
			$this->LendingGroupID = $this->conn->lastInsertId();
			$lend = new Lending();
			foreach($this->Lendings as $lending)
			{		
				$lend->StartDate = $lending->StartDate;
				$lend->DueDate = $lending->DueDate;
				$lend->Product = $lending->Product;
				$lend->ExtraInfo = $lending->ExtraInfo;
				$lend->Comments = $lending->Comments;
				
				$lend->addLendingToGroup($this->LendingGroupID );
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	function checkOffLendingGroup()
	{
		// select all query
		$query = "UPDATE lendings_group SET Active = 0 WHERE LendingGroupID = :LendingGroupID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":LendingGroupID", $this->LendingGroupID);
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