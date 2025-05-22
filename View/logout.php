<?php
require_once('../config/session_handler.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the logout action if user was logged in
if (isset($_SESSION['username'])) {
    error_log("User logged out: " . $_SESSION['username']);
}

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Set logout success message
$_SESSION['logout_message'] = "You have been successfully logged out.";

// Redirect to login page
header("Location: login.php");
exit();
?>
