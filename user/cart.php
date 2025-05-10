<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_items = $conn->query("
    SELECT g.*, c.quantity 
    FROM Cart c
    JOIN Game g ON c.game_id = g.game_id
    WHERE c.user_id = $user_id
");

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container my-5">
        <h2 class="text-gaming mb-4">Your Cart</h2>
        
        <?php if ($cart_items->num_rows > 0): ?>
            <div class="card gaming-card">
                <div class="card-body">
                    <?php while ($item = $cart_items->fetch_assoc()): 
                        $total += $item['price'] * $item['quantity'];
                    ?>
                    <div class="d-flex align-items-center mb-3 p-3 border-bottom gaming-item">
                        <img src="<?= $item['cover_image_url'] ?>" class="me-3" style="width: 80px;">
                        <div class="flex-grow-1">
                            <h5><?= htmlspecialchars($item['title']) ?></h5>
                            <p class="text-neon">$<?= number_format($item['price'], 2) ?></p>
                        </div>
                        <div class="d-flex align-items-center">
                            <form method="POST" action="update-cart.php" class="d-flex align-items-center">
                                <input type="hidden" name="game_id" value="<?= $item['game_id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                       class="form-control form-control-sm mx-2" style="width: 60px;">
                                <button type="submit" class="btn btn-sm btn-gaming">Update</button>
                            </form>
                            <a href="remove-from-cart.php?id=<?= $item['game_id'] ?>" 
                               class="btn btn-sm btn-danger ms-2">Remove</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h4>Total: <span class="text-gaming">$<?= number_format($total, 2) ?></span></h4>
                        <a href="checkout.php" class="btn btn-gaming btn-lg">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center">
                <h3 class="text-muted">Your cart is empty</h3>
                <a href="/MyGWebsite/games.php" class="btn btn-gaming mt-3">Browse Games</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
