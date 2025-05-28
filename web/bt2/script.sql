-- Drop tables if they already exist (for reset/testing purposes)
DROP TABLE IF EXISTS ProductInventory;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Inventory;

CREATE TABLE Inventory (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    capacity INT(10) NOT NULL,
    phone VARCHAR(10),
    create_at DATE
);

CREATE TABLE Product (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    price FLOAT(10) NOT NULL
);

CREATE TABLE ProductInventory (
    InventoryId VARCHAR(20),
    ProductId VARCHAR(20),
    amount INT(10) NOT NULL,
    PRIMARY KEY (InventoryId, ProductId),
    FOREIGN KEY (InventoryId) REFERENCES Inventory(id) ON DELETE CASCADE,
    FOREIGN KEY (ProductId) REFERENCES Product(id) ON DELETE CASCADE
);

CREATE TABLE Employee (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    InventoryId VARCHAR(20),
    FOREIGN KEY (InventoryId) REFERENCES Inventory(id) ON DELETE SET NULL
);

INSERT INTO Inventory (id, name, address, capacity, phone, create_at)
VALUES 
('KHO001', 'Kho Chính', 'Hà Nội', 10000, '0123456789', '2023-01-01'),
('KHO002', 'Kho Phụ', 'Hồ Chí Minh', 5000, '0987654321', '2023-02-15');

INSERT INTO Employee (id, name, username, password, role, InventoryId)
VALUES
('ND001', 'Nguyễn Thị Ánh', 'anhnguyen', 'matkhau123', 'quanly', 'KHO001'),
('ND002', 'Trần Văn Bảo', 'baotran', 'baomat123', 'nhanvien', 'KHO001'),
('ND003', 'Lê Hoàng', 'hoangle', 'hoangle2024', 'nhanvien', 'KHO002');

INSERT INTO Product (id, name, image, category, price)
VALUES
('SP001', 'Laptop', 'Điện tử', 899.99),
('SP002', 'Ghế văn phòng', 'Nội thất', 149.50),
('SP003', 'Bóng đèn LED', 'Chiếu sáng', 3.75),
('SP004', 'Điện thoại thông minh', 'Điện tử', 699.00),
('SP005', 'Bàn làm việc', 'Nội thất', 249.99),
('SP006', 'Chuột không dây', 'Điện tử', 25.49),
('SP007', 'Kệ sách', 'Nội thất', 120.00),
('SP008', 'Máy lạnh', 'Thiết bị điện', 399.99),
('SP009', 'Máy pha cà phê', 'Thiết bị điện', 89.95),
('SP010', 'Quạt trần', 'Chiếu sáng', 59.99);

INSERT INTO ProductInventory (InventoryId, ProductId, amount)
VALUES
('KHO001', 'SP001', 50),
('KHO001', 'SP002', 200),
('KHO001', 'SP003', 1000),
('KHO001', 'SP004', 80),
('KHO001', 'SP005', 40),
('KHO002', 'SP006', 150),
('KHO002', 'SP007', 60),
('KHO002', 'SP008', 25),
('KHO002', 'SP009', 90),
('KHO002', 'SP010', 70);