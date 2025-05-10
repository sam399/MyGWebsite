<?php
require 'includes/auth-check.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_id = (int)$_POST['game_id'];
    $user_id = $_SESSION['user_id'];
    $rating = min(5, max(1, (int)$_POST['rating'])); // Ensure rating is between 1-5
    $comment = trim($_POST['comment']);

    // Check if user already reviewed this game
    $check = $conn->prepare("SELECT 1 FROM Review WHERE user_id = ? AND game_id = ?");
    $check->bind_param("ii", $user_id, $game_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        // Update existing review
        $stmt = $conn->prepare("
            UPDATE Review 
            SET rating = ?, comment_text = ?, timestamp = CURRENT_TIMESTAMP 
            WHERE user_id = ? AND game_id = ?
        ");
        $stmt->bind_param("isii", $rating, $comment, $user_id, $game_id);
    } else {
        // Insert new review
        $stmt = $conn->prepare("
            INSERT INTO Review (user_id, game_id, rating, comment_text) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $user_id, $game_id, $rating, $comment);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Review submitted successfully!";
    } else {
        $_SESSION['error'] = "Failed to submit review. Please try again.";
    }

    header("Location: game.php?id=$game_id");
    exit;
}

// If not POST request, redirect to home
header("Location: index.php");
exit;
?>