<?php
require __DIR__ . '/../includes/admin-auth-check.php';

$user_id = $_GET['id'] ?? null;
if (!$user_id) die("User ID required");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $is_banned = isset($_POST['is_banned']) ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE User SET username=?, email=?, is_admin=?, is_banned=? WHERE user_id=?");
    $stmt->bind_param("ssiii", $username, $email, $is_admin, $is_banned, $user_id);
    $stmt->execute();
    
    $_SESSION['success'] = "User updated successfully";
    header("Location: users.php");
    exit;
}

// Get user data
$user = $conn->query("SELECT * FROM User WHERE user_id = $user_id")->fetch_assoc();
if (!$user) die("User not found");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/admin-header.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit User: <?= htmlspecialchars($user['username']) ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                        <?php unset($_SESSION['success']); endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" 
                                       value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_admin" class="form-check-input" id="is_admin"
                                       <?= $user['is_admin'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_admin">Administrator</label>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_banned" class="form-check-input" id="is_banned"
                                       <?= $user['is_banned'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_banned">Banned User</label>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="users.php" class="btn btn-secondary">Back to Users</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>