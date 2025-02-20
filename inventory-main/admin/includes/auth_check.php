<?php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

function checkRole($required_role) {
    checkAuth();
    if ($_SESSION['role'] !== $required_role) {
        header('Location: ../index.php');
        exit();
    }
}

function isAuthorized($allowed_roles) {
    checkAuth();
    return in_array($_SESSION['role'], $allowed_roles);
}
?>