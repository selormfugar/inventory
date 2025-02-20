<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $contact_details = trim($_POST['contact_details']);

    if (!empty($name) && !empty($email) && !empty($phone)&& !empty($address)  && !empty($contact_details)) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, email, phone,address, contact_details) VALUES (?, ?, ?,?,?)");
        if ($stmt->execute([$name, $email, $phone, $address, $contact_details])) {
            header("Location: suppliers.php?success=1");
            exit();
        } else {
            echo "<p style='color: red; text-align: center;'>Failed to save supplier.</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>All fields are required.</p>";
    }
} else {
    header("Location: add_supplier.php");
    exit();
}
?>
