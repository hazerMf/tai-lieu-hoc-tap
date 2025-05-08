-- cau 1
CREATE DATABASE VehicleOwnership;
GO

USE VehicleOwnership;
GO

CREATE TABLE Owner (
    ownerID INT PRIMARY KEY,
    name NVARCHAR(50)
);

CREATE TABLE Vehicle (
    vehicleID INT PRIMARY KEY,
    maker VARCHAR(30),
    model VARCHAR(30),
    year INT,
    ownerID INT,
    FOREIGN KEY (ownerID) REFERENCES Owner(ownerID)
);

CREATE TABLE Car (
    vehicleID INT PRIMARY KEY,
    NumDoors INT,
    bodyStyle VARCHAR(30),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle(vehicleID)
);
CREATE TABLE Motorcycle (
    vehicleID INT PRIMARY KEY,
    type VARCHAR(30),
    engineSize INT,
    FOREIGN KEY (vehicleID) REFERENCES Vehicle(vehicleID)
);


-- cau 2
--select * from Members
--where Sex = 'Male' and BirthDate >'1990-12-31'

-- cau 3
--select BookID, Title, PublicationYear, Books.GenreID, GenreName
--from Books join Genres on Books.GenreID = Genres.GenreID
--where PublicationYear < 1900 and GenreName = 'Adventure'

-- cau 4
--select distinct Authors.AuthorID, AuthorName,Country,Sex,YearOfBirth
--from Authors 
--join Books on Books.AuthorID = Authors.AuthorID
--join BookCopies on BookCopies.BookID = Books.BookID
--join LoanDetails on LoanDetails.CopyID = BookCopies.CopyID
--where Country = 'United Kingdom' or Country = 'United States'
--order by Country asc , Authors.AuthorName desc

-- cau 5
--select Genres.GenreID, 
--		GenreName, 
--		count(CASE WHEN Books.PublicationYear > 1980 THEN CopyID END) as NumberOfBookCopies
--from Genres
--left join Books on Books.GenreID= Genres.GenreID
--left join BookCopies on Books.BookID = BookCopies.BookID
--group by Genres.GenreID, Genres.GenreName
--order by NumberOfBookCopies desc , Genres.GenreName ASC

--cau 6


--cau 7
