<?php
session_start();
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO User (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        header("Location: /MyGWebsite/auth/login.php"); 
        exit;
    } else {
        $error = "Registration failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - GamersHub</title>
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                        url('/MyGWebsite/assets/gaming-bg.jpg');
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            background: rgba(33, 33, 33, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #00ff00;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .input-group {
            margin-bottom: 1.5rem;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #00ff00;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #00ff99;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
        }
        button {
            width: 100%;
            padding: 12px;
            background: #00ff00;
            border: none;
            border-radius: 5px;
            color: #000;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: #00ff99;
            transform: scale(1.02);
        }
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        .login-link a {
            color: #00ff00;
            text-decoration: none;
        }
        .login-link a:hover {
            color: #00ff99;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Join the Game</h1>
        <form method="POST" action="/MyGWebsite/auth/register.php">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="/MyGWebsite/auth/login.php">Login here</a>
        </div>
    </div>
</body>
</html>