<?php
namespace Model;

use PDO;
use PDOException;
use Exception;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register($userData) {
        try {
           
            $requiredFields = ['fullname', 'username', 'email', 'password', 'gender', 'dob', 'voter_id', 'phone'];
            foreach ($requiredFields as $field) {
                if (empty($userData[$field])) {
                    throw new Exception("$field is required");
                }
            }

          
            $stmt = $this->db->executeQuery(
                "SELECT id FROM users WHERE username = ? OR email = ?",
                [$userData['username'], $userData['email']]
            );
            if ($stmt->fetch()) {
                throw new Exception("Username or email already exists");
            }

           
            $hashedPassword = password_hash(
                $userData['password'],
                PASSWORD_DEFAULT,
                ['cost' => 12]
            );
            
  
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

            error_log("User registered successfully: " . $userData['username']);
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkDatabaseConnection() {
        try {
            $stmt = $this->db->executeQuery('SELECT 1');
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Database connection check failed: " . $e->getMessage());
            return false;
        }
    }

    public function authenticate($username, $password) {
        try {
         
            if (empty($username) || empty($password)) {
                return ['success' => false, 'message' => 'Username and password are required'];
            }

            $sql = "SELECT id, username, password, fullname, email FROM users WHERE username = ? OR email = ?";
            $stmt = $this->db->executeQuery($sql, [$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Login attempt for: " . $username);
            error_log("User found: " . ($user ? 'Yes' : 'No'));

            if ($user && isset($user['password'])) {
                if (password_verify($password, $user['password'])) {
                    error_log("Password verified successfully");
                    return [
                        'success' => true,
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'fullname' => $user['fullname'],
                            'email' => $user['email']
                        ]
                    ];
                }
            }

            return ['success' => false, 'message' => 'Invalid username or password'];
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    private function emailExists($email) {
        $result = $this->db->executeQuery("SELECT id FROM users WHERE email = ?", [$email]);
        return $result->rowCount() > 0;
    }

    private function usernameExists($username) {
        $result = $this->db->executeQuery("SELECT id FROM users WHERE username = ?", [$username]);
        return $result->rowCount() > 0;
    }
}