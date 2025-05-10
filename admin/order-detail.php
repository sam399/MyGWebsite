<?php
require __DIR__ . '/../includes/admin-auth-check.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) die("Order ID required");

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE `Order` SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    
    $_SESSION['success'] = "Order status updated";
    header("Location: order-detail.php?id=$order_id");
    exit;
}

// Get order details
$order = $conn->query("
    SELECT o.*, u.username, u.email
    FROM `Order` o
    JOIN User u ON o.user_id = u.user_id
    WHERE o.order_id = $order_id
")->fetch_assoc();

if (!$order) die("Order not found");

// Get order items
$items = $conn->query("
    SELECT oi.*, g.title, g.cover_image_url
    FROM Order_Items oi
    JOIN Game g ON oi.game_id = g.game_id
    WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order_id ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .order-status {
            font-size: 1.1rem;
            padding: 0.5em 1em;
        }
        .game-cover {
            max-height: 60px;
            width: auto;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/admin-header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-receipt"></i> 
                        Order #<?= $order_id ?>
                    </h2>
                    <a href="orders.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Orders
                    </a>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); endif; ?>
                
                <div class="row">
                    <!-- Order Summary -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6>Customer</h6>
                                    <p>
                                        <?= htmlspecialchars($order['username']) ?><br>
                                        <?= htmlspecialchars($order['email']) ?>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>Order Date</h6>
                                    <p><?= date('F j, Y \a\t g:i A', strtotime($order['order_date'])) ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>Status</h6>
                                    <?php 
                                    $badge_class = [
                                        'pending' => 'bg-secondary',
                                        'paid' => 'bg-primary',
                                        'shipped' => 'bg-success',
                                        'cancelled' => 'bg-danger'
                                    ][$order['status']] ?? 'bg-warning';
                                    ?>
                                    <span class="badge order-status <?= $badge_class ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>Total Amount</h6>
                                    <h4>$<?= number_format($order['total_amount'], 2) ?></h4>
                                </div>
                                
                                <!-- Status Update Form -->
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Update Status</label>
                                        <select name="status" class="form-select">
                                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save"></i> Update Status
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Order Items</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($item = $items->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($item['cover_image_url']): ?>
                                                        <img src="<?= $item['cover_image_url'] ?>" 
                                                             class="game-cover me-3" 
                                                             alt="<?= htmlspecialchars($item['title']) ?>">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= htmlspecialchars($item['title']) ?></strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$<?= number_format($item['price'], 2) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th>$<?= number_format($order['total_amount'], 2) ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>