<?php
// Input sanitization
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Get all products with category and supplier names
function get_products($pdo) {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name, s.name as supplier_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         LEFT JOIN suppliers s ON p.supplier_id = s.id");
    return $stmt->fetchAll();
}

// Get all categories
function get_categories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll();
}

// Get all suppliers
function get_suppliers($pdo) {
    $stmt = $pdo->query("SELECT * FROM suppliers");
    return $stmt->fetchAll();
}

// Get all bills
function get_bills($pdo) {
    $stmt = $pdo->query("SELECT b.*, bc.name as category_name 
                         FROM bills b 
                         LEFT JOIN bill_categories bc ON b.category_id = bc.id");
    return $stmt->fetchAll();
}

// Get all users
function get_users($pdo) {
    $stmt = $pdo->query("SELECT * FROM users");
    return $stmt->fetchAll();
}

// Format currency
function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

// Format date
function format_date($date) {
    return date('Y-m-d', strtotime($date));
}

// Display alert message
function display_alert($message, $type = 'success') {
    return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

// Get user by ID (renamed from get_current_user)
function get_user_by_id($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// Check if user has admin role
function is_admin($user) {
    return isset($user['role']) && $user['role'] === 'admin';
}

// Generate random string
function generate_random_string($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 
            ceil($length/strlen($x)))), 1, $length);
}

// Get notifications
function get_notifications($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND status = 'unread' ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Add notification
function add_notification($pdo, $user_id, $type, $message) {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $type, $message]);
}

// Check low stock
function check_low_stock($pdo) {
    $stmt = $pdo->query("SELECT * FROM products WHERE stock < 10");
    return $stmt->fetchAll();
}

// Get sales statistics
function get_sales_stats($pdo) {
    $stats = [];
    
    // Total sales today
    $stmt = $pdo->query("SELECT SUM(total_price) FROM sales WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $stmt->fetchColumn() ?: 0;
    
    // Total sales this month
    $stmt = $pdo->query("SELECT SUM(total_price) FROM sales WHERE MONTH(created_at) = MONTH(CURDATE())");
    $stats['month'] = $stmt->fetchColumn() ?: 0;
    
    // Total sales this year
    $stmt = $pdo->query("SELECT SUM(total_price) FROM sales WHERE YEAR(created_at) = YEAR(CURDATE())");
    $stats['year'] = $stmt->fetchColumn() ?: 0;
    
    return $stats;
}

// Log activity
function log_activity($pdo, $user_id, $action, $details = '') {
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $action, $details]);
}

// Validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if SKU exists
function sku_exists($pdo, $sku, $exclude_id = null) {
    $sql = "SELECT COUNT(*) FROM products WHERE sku = ?";
    $params = [$sku];
    
    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
} 