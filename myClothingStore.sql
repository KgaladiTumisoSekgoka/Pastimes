-- Remember if you would like to run loadClothingStore.php comment out code line 3 to 6

Drop Schema myClothingStore;
CREATE DATABASE myClothingStore;
USE myClothingStore;

DROP TABLE IF EXISTS tbladmin;
CREATE TABLE tbladmin (
  AdminID int NOT NULL AUTO_INCREMENT,
  Name varchar(100) DEFAULT NULL,
  Email varchar(100) NOT NULL,
  PasswordHash varchar(255) NOT NULL,
  PRIMARY KEY (AdminID),
  UNIQUE KEY (Email)
);

INSERT INTO `tbladmin` (`AdminID`, `Name`, `Email`, `PasswordHash`) VALUES
(1, 'Tumiso', 'kgaladisekgoka@gmail.com', '$2y$10$M4TZVpFKRc09Ba7vSP/pR.CGMOD.9NWzdtjOq.4teN1Cn2hG15Tz.'),
(2, 'Alice Johnson', 'alice.johnson@example.com', '$2y$10$NQv3IvRnrqQwImKVfo8Pje/ihERFUw8Fyd4bFZCUL.K7/ewJ73AJK'),
(3, 'Bob Smith', 'bob.smith@example.com', '$2y$10$f0GZW2nXcEegDg8xZ0akg.Iq6J6SKsdzrwMJsc4k682GABOTZa3SW'),
(4, 'Charlie Brown', 'charlie.brown@example.com', '$2y$10$K1Ux3Y8RDnbnYoJffCMGrOS5s/Jw.6z/oPakxDHznMT1ZkF2DK3xm'),
(5, 'Diana Prince', 'diana.prince@example.com', '$2y$10$UOykdZcxL.tWLDDHNvOcue6p7oTn7fgpTC529tVkLn3ukFXD3e3L2');



DROP TABLE IF EXISTS tblseller;
CREATE TABLE tblseller (
  SellerID int NOT NULL AUTO_INCREMENT,
  Username varchar(50) NOT NULL,
  Email varchar(100) NOT NULL,
  Password varchar(255) NOT NULL,
  SellerName varchar(100) DEFAULT NULL,
  ContactNo varchar(15) DEFAULT NULL,
  Address text,
  isApproved tinyint(1) DEFAULT 0,
  DateJoined timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (SellerID),
  UNIQUE KEY (Email)
);

INSERT INTO `tblseller` (`SellerID`, `Username`, `Email`, `Password`, `SellerName`, `ContactNo`, `Address`, `isApproved`, `DateJoined`) VALUES
(1, 'seller1', 'seller1@example.com', '$2y$10$BCHCCidCWlyZhP12c6rW7e7nNozftWKUIHb5xGrvYWiLKUakjqhcm', 'Alice Johnson', '1234567890', '123 Main St, Cityville', 0, '2024-10-28 06:22:12'),
(2, 'seller2', 'seller2@example.com', '$2y$10$LqSPQbzfD4MqLxJstHTlYejLaozp2IT5QxW87Re13nMJS90lzgltO', 'Bob Smith', '2345678901', '456 Elm St, Townsville', 0, '2024-10-28 06:23:44'),
(3, 'seller3', 'seller3@example.com', '$2y$10$qgO6Se26UIwW5M7URx.hL.N5tqq3ATlizGeLZ6P8jliHudMI4ZAH6', 'Carol Davis', '3456789012', '789 Maple Ave, Villageburg', 0, '2024-10-28 06:24:29'),
(4, 'seller4', 'seller4@example.com', '$2y$10$4UGi4Ta6JoGKNosLIa5v2.8WdxJKg1QED1LbCwUJsfOlpnIh0YOrO', 'David Wilson', '4567890123', '101 Oak St, Hamletville', -1, '2024-10-28 06:26:11'),
(5, 'demo', 'demo@gmail.com', '$2y$10$NuDi.YhYkCLk3HCBjtx.0eKkBTHL/hmzx62miLh94p6EmbyZk6aQm', 'Demo', '1234567890', 'Demo st', 1, '2024-11-03 08:01:44');

-- Table: tbluser
DROP TABLE IF EXISTS tbluser;
CREATE TABLE tbluser (
  UserID INT NOT NULL AUTO_INCREMENT,
  Username VARCHAR(50) UNIQUE NOT NULL,
  Name VARCHAR(100) DEFAULT NULL,
  Surname VARCHAR(100) DEFAULT NULL,
  Address VARCHAR(100) DEFAULT NULL,
  Email VARCHAR(100) UNIQUE NOT NULL,
  PasswordHash VARCHAR(255) NOT NULL,
  isApproved TINYINT(1) DEFAULT 0,
  PRIMARY KEY (UserID)
);

INSERT INTO `tbluser` (`UserID`, `Username`, `Name`, `Surname`, `Address`, `Email`, `PasswordHash`, `isApproved`) VALUES
(1, 'jdoe', 'John', 'Doe', '123 Main St', 'j.doe@abc.co.za', '$2y$10$QAilIQ7WHuhcBOeUGPlt/OTVFoXUNmHZmM2zB03FlQIHlGEDMBtBm', 0),
(2, 'jsmith', 'Jane', 'Smith', '456 Elm St', 'j.smith@abc.co.za', '$2y$10$Spp3DTqlvRdvAo0dp24pU.ULZLZfrBo.SXvfsvTswrtqcaD6vkoFe', 0),
(3, 'mreed', 'Michael', 'Reed', '789 Oak St', 'm.reed@abc.co.za', '$2y$10$sHCA82MT7i08fKdfP/gUVuzXPM3gCRYyfvLBJwylY2Gp/OsA9ouD6', 0),
(4, 'slee', 'Sarah', 'Lee', '321 Pine St', 's.lee@abc.co.za', '$2y$10$BHJj7.rX238zYeLaND1Zo.QgvLkGVajHMjjtYLsa6AOC4ojZdnxvq', 0),
(5, 'dkim', 'David', 'Kim', '654 Maple St', 'd.kim@abc.co.za', '$2y$10$XxP0VOc3HMQ9WYwhZ5MkweLplekHPb7sjNEXCT32QONyEPRPdjQj6', 0);
-- Unhashed password is in the UserData.txt found in the _resources folder :D

