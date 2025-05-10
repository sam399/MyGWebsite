<?php
session_start();
require 'config.php';

// Getting filter parameters
$platform = $_GET['platform'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Get platforms and genres for filters
$platforms = $conn->query("SELECT * FROM Platform ORDER BY name");
$genres = $conn->query("SELECT * FROM Genre ORDER BY name");

// Build base query
$query = "
    SELECT g.*, p.name as platform_name, gr.name as genre_name,
           AVG(r.rating) as avg_rating
    FROM Game g
    LEFT JOIN Platform p ON g.platform_id = p.platform_id
    LEFT JOIN Genre gr ON g.genre_id = gr.genre_id
    LEFT JOIN Review r ON g.game_id = r.game_id
    WHERE 1=1
";

if ($platform) {
    $query .= " AND g.platform_id = " . (int)$platform;
}
if ($genre) {
    $query .= " AND g.genre_id = " . (int)$genre;
}

$query .= " GROUP BY g.game_id";

switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY g.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY g.price DESC";
        break;
    case 'rating':
        $query .= " ORDER BY avg_rating DESC";
        break;
    default:
        $query .= " ORDER BY g.release_date DESC";
}

$games = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Games | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="browse-header">
        <div class="container">
            <h1><i class="bi bi-controller"></i> Game Library</h1>
            <p>Discover your next gaming adventure</p>
        </div>
    </div>

    <div class="container py-4">
        <!-- Filters -->
        <div class="card gaming-card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="platform" class="form-select">
                            <option value="">All Platforms</option>
                            <?php while ($p = $platforms->fetch_assoc()): ?>
                            <option value="<?= $p['platform_id'] ?>" <?= $platform == $p['platform_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="genre" class="form-select">
                            <option value="">All Genres</option>
                            <?php while ($g = $genres->fetch_assoc()): ?>
                            <option value="<?= $g['genre_id'] ?>" <?= $genre == $g['genre_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="rating" <?= $sort == 'rating' ? 'selected' : '' ?>>Top Rated</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-gaming w-100">
                            <i class="bi bi-filter"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Games Grid -->
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php while ($game = $games->fetch_assoc()): ?>
            <div class="col">
                <div class="game-card">
                    <div class="game-card-image">
                        <img src="<?= htmlspecialchars($game['cover_image_url'] ?? 'assets/placeholder.jpg') ?>" 
                             alt="<?= htmlspecialchars($game['title']) ?>">
                        <div class="game-card-price">$<?= number_format($game['price'], 2) ?></div>
                        <?php if ($game['avg_rating']): ?>
                        <div class="game-card-rating">
                            <i class="bi bi-star-fill"></i> <?= number_format($game['avg_rating'], 1) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="game-card-body">
                        <h5 class="game-title"><?= htmlspecialchars($game['title']) ?></h5>
                        <div class="game-card-meta">
                            <span><?= htmlspecialchars($game['platform_name']) ?></span>
                            <span><?= htmlspecialchars($game['genre_name']) ?></span>
                        </div>
                        <a href="game.php?id=<?= $game['game_id'] ?>" class="btn btn-gaming w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
