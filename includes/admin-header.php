<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">
            <i class="bi bi-house-fill"></i> GameHub Home
        </a>
        <div class="d-flex align-items-center">
            <span class="text-light me-3">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>
