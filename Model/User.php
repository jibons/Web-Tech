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
            error_log("Authentication attempt for: " . $username);

            // Basic validation
            if (empty($username) || empty($password)) {
                return ['success' => false, 'message' => 'Username and password are required'];
            }

            // Use prepared statement for secure query
            $sql = "SELECT id, username, password, role, fullname FROM users WHERE username = ? OR email = ? LIMIT 1";
            $stmt = $this->db->executeQuery($sql, [$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("User query executed, found: " . ($user ? 'Yes' : 'No'));

            if (!$user) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Explicitly check password field
            if (!isset($user['password']) || empty($user['password'])) {
                error_log("Password field missing or empty for user: " . $username);
                // Try to fix the issue by updating password
                $this->db->executeQuery(
                    "UPDATE users SET password = ? WHERE username = ?",
                    [password_hash($password, PASSWORD_DEFAULT), $username]
                );
                return ['success' => false, 'message' => 'Please try logging in again'];
            }

            // Verify password
            if (password_verify($password, $user['password'])) {
                error_log("Password verified for user: " . $username);
                unset($user['password']); // Remove password from session data
                return ['success' => true, 'user' => $user];
            }

            error_log("Password verification failed for user: " . $username);
            return ['success' => false, 'message' => 'Invalid credentials'];

        } catch (Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            throw new Exception('Authentication failed');
        }
    }
}
