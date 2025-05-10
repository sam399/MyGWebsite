<?php
require __DIR__ . '/../includes/auth-check.php'; // Ensures user is logged in
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $conn->prepare("SELECT username, email, registration_date FROM User WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container my-5">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="profile.php" class="list-group-item list-group-item-action active">Profile</a>
                    <a href="orders.php" class="list-group-item list-group-item-action">My Orders</a>
                    <a href="wishlist.php" class="list-group-item list-group-item-action">Wishlist</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h3>Account Details</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Username</dt>
                            <dd class="col-sm-9"><?= htmlspecialchars($user['username']) ?></dd>
                            
                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9"><?= htmlspecialchars($user['email']) ?></dd>
                            
                            <dt class="col-sm-3">Member Since</dt>
                            <dd class="col-sm-9"><?= date('F j, Y', strtotime($user['registration_date'])) ?></dd>
                        </dl>
                        
                        <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
                        <a href="change-password.php" class="btn btn-outline-secondary">Change Password</a>
                    </div>
                </div>
                
                <!-- Recent Orders Preview -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $orders = $conn->query("
                            SELECT o.order_id, o.order_date, o.total_amount, 
                                   COUNT(oi.item_id) as item_count
                            FROM Orders o
                            JOIN Order_Items oi ON o.order_id = oi.order_id
                            WHERE o.user_id = $user_id
                            GROUP BY o.order_id
                            ORDER BY o.order_date DESC
                            LIMIT 3
                        ");
                        
                        if ($orders->num_rows > 0) {
                            while ($order = $orders->fetch_assoc()) {
                                echo '<div class="mb-3">';
                                echo '<h5>Order #' . $order['order_id'] . '</h5>';
                                echo '<p>' . date('M j, Y', strtotime($order['order_date'])) . ' | ';
                                echo $order['item_count'] . ' items | $' . $order['total_amount'] . '</p>';
                                echo '<a href="orders.php?id=' . $order['order_id'] . '" class="btn btn-sm btn-outline-primary">View Details</a>';
                                echo '</div>';
                            }
                            echo '<a href="orders.php" class="btn btn-link">View all orders</a>';
                        } else {
                            echo '<p>No orders yet.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>