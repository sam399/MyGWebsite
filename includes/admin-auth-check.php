<?php
require __DIR__ . '/../config.php';
session_start();

// Redirect non-admins
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: /MyGWebsite/auth/login.php");
    exit;
}

// Verify CSRF token for forms
function verify_csrf() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
}
?>
