<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/game-website/admin/dashboard.php">GameHub Admin</a>
        <div class="d-flex">
            <span class="navbar-text me-3 text-white">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
            <a href="/game-website/auth/logout.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white p-3">
                <h4>Admin Panel</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="games.php">
                            <i class="bi bi-controller"></i> Games
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="orders.php">
                            <i class="bi bi-cart