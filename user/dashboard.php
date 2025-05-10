<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

// Fetch user's stats
$stats = [
    'total_orders' => $conn->query("SELECT COUNT(*) FROM Orders WHERE user_id = $user_id")->fetch_row()[0],
    'wishlist_count' => $conn->query("SELECT COUNT(*) FROM Wishlist WHERE user_id = $user_id")->fetch_row()[0],
    'reviews_count' => $conn->query("SELECT COUNT(*) FROM Review WHERE user_id = $user_id")->fetch_row()[0]
];

// Fetch recent orders
$recent_orders = $conn->query("
    SELECT o.*, COUNT(oi.item_id) as items_count
    FROM Orders o
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    WHERE o.user_id = $user_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT 5
");

// Fetch wishlist items
$wishlist = $conn->query("
    SELECT g.*, p.name as platform_name
    FROM Game g
    JOIN Wishlist w ON g.game_id = w.game_id
    LEFT JOIN Platform p ON g.platform_id = p.platform_id
    WHERE w.user_id = $user_id
    LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container py-4">
        <h1 class="text-gaming mb-4">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        
        <!-- Quick Stats -->
        <div class="stats-grid mb-5">
            <div class="card stats-card">
                <div class="card-body">
                    <h5><i class="bi bi-cart"></i> Orders</h5>
                    <h2><?= $stats['total_orders'] ?></h2>
                    <a href="orders.php" class="stretched-link"></a>
                </div>
            </div>
            
            <div class="card stats-card">
                <div class="card-body">
                    <h5><i class="bi bi-heart"></i> Wishlist</h5>
                    <h2><?= $stats['wishlist_count'] ?></h2>
                    <a href="wishlist.php" class="stretched-link"></a>
                </div>
            </div>
            
            <div class="card stats-card">
                <div class="card-body">
                    <h5><i class="bi bi-star"></i> Reviews</h5>
                    <h2><?= $stats['reviews_count'] ?></h2>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card gaming-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-gaming">View All</a>
            </div>
            <div class="card-body">
                <?php if ($recent_orders->num_rows > 0): ?>
                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 gaming-item">
                        <div>
                            <h6>Order #<?= $order['order_id'] ?></h6>
                            <small class="text-muted">
                                <?= date('M j, Y', strtotime($order['order_date'])) ?> • 
                                <?= $order['items_count'] ?> items
                            </small>
                        </div>
                        <div>
                            <span class="badge bg-success">$<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No orders yet</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Wishlist Preview -->
        <div class="card gaming-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Wishlist</h5>
                <a href="wishlist.php" class="btn btn-sm btn-gaming">View All</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if ($wishlist->num_rows > 0): ?>
                        <?php while ($game = $wishlist->fetch_assoc()): ?>
                        <div class="col-md-3">
                            <div class="game-card">
                                <img src="<?= htmlspecialchars($game['cover_image_url'] ?? '/assets/placeholder.jpg') ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($game['title']) ?>">
                                <div class="card-body">
                                    <h6><?= htmlspecialchars($game['title']) ?></h6>
                                    <p class="text-gaming">$<?= number_format($game['price'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <h3 class="text-muted">No items found</h3>
                            <a href="/MyGWebsite/search.php" class="btn btn-gaming mt-3">
                                <i class="bi bi-search"></i> Browse Games
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
