<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Welcome to Online Voting System</h1>
    </div>

    <div class="nav">
        <a href="Home.php">Home</a>
        <?php if(!isLoggedIn()): ?>
            <a href="User_Reg.php">Register</a>
            <a href="login.php">Login</a>
        <?php else: ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="submit.php">Cast Vote</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="about-section">
            <h2>About Online Voting System</h2>
            <div class="about-content">
                <div class="about-text">
                    <p class="lead">Welcome to our secure and easy-to-use online voting platform.</p>
                    <div class="features-grid">
                        <div class="feature-item">
                            <i class="feature-icon register-icon"></i>
                            <h3>Register as a voter</h3>
                            <p>Quick and secure registration process with ID verification</p>
                        </div>
                        <div class="feature-item">
                            <i class="feature-icon login-icon"></i>
                            <h3>Login securely</h3>
                            <p>Protected access with advanced security measures</p>
                        </div>
                        <div class="feature-item">
                            <i class="feature-icon vote-icon"></i>
                            <h3>Cast your vote</h3>
                            <p>Simple and confidential voting process</p>
                        </div>
                        <div class="feature-item">
                            <i class="feature-icon results-icon"></i>
                            <h3>View results</h3>
                            <p>Real-time election results and statistics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <?php if(isLoggedIn()): ?>
                <div class="user-info">
                    <p>You are logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <?php if(!hasVoted()): ?>
                        <a href="submit.php" class="btn">Cast Your Vote</a>
                    <?php else: ?>
                        <p class="info">You have already cast your vote.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="login-prompt">
                    <h2>User Access</h2>
                    <p>If you have an account, please <a href="login.php">Login</a></p>
                    <p>If you don't have an account, please <a href="User_Reg.php">Register</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
   
    function isLoggedIn() {
        return isset($_SESSION) && isset($_SESSION['username']);
    }
    function hasVoted() {
        
        return false; 
    }
    ?>
</body>
</html>