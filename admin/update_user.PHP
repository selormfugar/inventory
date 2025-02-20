<?php

header('Content-Type: application/json');
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

$response = ["status" => "error", "message" => "Something went wrong"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'], $_POST['username'], $_POST['role'], $_POST['email'])) {
        $user_id = trim($_POST['user_id']);
        $username = trim($_POST['username']);
        $role = trim($_POST['role']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        if (empty($user_id) || empty($username) || empty($role) || empty($email)) {
            echo json_encode(["status" => "error", "message" => "All required fields must be filled."]);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format."]);
            exit();
        }

        try {
            // Check if email already exists (excluding the current user)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_profiles WHERE email = :email AND user_id != :user_id");
            $stmt->execute([':email' => $email, ':user_id' => $user_id]);
            if ($stmt->fetchColumn()) {
                echo json_encode(["status" => "error", "message" => "Email already in use. Choose another."]);
                exit();
            }

            // Check if username already exists (excluding the current user)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND id != :user_id");
            $stmt->execute([':username' => $username, ':user_id' => $user_id]);
            if ($stmt->fetchColumn()) {
                echo json_encode(["status" => "error", "message" => "Username already taken. Choose another."]);
                exit();
            }

            // Begin transaction
            $pdo->beginTransaction();

            // Update users table
            $query = "UPDATE users SET username = :username, role = :role WHERE id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':role' => $role,
                ':user_id' => $user_id
            ]);

            // Update user_profiles table
            $query = "UPDATE user_profiles SET full_name = :full_name, email = :email, phone = :phone, address = :address 
                      WHERE user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':user_id' => $user_id
            ]);

            $pdo->commit();

            echo json_encode(["status" => "success", "message" => "User updated successfully!"]);
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
}
