<?php
class Member{
 
    // database connection and table name
    private $conn;
 
    // object properties
	public $MemberId;
    public $Name;
    public $LastName;
    public $UserName;
    public $PassWord;
    public $InActive;
	public $Info;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	
	function getMemberByID(){
		
		$query = "SELECT 
	ml.MemberID, 
    Name, 
	Lastname,
    Username,
    Inactive, 
    DateOfBirth, 
    Street, 
    HouseNumber, 
    PostalNumber, 
    City, 
    TelephoneNumber, 
    GSMNumber, 
    Email, 
    HasDonated, 
    Informed, 
    IncreasedCompensation 
FROM members_lookup ml
INNER JOIN members_info mi
ON ml.MemberId = mi.MemberId
WHERE ml.MemberID = :MemberID";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":MemberID", $this->MemberId);
		
		// execute query
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			$this->Name=$row['Name'];
			$this->LastName=$row['Lastname'];
			$this->UserName=$row['Username'];
			$this->InActive=(bool)$row['Inactive'];	
			$memberInfo_item= array(
				"DateOfBirth" => $row['DateOfBirth'],
				"Street" => $row['Street'],
				"HouseNumber" => $row['HouseNumber'],
				"PostalNumber" => $row['PostalNumber'],
				"City" => $row['City'],
				"TelephoneNumber" => $row['TelephoneNumber'],
				"GsmNumber" => $row['GSMNumber'],
				"Email" => $row['Email'],
				"HasDonated" => (bool)$row['HasDonated'],
				"WantsToBeInformed" => (bool)$row['Informed'],
				"IncreasedCompensation" => (bool)$row['IncreasedCompensation']
			);
			$this->Info = $memberInfo_item;
			return true;
		}
	 
		return false;   
	}
	function getAllMembers()
	{
		
		$query = "SELECT 
	ml.MemberID, 
    Name, 
	Lastname,
    Username,
    Inactive, 
    DateOfBirth, 
    Street, 
    HouseNumber, 
    PostalNumber, 
    City, 
    TelephoneNumber, 
    GSMNumber, 
    Email, 
    HasDonated, 
    Informed, 
    IncreasedCompensation 
FROM members_lookup ml
INNER JOIN members_info mi
ON ml.MemberId = mi.MemberId
WHERE ml.Inactive = 'false'";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$members_arr=array();
		
		// retrieve our table contents
		// fetch() is faster than fetchAll()
		// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			
			$memberInfo_item= array(
				"DateOfBirth" => $row['DateOfBirth'],
				"Street" => $row['Street'],
				"HouseNumber" => $row['HouseNumber'],
				"PostalNumber" => $row['PostalNumber'],
				"City" => $row['City'],
				"TelephoneNumber" => $row['TelephoneNumber'],
				"GsmNumber" => $row['GSMNumber'],
				"Email" => $row['Email'],
				"HasDonated" => (bool)$row['HasDonated'],
				"WantsToBeInformed" => (bool)$row['Informed'],
				"IncreasedCompensation" => (bool)$row['IncreasedCompensation']
			);
			
			$member_item= array(
				"MemberID" => $row['MemberID'],
				"Name" => $row['Name'],
				"Lastname" => $row['Lastname'],
				"Username" => $row['Username'],
				"Info" => $memberInfo_item
			);
			array_push($members_arr, $member_item);
		}
		return $members_arr;
	}
	function addMember(){
		$query = "INSERT INTO members_lookup (Name,Lastname,Username,Password,Inactive) VALUES (:name,:lastName,:userName,:passWord,0)";
	 
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
	function updateMember(){
		$query = "UPDATE members_lookup SET Name = :pName,Lastname = :pLastName, Username = :pUsername WHERE MemberID = :pMemberID";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":pName", $this->Name);
		$stmt->bindParam(":pLastName", $this->LastName);
		$stmt->bindParam(":pUsername", $this->UserName);
		$stmt->bindParam(":pMemberID", $this->MemberId);
		
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;   
	}
	function updatePassword(){
		$query = "UPDATE members_lookup SET Password = :pPassword WHERE MemberID = :pMemberID";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":pPassword", $this->PassWord);
		$stmt->bindParam(":pMemberID", $this->MemberId);
		
		// execute query
		if($stmt->execute()){
			return 'true';
		}
		return 'false';   
	}
	function getMemberByEmail($email){
		$query = "SELECT mi.MemberID, LastName, Name, Username FROM members_lookup ml 
		INNER JOIN members_info mi ON ml.MemberID = mi.MemberID
		WHERE mi.Email = :pEmail";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":pEmail", $email);
		
		
		// execute query
		if($stmt->execute()){
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->MemberId = $row['MemberID'];
				$this->LastName = $row['LastName'];
				$this->Name = $row['Name'];
				$this->Username = $row['Username'];
			}
			return true;
		}
		return false;   
	}
	function deleteMember(){
		$query = "UPDATE members_lookup SET Inactive = '1' WHERE MemberId = :MemberId";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":MemberId", $this->MemberId);
		
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;   
	}
}
class MemberInfo{
	// database connection and table name
    private $conn;
    public $DateOfBirth;
    public $Street;
    public $HouseNumber;
    public $PostalNumber;
    public $City;
    public $TelephoneNumber;
    public $GsmNumber;
    public $Email;
    public $HasDonated;
    public $WantsToBeInformed;
    public $IncreasedCompensation;
	
