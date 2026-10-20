<?php
session_start();
require_once "db.php";

$id = $_GET["id"] ?? 0;
$error = "";

$stmt = $conn->prepare("SELECT * FROM events_crud WHERE id = :id");
$stmt->execute([":id" => $id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $category = $_POST["category"];
    $description = trim($_POST["description"]);
    $event_date = $_POST["event_date"];
    $status = $_POST["status"];
    $poster_name = $event["poster_image"];

    if (empty($title) || empty($description) || empty($event_date)) {
        $error = "Please fill in all required fields.";
    } else {
        
        if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] == 0) {
            $file_name = $_FILES["poster"]["name"];
            $file_size = $_FILES["poster"]["size"];
            $file_tmp = $_FILES["poster"]["tmp_name"];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = ["jpg", "jpeg", "png", "webp"];

            if (!in_array($file_ext, $allowed_extensions)) {
                $error = "Only JPG, PNG and WEBP image files are allowed.";
            } elseif ($file_size > 2 * 1024 * 1024) {
                $error = "Poster size must be less than 2 MB.";
            } else {
                $new_file_name = "poster_" . time() . "_" . rand(100, 999) . "." . $file_ext;
                if (move_uploaded_file($file_tmp, "uploads/" . $new_file_name)) {
                    
                    if ($event["poster_image"] != "default_poster.png" && file_exists("uploads/" . $event["poster_image"])) {
                        unlink("uploads/" . $event["poster_image"]);
                    }
                    $poster_name = $new_file_name;
                }
            }
        }

        if (empty($error)) {
            try {
                $sql = "UPDATE events_crud 
                        SET title = :title, category = :category, description = :description, 
                            event_date = :event_date, status = :status, poster_image = :poster 
                        WHERE id = :id";
                $update = $conn->prepare($sql);
                $update->execute([
                    ":title" => $title,
                    ":category" => $category,
                    ":description" => $description,
                    ":event_date" => $event_date,
                    ":status" => $status,
                    ":poster" => $poster_name,
                    ":id" => $id
                ]);

                $_SESSION['msg'] = "Event updated successfully!";
                header("Location: events.php");
                exit();
            } catch (PDOException $e) {
                $error = "Update Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Edit Event Details</p>
</header>

<div class="container">
    <h2>Edit Event #<?php echo $event['id']; ?></h2>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

        <label>Category:</label>
        <select name="category" required>
            <option value="Workshop" <?php if ($event['category'] == 'Workshop') echo 'selected'; ?>>Workshop</option>
            <option value="Hackathon" <?php if ($event['category'] == 'Hackathon') echo 'selected'; ?>>Hackathon</option>
            <option value="Seminar" <?php if ($event['category'] == 'Seminar') echo 'selected'; ?>>Seminar</option>
            <option value="Cultural" <?php if ($event['category'] == 'Cultural') echo 'selected'; ?>>Cultural</option>
        </select>

        <label>Event Date:</label>
        <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required>

        <label>Status:</label>
        <select name="status">
            <option value="open" <?php if ($event['status'] == 'open') echo 'selected'; ?>>Open</option>
            <option value="closed" <?php if ($event['status'] == 'closed') echo 'selected'; ?>>Closed</option>
        </select>

        <label>Event Description:</label>
        <textarea name="description" rows="4" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;" required><?php echo htmlspecialchars($event['description']); ?></textarea>

        <p style="margin: 10px 0;">
            <strong>Current Poster:</strong><br>
            <img src="uploads/<?php echo $event['poster_image']; ?>" width="100" style="margin-top: 5px; border-radius: 4px;">
        </p>

        <label>Replace Poster (Optional):</label>
        <input type="file" name="poster" accept="image/*">

        <br><br>
        <button type="submit">Update Event</button>
    </form>
    
    <p style="margin-top: 15px;"><a href="events.php">&larr; Back to Events List</a></p>
</div>

</body>
</html>
