<?php
require_once "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = $_POST["role"] ?? "student";

    
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            
            $checkSql = "SELECT id FROM users WHERE email = :email OR username = :username";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([
                ":email" => $email,
                ":username" => $username
            ]);

            if ($checkStmt->rowCount() > 0) {
                $error = "This email or username is already registered!";
            } else {
                
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                
                $sql = "INSERT INTO users (name, username, email, password, role) 
                        VALUES (:name, :username, :email, :password, :role)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ":name" => $name,
                    ":username" => $username,
                    ":email" => $email,
                    ":password" => $hashed_password,
                    ":role" => $role
                ]);

                $message = "Registration successful! Your account has been created.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>User Registration</p>
</header>

<div class="container">
    <h2>Create Student Account</h2>

    <?php if ($message != ""): ?>
        <div class="alert success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label>Full Name:</label>
        <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

        <label>Username:</label>
        <input type="text" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">

        <label>Email Address:</label>
        <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required>

        <label>Register As:</label>
        <select name="role">
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
        </select>

        <br><br>
        <button type="submit">Register</button>
    </form>
    
    <p style="text-align: center; margin-top: 15px;">
        <a href="test_registration.php">View Test Cases & Results</a>
    </p>
</div>

</body>
</html>
