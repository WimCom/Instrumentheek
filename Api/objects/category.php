<?php
class Category{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $CategoryID;
    public $CategoryCode;
    public $Description;
    public $Kind;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	// read products
	function getCategoryById(){
	 
		// select all query
		$query = "SELECT CategoryCode,Description,Kind FROM products_category WHERE CategoryID = :Categoryid";
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":Categoryid", $this->CategoryID);
		// execute query
		$stmt->execute();
	 
		$num = $stmt->rowCount();

		if($num > 0)
		{
			if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
					$this->CategoryCode = $row['CategoryCode'];
					$this->Description = $row['Description'];
					$this->Kind = $row['Kind'];
					
					//r json_encode($category);
			}
		}
	}

	function addCategory()
	{
		$query = "INSERT INTO products_category (CategoryCode,Description,Kind) VALUES (:pCode,:pDescription,:pKind);";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":pCode", $this->CategoryCode);
		$stmt->bindParam(":pDescription", $this->Description);
		$stmt->bindParam(":pKind", $this->Kind);
		
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;   
	}
	
	function getAllCategories()
	{
		$query = "SELECT CategoryCode,Description,Kind,CategoryID FROM products_category";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$category_arr=array();
		
		// retrieve our table contents
		// fetch() is faster than fetchAll()
		// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			
			$category_item= array(
				"CategoryID" => $row['CategoryID'],
				"CategoryCode" => $row['CategoryCode'],
				"Description" => $row['Description'],
				"Kind" => $row['Kind']
			);
			
			
			array_push($category_arr, $category_item);
		}
		return $category_arr;
	}
}