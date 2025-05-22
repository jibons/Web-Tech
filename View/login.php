<?php
require_once('../config/session_handler.php');
require_once('../Model/Database.php');
require_once('../Model/User.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log("Login page loaded");

$error = '';
$registration_success = isset($_SESSION['registration_success']) ? $_SESSION['registration_success'] : false;
unset($_SESSION['registration_success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        error_log("Login form submitted");
        $db = new Database();
        $userModel = new User($db);

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        error_log("Attempting login for username: " . $username);
        
        $result = $userModel->authenticate($username, $password);
        error_log("Authentication result: " . json_encode($result));
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['role'] = $result['user']['role'];
            
            error_log("Login successful. Redirecting to dashboard.");
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
            error_log("Login failed: " . $error);
        }
    } catch(Exception $e) {
        $error = "An error occurred. Please try again later.";
        error_log("Login Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
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
            <?php if($registration_success): ?>
                <div class="success">Registration successful! Please login.</div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
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