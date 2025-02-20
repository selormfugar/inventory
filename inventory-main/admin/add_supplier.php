<?php
session_start();
 // Database connection
 require_once 'includes/head.php';
 require_once 'includes/header.php';
 require_once 'includes/functions.php';
 require_once 'includes/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            display: block;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .cancel-btn {
            background-color: #ff4c4c;
            color: white;
        }
        .cancel-btn:hover {
            background-color: #e63c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Supplier</h2>
        <form action="save_supplier.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone" required>

            <label>Contact Details:</label>
            <textarea name="contact_details" rows="4" required></textarea>

            <label>Address:</label> 
            <textarea name="address" rows="4" required></textarea>
            
            <div class="btn-container">
                <button type="submit" class="btn submit-btn">Save</button>
                <a href="suppliers.php" class="btn cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
