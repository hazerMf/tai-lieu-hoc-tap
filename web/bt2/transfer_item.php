<?php
require_once 'functions.php';
session_start();

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];
    $fromInventory = $_POST['from_inventory'];
    $toInventory = $_POST['to_inventory'];
    $quantity = intval($_POST['quantity']);

    global $mysqli;

    if ($toInventory === 'retailer') {
        // Subtract quantity from current inventory
        $stmt = $mysqli->prepare("UPDATE ProductInventory SET amount = amount - ? WHERE ProductId = ? AND InventoryId = ?");
        $stmt->bind_param("iss", $quantity, $itemId, $fromInventory);
        $stmt->execute();
    } else {
        // Move quantity to another inventory
        // Subtract from current
        $stmt = $mysqli->prepare("UPDATE ProductInventory SET amount = amount - ? WHERE ProductId = ? AND InventoryId = ?");
        $stmt->bind_param("iss", $quantity, $itemId, $fromInventory);
        $stmt->execute();

        // Add to destination (insert if not exists)
        $stmt = $mysqli->prepare("SELECT amount FROM ProductInventory WHERE ProductId = ? AND InventoryId = ?");
        $stmt->bind_param("ss", $itemId, $toInventory);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt = $mysqli->prepare("UPDATE ProductInventory SET amount = amount + ? WHERE ProductId = ? AND InventoryId = ?");
            $stmt->bind_param("iss", $quantity, $itemId, $toInventory);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO ProductInventory (ProductId, InventoryId, amount) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $itemId, $toInventory, $quantity);
        }
        $stmt->execute();
    }

    header('Location: inventory_items.php?inventory=' . urlencode($fromInventory));
    exit;
}
?>