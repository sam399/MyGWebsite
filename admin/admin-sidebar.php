<div class="bg-dark text-white p-3">
    <h4>Admin Panel</h4>
    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : 'text-white' ?>" 
               href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'games.php' ? 'active' : 'text-white' ?>" 
               href="games.php">
                <i class="bi bi-controller"></i> Games
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : 'text-white' ?>" 
               href="users.php">
                <i class="bi bi-people"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : 'text-white' ?>" 
               href="orders.php">
                <i class="bi bi-cart"></i> Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : 'text-white' ?>" 
               href="reviews.php">
                <i class="bi bi-chat-left-text"></i> Reviews
            </a>
        </li>
    </ul>
</div>
