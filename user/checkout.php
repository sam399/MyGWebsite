<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $stmt = $conn->prepare("
            INSERT INTO Orders (user_id, total_amount) 
            SELECT ?, SUM(g.price * c.quantity)
            FROM Cart c
            JOIN Game g ON c.game_id = g.game_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("ii", $user_id, $user_id);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            
            // Move cart items to order items
            $conn->query("
                INSERT INTO Order_Items (order_id, game_id, price)
                SELECT $order_id, g.game_id, g.price
                FROM Cart c
                JOIN Game g ON c.game_id = g.game_id
                WHERE c.user_id = $user_id
            ");
            
            // Clear cart
            $conn->query("DELETE FROM Cart WHERE user_id = $user_id");
            
            $conn->commit();
            header("Location: order-confirmation.php?id=$order_id");
            exit;
        }
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Checkout failed. Please try again.";
    }
}

// Get cart total
$result = $conn->query("
    SELECT SUM(g.price * c.quantity) as total
    FROM Cart c
    JOIN Game g ON c.game_id = g.game_id
    WHERE c.user_id = $user_id
")->fetch_assoc();

$total = $result['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card gaming-card">
                    <div class="card-body">
                        <h2 class="text-gaming mb-4">Checkout</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" id="checkout-form">
                            <div class="mb-4">
                                <h5>Order Summary</h5>
                                <div class="d-flex justify-content-between">
                                    <span>Total Amount:</span>
                                    <span class="text-gaming">$<?= number_format($total, 2) ?></span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-gaming btn-lg w-100">
                                Complete Purchase
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
