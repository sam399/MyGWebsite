<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Handle user deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    // Don't allow deletion of own account
    if ($user_id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM User WHERE user_id = $user_id");
    }
    header("Location: users.php");
    exit;
}

// Handle admin toggle
if (isset($_GET['toggle_admin']) && !empty($_GET['toggle_admin'])) {
    $user_id = (int)$_GET['toggle_admin'];
    // Don't allow removing own admin rights
    if ($user_id != $_SESSION['user_id']) {
        $conn->query("UPDATE User SET is_admin = NOT is_admin WHERE user_id = $user_id");
    }
    header("Location: users.php");
    exit;
}

// Fetch all users
$users = $conn->query("
    SELECT user_id, username, email, registration_date, is_admin,
           (SELECT COUNT(*) FROM Orders WHERE Orders.user_id = User.user_id) as order_count
    FROM User
    ORDER BY registration_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Admin Dashboard</title>
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
            <h2 class="page-title"><i class="bi bi-people-fill"></i> Player Management</h2>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list"></i> Active Players</h5>
                        <button class="btn btn-gaming">
                            <i class="bi bi-person-plus"></i> Add New Player
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Registration Date</th>
                                    <th>Orders</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $user['user_id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2"></i>
                                            <?= htmlspecialchars($user['username']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($user['registration_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $user['order_count'] ?> orders
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['is_admin'] ? 'danger' : 'secondary' ?>">
                                            <?= $user['is_admin'] ? 'Admin' : 'Player' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                            <div class="btn-group">
                                                <a href="users.php?toggle_admin=<?= $user['user_id'] ?>" 
                                                   class="btn btn-sm btn-warning"
                                                   title="Toggle admin status">
                                                    <i class="bi bi-shield"></i>
                                                </a>
                                                <a href="users.php?delete=<?= $user['user_id'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Delete this player permanently?')"
                                                   title="Delete user">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
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

<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_ids = $_POST['user_ids'] ?? [];
    
    if (!empty($user_ids)) {
        $ids = implode(',', array_map('intval', $user_ids));
        
        switch ($_POST['action']) {
            case 'delete':
                $conn->query("DELETE FROM User WHERE user_id IN ($ids)");
                break;
            case 'ban':
                $conn->query("UPDATE User SET is_banned = 1 WHERE user_id IN ($ids)");
                break;
            case 'unban':
                $conn->query("UPDATE User SET is_banned = 0 WHERE user_id IN ($ids)");
                break;
        }
    }
}

// Build filter query
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(username LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($role === 'admin') {
    $where[] = "is_admin = 1";
} elseif ($role === 'user') {
    $where[] = "is_admin = 0";
}

if ($status === 'banned') {
    $where[] = "is_banned = 1";
} elseif ($status === 'active') {
    $where[] = "is_banned = 0";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get pagination
$per_page = 20;
$page = max(1, $_GET['page'] ?? 1);
$offset = ($page - 1) * $per_page;

// Get users
$users = $conn->query("
    SELECT user_id, username, email, registration_date, is_admin, is_banned 
    FROM User 
    $where_clause
    ORDER BY registration_date DESC
    LIMIT $per_page OFFSET $offset
");

// Get total count
$total = $conn->query("SELECT COUNT(*) FROM User $where_clause")->fetch_row()[0];
$total_pages = ceil($total / $per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .table-responsive { overflow-x: auto; }
        .pagination { justify-content: center; }
        .filter-card { margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/admin-header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mb-4"><i class="bi bi-people"></i> User Management</h2>
                
                <!-- Filters -->
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" 
                                       value="<?= htmlspecialchars($search) ?>" placeholder="Username or email">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="">All Roles</option>
                                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admins</option>
                                    <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Regular Users</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="banned" <?= $status === 'banned' ? 'selected' : '' ?>>Banned</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Bulk Actions -->
                <form method="POST" id="bulk-form" class="mb-3">
                    <div class="d-flex gap-2">
                        <select name="action" class="form-select" style="max-width: 200px;">
                            <option value="">Bulk Actions</option>
                            <option value="ban">Ban Selected</option>
                            <option value="unban">Unban Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">Apply</button>
                    </div>
                
                    <!-- User Table -->
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Registered</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                    <tr class="<?= $user['is_banned'] ? 'table-danger' : '' ?>">
                                        <td><input type="checkbox" name="user_ids[]" value="<?= $user['user_id'] ?>"></td>
                                        <td><?= $user['user_id'] ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= date('M j, Y', strtotime($user['registration_date'])) ?></td>
                                        <td>
                                            <?php if ($user['is_admin']): ?>
                                                <span class="badge bg-primary">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['is_banned']): ?>
                                                <span class="badge bg-danger">Banned</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="user-edit.php?id=<?= $user['user_id'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Edit User">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
                
                <!-- Pagination -->
                <nav>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                Previous
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                Next
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select all checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('input[name="user_ids[]"]').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Confirm bulk actions
        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            const action = this.action.value;
            if (!action) {
                e.preventDefault();
                return;
            }
            
            if (action === 'delete' && !confirm('Permanently delete selected users?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>