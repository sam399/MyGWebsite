<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub - Discover Your Next Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="/MyGWebsite/css/gaming-theme.css" rel="stylesheet">
    <link href="/MyGWebsite/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="d-flex">
           <?php if (isset($_SESSION['user_id'])): ?>
             <span class="navbar-text me-3">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
              <a href="/MyGWebsite/auth/logout.php" class="btn btn-outline-light">Logout</a>
           <?php else: ?>
              <a href="/MyGWebsite/auth/login.php" class="btn btn-outline-light me-2">Login</a>
              <a href="/MyGWebsite/auth/register.php" class="btn btn-light">Register</a>
            <?php endif; ?>
        </div>
        <div class="container">
            <a class="navbar-brand" href="/MyGWebsite/index.php">GameHub</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/MyGWebsite/index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/MyGWebsite/search.php">Browse</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/MyGWebsite/user/profile.php">My Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="/MyGWebsite/user/cart.php">Cart</a></li>
                        <li class="nav-item"><a class="nav-link" href="/MyGWebsite/user/wishlist.php">Wishlist</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="navbar-text me-3">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="/MyGWebsite/auth/logout.php" class="btn btn-outline-light">Logout</a>
                    <?php else: ?>
                        <a href="/MyGWebsite/auth/login.php" class="btn btn-outline-light">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container my-5">