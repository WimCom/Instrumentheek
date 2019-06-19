<?php
class Login{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $Username;
    public $Password;
	public $MemberId;
	public $Role;
	public $SessionId;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
	
	function validate()
	{
		$query = "SELECT Password,MemberId,Role FROM members_lookup WHERE Username = :username and Inactive = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":username", $this->Username);
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num > 0)
		{
			if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				if (md5($this->Password,false) == str_replace("-","",strtolower($row['Password'])))
				{
					$this->MemberId = $row['MemberId'];
					$this->Role = $row['Role'];
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}
	
	function login()
	{
		//close all previous sessions
		$sessionid = md5($this->MemberId . strval(time()));
		//return $this->MemberId;
		$query = "UPDATE login_session SET Active = 0, EndDate = NOW() WHERE MemberID = :memberid AND Active = 1";
		
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":memberid", $this->MemberId);

		// execute query
		$stmt->execute();
		
		$sessionid = md5($this->MemberId . strval(time()));
		//return $this->MemberId;
		$query = "INSERT INTO login_session (SessionId,MemberID,StartDate,EndDate,Active) VALUES (:sessionid, :memberid, NOW(),null,1)";
		
		$stmt = $this->conn->prepare($query);
		
		$stmt->bindParam(":sessionid", $sessionid);
		$stmt->bindParam(":memberid", $this->MemberId);

		// execute query
		if($stmt->execute()){
			$this->SessionId = $sessionid;
			return;
		}
		else
		{
			$this->SessionId = "";
			return "";  
		}
	}
}