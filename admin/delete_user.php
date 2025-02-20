<?php

header('Content-Type: application/json');
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

$response = ["status" => "error", "message" => "Something went wrong"];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = trim($_POST['user_id']);

    if (empty($user_id)) {
        echo json_encode(["status" => "error", "message" => "Invalid user ID."]);
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Delete from user_profiles first due to foreign key constraint
        $query = "DELETE FROM user_profiles WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        // Delete from users table
        $query = "DELETE FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        $pdo->commit();

        echo json_encode(["status" => "success", "message" => "User deleted successfully!"]);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit();
    }
}
