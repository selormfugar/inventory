<?php

header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'includes/db.php';

$response = ["status" => "error", "message" => "Something went wrong"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'], $_POST['role'], $_POST['email'], $_POST['full_name'], $_POST['phone'], $_POST['address'])) {
        $userId = trim($_POST['user_id']);
        $role = trim($_POST['role']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $password = isset($_POST['password']) ? trim($_POST['password']) : "";

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format."]);
            exit();
        }

        try {
            // Check if email already exists for another user
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_profiles WHERE email = :email AND user_id != :user_id");
            $stmt->execute([':email' => $email, ':user_id' => $userId]);
            $emailExists = $stmt->fetchColumn();

            if ($emailExists) {
                echo json_encode(["status" => "error", "message" => "Email already exists. Choose another."]);
                exit();
            }

            // Start transaction
            $pdo->beginTransaction();

            // Update users table (only update password if provided)
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET role = :role, password = :password WHERE id = :user_id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([':role' => $role, ':password' => $hashed_password, ':user_id' => $userId]);
            } else {
                $query = "UPDATE users SET role = :role WHERE id = :user_id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([':role' => $role, ':user_id' => $userId]);
            }

            // Update user_profiles table
            $query = "UPDATE user_profiles SET full_name = :full_name, email = :email, phone = :phone, address = :address WHERE user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':user_id' => $userId
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

?>
