<?php
include('../config/session_handler.php');
include('../Model/Database.php');
include('../Model/User.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log("Login page loaded");

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
$error = '';
$registration_success = isset($_SESSION['registration_success']) ? $_SESSION['registration_success'] : false;
unset($_SESSION['registration_success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = new Database();
        $userModel = new User();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Please enter both email and password";
        } else {
            $result = $userModel->authenticate($email, $password);
            
            if ($result['success']) {
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                $_SESSION['role'] = 'voter';
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Invalid email or password";
                error_log("Login failed: " . $error);
            }
        }
    } catch(Exception $e) {
        $error = "An error occurred. Please try again later.";
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
        <h1>Login to Vote</h1>
    </div>

    <div class="nav">
        <a href="Home.php">Home</a>
        <a href="User_Reg.php">Register</a>
        <a href="login.php">Login</a>
    </div>

    <div class="container">
        <div class="login-form">
            <?php if(isset($_SESSION['logout_message'])): ?>
                <div class="success"><?php echo htmlspecialchars($_SESSION['logout_message']); ?></div>
                <?php unset($_SESSION['logout_message']); ?>
            <?php endif; ?>
            <?php if($registration_success): ?>
                <div class="success">Registration successful! Please login.</div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="submit-btn">Login</button>
            </form>

            <div class="links">
                <p>Don't have an account? <a href="User_Reg.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
