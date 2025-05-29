<?php
include "../config/session_handler.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['username'])) {
    error_log("User logged out: " . $_SESSION['username']);
}


$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

session_destroy();

$_SESSION['logout_message'] = "You have been successfully logged out.";

header("Location: login.php");
exit();