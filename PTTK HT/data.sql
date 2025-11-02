-------------------------------------------------------------
-- 1️⃣  Users (linked 1-to-1 with Customers)
-------------------------------------------------------------
INSERT INTO tblUser (name, password, address, email, phoneNumber, role)
VALUES
('Nguyen Van A', '123', 'HCM', 'a@gmail.com', '090000001', 'customer'),
('Nguyen Van Anh', '123', 'HCM', 'b@gmail.com', '090000002', 'customer'),
('Tran Thi B', '123', 'HCM', 'c@gmail.com', '090000003', 'customer');

-------------------------------------------------------------
-- 2️⃣  Customers (use fixed IDs per your spec)
-------------------------------------------------------------
-- Assume tblUser IDs generated as 1,2,3
INSERT INTO tblCustomer (tblUserId) VALUES (2);
INSERT INTO tblCustomer (tblUserId) VALUES (1);
INSERT INTO tblCustomer (tblUserId) VALUES (3);

-------------------------------------------------------------
-- 3️⃣  Items
-------------------------------------------------------------
INSERT INTO tblItem (name, price, description) VALUES
('Pepsi', 10000, 'Soft drink'),
('Kool-Aid', 15000, 'Fruit drink'),
('Watermelon', 47000, 'Fresh fruit');

-------------------------------------------------------------
-- 4️⃣  Orders for Nguyen Van A (customerId = 2)
-------------------------------------------------------------
INSERT INTO tblOrder (createDate, status, tblCustomerCustomerId)
VALUES 
('2025-09-11', 'Completed', 2),  -- 1st invoice (total 4,200,000)
('2025-09-12', 'Completed', 2),  -- 2nd invoice (total 800,000)
('2025-09-15', 'Completed', 2);  -- 3rd invoice (any date)

-------------------------------------------------------------
-- 5️⃣  Invoices for Nguyen Van A (1-to-1 with orders)
-------------------------------------------------------------
-- Assume tblOrder IDs auto 1,2,3
INSERT INTO tblInvoice (billingAddress, discount, paymentMethod, tblOrderId, payDate)
VALUES
('HCM', 0, 'Cash', 1, '2025-09-11'),
('HCM', 0, 'Cash', 2, '2025-09-11'),
('HCM', 0, 'Cash', 3, '2025-09-15');

-------------------------------------------------------------
-- 6️⃣  Items per order (tblItemOrder)
-------------------------------------------------------------
-- Order 1 (total 4,200,000)
INSERT INTO tblItemOrder (tblOrderId, tblItemId, quantity, price)
VALUES
(1, 1, 33, 10000),     -- Pepsi 330,000
(1, 2, 70, 15000),     -- Kool-Aid 1,050,000
(1, 3, 60, 47000);     -- Watermelon 2,820,000  → total ≈ 4,200,000

-- Order 2 (total 800,000)
INSERT INTO tblItemOrder (tblOrderId, tblItemId, quantity, price)
VALUES
(2, 1, 80, 10000);     -- 800,000

-- Order 3 (any data)
INSERT INTO tblItemOrder (tblOrderId, tblItemId, quantity, price)
VALUES
(3, 2, 10, 15000);     -- 150,000 (extra)

-------------------------------------------------------------
-- 7️⃣  Orders for Nguyen Van Anh (customerId = 1)
-------------------------------------------------------------
INSERT INTO tblOrder (createDate, status, tblCustomerCustomerId)
VALUES
('2025-09-11', 'Completed', 1),
('2025-09-12', 'Completed', 1);

INSERT INTO tblInvoice (billingAddress, discount, paymentMethod, tblOrderId, payDate)
VALUES
('HCM', 0, 'Card', 4, '2025-09-11'),
('HCM', 0, 'Card', 5, '2025-09-11');

-- Total revenue ≈ 3,500,000
INSERT INTO tblItemOrder (tblOrderId, tblItemId, quantity, price)
VALUES
(4, 3, 40, 47000),   -- 1,880,000
(5, 2, 108, 15000);  -- 1,620,000 → total 3,500,000

-------------------------------------------------------------
-- 8️⃣  Orders for Tran Thi B (customerId = 3)
-------------------------------------------------------------
INSERT INTO tblOrder (createDate, status, tblCustomerCustomerId)
VALUES ('2025-09-11', 'Completed', 3);

INSERT INTO tblInvoice (billingAddress, discount, paymentMethod, tblOrderId, payDate)
VALUES ('HCM', 0, 'Cash', 6, '2025-09-11');

-- Total 20,000
INSERT INTO tblItemOrder (tblOrderId, tblItemId, quantity, price)
VALUES (6, 1, 2, 10000);


SELECT * FROM tblInvoice WHERE payDate BETWEEN '2025-09-11' AND '2025-09-12';

SELECT c.customerId, u.name, SUM(o.quantity*o.price) AS totalRevenue
            FROM tblInvoice as i
			JOIN tblOrder as r on r.id = i.tblOrderId
            JOIN tblItemOrder as o on r.id = o.tblOrderId
            JOIN tblCustomer as c ON r.tblCustomerCustomerId = c.customerId
            JOIN tblUser as u ON c.customerId = u.id
            WHERE i.payDate BETWEEN '2025-09-11' AND '2025-09-12'
            GROUP BY c.customerId, u.name