	   // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	
	function addMemberInfo($memberId)
	{
	$query = "INSERT INTO members_info (MemberId,DateOfBirth,Street,HouseNumber,PostalNumber,City,TelephoneNumber,GSMNumber,Email,HasDonated, Informed, IncreasedCompensation)
         VALUES (:pMemberId,
		 :pDateOfBirth,
		 :pStreet,
		 :pHouseNumber,
		 :pPostalNumber,
		 :pCity,
		 :pTelephoneNumber,
		 :pGSMNumber,
		 :pEmail,
		 :pHasDonated,
		 :pInformed,
		 :pIncreasedCompensation)";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":pMemberId", $memberId);
		$stmt->bindParam(":pDateOfBirth", $this->DateOfBirth);
		$stmt->bindParam(":pStreet", $this->Street);
		$stmt->bindParam(":pHouseNumber", $this->HouseNumber);
		$stmt->bindParam(":pPostalNumber", $this->PostalNumber);
		$stmt->bindParam(":pCity", $this->City);
		$stmt->bindParam(":pTelephoneNumber", $this->TelephoneNumber);
		$stmt->bindParam(":pGSMNumber", $this->GsmNumber);
		$stmt->bindParam(":pEmail", $this->Email);
		$stmt->bindParam(":pHasDonated", $this->HasDonated);
		$stmt->bindParam(":pInformed", $this->WantsToBeInformed);
		$stmt->bindParam(":pIncreasedCompensation", $this->IncreasedCompensation);
		
		// execute query
		if($stmt->execute()){
			return true;
		}
		else
		{
			echo$stmt->debugDumpParams();
			echo $stmt->errorCode();
		}
		return false;   
	}
	function updateMemberInfo($memberId)
	{
		
	$query = "UPDATE members_info SET DateOfBirth = :pDateOfBirth, Street = :pStreet, HouseNumber = :pHouseNumber, PostalNumber = :pPostalNumber, ".
                "City = :pCity, TelephoneNumber = :pTelephoneNumber, GSMNumber = :pGSMNumber, Email = :pEmail, ".
                "Informed = :pInformed, IncreasedCompensation = :pIncreasedCompensation WHERE MemberId = :pMemberID";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
		$increased = $this->IncreasedCompensation ? '1' : '0';
		$informed = $this->WantsToBeInformed ? '1' : '0';
		
		$stmt->bindParam(":pMemberID", $memberId);
		$stmt->bindParam(":pDateOfBirth", $this->DateOfBirth);
		$stmt->bindParam(":pStreet", $this->Street);
		$stmt->bindParam(":pHouseNumber", $this->HouseNumber);
		$stmt->bindParam(":pPostalNumber", $this->PostalNumber);
		$stmt->bindParam(":pCity", $this->City);
		$stmt->bindParam(":pTelephoneNumber", $this->TelephoneNumber);
		$stmt->bindParam(":pGSMNumber", $this->GsmNumber);
		$stmt->bindParam(":pEmail", $this->Email);
		$stmt->bindParam(":pInformed", $informed);
		$stmt->bindParam(":pIncreasedCompensation", $increased);
		
		// execute query
		if($stmt->execute())
		{
			return true;
		}
		else
		{
			
		}
		return false;   
	}
}