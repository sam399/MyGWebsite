<?php 
session_start();
include __DIR__ . '/includes/header.php';
require __DIR__ . '/config.php';

// Get game ID from URL
$game_id = $_GET['id'] ?? null;
if (!$game_id || !is_numeric($game_id)) {
    die("Invalid game ID");
}

// Fetch game details
$stmt = $conn->prepare("
    SELECT g.*, p.name as platform_name, esrb.code as esrb_rating
    FROM Game g
    JOIN Platform p ON g.platform_id = p.platform_id
    LEFT JOIN ESRB_Ratings esrb ON g.esrb_id = esrb.esrb_id
    WHERE g.game_id = ?
");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) die("Game not found");
?>

<section class="game-details">
    <div class="row">
        <!-- Game Cover -->
        <div class="col-md-4">
            <img src="<?= htmlspecialchars($game['cover_image_url']) ?>" 
                 class="img-fluid rounded" 
                 alt="<?= htmlspecialchars($game['title']) ?>">
        </div>
        
        <!-- Game Info -->
        <div class="col-md-8">
            <h1><?= htmlspecialchars($game['title']) ?></h1>
            <div class="d-flex gap-3 mb-3">
                <span class="badge bg-primary"><?= htmlspecialchars($game['platform_name']) ?></span>
                <?php if ($game['esrb_rating']): ?>
                    <span class="badge bg-warning text-dark">ESRB: <?= $game['esrb_rating'] ?></span>
                <?php endif; ?>
            </div>
            
            <p class="lead"><?= htmlspecialchars($game['developer']) ?></p>
            <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
            
            <div class="d-flex align-items-center mt-4">
                <h3 class="mb-0 me-3">$<?= number_format($game['price'], 2) ?></h3>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="d-flex gap-2">
                        <form method="POST" action="/MyGWebsite/user/add-to-cart.php">
                            <input type="hidden" name="game_id" value="<?= $game_id ?>">
                            <button type="submit" class="btn btn-gaming">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        <form method="POST" action="/MyGWebsite/user/add-to-wishlist.php">
                            <input type="hidden" name="game_id" value="<?= $game_id ?>">
                            <button type="submit" class="btn btn-outline-gaming">
                                <i class="bi bi-heart"></i> Add to Wishlist
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="/MyGWebsite/auth/login.php" class="btn btn-gaming">Login to Purchase</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="mt-5">
    <h2>Reviews</h2>
    <div class="row" id="reviews-container">
        <?php
        $reviews_stmt = $conn->prepare("
            SELECT r.*, u.username 
            FROM Review r
            JOIN User u ON r.user_id = u.user_id
            WHERE r.game_id = ?
            ORDER BY r.timestamp DESC
        ");
        $reviews_stmt->bind_param("i", $game_id);
        $reviews_stmt->execute();
        $reviews = $reviews_stmt->get_result();
        
        if ($reviews->num_rows > 0) {
            while ($review = $reviews->fetch_assoc()) {
                include __DIR__ . '/includes/review-card.php';
            }
        } else {
            echo '<p class="text-muted">No reviews yet. Be the first!</p>';
        }
        ?>
    </div>
</section>

<!-- Add Review Form (Visible to logged-in users) -->
<section class="mt-5">
    <?php if (isset($_SESSION['user_id'])): ?>
        <h3>Add Your Review</h3>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form method="POST" action="/MyGWebsite/submit-review.php">
            <input type="hidden" name="game_id" value="<?= $game_id ?>">
            <div class="mb-3">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select" required>
                    <option value="5">★★★★★ (5)</option>
                    <option value="4">★★★★☆ (4)</option>
                    <option value="3">★★★☆☆ (3)</option>
                    <option value="2">★★☆☆☆ (2)</option>
                    <option value="1">★☆☆☆☆ (1)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Your Review</label>
                <textarea name="comment" class="form-control" rows="3" required 
                          placeholder="Share your thoughts about this game..."></textarea>
            </div>
            <button type="submit" class="btn btn-gaming">
                <i class="bi bi-star"></i> Submit Review
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            Please <a href="/MyGWebsite/auth/login.php">login</a> to write a review.
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>