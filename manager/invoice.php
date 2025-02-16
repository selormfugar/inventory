<?php
require_once 'db.php'; // Ensure you include your database connection
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_sale':
                try {
                    $pdo->beginTransaction();

                    // Insert new invoice
                    $stmt = $pdo->prepare("INSERT INTO invoices (customer_name, customer_number, total_amount, status) 
                                           VALUES (?, ?, ?, 'unpaid')");
                    $stmt->execute([
                        sanitize_input($_POST['customer_name']),
                        sanitize_input($_POST['customer_number']),
                        (float)$_POST['total_amount']
                    ]);
                    $invoice_id = $pdo->lastInsertId();

                    // Prepare sales insert query
                    $stmt = $pdo->prepare("INSERT INTO sales (invoice_id, product_id, quantity, unit_price, created_at) 
                                           VALUES (?, ?, ?, ?, NOW())");

                    foreach ($_POST['products'] as $index => $product_id) {
                        if (!empty($product_id)) {
                            $stmt->execute([
                                $invoice_id,
                                (int)$product_id,
                                (int)$_POST['quantities'][$index],
                                (float)$_POST['prices'][$index]
                            ]);

                            // Reduce product stock
                            $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
                                ->execute([(int)$_POST['quantities'][$index], (int)$product_id]);
                        }
                    }

                    $pdo->commit();
                    echo json_encode(["status" => "success", "message" => "Sale recorded successfully!"]);
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
                }
                break;

            case 'update_status':
                try {
                    $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['status'],
                        (int)$_POST['invoice_id']
                    ]);
                    echo json_encode(["status" => "success", "message" => "Invoice status updated!"]);
                } catch (PDOException $e) {
                    echo json_encode(["status" => "error", "message" => "Error updating status: " . $e->getMessage()]);
                }
                break;
        }
    }
}
?>
