<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'] ?? null;

// Fetch all orders for the user
$orders = $conn->query("
    SELECT o.*, COUNT(oi.item_id) as item_count
    FROM Orders o
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    WHERE o.user_id = $user_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container py-5">
        <h2 class="page-title mb-4"><i class="bi bi-receipt"></i> My Orders</h2>

        <?php if ($orders->num_rows > 0): ?>
            <div class="gaming-card">
                <div class="card-body">
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <div class="gaming-item p-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <h5 class="text-gaming">#<?= $order['order_id'] ?></h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted">Date</div>
                                    <?= date('M j, Y H:i', strtotime($order['order_date'])) ?>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-muted">Items</div>
                                    <?= $order['item_count'] ?>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-muted">Total</div>
                                    <span class="text-gaming">$<?= number_format($order['total_amount'], 2) ?></span>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge bg-<?= $order['payment_status'] == 'paid' ? 'success' : 
                                        ($order['payment_status'] == 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>
                                </div>
                                <div class="col-md-1">
                                    <a href="?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-gaming">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>

                            <?php if ($order_id == $order['order_id']): ?>
                                <div class="mt-4 pt-4 border-top">
                                    <h6>Order Details</h6>
                                    <?php
                                    $items = $conn->query("
                                        SELECT oi.*, g.title, g.cover_image_url
                                        FROM Order_Items oi
                                        JOIN Game g ON oi.game_id = g.game_id
                                        WHERE oi.order_id = {$order['order_id']}
                                    ");
                                    while ($item = $items->fetch_assoc()):
                                    ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <?php if ($item['cover_image_url']): ?>
                                                <img src="<?= $item['cover_image_url'] ?>" 
                                                     class="me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <div><?= htmlspecialchars($item['title']) ?></div>
                                                <div class="text-gaming">$<?= number_format($item['price'], 2) ?></div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <h3 class="text-muted">No orders yet</h3>
                <a href="/MyGWebsite/search.php" class="btn btn-gaming mt-3">Browse Games</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

