<?php
session_start();
require_once(__DIR__ . '/../Model/Database.php');
require_once(__DIR__ . '/../Model/User.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $db = new Database();
    $user = new User();
    
    // Get complete user profile data
    $userData = $db->executeQuery(
        "SELECT fullname, username, email, gender, dob, voter_id, phone FROM users WHERE id = ?", 
        [$_SESSION['user_id']]
    )->fetch(PDO::FETCH_ASSOC);

    // Get voting status
    $hasVoted = $db->executeQuery(
        "SELECT id FROM votes WHERE user_id = ?",
        [$_SESSION['user_id']]
    )->rowCount() > 0;

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $_SESSION['error'] = "Error loading dashboard";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Online Voting System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .profile-section, .voting-status {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .profile-info p {
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .profile-info strong {
            color: #555;
            min-width: 120px;
        }
        .voted {
            color: #28a745;
            font-weight: bold;
            padding: 10px;
            background: #d4edda;
            border-radius: 4px;
        }
        .not-voted {
            color: #dc3545;
            font-weight: bold;
            padding: 10px;
            background: #f8d7da;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($userData['fullname'] ?? 'User'); ?></h1>
    </div>

    <div class="nav">
        <a href="vote.php">Cast Vote</a>
        <a href="results.php">View Results</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="profile-section">
                <h2>Your Profile</h2>
                <div class="profile-info">
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($userData['fullname'] ?? ''); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username'] ?? ''); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($userData['gender'] ?? '')); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($userData['dob'] ?? ''); ?></p>
                    <p><strong>Voter ID:</strong> <?php echo htmlspecialchars($userData['voter_id'] ?? ''); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($userData['phone'] ?? ''); ?></p>
                </div>
            </div>

            <div class="voting-status">
                <h2>Voting Status</h2>
                <?php if($hasVoted): ?>
                    <p class="voted">You have already cast your vote.</p>
                <?php else: ?>
                    <p class="not-voted">You haven't voted yet.</p>
                    <a href="vote.php" class="btn">Cast Your Vote</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>