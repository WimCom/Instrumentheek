<?php
include_once '../objects/category.php';
include_once '../objects/picture.php';
class Product{
    // database connection and table name
    private $conn;
 
    // object properties
    public $ProductID;
    public $ProductName;
    public $CreationDate;
    public $ProductCode;
    public $Active;
  	public $Category;
	public $CurrentLending;
	public $CurrentReservations;
	public $Info;
	public $PictureList;
	public $Lendings;
	public $Status;//0: normal, 1: maintenance, 2: repair
	
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	// read products
	function read(){
		
		// select all query
		$query = "SELECT ProductId,ProductName,CreationDate,ProductCode,CategoryID,Active,Status FROM products_lookup WHERE Active = 1";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// execute query
		$stmt->execute();
	 
		return $stmt;
	}
	// create product
	function addProduct(){
		$query = "INSERT INTO products_lookup (ProductName,ProductCode,CreationDate,CategoryID,Active) " .
                "VALUES (:ProductName,:ProductCode,:CreationDate,:CategoryID,1)";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->ProductName=htmlspecialchars(strip_tags($this->ProductName));
		$this->ProductCode=htmlspecialchars(strip_tags($this->ProductCode));
		$this->CreationDate=htmlspecialchars(strip_tags($this->CreationDate));
		$this->CategoryID=htmlspecialchars(strip_tags($this->Category->CategoryID));

		$stmt->bindParam(":ProductName", $this->ProductName);
		$stmt->bindParam(":ProductCode", $this->ProductCode);
		$stmt->bindParam(":CreationDate", $this->CreationDate);
		$stmt->bindParam(":CategoryID", $this->CategoryID);
		
		
	 
		// execute query
		if($stmt->execute()){
			$this->ProductID = $this->conn->lastInsertId();
			return true;
		}
	 
		return false;   
	}
	
	function updateProduct(){
		$query = "UPDATE products_lookup SET ProductName = :ProductName, ProductCode = :ProductCode, CategoryID = :CategoryID " .
                "WHERE ProductId = :ProductId";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->ProductName=htmlspecialchars(strip_tags($this->ProductName));
		$this->ProductCode=htmlspecialchars(strip_tags($this->ProductCode));
		$this->CategoryID=htmlspecialchars(strip_tags($this->Category->CategoryID));

		$stmt->bindParam(":ProductName", $this->ProductName);
		$stmt->bindParam(":ProductCode", $this->ProductCode);
		$stmt->bindParam(":CategoryID", $this->CategoryID);
		$stmt->bindParam(":ProductId", $this->ProductId);
		
	 
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;   
	}
	
	function getProductByID(){
		$query = "SELECT ProductName, CreationDate, ProductCode, CategoryID, Active FROM products_lookup WHERE ProductID = :productID";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":productID", $this->ProductID);
		
		// execute query
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$this->ProductName=$row['ProductName'];
			$this->ProductCode=$row['ProductCode'];
			$this->CreationDate=$row['CreationDate'];
			$cat = new Category();
			$cat->CategoryID = $row['CategoryID'];;
			$cat->getCategoryById();
			$this->Category = $cat;
			$this->Active = (bool)$row['Active'];
			$picture = new Picture();
			$picture->product = $this;
			$this->PictureList = $picture->GetPicturesByProduct();
			$inf = new ProductInfo();
			$inf->ProductID = $this->ProductID;
			$inf->getProductInfo();
			$this->Info = $inf;
			
			return true;
		}
	 
		return false;   
	}
	function deleteProduct()
	{
		$query = "UPDATE products_lookup SET Active = 0 WHERE ProductId = :ProductId";
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":ProductId", $this->ProductID);
		
		// execute query
		if($stmt->execute())
		{
			return true;
		}
		return false;   
	}
	function updateProductStatus()
	{
		echo( $this->Status);
		$query = "UPDATE products_lookup SET Status = :Status WHERE ProductId = :ProductId";
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":ProductId", $this->ProductID);
		$stmt->bindParam(":Status", $this->Status);
		// execute query
		if($stmt->execute())
		{
			return true;
		}
		return false;   
	}
}
class ProductInfo{
    // database connection and table name
    private $conn;
	
	public $ProductID;
	public $Description;
    public $TechnicalDetails;
    public $SerialNumber;
    public $BeforeLending;
    public $AfterLending;
	public $Thumbnail;
 
    // constructor with $db as database connection
    public function __construct(){
		// instantiate database and product object
		$database = new Database();
		$db = $database->getConnection();
        $this->conn = $db;
    }
	function addProductInfo(){
		$query = "INSERT INTO products_info (ProductId, Description,TechnicalDetails,BeforeLending,AfterLending)" .
                 "VALUES (:pProductID,:pDescription,:pTechnicalDetails,:pBeforeLending,:pAfterLending)";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":pProductID", $this->ProductID);
		$stmt->bindParam(":pDescription", $this->Description);
		$stmt->bindParam(":pTechnicalDetails", $this->TechnicalDetails);
		$stmt->bindParam(":pBeforeLending", $this->BeforeLending);
		$stmt->bindParam(":pAfterLending", $this->AfterLending);
		
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;   
	}
	
	function updateProductInfo(){
		$query = "UPDATE products_info SET Description = :pDescription, TechnicalDetails = :pTechnicalDetails, BeforeLending = :pBeforeLending, AfterLending = :pAfterLending " .
                 "WHERE ProductId = :pProductID";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(":pProductID", $this->ProductID);
		$stmt->bindParam(":pDescription", $this->Description);
		$stmt->bindParam(":pTechnicalDetails", $this->TechnicalDetails);
		$stmt->bindParam(":pBeforeLending", $this->BeforeLending);
		$stmt->bindParam(":pAfterLending", $this->AfterLending);
		$stmt->bindParam(":pAfterLending", $this->AfterLending);
		
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;   
	}
	function getProductInfo()
	{
		// select all query
		$query = "SELECT Description,TechnicalDetails,BeforeLending,AfterLending,Thumbnail FROM products_info WHERE ProductID = :productID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":productID", $this->ProductID);
		// execute query
		$stmt->execute();
		$num = $stmt->rowCount();
		
		if($num > 0)
		{
			if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$this->Description = $row['Description'];
					$this->TechnicalDetails = $row['TechnicalDetails'];
					$this->BeforeLending = $row['BeforeLending'];
					$this->AfterLending = $row['AfterLending'];
					$this->Thumbnail = $row['Thumbnail'];
					return true;
				}
		}
		else
		{
			return false;
		}
		
	}
}