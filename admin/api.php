<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}


try {
    if (!isset($_POST['action'])) {
        throw new Exception('No action specified');
    }

    switch ($_POST['action']) {
        case 'add_sale':
            handleSaleSubmission($pdo);
            break;
        case 'add_invoice':
            handleInvoiceSubmission($pdo);
            break;
        case 'print_sale':
            handlePrintSale($pdo);
            break;
        case 'print_invoice':
            handlePrintInvoice($pdo);
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

function handleSaleSubmission($pdo) {
    try {
        validateRequiredFields(['customer_name', 'customer_number', 'total_amount', 'interest','products', 'quantities', 'prices']);

        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not authenticated");
        }
        $user_id = $_SESSION['user_id'];

        $pdo->beginTransaction();

        // Insert into sales table with user_id
        $stmt = $pdo->prepare("INSERT INTO sales (customer_name, customer_number, interest, invoice_date, total_amount, user_id) 
                              VALUES (:customer_name, :customer_number,:interest, NOW(), :total_amount, :user_id)");
        
        $stmt->execute([
            ':customer_name' => htmlspecialchars(trim($_POST['customer_name'])),
            ':customer_number' => htmlspecialchars(trim($_POST['customer_number'])),
            ':total_amount' => (float)$_POST['total_amount'],
            ':user_id' => $user_id,
            ':interest' => $_POST['interest']
        ]);
        
        $sales_id = $pdo->lastInsertId();
        if (!$sales_id) {
            throw new Exception("Failed to create sale record.");
        }

        // Insert into sale_order table
        $stmt = $pdo->prepare("INSERT INTO sale_order (sales_id, product_id, quantity, interest, unit_price, total_price, created_at, user_id) 
                              VALUES (:sales_id, :product_id, :quantity, :unit_price,:interest,:total_price, NOW(), :user_id)");

        foreach ($_POST['products'] as $index => $product_id) {
            if (!empty($product_id)) {
                validateProduct($pdo, $product_id, $_POST['quantities'][$index]);

                $quantity = (int)$_POST['quantities'][$index];
                $unit_price = (float)$_POST['prices'][$index];
                $interest = $_POST['interest'];
                $total_price = ($quantity * $unit_price);
                if ($index == 0) { // Add interest only once (on the first product)
                    $total_price += $interest;
                }
                

                $stmt->execute([
                    ':sales_id' => $sales_id,
                    ':product_id' => $product_id,
                    ':quantity' => $quantity,
                    ':interest' => $interest,
                    ':unit_price' => $unit_price,
                    ':total_price' => $total_price,
                    ':user_id' => $user_id
                ]);

                // Update product stock & log stock movement
                updateProductStock($pdo, $product_id, $quantity, $user_id);
            }
        }

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Sale recorded successfully.","id"=>$sales_id]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        header('Content-Type: application/json');
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
            throw new Exception("Failed to create invoice record.");
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
            }
        }
        
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Invoice generated successfully.", "id" => $invoice_id]);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
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
  

  
function handlePrintSale($pdo) {
    try {
        validateRequiredFields(['sale_id']);

        $sale_id = (int)$_POST['sale_id'];

        // Fetch sale details
        $stmt = $pdo->prepare("
            SELECT s.*, so.product_id, p.name AS product_name, so.quantity, so.unit_price, so.total_price
            FROM sales s
            JOIN sale_order so ON s.id = so.sales_id
            JOIN products p ON so.product_id = p.id
            WHERE s.id = :sale_id
        ");
        $stmt->execute([':sale_id' => $sale_id]);
        $sale_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($sale_data)) {
            throw new Exception("Sale not found.");
        }

        // Organize data for printing
        $sale = [
            'id' => $sale_data[0]['id'],
            'customer_name' => $sale_data[0]['customer_name'],
            'customer_number' => $sale_data[0]['customer_number'],
            'invoice_date' => $sale_data[0]['invoice_date'],
            'total_amount' => $sale_data[0]['total_amount'],
            'products' => []
        ];

        foreach ($sale_data as $row) {
            $sale['products'][] = [
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'total_price' => $row['total_price']
            ];
        }

        // Return data for printing
        echo json_encode(["status" => "success", "data" => $sale]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
    }
}

function handlePrintInvoice($pdo) {
    try {
        validateRequiredFields(['invoice_id']);

        $invoice_id = (int)$_POST['invoice_id'];

        // Fetch invoice details
        $stmt = $pdo->prepare("
            SELECT i.*, nio.product_id, p.name AS product_name, nio.quantity, nio.unit_price, nio.total_price, i.total_amount as total
            FROM invoices i
            JOIN new_invoice_order nio ON i.id = nio.invoice_id
            JOIN products p ON nio.product_id = p.id
            WHERE i.id = :invoice_id
        ");
        $stmt->execute([':invoice_id' => $invoice_id]);
        $invoice_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($invoice_data)) {
            throw new Exception("Invoice not found.");
        }

        // Organize data for printing
        $invoice = [
            'id' => $invoice_data[0]['id'],
            'customer_name' => $invoice_data[0]['customer_name'],
            'customer_number' => $invoice_data[0]['customer_number'],
            'invoice_date' => $invoice_data[0]['invoice_date'],
            'total_amount' => $invoice_data[0]['total_amount'],
            'total' => $invoice_data[0]['total'],
            'products' => []
        ];

        foreach ($invoice_data as $row) {
            $invoice['products'][] = [
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'total_price' => $row['total_price']
            ];
        }

        // Return data for printing
        echo json_encode(["status" => "success", "data" => $invoice]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
    }
}