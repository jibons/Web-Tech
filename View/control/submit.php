<?php
session_start();
include "../config/database.php";
include "../config/session_handler.php";
use Model\Database;
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
    if (isset($_POST['candidate'])) {
        $candidate_id = $_POST['candidate'];
        
       
        $stmt = $conn->prepare("SELECT id FROM candidates WHERE id = ? AND election_id = ?");
        $stmt->bind_param("ii", $candidate_id, $election['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 1) {
        
            $stmt = $conn->prepare("INSERT INTO votes (election_id, user_id, candidate_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $election['id'], $_SESSION['user_id'], $candidate_id);
            
            if ($stmt->execute()) {
            
                $action = "Vote Cast";
                $description = "Vote cast in election: " . $election['title'];
                $ip = $_SERVER['REMOTE_ADDR'];
                
                $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
                $log_stmt->bind_param("isss", $_SESSION['user_id'], $action, $description, $ip);
                $log_stmt->execute();

                $_SESSION['success'] = "Your vote has been recorded successfully!";
                header("Location: ../Home.php");
                exit();
            } else {
                $_SESSION['error'] = "Error recording your vote. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Invalid candidate selection.";
        }
    } else {
        $_SESSION['error'] = "Please select a candidate.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote</title>
    <link rel="stylesheet" href="../View/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Cast Your Vote</h1>
    </div>

    <div class="nav">
        <a href="../View/Home.php">Home</a>
        <a href="../View/User_Reg.php">Register</a>
        <a href="../View/login.php">Login</a>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?> </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?> </div>
        <?php endif; ?>
        <div class="voting-form">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2>Select Your Candidate</h2>
                <?php if (!empty($candidates)): ?>
                    <?php foreach ($candidates as $index => $candidate): ?>
                        <?php $inputId = 'candidate_' . $candidate['id']; ?>
                        <div class="candidate">
                            <input type="radio" name="candidate" id="<?php echo $inputId; ?>" value="<?php echo $candidate['id']; ?>" <?php echo $index === 0 ? 'required' : ''; ?>>
                            <label for="<?php echo $inputId; ?>"><?php echo htmlspecialchars($candidate['name']); ?></label>
                            <p><?php echo htmlspecialchars($candidate['party']); ?></p>
                            <?php if (!empty($candidate['photo'])): ?>
                                <img src="../../uploads/photos/<?php echo htmlspecialchars($candidate['photo']); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>" style="max-width:100px;">
                            <?php endif; ?>
                            <?php if (!empty($candidate['bio'])): ?>
                                <p><?php echo htmlspecialchars($candidate['bio']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No candidates available for this election.</p>
                <?php endif; ?>
                <button type="submit" class="submit-btn">Submit Vote</button>
            </form>
        </div>
        <div class="links">
            <a href="../View/Home.php">Back to Home</a>
        </div>
    </div>
</body>
</html>