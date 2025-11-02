
-- Drop all foreign key constraints in the database
DECLARE @sql NVARCHAR(MAX) = N'';
SELECT @sql += 'ALTER TABLE [' + OBJECT_SCHEMA_NAME(parent_object_id) + '].[' 
    + OBJECT_NAME(parent_object_id) + '] DROP CONSTRAINT [' + name + '];' + CHAR(13)
FROM sys.foreign_keys;
EXEC sp_executesql @sql;
GO


-- Drop tables if exist
IF OBJECT_ID('tblItemOrder', 'U') IS NOT NULL DROP TABLE tblItemOrder;
IF OBJECT_ID('tblImportItem', 'U') IS NOT NULL DROP TABLE tblImportItem;
IF OBJECT_ID('tblInvoice', 'U') IS NOT NULL DROP TABLE tblInvoice;
IF OBJECT_ID('tblOnlineOrder', 'U') IS NOT NULL DROP TABLE tblOnlineOrder;
IF OBJECT_ID('tblDirectOrder', 'U') IS NOT NULL DROP TABLE tblDirectOrder;
IF OBJECT_ID('tblOrder', 'U') IS NOT NULL DROP TABLE tblOrder;
IF OBJECT_ID('tblImportInvoice', 'U') IS NOT NULL DROP TABLE tblImportInvoice;
IF OBJECT_ID('tblSupplier', 'U') IS NOT NULL DROP TABLE tblSupplier;
IF OBJECT_ID('tblStaff', 'U') IS NOT NULL DROP TABLE tblStaff;
IF OBJECT_ID('tblCustomer', 'U') IS NOT NULL DROP TABLE tblCustomer;
IF OBJECT_ID('tblUser', 'U') IS NOT NULL DROP TABLE tblUser;
IF OBJECT_ID('tblItem', 'U') IS NOT NULL DROP TABLE tblItem;
GO


-- =======================
-- Table: tblUser
-- =======================
CREATE TABLE tblUser (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    email VARCHAR(255),
    phoneNumber VARCHAR(20),
    role VARCHAR(50)
);
GO

-- =======================
-- Table: tblCustomer
-- =======================
CREATE TABLE tblCustomer (
    customerId INT IDENTITY(1,1) PRIMARY KEY,
    tblUserId INT,
    FOREIGN KEY (tblUserId) REFERENCES tblUser(id)
);
GO

-- =======================
-- Table: tblStaff
-- =======================
CREATE TABLE tblStaff (
    staffId INT PRIMARY KEY,
    tblUserId INT NOT NULL UNIQUE,
    FOREIGN KEY (tblUserId) REFERENCES tblUser(id)
);
GO

-- =======================
-- Table: tblSupplier
-- =======================
CREATE TABLE tblSupplier (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(20),
    email VARCHAR(255)
);
GO

-- =======================
-- Table: tblImportInvoice
-- =======================
CREATE TABLE tblImportInvoice (
    id INT IDENTITY(1,1) PRIMARY KEY,
    date DATE,
    tblStaffStaffId INT NOT NULL,
    tblSupplierId INT NOT NULL,
    FOREIGN KEY (tblStaffStaffId) REFERENCES tblStaff(staffId),
    FOREIGN KEY (tblSupplierId) REFERENCES tblSupplier(id)
);
GO

-- =======================
-- Table: tblOrder
-- =======================
CREATE TABLE tblOrder (
    id INT IDENTITY(1,1) PRIMARY KEY,
    createDate DATE,
    status VARCHAR(50),
    tblCustomerCustomerId INT NOT NULL,
    FOREIGN KEY (tblCustomerCustomerId) REFERENCES tblCustomer(customerId)
);
GO

-- =======================
-- Table: tblInvoice
-- =======================
CREATE TABLE tblInvoice (
    id INT IDENTITY(1,1) PRIMARY KEY,
    billingAddress VARCHAR(255),
    discount FLOAT,
    paymentMethod VARCHAR(255),
    tblOrderId INT NOT NULL,
	payDate DATE NOT NULL,
    FOREIGN KEY (tblOrderId) REFERENCES tblOrder(id)
);
GO

-- =======================
-- Table: tblDirectOrder
-- =======================
CREATE TABLE tblDirectOrder (
    tblOrderId INT PRIMARY KEY,
    tblStaffStaffId INT NOT NULL,
    deliverFee FLOAT,
    FOREIGN KEY (tblOrderId) REFERENCES tblOrder(id),
    FOREIGN KEY (tblStaffStaffId) REFERENCES tblStaff(staffId)
);
GO

-- =======================
-- Table: tblOnlineOrder
-- =======================
CREATE TABLE tblOnlineOrder (
    tblOrderId INT PRIMARY KEY,
    tblStaffStaffId INT,
    deliverAddress VARCHAR(255),
    deliverFee FLOAT,
    FOREIGN KEY (tblOrderId) REFERENCES tblOrder(id),
    FOREIGN KEY (tblStaffStaffId) REFERENCES tblStaff(staffId)
);
GO

-- =======================
-- Table: tblItem
-- =======================
CREATE TABLE tblItem (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price FLOAT NOT NULL,
    description VARCHAR(255)
);
GO

-- =======================
-- Table: tblItemOrder
-- =======================
CREATE TABLE tblItemOrder (
    tblOrderId INT NOT NULL,
    tblItemId INT NOT NULL,
    quantity INT,
    price FLOAT,
    PRIMARY KEY (tblOrderId, tblItemId),
    FOREIGN KEY (tblOrderId) REFERENCES tblOrder(id),
    FOREIGN KEY (tblItemId) REFERENCES tblItem(id)
);
GO

-- =======================
-- Table: tblImportItem
-- =======================
CREATE TABLE tblImportItem (
    tblImportInvoiceId INT NOT NULL,
    tblItemId INT NOT NULL,
    quantity INT,
    price FLOAT,
    PRIMARY KEY (tblImportInvoiceId, tblItemId),
    FOREIGN KEY (tblImportInvoiceId) REFERENCES tblImportInvoice(id),
    FOREIGN KEY (tblItemId) REFERENCES tblItem(id)
);
GO
