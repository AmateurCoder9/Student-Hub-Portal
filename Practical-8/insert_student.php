<?php

require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = $_POST["student_id"];
    $event_id = $_POST["event_id"];

    try {

        
        $sql = "INSERT INTO registrations (student_id, event_id)
                VALUES (:student_id, :event_id)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":student_id" => $student_id,
            ":event_id" => $event_id
        ]);

        $message = "Registration successful!";

    } catch (PDOException $e) {

        if ($e->getCode() == 23000) {
            $message = "Student is already registered for this event.";
        } else {
            $message = "Registration failed.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub Registration</title>
</head>

<body>

<h1>StudentHub Event Registration</h1>

<?php if ($message != ""): ?>
    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>
<?php endif; ?>

<form method="POST">

    <label>Student ID:</label>
    <input type="number" name="student_id" required>

    <br><br>

    <label>Event ID:</label>
    <input type="number" name="event_id" required>

    <br><br>

    <button type="submit">Register</button>

</form>

</body>
</html>
