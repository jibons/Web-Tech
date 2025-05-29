<?php
namespace Model;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection = null;

    private function __construct() {
        try {
           
            $tempPdo = new PDO("mysql:host=localhost", "root", "");
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS voting_system");
            
            
            $this->connection = new PDO(
                "mysql:host=localhost;dbname=voting_system;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

           
            $this->createTables();

        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            throw new PDOException("Database connection failed. Please try again.");
        }
    }

    private function createTables() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            fullname VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            gender VARCHAR(10),
            dob DATE,
            voter_id VARCHAR(50),
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->connection->exec($sql);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function executeQuery($sql, $params = []) {
        if (!$this->connection) {
            throw new PDOException("No database connection");
        }

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw $e;
        }
    }
}