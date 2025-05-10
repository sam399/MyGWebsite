<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Handle status update
if (isset($_GET['update_status'])) {
    $order_id = (int)$_GET['update_status'];
    $status = $conn->real_escape_string($_GET['status']);
    $conn->query("UPDATE Orders SET payment_status = '$status' WHERE order_id = $order_id");
    header("Location: orders.php");
    exit;
}

// Fetch all orders with user details
$orders = $conn->query("
    SELECT o.*, u.username,
           (SELECT COUNT(*) FROM Order_Items WHERE order_id = o.order_id) as item_count
    FROM Orders o
    JOIN User u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Admin Dashboard</title>
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
            <h2 class="page-title"><i class="bi bi-cart-fill"></i> Order Management</h2>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list"></i> All Orders</h5>
                        <div class="btn-group">
                            <button class="btn btn-gaming">
                                <i class="bi bi-download"></i> Export Orders
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $order['order_id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2"></i>
                                            <?= htmlspecialchars($order['username']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $order['item_count'] ?> items
                                        </span>
                                    </td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($order['order_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $order['payment_status'] == 'paid' ? 'success' : 
                                            ($order['payment_status'] == 'pending' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($order['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-gaming dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="?update_status=<?= $order['order_id'] ?>&status=pending">Pending</a></li>
                                                <li><a class="dropdown-item" href="?update_status=<?= $order['order_id'] ?>&status=paid">Paid</a></li>
                                                <li><a class="dropdown-item" href="?update_status=<?= $order['order_id'] ?>&status=cancelled">Cancelled</a></li>
                                            </ul>
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

<?php
require __DIR__ . '/../includes/admin-auth-check.php';

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_ids = $_POST['order_ids'] ?? [];
    
    if (!empty($order_ids)) {
        $ids = implode(',', array_map('intval', $order_ids));
        
        switch ($_POST['action']) {
            case 'mark_paid':
                $conn->query("UPDATE `Order` SET status = 'paid' WHERE order_id IN ($ids)");
                break;
            case 'mark_shipped':
                $conn->query("UPDATE `Order` SET status = 'shipped' WHERE order_id IN ($ids)");
                break;
            case 'cancel':
                $conn->query("UPDATE `Order` SET status = 'cancelled' WHERE order_id IN ($ids)");
                break;
        }
    }
}

// Build filter query
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(o.order_id = ? OR u.username LIKE ? OR u.email LIKE ?)";
    $params[] = $search;
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'iss';
}

if (!empty($status) && in_array($status, ['pending', 'paid', 'shipped', 'cancelled'])) {
    $where[] = "o.status = ?";
    $params[] = $status;
    $types .= 's';
}

if (!empty($date_from)) {
    $where[] = "o.order_date >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if (!empty($date_to)) {
    $where[] = "o.order_date <= ?";
    $params[] = $date_to . ' 23:59:59';
    $types .= 's';
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get pagination
$per_page = 15;
$page = max(1, $_GET['page'] ?? 1);
$offset = ($page - 1) * $per_page;

// Get orders
$query = "
    SELECT o.order_id, o.order_date, o.total_amount, o.status, 
           u.user_id, u.username, u.email,
           COUNT(oi.item_id) as item_count
    FROM `Order` o
    JOIN User u ON o.user_id = u.user_id
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    $where_clause
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT $per_page OFFSET $offset
";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result();

// Get total count
$count_query = "
    SELECT COUNT(*) 
    FROM (
        SELECT o.order_id
        FROM `Order` o
        JOIN User u ON o.user_id = u.user_id
        $where_clause
        GROUP BY o.order_id
    ) AS filtered_orders
";

$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total / $per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Management | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .badge-pending { background-color: #6c757d; }
        .badge-paid { background-color: #0d6efd; }
        .badge-shipped { background-color: #198754; }
        .badge-cancelled { background-color: #dc3545; }
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/admin-header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mb-4"><i class="bi bi-cart"></i> Order Management</h2>
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" 
                                       value="<?= htmlspecialchars($search) ?>" 
                                       placeholder="Order ID, username, or email">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <div class="input-group">
                                    <input type="date" name="date_from" class="form-control" 
                                           value="<?= htmlspecialchars($date_from) ?>">
                                    <span class="input-group-text">to</span>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="<?= htmlspecialchars($date_to) ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="orders.php" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Bulk Actions -->
                <form method="POST" id="bulk-form" class="mb-3">
                    <div class="d-flex gap-2">
                        <select name="action" class="form-select" style="max-width: 200px;">
                            <option value="">Bulk Actions</option>
                            <option value="mark_paid">Mark as Paid</option>
                            <option value="mark_shipped">Mark as Shipped</option>
                            <option value="cancel">Cancel Orders</option>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">Apply</button>
                    </div>
                
                    <!-- Order Table -->
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="select-all"></th>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td><input type="checkbox" name="order_ids[]" value="<?= $order['order_id'] ?>"></td>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($order['username']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($order['email']) ?></small>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                                        <td><?= $order['item_count'] ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <?php 
                                            $badge_class = [
                                                'pending' => 'badge-pending',
                                                'paid' => 'badge-paid',
                                                'shipped' => 'badge-shipped',
                                                'cancelled' => 'badge-cancelled'
                                            ][$order['status']] ?? 'badge-secondary';
                                            ?>
                                            <span class="badge rounded-pill status-badge <?= $badge_class ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order-detail.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Order">
                                                <i class="bi bi-eye"></i>
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
            document.querySelectorAll('input[name="order_ids[]"]').forEach(checkbox => {
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
            
            if (action === 'cancel' && !confirm('Cancel selected orders?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>