<?php
require_once(__DIR__ . '/../Model/Database.php');

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private function validateInput($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = ucfirst($field) . " is required";
                continue;
            }
            
            $value = trim($data[$field]);
            
            switch ($rule) {
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Invalid email format";
                    }
                    break;
                case 'password':
                    if (strlen($value) < 6) {
                        $errors[] = "Password must be at least 6 characters";
                    }
                    break;
                case 'phone':
                    if (!preg_match('/^[0-9]{10,11}$/', $value)) {
                        $errors[] = "Invalid phone number";
                    }
                    break;
                case 'voter_id':
                    if (!preg_match('/^[A-Z0-9]{6,12}$/', $value)) {
                        $errors[] = "Invalid voter ID format";
                    }
                    break;
            }
        }
        return $errors;
    }

    public function register($userData) {
        try {
            // Debug log
            error_log("Registration attempt for: " . $userData['email']);

            // Validate input
            $validationRules = [
                'fullname' => 'required',
                'username' => 'required',
                'email' => 'email',
                'password' => 'password',
                'gender' => 'required',
                'dob' => 'required',
                'voter_id' => 'voter_id',
                'phone' => 'phone'
            ];

            $validationErrors = $this->validateInput($userData, $validationRules);
            if (!empty($validationErrors)) {
                error_log("Validation failed: " . json_encode($validationErrors));
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors
                ];
            }

            // Check existing user with detailed error
            try {
                $stmt = $this->db->executeQuery(
                    "SELECT id, email, username FROM users WHERE email = ? OR username = ?",
                    [$userData['email'], $userData['username']]
                );
                
                if ($existingUser = $stmt->fetch()) {
                    $message = $existingUser['email'] === $userData['email'] 
                        ? 'Email already registered' 
                        : 'Username already taken';
                    error_log("Duplicate user attempt: " . $message);
                    return [
                        'success' => false,
                        'message' => $message
                    ];
                }
            } catch (Exception $e) {
                error_log("Database check error: " . $e->getMessage());
                throw $e;
            }

            // Insert new user with error details
            try {
                $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
                // Ensure SQL matches exact column names
                $sql = "INSERT INTO users (fullname, username, email, password, gender, dob, voter_id, phone) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                error_log("Executing SQL: " . $sql); // Debug log
                error_log("Data: " . json_encode([
                    $userData['fullname'],
                    $userData['username'],
                    $userData['email'],
                    'HASHED',
                    $userData['gender'],
                    $userData['dob'],
                    $userData['voter_id'],
                    $userData['phone']
                ]));

                $stmt = $this->db->executeQuery($sql, [
                    $userData['fullname'],
                    $userData['username'],
                    $userData['email'],
                    $hashedPassword,
                    $userData['gender'],
                    $userData['dob'],
                    $userData['voter_id'],
                    $userData['phone']
                ]);

                return [
                    'success' => true,
                    'message' => 'Registration successful',
                    'user_id' => $this->db->lastInsertId()
                ];
            } catch (Exception $e) {
                error_log("Database insert error: " . $e->getMessage());
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    public function authenticate($email, $password) {
        try {
            // Input validation
            if (empty($email) || empty($password)) {
                error_log("Empty email or password");
                return [
                    'success' => false,
                    'message' => 'Email and password are required'
                ];
            }

            // Get user by email or username
            $stmt = $this->db->executeQuery(
                "SELECT id, username, email, password, fullname FROM users WHERE email = ? OR username = ?", 
                [$email, $email]
            );
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if user exists and has password
            if (!$user || !isset($user['password']) || empty($user['password'])) {
                error_log("User not found or invalid password hash");
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            // Type check and verify password
            $storedHash = (string)$user['password'];
            if (!is_string($storedHash) || !password_verify((string)$password, $storedHash)) {
                error_log("Password verification failed");
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            // Success - remove sensitive data
            unset($user['password']);
            return [
                'success' => true,
                'user' => $user,
                'message' => 'Login successful'
            ];
            
        } catch (Exception $e) {
            error_log("Authentication Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Authentication failed'
            ];
        }
    }

    public function vote($userId, $candidateId) {
        try {
            $this->db->beginTransaction();
            
            // Check if already voted
            $stmt = $this->db->executeQuery(
                "SELECT id FROM votes WHERE user_id = ?",
                [$userId]
            );

            if ($stmt->rowCount() > 0) {
                throw new Exception("Already voted");
            }

            // Record vote
            $this->db->executeQuery(
                "INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)",
                [$userId, $candidateId]
            );

            $this->db->commit();
            return ['success' => true, 'message' => 'Vote recorded'];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Voting Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}