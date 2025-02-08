<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $response = array('status' => 'error', 'message' => '');
    
    try {
        // Validate input
        if (empty($username) || empty($password)) {
            $response['message'] = 'Please fill in all fields';
            echo json_encode($response);
            exit;
        }
        
        // Check user credentials
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // if ($user && password_verify($password, $user['password'])) {
            if ($user && $password) {

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Return success and redirect URL based on role
            $redirect_url = '';
            switch ($user['role']) {
                case 'admin':
                    $redirect_url = './admin/';
                    break;
                case 'manager':
                    $redirect_url = './manager/';
                    break;
                case 'staff':
                    $redirect_url = './staff/';
                    break;
                default:
                    $redirect_url = './index.php';
            }
            
            $response['status'] = 'success';
            $response['redirect'] = $redirect_url;
        } else {
            $response['message'] = 'Invalid username or password';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Login failed. Please try again later.';
        error_log("Login error: " . $e->getMessage());
    }
    
    echo json_encode($response);
    exit;
}
?>