<?php 
include 'includes/header.php';
require 'config.php';

// Fetch featured games
$sql = "SELECT g.*, p.name as platform_name 
        FROM Game g 
        JOIN Platform p ON g.platform_id = p.platform_id 
        ORDER BY g.release_date DESC 
        LIMIT 6";
$result = $conn->query($sql);
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to GameHub</h1>
        <p>Your Ultimate Gaming Destination</p>
        <a href="search.php" class="btn btn-gaming btn-lg">Explore Games</a>
    </div>
</div>

<section class="featured-games">
    <div class="section-header">
        <h2><i class="bi bi-stars"></i> Featured Games</h2>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while($game = $result->fetch_assoc()): ?>
        <div class="col">
            <div class="game-card">
                <div class="game-card-image">
                    <img src="<?= htmlspecialchars($game['cover_image_url'] ?? 'assets/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($game['title']) ?>">
                    <div class="game-card-price">$<?= number_format($game['price'], 2) ?></div>
                </div>
                <div class="game-card-body">
                    <h5><?= htmlspecialchars($game['title']) ?></h5>
                    <div class="game-card-meta">
                        <span class="platform"><?= htmlspecialchars($game['platform_name']) ?></span>
                        <span class="developer"><?= htmlspecialchars($game['developer']) ?></span>
                    </div>
                    <a href="game.php?id=<?= $game['game_id'] ?>" class="btn btn-gaming w-100">View Details</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<section class="top-rated">
    <div class="section-header">
        <h2><i class="bi bi-trophy"></i> Top Rated Games</h2>
    </div>
    <?php
    $sql = "SELECT g.*, AVG(r.rating) as avg_rating
            FROM Game g
            JOIN Review r ON g.game_id = r.game_id
            GROUP BY g.game_id
            ORDER BY avg_rating DESC
            LIMIT 3";
    $result = $conn->query($sql);
    ?>
    <div class="top-rated-list">
        <?php while($game = $result->fetch_assoc()): ?>
        <div class="top-rated-item">
            <div class="rating-badge">★ <?= number_format($game['avg_rating'], 1) ?></div>
            <div class="game-info">
                <h5><?= htmlspecialchars($game['title']) ?></h5>
                <small><?= htmlspecialchars($game['developer']) ?></small>
            </div>
            <a href="game.php?id=<?= $game['game_id'] ?>" class="btn btn-outline-gaming">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>