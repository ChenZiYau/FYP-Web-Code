<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Kill the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session
session_destroy();

// 4. Send them back to the login page
header("Location: login.php");
exit;