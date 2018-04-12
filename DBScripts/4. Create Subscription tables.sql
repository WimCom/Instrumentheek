CREATE TABLE Subscriptions_lookup
(
	SubscriptionId int auto_increment,
	MemberId int,
	DateStart datetime,
	DateEnd datetime,
	Active tinyint(1),
    PRIMARY KEY(SubscriptionId),
    FOREIGN KEY(MemberId) REFERENCES Members_lookup(MemberId)
);