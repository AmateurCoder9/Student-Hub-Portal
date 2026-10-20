<?php
session_start();

require_once "db.php";

if (isset($_SESSION['user_id'])) {
    $clear = $conn->prepare("UPDATE users_auth SET remember_token = NULL WHERE id = :id");
    $clear->execute([":id" => $_SESSION['user_id']]);
}

$_SESSION = [];
session_destroy();

if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

header("Location: login.php?msg=logout");
exit();
?>
