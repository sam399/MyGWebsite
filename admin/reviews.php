<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Handle review deletion
if (isset($_GET['delete'])) {
    $review_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM Review WHERE review_id = $review_id");
    header("Location: reviews.php");
    exit;
}

// Fetch all reviews with game and user details
$reviews = $conn->query("
    SELECT r.*, g.title as game_title, u.username
    FROM Review r
    JOIN Game g ON r.game_id = g.game_id
    JOIN User u ON r.user_id = u.user_id
    ORDER BY r.timestamp DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="css/admin-style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include __DIR__ . '/admin-sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <h2 class="page-title"><i class="bi bi-chat-left-text"></i> Review Management</h2>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list"></i> All Reviews</h5>
                        <div class="btn-group">
                            <button class="btn btn-gaming">
                                <i class="bi bi-filter"></i> Filter Reviews
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Game</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($review = $reviews->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $review['review_id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-controller me-2"></i>
                                            <?= htmlspecialchars($review['game_title']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2"></i>
                                            <?= htmlspecialchars($review['username']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            <?= str_repeat('★', $review['rating']) ?>
                                            <?= str_repeat('☆', 5 - $review['rating']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;">
                                            <?= htmlspecialchars($review['comment_text']) ?>
                                        </div>
                                    </td>
                                    <td><?= date('Y-m-d H:i', strtotime($review['timestamp'])) ?></td>
                                    <td>
                                        <a href="reviews.php?delete=<?= $review['review_id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Delete this review permanently?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
