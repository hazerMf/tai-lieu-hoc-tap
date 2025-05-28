<?php
require_once 'db_connect.php';

// User authentication functions
function isLoggedIn() {
    return isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function authenticateUser($username, $password) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT id, name, username, role FROM Employee WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Product functions
function getAllProducts() {
    global $mysqli;
    $result = $mysqli->query("SELECT p.*, COALESCE(SUM(pi.amount), 0) as stock 
                             FROM Product p 
                             LEFT JOIN ProductInventory pi ON p.id = pi.ProductId 
                             GROUP BY p.id");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getProductById($id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM Product WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addProduct($id, $name, $image, $category, $price, $quantity = null, $warehouseId = null) {
    global $mysqli;
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // Insert product
        $stmt = $mysqli->prepare("INSERT INTO Product (id, name, image, category, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $id, $name, $image, $category, $price);
        $stmt->execute();
        
        // If quantity and warehouse are provided, add initial inventory
        if ($quantity !== null && $warehouseId !== null && $quantity > 0) {
            $stmt = $mysqli->prepare("INSERT INTO ProductInventory (ProductId, InventoryId, amount) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $id, $warehouseId, $quantity);
            $stmt->execute();
        }
        
        // Commit transaction
        $mysqli->commit();
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $mysqli->rollback();
        return false;
    }
}

function updateProduct($id, $name, $image, $category, $price, $quantity = null, $warehouseId = null) {
    global $mysqli;
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // Update product details
        $stmt = $mysqli->prepare("UPDATE Product SET name = ?, image = ?, category = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sssds", $name, $image, $category, $price, $id);
        $stmt->execute();
        
        // If quantity and warehouse are provided, update inventory
        if ($quantity !== null && $warehouseId !== null) {
            // Check if inventory record exists
            $stmt = $mysqli->prepare("SELECT amount FROM ProductInventory WHERE ProductId = ? AND InventoryId = ?");
            $stmt->bind_param("ss", $id, $warehouseId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update existing inventory
                $stmt = $mysqli->prepare("UPDATE ProductInventory SET amount = ? WHERE ProductId = ? AND InventoryId = ?");
                $stmt->bind_param("iss", $quantity, $id, $warehouseId);
            } else {
                // Insert new inventory record
                $stmt = $mysqli->prepare("INSERT INTO ProductInventory (ProductId, InventoryId, amount) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $id, $warehouseId, $quantity);
            }
            $stmt->execute();
        }
        
        // Commit transaction
        $mysqli->commit();
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $mysqli->rollback();
        return false;
    }
}

function deleteProduct($id) {
    global $mysqli;
    // First delete from ProductInventory
    $stmt = $mysqli->prepare("DELETE FROM ProductInventory WHERE ProductId = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    
    // Then delete from Product
    $stmt = $mysqli->prepare("DELETE FROM Product WHERE id = ?");
    $stmt->bind_param("s", $id);
    return $stmt->execute();
}

// Warehouse functions
function getAllWarehouses() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM Inventory");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getWarehouseById($id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM Inventory WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addWarehouse($id, $name, $address, $capacity, $phone) {
    global $mysqli;
    $create_at = date('Y-m-d');
    $stmt = $mysqli->prepare("INSERT INTO Inventory (id, name, address, capacity, phone, create_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $id, $name, $address, $capacity, $phone, $create_at);
    return $stmt->execute();
}

function updateWarehouse($id, $name, $address, $capacity, $phone) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE Inventory SET name = ?, address = ?, capacity = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssiss", $name, $address, $capacity, $phone, $id);
    return $stmt->execute();
}

function deleteWarehouse($id) {
    global $mysqli;
    // First delete from ProductInventory
    $stmt = $mysqli->prepare("DELETE FROM ProductInventory WHERE InventoryId = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    
    // Then delete from Inventory
    $stmt = $mysqli->prepare("DELETE FROM Inventory WHERE id = ?");
    $stmt->bind_param("s", $id);
    return $stmt->execute();
}

// Inventory functions
function getProductInventory($productId) {
    global $mysqli;
    $stmt = $mysqli->prepare("
        SELECT pi.*, i.name as warehouse_name 
        FROM ProductInventory pi 
        JOIN Inventory i ON pi.InventoryId = i.id 
        WHERE pi.ProductId = ?
    ");
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Statistics functions
function getTotalProducts() {
    global $mysqli;
    $result = $mysqli->query("SELECT COUNT(DISTINCT id) as total FROM Product");
    return $result->fetch_assoc()['total'];
}

function getTotalWarehouses() {
    global $mysqli;
    $result = $mysqli->query("SELECT COUNT(DISTINCT id) as total FROM Inventory");
    return $result->fetch_assoc()['total'];
}

function getTotalInventory() {
    global $mysqli;
    $result = $mysqli->query("SELECT COALESCE(SUM(amount), 0) as total FROM ProductInventory");
    return $result->fetch_assoc()['total'];
}

function getProductsByCategory() {
    global $mysqli;
    $result = $mysqli->query("
        SELECT category, COUNT(DISTINCT id) as count 
        FROM Product 
        GROUP BY category
    ");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTopProducts() {
    global $mysqli;
    $result = $mysqli->query("
        SELECT p.id, p.name, p.category, p.price, COALESCE(SUM(pi.amount), 0) as total 
        FROM Product p 
        LEFT JOIN ProductInventory pi ON p.id = pi.ProductId 
        GROUP BY p.id 
        ORDER BY total DESC 
        LIMIT 5
    ");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getItemsByInventory($inventoryId) {
    global $mysqli;
    $stmt = $mysqli->prepare("
        SELECT p.id, p.name, p.category, COALESCE(pi.amount, 0) as quantity
        FROM Product p
        LEFT JOIN ProductInventory pi ON p.id = pi.ProductId AND pi.InventoryId = ?
        ORDER BY p.name
    ");
    $stmt->bind_param("s", $inventoryId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?> 