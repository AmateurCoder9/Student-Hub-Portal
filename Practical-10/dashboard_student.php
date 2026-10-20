<?php
require_once "auth_check.php";
checkLogin(); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Student Dashboard</p>
</header>

<div class="container" style="max-width: 600px;">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p><strong>Your Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
    <p><strong>Session ID:</strong> <code><?php echo session_id(); ?></code></p>
    <p><strong>Timeout Status:</strong> Session will expire after 15 minutes of inactivity.</p>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

    <p><a href="dashboard_admin.php" style="color: #4f46e5;">&rarr; Try opening Admin Panel (RBAC test)</a></p>
    <p><a href="logout.php" style="color: red; font-weight: bold;">Logout</a></p>
</div>

</body>
</html>
