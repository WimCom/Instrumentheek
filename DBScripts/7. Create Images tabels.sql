CREATE TABLE Image_lookup
(
	ImageID int auto_increment,
    Description text,
    FileName text,
    DateAdded datetime,
    PRIMARY KEY(ImageID)
);
    
CREATE TABLE Image_Product
(
	ImageProductID int auto_increment,
    ImageID int,
    ProductID int,
    PRIMARY KEY (ImageProductID),
    FOREIGN KEY (ImageID) REFERENCES Image_lookup(ImageID),
    FOREIGN KEY (ProductID) REFERENCES Products_lookup(ProductID)
);


