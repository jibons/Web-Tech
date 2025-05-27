<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($userId, $userName) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $userName;
    $_SESSION['last_activity'] = time();
}

function logout() {
    session_unset();
    session_destroy();
}

function checkAuth() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
    
    // Check session timeout (30 minutes)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        logout();
        header('Location: ../login.php');
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}
