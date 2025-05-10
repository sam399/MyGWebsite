<?php
require 'includes/auth-check.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $game_id = (int)$_POST['game_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if already in cart
    $stmt = $conn->prepare("SELECT quantity FROM Cart WHERE user_id = ? AND game_id = ?");
    $stmt->bind_param("ii", $user_id, $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $conn->query("UPDATE Cart SET quantity = quantity + 1 
                     WHERE user_id = $user_id AND game_id = $game_id");
    } else {
        // Add new item
        $conn->query("INSERT INTO Cart (user_id, game_id, quantity) 
                     VALUES ($user_id, $game_id, 1)");
    }
    
    header("Location: user/cart.php");
    exit;
}
