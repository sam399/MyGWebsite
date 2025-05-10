<?php
session_start();
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, is_admin FROM User WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            // Start a new session
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = ($user['is_admin'] == 1);
            
            // Redirect based on user type
            if ($user['is_admin'] == 1) {
                header("Location: ../admin/dashboard.php");
            } else {
                // Redirect to user dashboard instead of index
                header("Location: ../user/dashboard.php");
            }
            exit();
        }
    }
    
    // Show generic error for any failure
    $error = "Invalid email or password";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | GamersHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                        url('../assets/gaming-bg.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Rajdhani', sans-serif;
        }
        .login-card {
            background: rgba(28, 28, 36, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.2);
            color: #fff;
            backdrop-filter: blur(10px);
        }
        .form-control {
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid #00ff00;
            color: #fff;
            padding: 12px;
            margin-bottom: 1rem;
        }
        .form-control:focus {
            background: rgba(0, 0, 0, 0.6);
            border-color: #00ff99;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.3);
            color: #fff;
        }
        .btn-primary {
            background: linear-gradient(45deg, #00ff00, #00aa00);
            border: none;
            padding: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            background: linear-gradient(45deg, #00ff99, #00ff00);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.4);
        }
        h2 {
            color: #00ff00;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: bold;
        }
        .form-label {
            color: #00ff00;
            font-weight: 600;
        }
        a {
            color: #00ff00;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        a:hover {
            color: #00ff99;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.4);
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.9);
            border: none;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <h2>Player Login</h2>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger mt-3" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>