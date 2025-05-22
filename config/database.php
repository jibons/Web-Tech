<?php
class DatabaseConfig {
    private static $host = 'localhost';
    private static $dbname = 'voting_system';
    private static $username = 'root';
    private static $password = '';

    public static function getConnection() {
        try {
            $dsn = "mysql:host=".self::$host.";dbname=".self::$dbname.";charset=utf8mb4";
            $pdo = new PDO($dsn, self::$username, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
}
