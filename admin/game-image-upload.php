<?php
require __DIR__ . '/../includes/admin-auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cover_image'])) {
    $game_id = (int)$_POST['game_id'];
    $upload_dir = __DIR__ . '/../assets/game-covers/';
    
    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = 'game-' . $game_id . '-' . uniqid() . '.jpg';
    $target_path = $upload_dir . $filename;
    
    // Validate and move file
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($_FILES['cover_image']['type'], $allowed_types)) {
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path)) {
            // Update database with relative path
            $web_path = '/assets/game-covers/' . $filename;
            $conn->query("UPDATE Game SET cover_image_url = '$web_path' WHERE game_id = $game_id");
            echo json_encode(['success' => true, 'path' => $web_path]);
        }
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit;
}