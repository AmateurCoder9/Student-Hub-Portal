<?php
require_once "auth.php";

$error = "";
$msg = $_GET["msg"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, role, status FROM users_p14 WHERE username = :u LIMIT 1");
    $stmt->execute([":u" => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        if ($user["status"] === "inactive") {
            $error = "This account is inactive. Please contact an admin.";
        } else {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            logAction($conn, "LOGIN_SUCCESS", "Logged in as " . $user["role"]);

            header("Location: dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header style="background: #1e1b4b;">
    <h1>StudentHub Portal</h1>
    <p>Admin Login</p>
</header>

<div class="container" style="max-width: 450px;">
    <h2>Sign In to Admin Panel</h2>

    <?php if ($msg == "admin_only"): ?>
        <div class="alert error">Admin access required! Please log in with admin account.</div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="admin" required>

        <label>Password:</label>
        <input type="password" name="password" value="Admin@1234" required>

        <br><br>
        <button type="submit">Login</button>
    </form>

    <div style="margin-top: 15px; font-size: 13px; color: #666; background: #fafafa; padding: 10px; border-radius: 4px;">
        <p style="margin: 0 0 5px 0;"><strong>Test Credentials:</strong></p>
        <p style="margin: 2px 0;">Admin: <code>admin</code> / <code>Admin@1234</code></p>
        <p style="margin: 2px 0;">Student: <code>rahul</code> / <code>Student@1234</code></p>
    </div>
</div>

</body>
</html>
