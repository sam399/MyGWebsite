<?php
require 'includes/auth-check.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $game_id = (int)$_POST['game_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if already in wishlist
    $stmt = $conn->prepare("SELECT 1 FROM Wishlist WHERE user_id = ? AND game_id = ?");
    $stmt->bind_param("ii", $user_id, $game_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        // Add to wishlist if not already there
        $conn->query("INSERT INTO Wishlist (user_id, game_id) 
                     VALUES ($user_id, $game_id)");
    }
    
    header("Location: user/wishlist.php");
    exit;
}
