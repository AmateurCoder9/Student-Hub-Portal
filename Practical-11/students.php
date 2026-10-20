<?php
session_start();
require_once "db.php";

$search = trim($_GET['search'] ?? '');
$course = trim($_GET['course'] ?? '');

$query = "SELECT * FROM students_crud WHERE 1=1";
$params = [];

if ($search != "") {
    $query .= " AND (name LIKE :s OR email LIKE :s OR mobile LIKE :s)";
    $params[':s'] = "%" . $search . "%";
}

if ($course != "") {
    $query .= " AND course = :c";
    $params[':c'] = $course;
}

$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Manage Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Student Management System (CRUD)</p>
</header>

<div class="container" style="max-width: 950px;">
    <h2>Student Records</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert success"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="students.php" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <input type="text" name="search" placeholder="Search name, email, or mobile..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 2;">
        
        <select name="course" style="flex: 1;">
            <option value="">All Courses</option>
            <option value="IT" <?php if ($course == "IT") echo "selected"; ?>>IT</option>
            <option value="CE" <?php if ($course == "CE") echo "selected"; ?>>CE</option>
            <option value="CSE" <?php if ($course == "CSE") echo "selected"; ?>>CSE</option>
            <option value="EC" <?php if ($course == "EC") echo "selected"; ?>>EC</option>
        </select>

        <button type="submit" style="width: auto;">Search</button>
        <?php if ($search != "" || $course != ""): ?>
            <a href="students.php" style="padding: 10px; background: #666; color: white; text-decoration: none; border-radius: 4px;">Reset</a>
        <?php endif; ?>
    </form>

    <div style="margin-bottom: 15px; display: flex; justify-content: space-between;">
        <a href="add_student.php" style="background: #4f46e5; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-weight: bold;">+ Add New Student</a>
        <a href="export_csv.php?search=<?php echo urlencode($search); ?>&course=<?php echo urlencode($course); ?>" style="background: #0f766e; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-weight: bold;">Export to CSV</a>
    </div>

    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Sem</th>
            <th>Mobile</th>
            <th>Actions</th>
        </tr>
        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $row): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['course']); ?></td>
                <td><?php echo $row['semester']; ?></td>
                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                <td>
                    <a href="edit_student.php?id=<?php echo $row['id']; ?>" style="color: blue; margin-right: 10px;">Edit</a>
                    <a href="delete_student.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete <?php echo addslashes($row['name']); ?>?');" style="color: red;">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #888;">No students found matching your search.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
