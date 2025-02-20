<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();
// require_once 'includes/functions.php';
require_once 'includes/config.php';
require_once 'includes/db.php';

$response = ["status" => "error", "message" => "Something went wrong"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'], $_POST['role'], $_POST['email'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        if (empty($username) || empty($password) || empty($role) || empty($email)) {
            echo json_encode(["status" => "error", "message" => "All required fields must be filled."]);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format."]);
            exit();
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_profiles WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $emailExists = $stmt->fetchColumn();
        
            if ($emailExists) {
                echo json_encode(["status" => "error", "message" => "Email already exists. Please use a different email."]);
                exit();
            }
        
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $usernameExists = $stmt->fetchColumn();
        
            if ($usernameExists) {
                echo json_encode(["status" => "error", "message" => "Username already exists. Please choose another username."]);
                exit();
            }
        
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
            // Start transaction
            $pdo->beginTransaction();
        
            // Insert into users table
            $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password,
                ':role' => $role
            ]);
            $user_id = $pdo->lastInsertId();
        
            // Insert into user_profiles table
            $query = "INSERT INTO user_profiles (user_id, full_name, email, phone, address) 
                      VALUES (:user_id, :full_name, :email, :phone, :address)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
            ]);
        
            $pdo->commit();
        
            echo json_encode(["status" => "success", "message" => "User added successfully!"]);
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit();
        }
        
    }
}
