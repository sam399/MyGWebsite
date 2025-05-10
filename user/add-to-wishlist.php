<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $game_id = (int)$_POST['game_id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO Wishlist (user_id, game_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $game_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Game added to wishlist!";
        header("Location: wishlist.php");
        exit;
    }
}

header("Location: /MyGWebsite/search.php");
exit;
