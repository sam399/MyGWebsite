<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header('Location: cart.php');
    exit;
}

// Fetch order details with user information
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.email,
           COUNT(oi.item_id) as total_items
    FROM Orders o
    JOIN User u ON o.user_id = u.user_id
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    WHERE o.order_id = ? AND o.user_id = ?
    GROUP BY o.order_id
");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Fetch order items
$items = $conn->query("
    SELECT oi.*, g.title, g.developer
    FROM Order_Items oi
    JOIN Game g ON oi.game_id = g.game_id
    WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="gaming-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h2 class="mt-2">Order Confirmed!</h2>
                            <p class="text-muted">Order #<?= $order_id ?></p>
                        </div>

                        <!-- Receipt Details -->
                        <div class="receipt-section">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Customer Details</h5>
                                    <p>Name: <?= htmlspecialchars($order['username']) ?><br>
                                    Email: <?= htmlspecialchars($order['email']) ?><br>
                                    Date: <?= date('F j, Y g:i A', strtotime($order['order_date'])) ?></p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h5>Order Summary</h5>
                                    <p>Items: <?= $order['total_items'] ?><br>
                                    Status: <span class="badge bg-success">Paid</span></p>
                                </div>
                            </div>

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Game</th>
                                        <th>Developer</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = $items->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['title']) ?></td>
                                        <td><?= htmlspecialchars($item['developer']) ?></td>
                                        <td class="text-end">$<?= number_format($item['price'], 2) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total:</th>
                                        <th class="text-end text-gaming">$<?= number_format($order['total_amount'], 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="text-center mt-4">
                                <button onclick="window.print()" class="btn btn-outline-gaming me-2">
                                    <i class="bi bi-printer"></i> Print Receipt
                                </button>
                                <a href="orders.php" class="btn btn-gaming">
                                    <i class="bi bi-list"></i> View All Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
