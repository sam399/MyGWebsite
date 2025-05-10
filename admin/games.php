<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Handle game deletion
if (isset($_GET['delete'])) {
    $game_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM Game WHERE game_id = $game_id");
    header("Location: games.php?deleted=1");
    exit;
}

// Fetch all games with platform names
$games = $conn->query("
    SELECT g.*, p.name as platform_name
    FROM Game g
    LEFT JOIN Platform p ON g.platform_id = p.platform_id
    ORDER BY g.game_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Management | Admin Dashboard</title>
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
            <h2 class="page-title"><i class="bi bi-controller"></i> Game Library</h2>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-joystick"></i> Manage Games</h5>
                        <a href="game-edit.php?new" class="btn btn-gaming">
                            <i class="bi bi-plus-lg"></i> Add New Game
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Platform</th>
                                    <th>Release Date</th>
                                    <th>Developer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($game = $games->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $game['game_id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-disc me-2"></i>
                                            <?= htmlspecialchars($game['title']) ?>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($game['price'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= htmlspecialchars($game['platform_name'] ?? 'None') ?>
                                        </span>
                                    </td>
                                    <td><?= $game['release_date'] ?? 'TBA' ?></td>
                                    <td><?= htmlspecialchars($game['developer']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="game-edit.php?id=<?= $game['game_id'] ?>" 
                                               class="btn btn-sm btn-warning"
                                               title="Edit game">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="games.php?delete=<?= $game['game_id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this game permanently?')"
                                               title="Delete game">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
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