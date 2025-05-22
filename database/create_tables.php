<?php
// dashboard.php
// Database connection
include ('../config/database.php');

// Read SQL file
$sql = file_get_contents('tables.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Tables created successfully";
} else {
    echo "Error creating tables: " . $conn->error;
}

$conn->close();
?>