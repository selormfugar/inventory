<?php
header('Content-Type: application/json');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_sale':
                handleSaleSubmission($pdo);
                break;
            case 'add_invoice':
                handleInvoiceSubmission($pdo);
                break;
        }
    }
}

function handleSaleSubmission($pdo) {
    try {
        // Validate required fields
        validateRequiredFields(['customer_name', 'customer_number', 'total_amount', 'products', 'quantities', 'prices']);
        
        $pdo->beginTransaction();

        // Insert into sales table with input sanitization
        $stmt = $pdo->prepare("INSERT INTO sales (customer_name, customer_number, invoice_date, total_amount) 
                              VALUES (:customer_name, :customer_number, NOW(), :total_amount)");
        
        $stmt->execute([
            ':customer_name' => htmlspecialchars(trim($_POST['customer_name'])),
            ':customer_number' => htmlspecialchars(trim($_POST['customer_number'])),
            ':total_amount' => (float)$_POST['total_amount']
        ]);
        
        $sales_id = $pdo->lastInsertId();
        
        // Insert into sale_order table
        $stmt = $pdo->prepare("INSERT INTO sale_order (sales_id, product_id, quantity, unit_price, total_price, created_at) 
                              VALUES (:sales_id, :product_id, :quantity, :unit_price, :total_price, NOW())");
        
        foreach ($_POST['products'] as $index => $product_id) {
            if (!empty($product_id)) {
                // Validate product exists and stock is sufficient
                validateProduct($pdo, $product_id, $_POST['quantities'][$index]);
                
                $quantity = (int)$_POST['quantities'][$index];
                $unit_price = (float)$_POST['prices'][$index];
                $total_price = $quantity * $unit_price;
                
                $stmt->execute([
                    ':sales_id' => $sales_id,
                    ':product_id' => $product_id,
                    ':quantity' => $quantity,
                    ':unit_price' => $unit_price,
                    ':total_price' => $total_price
                ]);

                // Update product stock
                updateProductStock($pdo, $product_id, $quantity);
            }
        }
        
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Purchase successful."]);
        exit;
        
        
    } catch (Exception $e) {
      $pdo->rollBack();
echo json_encode(["status" => "error", "message" => $e->getMessage()]);
exit;

    }
}



function handleInvoiceSubmission($pdo) {
  try {
      validateRequiredFields(['customer_name', 'customer_number', 'total_amount', 'products', 'quantities', 'prices']);
      
      $pdo->beginTransaction();
      
      $stmt = $pdo->prepare("INSERT INTO invoices (customer_name, customer_number, invoice_date, total_amount) 
                            VALUES (:customer_name, :customer_number, NOW(), :total_amount)");
      
      $stmt->execute([
          ':customer_name' => htmlspecialchars(trim($_POST['customer_name'])),
          ':customer_number' => htmlspecialchars(trim($_POST['customer_number'])),
          ':total_amount' => (float)$_POST['total_amount']
      ]);
      
      $invoice_id = $pdo->lastInsertId();
      if (!$invoice_id) {
          throw new Exception("Failed to insert invoice.");
      }
      
      $stmt = $pdo->prepare("INSERT INTO new_invoice_order (invoice_id, product_id, quantity, unit_price, total_price, created_at) 
                            VALUES (:invoice_id, :product_id, :quantity, :unit_price, :total_price, NOW())");
      
      foreach ($_POST['products'] as $index => $product_id) {
          if (!empty($product_id)) {
              validateProduct($pdo, $product_id, $_POST['quantities'][$index]);
              
              $quantity = (int)$_POST['quantities'][$index];
              $unit_price = (float)$_POST['prices'][$index];
              $total_price = $quantity * $unit_price;
              
              $stmt->execute([
                  ':invoice_id' => $invoice_id,
                  ':product_id' => $product_id,
                  ':quantity' => $quantity,
                  ':unit_price' => $unit_price,
                  ':total_price' => $total_price
              ]);
              
              if ($stmt->errorCode() !== '00000') {
                  throw new Exception("Error inserting invoice order: " . implode(' ', $stmt->errorInfo()));
              }
          }
      }
      
      $pdo->commit();
      echo "Invoice submission successful.";
  } catch (Exception $e) {
      $pdo->rollBack();
      echo "Error: " . $e->getMessage();
  }
}

function validateRequiredFields($fields) {
    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: " . $field);
        }
    }
}

function validateProduct($pdo, $product_id, $requested_quantity) {
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception("Product not found");
    }
    
    if ($product['stock'] < $requested_quantity) {
        throw new Exception("Insufficient stock for product ID: " . $product_id);
    }
}

function updateProductStock($pdo, $product_id, $quantity) {
    $stmt = $pdo->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
    $stmt->execute([
        ':quantity' => $quantity,
        ':product_id' => $product_id
    ]);
}