DROP TABLE IF EXISTS tblclothes;
CREATE TABLE tblclothes (
  ClothesID int NOT NULL AUTO_INCREMENT,
  SellerID int,
  Name varchar(100) NOT NULL,
  Price decimal(10,2) NOT NULL,
  Quantity int NOT NULL,
  Description text,
  ImagePath varchar(255) DEFAULT NULL,
  Brand varchar(50) DEFAULT NULL,
  Sizes varchar(50) DEFAULT NULL,
  Category varchar(255) DEFAULT NULL,
  PRIMARY KEY (ClothesID),
  FOREIGN KEY (SellerID) REFERENCES tblseller(SellerID)
);

INSERT INTO `tblclothes` (`ClothesID`, `SellerID`, `Name`, `Price`, `Quantity`, `Description`, `ImagePath`, `Brand`, `Sizes`, `Category`) VALUES
(1, 1, 'Beige Women Coat', 899.99, 5, 'A stylish beige coat perfect for women during the fall season.', '_images/beige_women_coat.jpg', 'Fashionista', 'S, M, L', 'Women'),
(2, 2, 'Black Dress', 499.99, 14, 'An elegant black dress suitable for evening occasions.', '_images/black_dress.jpg', 'Elegant Wear', 'S, M, L, XL', 'Women'),
(3, 3, 'Blue Dress', 599.99, 9, 'A beautiful blue dress that flows gracefully for any event.', '_images/blue_dress.jpg', 'Dress Co.', 'S, M, L', 'Women'),
(4, 4, 'Long Shirt', 299.99, 8, 'A casual long shirt perfect for layering or wearing alone.', '_images/long_shirt.jpg', 'Casual Line', 'M, L, XL', 'Men'),
(5, 5, 'T-Shirt', 199.99, 25, 'A comfortable t-shirt for everyday wear.', '_images/t_shirt.jpg', 'Everyday Wear', 'S, M, L, XL', 'Men'),
(6, 1, 'Pink Linen Outfit', 999.99, 7, 'Elegant pink linen outfit perfect for warm weather.', '_images/pink_coat.jpg', 'Fashionista', 'S, M, L', 'Women'),
(7, 2, 'Everyday Explorer Alphabet Set', 299.99, 10, 'Childrens Casual Outfit Set, 2-Piece Set', '_images/kids_casual_outfit.png', 'Carters', '7, 8, 9, 10, 11, 12, 13, 14', 'Kids'),
(8, 3, 'Leather Belt', 99.99, 11, 'Reversible leather belt with a sleek silver buckle.', '_images/leather_belt.jpg', 'Old Khaki', 'XS, S, M, L, XL, XXL', 'Men'),
(9, 4, 'Girls Outfit', 99.99, 8, '2PCS, Girls Y2k Leopard Glasses & Figure Print Long Sleeve Tee + Pants', '_images/kids_girls_outfit.png', 'Puma', '4, 5, 6X', 'Kids');


DROP TABLE IF EXISTS tblmessages;
CREATE TABLE tblmessages (
  MessageID int NOT NULL AUTO_INCREMENT,
  SenderID int NOT NULL,
  ReceiverID int NOT NULL,
  MessageText text NOT NULL,
  Timestamp datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (MessageID),
  FOREIGN KEY (SenderID) REFERENCES tbladmin(AdminID),
  FOREIGN KEY (ReceiverID) REFERENCES tbladmin(AdminID)
);

DROP TABLE IF EXISTS tblorder;
CREATE TABLE tblorder (
  OrderID int NOT NULL AUTO_INCREMENT,
  UserID int NOT NULL,
  Username varchar(50) NOT NULL,
  ClothingName varchar(255) NOT NULL,
  Quantity int NOT NULL,
  Price decimal(10,2) NOT NULL,
  OrderDate datetime DEFAULT CURRENT_TIMESTAMP,
  TotalPrice decimal(10,2) NOT NULL,
  PRIMARY KEY (OrderID),
  FOREIGN KEY (UserID) REFERENCES tblseller(SellerID)
);

DROP TABLE IF EXISTS tblorderitems;
CREATE TABLE tblorderitems (
  id int NOT NULL AUTO_INCREMENT,
  order_id int NOT NULL,
  item_name varchar(255) NOT NULL,
  item_price decimal(10,2) NOT NULL,
  item_quantity int NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (order_id) REFERENCES tblorder(OrderID)
);

DROP TABLE IF EXISTS tblorders;
CREATE TABLE tblorders (
  order_id     VARCHAR(50)   NOT NULL,                 
  user_id      VARCHAR(50)   DEFAULT NULL,             
  seller_id    VARCHAR(50)   DEFAULT NULL,             
  session_id   VARCHAR(50)   DEFAULT NULL,             
  total_amount DECIMAL(10,2) DEFAULT NULL,             
  order_date   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  FOREIGN KEY (user_id) REFERENCES tblseller(SellerID),
  FOREIGN KEY (seller_id) REFERENCES tblseller(SellerID)
);