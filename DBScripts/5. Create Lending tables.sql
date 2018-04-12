CREATE TABLE Lendings_group
(
	LendingGroupID int auto_increment,
    MemberID int,
	Active tinyint(1),
    PRIMARY KEY(LendingGroupID),
    FOREIGN KEY(MemberID) REFERENCES Members_lookup(MemberID)
);

CREATE TABLE Lendings_lookup
(
	LendingID int auto_increment,
    LendingGroupID int,
    ProductID int,
    StartDate datetime,
	DueDate datetime,
    ReturnedDate datetime,
    ExtraInfo text,
    Comments text,
    Active tinyint(1),
    PRIMARY KEY(LendingID),
    FOREIGN KEY(LendingGroupID) REFERENCES Lendings_group(LendingGroupID),
    FOREIGN KEY(ProductID) REFERENCES Products_lookup(ProductID)
);