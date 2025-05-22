<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($userData) {
        try {
            // Validate required fields
            $requiredFields = ['fullname', 'username', 'email', 'password', 'gender', 'dob', 'voter_id', 'phone'];
            foreach ($requiredFields as $field) {
                if (empty($userData[$field])) {
                    throw new Exception("$field is required");
                }
            }

            // Check if username or email already exists
            $stmt = $this->db->executeQuery(
                "SELECT id FROM users WHERE username = ? OR email = ?",
                [$userData['username'], $userData['email']]
            );
            if ($stmt->fetch()) {
                throw new Exception("Username or email already exists");
            }

            // Hash password securely
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (fullname, username, email, gender, dob, voter_id, phone, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->executeQuery($sql, [
                $userData['fullname'],
                $userData['username'],
                $userData['email'],
                $userData['gender'],
                $userData['dob'],
                $userData['voter_id'],
                $userData['phone'],
                $hashedPassword
            ]);

            return ['success' => true, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function authenticate($username, $password) {
        try {
            // Debug log
            error_log("Starting authentication for user: " . $username);

            if (empty($username) || empty($password)) {
                error_log("Empty username or password");
                return ['success' => false, 'message' => 'Username and password are required'];
            }

            $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = $this->db->executeQuery($sql, [$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug user data
            error_log("User data found: " . ($user ? 'Yes' : 'No'));

            if (!$user) {
                error_log("User not found: " . $username);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Verify password field exists
            if (!isset($user['password'])) {
                error_log("Password field missing for user: " . $username);
                return ['success' => false, 'message' => 'Account configuration error'];
            }

            // Debug password verification
            error_log("Verifying password for user: " . $username);
            
            if (password_verify($password, $user['password'])) {
                error_log("Password verified successfully");
                // Remove sensitive data
                unset($user['password']);
                return ['success' => true, 'user' => $user];
            }

            error_log("Invalid password for user: " . $username);
            return ['success' => false, 'message' => 'Invalid credentials'];

        } catch (Exception $e) {
            error_log("Authentication Error: " . $e->getMessage());
            throw $e;
        }
    }
}
