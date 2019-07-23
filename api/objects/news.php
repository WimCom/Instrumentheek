<?php
include_once '../objects/member.php';
class News{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $NewsID;
    public $CreationDate;
    public $Title;
    public $HTML;
	//public $Member;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	
	function getAllNews()
	{
		$query = "SELECT NewsId,CreationDate,Title,HTML FROM news_lookup WHERE Active = 1 ORDER BY CreationDate DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$result=array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{    
			$news_item= array(
				"NewsID" => $row['NewsId'],
				"CreationDate" => $row['CreationDate'],
				"Title" => $row['Title'],
				"HTML" => $row['HTML']
			);
			array_push($result, $news_item);
		}
		return $result;
	}
}