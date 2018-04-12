CREATE TABLE Members_lookup
(
	MemberId int auto_increment,
	Name TEXT NOT NULL,
	Lastname TEXT NOT NULL,
	Username TEXT NOT NULL,
	Password TEXT,
	Inactive tinyint(1) DEFAULT 0,
    PRIMARY KEY(MemberId)
);
CREATE TABLE Members_Info
(
	MemberInfoId int auto_increment,
	MemberId int,
	DateOfBirth datetime,
	Street TEXT,
	HouseNumber TEXT,
	PostalNumber int,
	City TEXT,
	TelephoneNumber TEXT,
	GSMNumber TEXT,
	Email TEXT,
	HasDonated tinyint(1),
	Informed tinyint(1),
	IncreasedCompensation tinyint(1) DEFAULT 0,
    PRIMARY KEY(MemberInfoId),
    FOREIGN KEY(MemberID) REFERENCES Members_lookup(MemberId)
);