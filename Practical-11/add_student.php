<?php
session_start();
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $course = $_POST["course"];
    $semester = (int)$_POST["semester"];
    $mobile = trim($_POST["mobile"]);

    if (empty($name) || empty($email) || empty($course) || empty($mobile)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $sql = "INSERT INTO students_crud (name, email, course, semester, mobile) 
                    VALUES (:name, :email, :course, :semester, :mobile)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":name" => $name,
                ":email" => $email,
                ":course" => $course,
                ":semester" => $semester,
                ":mobile" => $mobile
            ]);

            $_SESSION['msg'] = "Student added successfully!";
            header("Location: students.php");
            exit();
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Add New Student Record</p>
</header>

<div class="container">
    <h2>Student Details</h2>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="add_student.php">
        <label>Full Name:</label>
        <input type="text" name="name" required>

        <label>Email Address:</label>
        <input type="email" name="email" required>

        <label>Course:</label>
        <select name="course" required>
            <option value="IT">IT</option>
            <option value="CE">CE</option>
            <option value="CSE">CSE</option>
            <option value="EC">EC</option>
        </select>

        <label>Semester:</label>
        <input type="number" name="semester" min="1" max="8" value="3" required>

        <label>Mobile Number:</label>
        <input type="text" name="mobile" required>

        <br><br>
        <button type="submit">Save Student</button>
    </form>
    
    <p style="margin-top: 15px;"><a href="students.php">&larr; Back to Student Records</a></p>
</div>

</body>
</html>
