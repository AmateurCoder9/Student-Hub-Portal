<?php
require_once "auth_check.php";
checkAdmin(); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header style="background: #1e1b4b;">
    <h1>StudentHub Portal</h1>
    <p>Administrator Dashboard</p>
</header>

<div class="container" style="max-width: 600px;">
    <h2>Admin Panel</h2>
    <p><strong>Logged in Admin:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <p><strong>Role Verification:</strong> Verified Admin Privileges (RBAC Passed)</p>

    <div class="alert success" style="margin-top: 15px;">
        Access granted! Only users with the <code>admin</code> role can view this dashboard.
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

    <p><a href="dashboard_student.php" style="color: #4f46e5;">&rarr; Switch to Student View</a></p>
    <p><a href="logout.php" style="color: red; font-weight: bold;">Logout</a></p>
</div>

</body>
</html>
