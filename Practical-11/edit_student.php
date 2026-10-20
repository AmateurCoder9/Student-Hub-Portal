<?php
session_start();
require_once "db.php";

$id = $_GET["id"] ?? 0;
$error = "";

$stmt = $conn->prepare("SELECT * FROM students_crud WHERE id = :id");
$stmt->execute([":id" => $id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: students.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $course = $_POST["course"];
    $semester = (int)$_POST["semester"];
    $mobile = trim($_POST["mobile"]);

    if (empty($name) || empty($email) || empty($course) || empty($mobile)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $sql = "UPDATE students_crud 
                    SET name = :name, email = :email, course = :course, semester = :semester, mobile = :mobile 
                    WHERE id = :id";
            $update = $conn->prepare($sql);
            $update->execute([
                ":name" => $name,
                ":email" => $email,
                ":course" => $course,
                ":semester" => $semester,
                ":mobile" => $mobile,
                ":id" => $id
            ]);

            $_SESSION['msg'] = "Student updated successfully!";
            header("Location: students.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Edit Student Record</p>
</header>

<div class="container">
    <h2>Edit Details (ID: <?php echo $student['id']; ?>)</h2>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

        <label>Email Address:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

        <label>Course:</label>
        <select name="course" required>
            <option value="IT" <?php if ($student['course'] == 'IT') echo 'selected'; ?>>IT</option>
            <option value="CE" <?php if ($student['course'] == 'CE') echo 'selected'; ?>>CE</option>
            <option value="CSE" <?php if ($student['course'] == 'CSE') echo 'selected'; ?>>CSE</option>
            <option value="EC" <?php if ($student['course'] == 'EC') echo 'selected'; ?>>EC</option>
        </select>

        <label>Semester:</label>
        <input type="number" name="semester" min="1" max="8" value="<?php echo $student['semester']; ?>" required>

        <label>Mobile Number:</label>
        <input type="text" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>" required>

        <br><br>
        <button type="submit">Update Student</button>
    </form>
    
    <p style="margin-top: 15px;"><a href="students.php">&larr; Back to Student Records</a></p>
</div>

</body>
</html>
