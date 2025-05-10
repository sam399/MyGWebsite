<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $game_id = (int)$_POST['game_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // First, check if the Cart table exists
        $tableExists = $conn->query("SHOW TABLES LIKE 'Cart'")->num_rows > 0;
        
        // Create Cart table if it doesn't exist
        if (!$tableExists) {
            $createTable = file_get_contents(__DIR__ . '/../database/create_cart_table.sql');
            $conn->multi_query($createTable);
            while ($conn->more_results() && $conn->next_result()); // Clear multi_query results
        }
        
        // Now try to insert/update cart
        $stmt = $conn->prepare("INSERT INTO Cart (user_id, game_id, quantity) VALUES (?, ?, 1) 
                              ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->bind_param("ii", $user_id, $game_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Game added to cart!";
        } else {
            $_SESSION['error_message'] = "Failed to add game to cart.";
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: cart.php");
    exit;
}

header("Location: ../index.php");
exit;
