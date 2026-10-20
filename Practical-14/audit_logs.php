<?php
require_once "auth.php";
checkAdmin();

$logs = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Audit Logs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .navbar { background: #1e1b4b; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .nav-links a { color: #cbd5e1; text-decoration: none; margin-right: 15px; font-weight: bold; }
        .nav-links a:hover, .nav-links a.active { color: white; }
    </style>
</head>
<body>

<div class="navbar">
    <div style="font-size: 18px; font-weight: bold;">StudentHub Admin Panel</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="audit_logs.php" class="active">Audit Logs</a>
        <a href="logout.php" style="color: #f87171;">Logout</a>
    </div>
</div>

<div class="container" style="max-width: 950px;">
    <h2>Administrative Audit Log Trail</h2>
    <p>Records all important administrative actions for system traceability.</p>

    <table>
        <tr>
            <th>ID</th>
            <th>Admin User</th>
            <th>Action Taken</th>
            <th>Target Details</th>
            <th>IP Address</th>
            <th>Timestamp</th>
        </tr>
        <?php if (count($logs) > 0): ?>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td>#<?php echo $log['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($log['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($log['action']); ?></td>
                <td><code><?php echo htmlspecialchars($log['target']); ?></code></td>
                <td><?php echo $log['ip']; ?></td>
                <td><?php echo $log['log_time']; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #888;">No actions logged yet.</td>
            </tr>
        <?php endif; ?>
    </table>
    
    <p style="margin-top: 15px;"><a href="dashboard.php">&larr; Back to Dashboard</a></p>
</div>

</body>
</html>
