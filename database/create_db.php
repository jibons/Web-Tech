
<?php

$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read SQL file
    $sql = file_get_contents('voting_db.sql');
    
    // Execute SQL commands
    $conn->exec($sql);
    echo "Database and tables created successfully";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>