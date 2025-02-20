<?php
// get_sale_order_details.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the response header to JSON
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'stan-inventory';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the sale_id from the query parameter
$saleId = $_GET['sale_id'] ?? null;

if (!$saleId) {
    echo json_encode(['error' => 'Sale ID is required.']);
    exit;
}

// Fetch order details from the sale_order table
try {
    $query = "
        SELECT so.id, so.quantity, p.name AS product_name
        FROM sale_order so
        JOIN products p ON so.product_id = p.id
        WHERE so.sales_id = :sale_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the orders as JSON
    echo json_encode($orders);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}