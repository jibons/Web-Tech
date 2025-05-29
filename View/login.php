<?php
session_start();
include "../Model/Database.php";
include "../Model/User.php";
include "../config/session_handler.php";

use Model\Database;
use Model\User;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$registration_success = isset($_SESSION['registration_success']) ? $_SESSION['registration_success'] : false;
unset($_SESSION['registration_success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $userModel = new User();
        
        $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            throw new Exception("Please enter both username and password");
        }

        $result = $userModel->authenticate($username, $password);
        
        if ($result['success']) {
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['fullname'] = $result['user']['fullname'];
            $_SESSION['logged_in'] = true;
            
            error_log("Login successful for user: " . $username);
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
            error_log("Login failed for user: " . $username . " - " . $error);
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
        error_log("Login Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Voting System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Online Voting System</h1>
    </div>

    <div class="nav">
        <a href="Home.php">Home</a>
        <a href="User_Reg.php">Register</a>
        <a href="login.php">Login</a>
    </div>

    <div class="container">
        <div class="form-section">
            <form method="POST" action="" class="login-form">
                <h2>Login to Your Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['registration_success'])): ?>
                    <div class="alert success">Registration successful! Please login.</div>
                    <?php unset($_SESSION['registration_success']); ?>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <div class="button-group">
                        <a href="Home.php" class="btn-back">Back</a>
                        <button type="submit" class="btn-small">Login</button>
                    </div>
                </div>

                <div class="links">
                    <a href="User_Reg.php">Create New Account</a>
                    <span> | </span>
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/validation.js"></script>
</body>
</html>