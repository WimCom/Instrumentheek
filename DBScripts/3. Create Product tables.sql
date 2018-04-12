CREATE TABLE Products_Category
(
	CategoryID int auto_increment,
	CategoryCode varchar(3),
    Kind TEXT,
    Description TEXT,
    PRIMARY KEY(CategoryID)
);

CREATE TABLE Products_lookup
(
	ProductId int auto_increment,
    ProductName TEXT,
	CreationDate datetime,
	ProductCode TEXT,
    CategoryID int,
	Active tinyint(1),
    PRIMARY KEY(ProductId),
    FOREIGN KEY(CategoryID) REFERENCES Products_Category(CategoryID)
);
CREATE TABLE Products_Info
(
	ProductInfoId int auto_increment,
	ProductId int,
    Description TEXT,
    TechnicalDetails TEXT,
    SerialNumber TEXT,
    BeforeLending TEXT,
    AfterLending TEXT,
    PRIMARY KEY(ProductInfoId),
    FOREIGN KEY(ProductId) REFERENCES Products_lookup(ProductId)
);


