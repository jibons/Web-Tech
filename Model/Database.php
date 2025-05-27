<?php
class Database {
    private $conn = null;
    private static $instance = null;
    
    public function __construct() {
        $this->connect();
    }

    public function connect() {
        try {
            if ($this->conn === null) {
                $this->conn = new PDO(
                    "mysql:host=localhost;dbname=voting_system",
                    "root",
                    "",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            }
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function executeQuery($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollBack() {
        return $this->conn->rollBack();
    }
}
