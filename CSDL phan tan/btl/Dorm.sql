create database Dorm;
go
use Dorm;

-- 0. Headquarter
CREATE TABLE Headquarter (
    Id VARCHAR(10) PRIMARY KEY,
    Name NVARCHAR(100) NOT NULL,
    Address NVARCHAR(255),
    Phone VARCHAR(20),
    Email VARCHAR(100)
);
-- 1. Dormitory
CREATE TABLE Dormitory (
    Id VARCHAR(10) PRIMARY KEY,
    Location NVARCHAR(100),
    Floors INT,
    Condition VARCHAR(20),
    NumberOfRooms INT,
	HeadquarterId VARCHAR(10),
	FOREIGN KEY (HeadquarterId) REFERENCES Headquarter(Id)
);

-- 2. Staff
CREATE TABLE Staff (
    Id VARCHAR(10) PRIMARY KEY,
    Name NVARCHAR(150),
    StaffType VARCHAR(20), -- Manager/Guard/Office
    Salary DECIMAL(12,2),
    Phone VARCHAR(20),
    Email VARCHAR(100) UNIQUE,
    DormitoryId VARCHAR(10),
    FOREIGN KEY (DormitoryId) REFERENCES Dormitory(Id)
);

-- 3. Room
CREATE TABLE Room (
    Id VARCHAR(10) PRIMARY KEY,
    DormitoryId VARCHAR(10),
    Capacity INT,
    Gender VARCHAR(10),
    Condition VARCHAR(20),
    Floor INT,
    FOREIGN KEY (DormitoryId) REFERENCES Dormitory(Id)
);

-- 4. Student
CREATE TABLE Student (
    Id VARCHAR(10) PRIMARY KEY,
    Name NVARCHAR(150),
    DateOfBirth DATE,
    Address NVARCHAR(255),
    Gender VARCHAR(10),
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(20),
    RoomId VARCHAR(10),
    FOREIGN KEY (RoomId) REFERENCES Room(Id)
);

-- 5. Contract
CREATE TABLE Contract (
    Id VARCHAR(10) PRIMARY KEY,
    StudentId VARCHAR(10) UNIQUE,
    StaffId VARCHAR(10),
    StartDate DATE,
    EndDate DATE,
    Penalty DECIMAL(12,2),
    FOREIGN KEY (StudentId) REFERENCES Student(Id),
    FOREIGN KEY (StaffId) REFERENCES Staff(Id)
);

-- 6. Bill
CREATE TABLE Bill (
    Id VARCHAR(10) PRIMARY KEY,
    StudentId VARCHAR(10),
    StaffId VARCHAR(10),
    Type VARCHAR(50),
    Amount DECIMAL(12,2),
    DueDate DATE,
    Status VARCHAR(20),
    FOREIGN KEY (StudentId) REFERENCES Student(Id),
    FOREIGN KEY (StaffId) REFERENCES Staff(Id)
);
