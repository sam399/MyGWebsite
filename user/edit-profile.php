<?php
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    $stmt = $conn->prepare("UPDATE User SET username = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $username, $email, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        $_SESSION['username'] = $username; // Update session with new username
        header("Location: profile.php");
        exit;
    }
}

// Get current user data
$user = $conn->query("SELECT username, email FROM User WHERE user_id = $user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | GameHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card gaming-card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-person-gear"></i> Edit Profile</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error'] ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="edit-profile.php">
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

                            <div class="d-flex justify-content-between">
                                <a href="profile.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Profile
                                </a>
                                <button type="submit" class="btn btn-gaming">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="change-password.php" class="btn btn-outline-gaming">
                                <i class="bi bi-key"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
