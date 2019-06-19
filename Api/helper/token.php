<?php
class token{
 
    // database connection and table name
    private $conn;
 
    // object properties
    
	public $Role;
	public $MemberId;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	
	function validateToken($Token)
	{
		$query = "SELECT ml.Role,ml.MemberId FROM login_session ls INNER JOIN members_lookup ml ON ls.MemberId = ml.MemberId WHERE SessionId = :sessionId";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":sessionId", $Token);
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num > 0)
		{	
			if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->Role = $row['Role'];
				$this->MemberId = $row['MemberId'];
				return;
			}
		}
		else
		{
			$this->role = 0;
			return;
		}
	}
}