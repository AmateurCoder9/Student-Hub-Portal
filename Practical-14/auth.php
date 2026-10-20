<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

function checkAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php?msg=admin_only");
        exit();
    }
}

function logAction($conn, $action, $target) {
    $user = $_SESSION['username'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    try {
        $stmt = $conn->prepare("INSERT INTO audit_logs (username, action, target, ip) VALUES (:u, :a, :t, :ip)");
        $stmt->execute([
            ":u" => $user,
            ":a" => $action,
            ":t" => $target,
            ":ip" => $ip
        ]);
    } catch (PDOException $e) {
        
    }
}
?>
