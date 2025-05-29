<?php
session_start();
include "../config/session_handler.php";
include "../Model/Database.php";


use Model\Database;

requireLogin();

try {
  
    $db = Database::getInstance();
    
  
    $stmt = $db->executeQuery("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception("User not found");
    }

    
    $active_elections = [];
    $voting_history = [];

} catch (PDOException $e) {
    error_log("Dashboard Database Error: " . $e->getMessage());
    die("Database error occurred. Please try again later.");
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Online Voting System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?></h1>
    </div>

    <div class="nav">
        <a href="Home.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <?php if($user['role'] === 'admin'): ?>
            <a href="manage_elections.php">Manage Elections</a>
            <a href="manage_users.php">Manage Users</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <div class="dashboard-grid">
        
            <div class="dashboard-section">
                <h2>Your Profile</h2>
                <div class="profile-info">
                    <?php if(isset($user['profile_photo']) && !empty($user['profile_photo'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
                    <?php endif; ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                    <p><strong>Voter ID:</strong> <?php echo htmlspecialchars($user['voter_id'] ?? 'N/A'); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo isset($user['dob']) ? htmlspecialchars($user['dob']) : 'N/A'; ?></p>
                    
                    <a href="edit_profile.php" class="btn">Edit Profile</a>
                </div>
            </div>

           
            <div class="dashboard-section">
                <h2>Active Elections</h2>
                <?php if($active_elections): ?>
                    <?php foreach($active_elections as $election): ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p><?php echo htmlspecialchars($election['description']); ?></p>
                            <p>Ends: <?php echo date('M j, Y g:i A', strtotime($election['end_date'])); ?></p>
                            <a href="cast_vote.php?election=<?php echo $election['id']; ?>" class="btn">Cast Vote</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No active elections at the moment.</p>
                <?php endif; ?>
            </div>

           
            <div class="dashboard-section">
                <h2>Your Voting History</h2>
                <?php if($voting_history): ?>
                    <table class="voting-history">
                        <thead>
                            <tr>
                                <th>Election</th>
                                <th>Candidate</th>
                                <th>Voted On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($voting_history as $vote): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vote['title']); ?></td>
                                    <td><?php echo htmlspecialchars($vote['candidate_name']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($vote['voted_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You haven't voted in any elections yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>