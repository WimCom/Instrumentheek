CREATE TABLE Reservation_lookup
(
	ReservationID int auto_increment,
    MemberID int,
    ProductID int,
	StartDate datetime,
	EndDate datetime,
    Comments text,
	Active tinyint(1),
    PRIMARY KEY(ReservationID),
    FOREIGN KEY(MemberID) REFERENCES Members_lookup(MemberID),
    FOREIGN KEY(ProductID) REFERENCES Products_lookup(ProductID)
);