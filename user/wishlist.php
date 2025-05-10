<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

$wishlist = $conn->query("
    SELECT g.* 
    FROM Game g
    JOIN Wishlist w ON g.game_id = w.game_id
    WHERE w.user_id = $user_id
    ORDER BY w.added_at DESC
");

// Add function to handle wishlist removal
if (isset($_GET['remove'])) {
    $game_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM Wishlist WHERE user_id = $user_id AND game_id = $game_id");
    header("Location: wishlist.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wishlist | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container py-5">
        <h2 class="page-title mb-4">
            <i class="bi bi-heart-fill"></i> My Wishlist
        </h2>

        <?php if ($wishlist->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php while ($game = $wishlist->fetch_assoc()): ?>
                    <div class="col">
                        <div class="game-card">
                            <div class="game-card-image">
                                <img src="<?= htmlspecialchars($game['cover_image_url'] ?? '/assets/placeholder.jpg') ?>" 
                                     alt="<?= htmlspecialchars($game['title']) ?>">
                                <div class="game-card-price">$<?= number_format($game['price'], 2) ?></div>
                            </div>
                            <div class="game-card-body">
                                <h5 class="game-title"><?= htmlspecialchars($game['title']) ?></h5>
                                <div class="game-card-meta">
                                    <span><?= htmlspecialchars($game['developer']) ?></span>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <a href="../game.php?id=<?= $game['game_id'] ?>" 
                                       class="btn btn-gaming flex-grow-1">
                                        View Details
                                    </a>
                                    <a href="?remove=<?= $game['game_id'] ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Remove from wishlist?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <h3 class="text-muted">Your wishlist is empty</h3>
                <p>Add games to your wishlist to keep track of what you want to play!</p>
                <a href="/MyGWebsite/search.php" class="btn btn-gaming mt-3">
                    <i class="bi bi-search"></i> Browse Games
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

