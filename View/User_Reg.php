<?php
session_start();
require_once(__DIR__ . '/../Model/User.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    
    // Enhanced data validation with more specific error messages
    $userData = [
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'fullname' => trim($_POST['fullname'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'voter_id' => trim($_POST['voter_id'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'gender' => $_POST['gender'] ?? '',
        'dob' => $_POST['dob'] ?? ''
    ];

    // Validate password match first
    if ($userData['password'] !== $userData['confirm_password']) {
        $errors[] = "Passwords do not match";
    }

    // Only proceed if passwords match
    if (empty($errors)) {
        try {
            $user = new User();
            $result = $user->register($userData);
            
            if ($result['success']) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                if (isset($result['errors']) && is_array($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                } elseif (isset($result['message'])) {
                    $errors[] = $result['message'];
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        } catch(Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            $errors[] = "Registration failed. Please try again.";
        }
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