<?php
session_start();
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $category = $_POST["category"];
    $description = trim($_POST["description"]);
    $event_date = $_POST["event_date"];
    $status = $_POST["status"];
    $poster_name = "default_poster.png";

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
                $error = "Only JPG, PNG, and WEBP image formats are supported.";
            } 
            
            elseif ($file_size > 2 * 1024 * 1024) {
                $error = "Poster size must be less than 2 MB.";
            } else {
                
                $new_file_name = "poster_" . time() . "_" . rand(100, 999) . "." . $file_ext;
                $destination = "uploads/" . $new_file_name;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $poster_name = $new_file_name;
                } else {
                    $error = "Failed to upload poster image to server.";
                }
            }
        }

        if (empty($error)) {
            try {
                $sql = "INSERT INTO events_crud (title, category, description, event_date, status, poster_image)
                        VALUES (:title, :category, :description, :event_date, :status, :poster)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ":title" => $title,
                    ":category" => $category,
                    ":description" => $description,
                    ":event_date" => $event_date,
                    ":status" => $status,
                    ":poster" => $poster_name
                ]);

                $_SESSION['msg'] = "Event added successfully!";
                header("Location: events.php");
                exit();
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Add Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Add New Event</p>
</header>

<div class="container">
    <h2>Event Information</h2>

    <?php if ($error != ""): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title:</label>
        <input type="text" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">

        <label>Category:</label>
        <select name="category" required>
            <option value="Workshop">Workshop</option>
            <option value="Hackathon">Hackathon</option>
            <option value="Seminar">Seminar</option>
            <option value="Cultural">Cultural</option>
        </select>

        <label>Event Date:</label>
        <input type="date" name="event_date" required value="<?php echo htmlspecialchars($_POST['event_date'] ?? date('Y-m-d')); ?>">

        <label>Status:</label>
        <select name="status">
            <option value="open">Open</option>
            <option value="closed">Closed</option>
        </select>

        <label>Event Description:</label>
        <textarea name="description" rows="4" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

        <label>Upload Event Poster (Max 2MB - JPG, PNG):</label>
        <input type="file" name="poster" accept="image/*">

        <br><br>
        <button type="submit">Save Event</button>
    </form>
    
    <p style="margin-top: 15px;"><a href="events.php">&larr; Back to Events List</a></p>
</div>

</body>
</html>
