<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /MyGWebsite/auth/login.php");
    exit;
}

require __DIR__ . '/auth-functions.php';

// Redirect to login if not authenticated
redirectIfNotLoggedIn();

// CSRF protection for forms
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>