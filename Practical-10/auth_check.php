<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

$timeout_duration = 900;

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
        
        session_unset();
        session_destroy();
        header("Location: login.php?msg=timeout");
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?msg=login_required");
        exit();
    }
}

function checkAdmin() {
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: login.php?msg=access_denied");
        exit();
    }
}
?>
