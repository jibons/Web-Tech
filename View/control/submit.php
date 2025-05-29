<?php
session_start();
include "../config/database.php";
include "../config/session_handler.php";


if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id, title FROM elections WHERE status = 'active' LIMIT 1");
$stmt->execute();
$election = $stmt->get_result()->fetch_assoc();

if (!$election) {
    $_SESSION['error'] = "No active election at the moment.";
    header("Location: ../Home.php");
    exit();
}

$stmt = $conn->prepare("SELECT id FROM votes WHERE election_id = ? AND user_id = ?");
$stmt->bind_param("ii", $election['id'], $_SESSION['user_id']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['error'] = "You have already voted in this election.";
    header("Location: ../Home.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, name, party, photo, bio FROM candidates WHERE election_id = ?");
$stmt->bind_param("i", $election['id']);
$stmt->execute();
$candidates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['candidate'])) {
            $candidate_id = $_POST['candidate'];
            
            // Verify candidate exists and belongs to active election
            $stmt = $conn->prepare("SELECT id FROM candidates WHERE id = ? AND election_id = ?");
            $stmt->bind_param("ii", $candidate_id, $election['id']);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows === 1) {
                // Record the vote
                $stmt = $conn->prepare("INSERT INTO votes (election_id, user_id, candidate_id) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $election['id'], $_SESSION['user_id'], $candidate_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Your vote has been recorded successfully!";
                    header("Location: ../Home.php");
                    exit();
                } else {
                    throw new Exception("Error recording your vote. Please try again.");
                }
            } else {
                throw new Exception("Invalid candidate selection.");
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log("Vote Error: " . $e->getMessage());
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
       Fatal error: Uncaught Error: Class "User" not found in C:\xampp\htdocs\WT\View\login.php:23 Stack trace: #0 {main} thrown in C:\xampp\htdocs\WT\View\login.php on line 23
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