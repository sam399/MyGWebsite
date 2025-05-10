<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Get stats
$stats = [
    'users' => $conn->query("SELECT COUNT(*) FROM User")->fetch_row()[0],
    'games' => $conn->query("SELECT COUNT(*) FROM Game")->fetch_row()[0],
    'orders' => $conn->query("SELECT COUNT(*) FROM Orders WHERE payment_status = 'paid'")->fetch_row()[0],
    'revenue' => $conn->query("SELECT SUM(total_amount) FROM Orders WHERE payment_status = 'paid'")->fetch_row()[0] ?? 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="css/admin-style.css" rel="stylesheet">
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

    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include __DIR__ . '/admin-sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <h2 class="page-title"><i class="bi bi-speedometer2"></i> Command Center</h2>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people-fill"></i> Active Players</h5>
                        <h2><?= number_format($stats['users']) ?></h2>
                    </div>
                </div>
                
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-controller"></i> Games</h5>
                        <h2><?= number_format($stats['games']) ?></h2>
                    </div>
                </div>
                
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-cart"></i> Orders</h5>
                        <h2><?= number_format($stats['orders']) ?></h2>
                    </div>
                </div>
                
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-currency-dollar"></i> Revenue</h5>
                        <h2>$<?= number_format($stats['revenue'], 2) ?></h2>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="activity-grid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-clock-history"></i> Recent Orders</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $orders = $conn->query("
                                    SELECT o.order_id, u.username, o.total_amount, o.order_date 
                                    FROM Orders o
                                    JOIN User u ON o.user_id = u.user_id
                                    ORDER BY o.order_date DESC 
                                    LIMIT 5
                                ");
                                while ($order = $orders->fetch_assoc()):
                                ?>
                                <div class="mb-3">
                                    <strong>Order #<?= $order['order_id'] ?></strong>
                                    <div>User: <?= htmlspecialchars($order['username']) ?></div>
                                    <div>Amount: $<?= number_format($order['total_amount'], 2) ?></div>
                                    <small class="text-muted"><?= $order['order_date'] ?></small>
                                </div>
                                <hr>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-exclamation-triangle"></i> Pending Reviews</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $reviews = $conn->query("
                                    SELECT r.review_id, g.title, u.username, r.rating, r.comment_text, r.timestamp 
                                    FROM Review r
                                    JOIN Game g ON r.game_id = g.game_id
                                    JOIN User u ON r.user_id = u.user_id
                                    ORDER BY r.timestamp DESC
                                    LIMIT 5
                                ");
                                while ($review = $reviews->fetch_assoc()):
                                ?>
                                <div class="mb-3">
                                    <strong><?= htmlspecialchars($review['title']) ?></strong>
                                    <div>By: <?= htmlspecialchars($review['username']) ?></div>
                                    <div>Rating: <?= str_repeat('★', $review['rating']) ?></div>
                                    <small class="text-muted"><?= date('M j, Y', strtotime($review['timestamp'])) ?></small>
                                </div>
                                <hr>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>