<?php
session_start();

/**
 * Check if a user is logged in
 * Redirect to user login page if not
 */
function checkUser() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // Use absolute path to prevent bypass
        header("Location: /user/login.php");
        exit();
    }
}

/**
 * Check if an admin is logged in
 * Redirect to admin login page if not
 */
function checkAdmin() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        // Use absolute path to prevent bypass
        header("Location: /admin/login.php");
        exit();
    }
}

/**
 * Optional: Helper to get logged-in user info
 */
function currentUser() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? null,
        ];
    }
    return null;
}

/**
 * Optional: Helper to get logged-in admin info
 */
function currentAdmin() {
    if (isset($_SESSION['admin_id'])) {
        return [
            'id' => $_SESSION['admin_id'],
            'email' => $_SESSION['admin_email'] ?? null,
        ];
    }
    return null;
}
?>