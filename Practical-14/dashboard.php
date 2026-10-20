<?php
require_once "auth.php";
checkAdmin(); 

if (isset($_GET['toggle_id'])) {
    $uid = (int)$_GET['toggle_id'];
    $stmt = $conn->prepare("SELECT username, status FROM users_p14 WHERE id = :id");
    $stmt->execute([":id" => $uid]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u) {
        $newStatus = ($u['status'] == 'active') ? 'inactive' : 'active';
        $update = $conn->prepare("UPDATE users_p14 SET status = :s WHERE id = :id");
        $update->execute([":s" => $newStatus, ":id" => $uid]);

        logAction($conn, "TOGGLE_USER_STATUS", "User " . $u['username'] . " -> " . $newStatus);
        header("Location: dashboard.php");
        exit();
    }
}

$totalUsers = $conn->query("SELECT COUNT(*) FROM users_p14")->fetchColumn();
$totalStudents = $conn->query("SELECT COUNT(*) FROM students_p14")->fetchColumn();
$totalEvents = $conn->query("SELECT COUNT(*) FROM events_p14")->fetchColumn();
$totalReg = $conn->query("SELECT COUNT(*) FROM registrations_p14")->fetchColumn();

$users = $conn->query("SELECT * FROM users_p14 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$recent = $conn->query("SELECT * FROM registrations_p14 ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat-box { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-align: center; }
        .stat-number { font-size: 28px; font-weight: bold; color: #4f46e5; margin: 5px 0; }
        .navbar { background: #1e1b4b; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .nav-links a { color: #cbd5e1; text-decoration: none; margin-right: 15px; font-weight: bold; }
        .nav-links a:hover, .nav-links a.active { color: white; }
    </style>
</head>
<body>

<div class="navbar">
    <div style="font-size: 18px; font-weight: bold;">StudentHub Admin Panel</div>
    <div class="nav-links">
        
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="audit_logs.php">Audit Logs</a>
        <a href="../Practical-11/students.php">Manage Students</a>
        <a href="../Practical-12/events.php">Manage Events</a>
        <a href="logout.php" style="color: #f87171;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
    </div>
</div>

<div class="container" style="max-width: 950px;">
    <h2>Dashboard Overview</h2>

    
    <div class="stats-grid">
        <div class="stat-box">
            <div>Total Users</div>
            <div class="stat-number"><?php echo $totalUsers; ?></div>
        </div>
        <div class="stat-box">
            <div>Students</div>
            <div class="stat-number"><?php echo $totalStudents; ?></div>
        </div>
        <div class="stat-box">
            <div>Events</div>
            <div class="stat-number"><?php echo $totalEvents; ?></div>
        </div>
        <div class="stat-box">
            <div>Registrations</div>
            <div class="stat-number"><?php echo $totalReg; ?></div>
        </div>
    </div>

    
    <h3>User Accounts & Status</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['role']); ?></td>
            <td>
                <span style="color: <?php echo $u['status'] == 'active' ? 'green' : 'red'; ?>; font-weight: bold;">
                    <?php echo ucfirst($u['status']); ?>
                </span>
            </td>
            <td>
                <a href="dashboard.php?toggle_id=<?php echo $u['id']; ?>" style="color: blue; text-decoration: underline;">
                    Set <?php echo $u['status'] == 'active' ? 'Inactive' : 'Active'; ?>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br>
    
    <h3>Latest Event Registrations</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Event Registered</th>
            <th>Date & Time</th>
        </tr>
        <?php foreach ($recent as $r): ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($r['student_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($r['event_title']); ?></td>
            <td><?php echo $r['registered_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
