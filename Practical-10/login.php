<?php
require_once "auth_check.php";

$error = "";
$msg = $_GET["msg"] ?? "";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_student.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    if (empty($username) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        
        $sql = "SELECT id, username, email, password, role FROM users_auth WHERE username = :u OR email = :u LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([":u" => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($user && password_verify($password, $user["password"])) {
            
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            
            if ($remember) {
                $token = bin2hex(random_bytes(16));
                $updateToken = $conn->prepare("UPDATE users_auth SET remember_token = :t, last_login = NOW() WHERE id = :id");
                $updateToken->execute([":t" => $token, ":id" => $user["id"]]);
                setcookie("remember_user", $token, time() + (86400 * 7), "/");
            }

            
            if ($user["role"] === "admin") {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_student.php");
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>User Login</p>
</header>

<div class="container">
    <h2>Login to Your Account</h2>

    <?php if ($msg == "timeout"): ?>
        <div class="alert error">Your session timed out due to inactivity. Please log in again.</div>
    <?php elseif ($msg == "access_denied"): ?>
        <div class="alert error">Access denied! You do not have permission to view that page.</div>
    <?php elseif ($msg == "login_required"): ?>
        <div class="alert error">Please log in to continue.</div>
    <?php elseif ($msg == "logout"): ?>
        <div class="alert success">You have been logged out successfully.</div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Username or Email:</label>
        <input type="text" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">

        <label>Password:</label>
        <input type="password" name="password" required>

        <p style="margin-top: 10px;">
            <label style="font-weight: normal; display: inline;">
                <input type="checkbox" name="remember"> Remember me for 7 days
            </label>
        </p>

        <button type="submit">Sign In</button>
    </form>

    <div style="margin-top: 20px; font-size: 13px; color: #666; background: #fafafa; padding: 10px; border-radius: 4px;">
        <p style="margin: 0 0 5px 0;"><strong>Demo Accounts:</strong></p>
        <p style="margin: 2px 0;">Admin: <code>admin_user</code> / <code>Admin@1234</code></p>
        <p style="margin: 2px 0;">Student: <code>student_user</code> / <code>Student@1234</code></p>
    </div>
</div>

</body>
</html>
