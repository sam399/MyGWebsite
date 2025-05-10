<?php
require __DIR__ . '/../includes/admin-auth-check.php';

$game_id = $_GET['id'] ?? 'new';
$is_new = ($game_id === 'new');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = (float)$_POST['price'];
    $description = $_POST['description'] ?? '';
    $release_date = $_POST['release_date'];
    $developer = $_POST['developer'] ?? '';
    $esrb_id = !empty($_POST['esrb_id']) ? (int)$_POST['esrb_id'] : null;
    $platform_id = !empty($_POST['platform_id']) ? (int)$_POST['platform_id'] : null;
    $genre_id = !empty($_POST['genre_id']) ? (int)$_POST['genre_id'] : null;
    
    if ($is_new) {
        // Insert new game
        $stmt = $conn->prepare("
            INSERT INTO Game (
                title, price, description, release_date, developer, 
                esrb_id, platform_id, genre_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sdssssii", 
            $title, $price, $description, $release_date, $developer,
            $esrb_id, $platform_id, $genre_id
        );
        $stmt->execute();
        $game_id = $conn->insert_id;
    } else {
        // Update existing game
        $stmt = $conn->prepare("
            UPDATE Game SET 
                title = ?, price = ?, description = ?, release_date = ?, 
                developer = ?, esrb_id = ?, platform_id = ?, genre_id = ? 
            WHERE game_id = ?
        ");
        $stmt->bind_param(
            "sdsssiiii",
            $title, $price, $description, $release_date, 
            $developer, $esrb_id, $platform_id, $genre_id, $game_id
        );
        $stmt->execute();
    }
    
    // Handle image upload if provided
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['size'] > 0) {
        $upload_dir = __DIR__ . '/../assets/game-covers/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = 'game-' . $game_id . '-' . uniqid() . '.jpg';
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path)) {
            $web_path = '/MyGWebsite/assets/game-covers/' . $filename;
            $conn->query("UPDATE Game SET cover_image_url = '$web_path' WHERE game_id = $game_id");
        }
    }
    
    header("Location: games.php?success=1");
    exit;
}

// Fetch game data if editing
$game = $is_new ? [
    'title' => '',
    'price' => 0,
    'description' => '',
    'release_date' => date('Y-m-d'),
    'developer' => '',
    'esrb_id' => null,
    'platform_id' => null,
    'genre_id' => null
] : $conn->query("SELECT * FROM Game WHERE game_id = $game_id")->fetch_assoc();

// Fetch related data
$platforms = $conn->query("SELECT * FROM Platform ORDER BY name");
$genres = $conn->query("SELECT * FROM Genre ORDER BY name");
$esrb_ratings = $conn->query("SELECT * FROM ESRB_Ratings"); // Fixed table name
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $is_new ? 'Add New Game' : 'Edit Game' ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .checkbox-group {
            columns: 3;
        }
    </style>
</head>
<body>
    <!-- Add Navigation Header -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="bi bi-house-fill"></i> GameHub Home
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a href="../auth/logout.php" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container py-4">
        <div class="form-container">
            <h2 class="mb-4">
                <i class="bi bi-controller"></i> 
                <?= $is_new ? 'Add New Game' : 'Edit: ' . htmlspecialchars($game['title']) ?>
            </h2>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header">Basic Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Title*</label>
                            <input type="text" name="title" class="form-control" 
                                   value="<?= htmlspecialchars($game['title']) ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price*</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" step="0.01" min="0" 
                                           class="form-control" value="<?= $game['price'] ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Release Date</label>
                                <input type="date" name="release_date" class="form-control" 
                                       value="<?= $game['release_date'] ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ESRB Rating</label>
                            <select name="esrb_id" class="form-select">
                                <option value="">-- Select Rating --</option>
                                <?php while ($rating = $esrb_ratings->fetch_assoc()): ?>
                                <option value="<?= $rating['esrb_id'] ?>" 
                                    <?= $rating['esrb_id'] == $game['esrb_id'] ? 'selected' : '' ?>>
                                    <?= $rating['code'] ?> - <?= $rating['description'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5"><?= 
                                htmlspecialchars($game['description']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Developer</label>
                            <input type="text" name="developer" class="form-control" 
                                   value="<?= htmlspecialchars($game['developer']) ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Platforms -->
                <div class="card mb-4">
                    <div class="card-header">Platform</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Platform</label>
                            <select name="platform_id" class="form-select" required>
                                <option value="">-- Select Platform --</option>
                                <?php while ($platform = $platforms->fetch_assoc()): ?>
                                <option value="<?= $platform['platform_id'] ?>" 
                                    <?= $platform['platform_id'] == $game['platform_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($platform['name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Genres -->
                <div class="card mb-4">
                    <div class="card-header">Genre</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Genre</label>
                            <select name="genre_id" class="form-select" required>
                                <option value="">-- Select Genre --</option>
                                <?php while ($genre = $genres->fetch_assoc()): ?>
                                <option value="<?= $genre['genre_id'] ?>" 
                                    <?= $genre['genre_id'] == $game['genre_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($genre['name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Cover Image -->
                <div class="card mb-4">
                    <div class="card-header">Cover Image</div>
                    <div class="card-body">
                        <?php if (!$is_new && !empty($game['cover_image_url'])): ?>
                        <div class="mb-3">
                            <img src="<?= $game['cover_image_url'] ?>" 
                                 style="max-height: 200px;" class="img-thumbnail">
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload New Cover</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 600x800px</small>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="games.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Game
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>