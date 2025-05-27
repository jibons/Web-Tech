<?php
require_once('../../config/session_handler.php');
require_once('../../Model/User.php');
require_once('../../Model/Database.php');

// Check if user is logged in
checkAuth();

// Check if user has already voted
try {
    $db = new Database();
    $hasVoted = $db->executeQuery(
        "SELECT id FROM votes WHERE user_id = ?",
        [$_SESSION['user_id']]
    )->rowCount() > 0;

    if ($hasVoted) {
        $_SESSION['error'] = "You have already cast your vote";
        header('Location: ../dashboard.php');
        exit();
    }

    // Get list of candidates
    $candidates = $db->executeQuery(
        "SELECT id, name, party, position, manifesto FROM candidates WHERE status = 'active'"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "System error: " . $e->getMessage();
    header('Location: ../dashboard.php');
    exit();
}

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['candidate_id']) || empty($_POST['candidate_id'])) {
            throw new Exception("Please select a candidate");
        }

        $user = new User();
        $result = $user->vote($_SESSION['user_id'], $_POST['candidate_id']);
        
        if ($result['success']) {
            $_SESSION['success'] = "Your vote has been recorded successfully!";
            header('Location: ../dashboard.php');
            exit();
        } else {
            throw new Exception($result['message']);
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cast Your Vote</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .candidate-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .candidate-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .candidate-info {
            margin: 10px 0;
        }
        .party-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .vote-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .vote-button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cast Your Vote</h1>
    </div>

    <div class="nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="../results.php">Results</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="candidates-grid">
                <?php if (!empty($candidates)): ?>
                    <?php foreach ($candidates as $candidate): ?>
                        <div class="candidate-card">
                            <div class="candidate-info">
                                <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                                <span class="party-badge">
                                    <?php echo htmlspecialchars($candidate['party']); ?>
                                </span>
                                <p><strong>Position:</strong> <?php echo htmlspecialchars($candidate['position']); ?></p>
                                <p><?php echo htmlspecialchars($candidate['manifesto']); ?></p>
                            </div>
                            <button type="submit" name="candidate_id" value="<?php echo $candidate['id']; ?>" 
                                    class="vote-button">
                                Vote for <?php echo htmlspecialchars($candidate['name']); ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No candidates are currently available for voting.</p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>