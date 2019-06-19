<?php
include_once '../objects/member.php';
class Picture{
 
    // database connection and table name
    private $conn;
 
    // object properties
	public $ImageID;
    public $FileName;
    public $AbsoluteUrl; 
    public $Description; 
    public $DateAdded; 
	public $Active;
    public $product; 
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	function GetPicturesByProduct()
	{
		// select all query
		$query = "SELECT il.ImageID,il.Description,il.FileName,il.DateAdded FROM image_product ip " .
                    "INNER JOIN image_lookup il " .
                    "ON ip.ImageID = il.ImageID " .
                    "WHERE ip.ProductID = :ProductID AND il.Active = 1";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":ProductID", $this->product->ProductID);
		// execute query
		$stmt->execute();
	
		$picture_arr = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$picture= array(
			"ImageID" => $row['ImageID'],
			"FileName" => $row['FileName'],
			"AbsoluteUrl" => "http://instrumentheek.be/Images/" . $row['FileName'],
			"Description" => $row['Description'],
			);
			
			array_push($picture_arr, $picture);
		}
		return $picture_arr;

	}
	function addPicture()
	{
		$query = "INSERT INTO image_lookup (Description,FileName,DateAdded,Active) VALUES (:Description,:FileName,NOW(),1);";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":Description", $this->Description);
		$stmt->bindParam(":FileName", $this->FileName);
		if($stmt->execute()){
			$this->ImageID = $this->conn->lastInsertId();
			$query = "INSERT INTO image_product (ImageID,ProductID) VALUES (:ImageID,:ProductID)";
			
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(":ImageID", $this->ImageID);
			$stmt->bindParam(":ProductID", $this->product->ProductID);
			if($stmt->execute())
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	

}