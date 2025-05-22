<?php
require_once('../Model/Database.php');
require_once('../Model/User.php');
require_once('../control/upload_handler.php');
require_once('../config/session_handler.php');

$db = new Database();
$userModel = new User($db);
$uploadHandler = new UploadHandler('../uploads/');

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $userData = [
            'fullname' => filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING),
            'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'gender' => $_POST['gender'],
            'dob' => $_POST['dob'],
            'voter_id' => $_POST['voter_id'],
            'phone' => $_POST['phone'],
            'password' => $_POST['password']
        ];

        $result = $userModel->register($userData);
        
        if ($result['success']) {
            $_SESSION['registration_success'] = true;
            header("Location: login.php");
            exit();
        } else {
            $errors[] = $result['message'];
        }
    } catch(Exception $e) {
        $errors[] = "Registration failed: " . $e->getMessage();
        error_log("Registration Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Voter Registration</h1>
    </div>

    <div class="nav">
        <a href="Home.php">Home</a>
        <a href="User_Reg.php">Register</a>
        <a href="login.php">Login</a>
    </div>

    <div class="container">
        <div class="main-content">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" 
                  class="registration-form" enctype="multipart/form-data">
                <?php if(!empty($errors)): ?>
                    <div class="error">
                        <?php foreach($errors as $err) echo htmlspecialchars($err) . "<br>"; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

                <div class="form-group">
                    <label for="voter_id">Voter ID:</label>
                    <input type="text" id="voter_id" name="voter_id" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="profile_photo">Profile Photo:</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="id_proof">ID Proof:</label>
                    <input type="file" id="id_proof" name="id_proof" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>

            <div class="links">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p><a href="Home.php">Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="js/validation.js"></script>
</body>
</html>