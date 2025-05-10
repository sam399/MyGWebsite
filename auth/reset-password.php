<?php
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Generate token (valid for 1 hour)
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600);
    
    $stmt = $conn->prepare("UPDATE User SET reset_token = ?, token_expires = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expires, $email);
    $stmt->execute();
    
    // Send email (pseudo-code)
    $reset_link = "https://yourdomain.com/auth/reset-password.php?token=$token";
    
    
    $message = "If that email exists, we've sent a reset link";
}
?>

