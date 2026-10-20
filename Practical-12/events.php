<?php
session_start();
require_once "db.php";

$category = $_GET['category'] ?? '';

$query = "SELECT * FROM events_crud WHERE 1=1";
$params = [];

if ($category != "") {
    $query .= " AND category = :cat";
    $params[':cat'] = $category;
}

$query .= " ORDER BY event_date ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Event Management System (CRUD)</p>
</header>

<div class="container" style="max-width: 950px;">
    <h2>University Events</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert success"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <form method="GET" action="events.php" style="display: flex; gap: 10px;">
            <select name="category" onchange="this.form.submit();">
                <option value="">All Categories</option>
                <option value="Workshop" <?php if ($category == 'Workshop') echo 'selected'; ?>>Workshop</option>
                <option value="Hackathon" <?php if ($category == 'Hackathon') echo 'selected'; ?>>Hackathon</option>
                <option value="Seminar" <?php if ($category == 'Seminar') echo 'selected'; ?>>Seminar</option>
                <option value="Cultural" <?php if ($category == 'Cultural') echo 'selected'; ?>>Cultural</option>
            </select>
            <?php if ($category != ""): ?>
                <a href="events.php" style="padding: 8px 12px; background: #666; color: white; text-decoration: none; border-radius: 4px;">Clear Filter</a>
            <?php endif; ?>
        </form>

        <a href="add_event.php" style="background: #4f46e5; color: white; padding: 10px 16px; text-decoration: none; border-radius: 4px; font-weight: bold;">+ Add New Event</a>
    </div>

    
    <table>
        <tr>
            <th>Poster</th>
            <th>Title</th>
            <th>Category</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php if (count($events) > 0): ?>
            <?php foreach ($events as $ev): ?>
            <tr>
                <td style="text-align: center; width: 100px;">
                    <?php 
                        $poster = "uploads/" . $ev['poster_image'];
                        if (!file_exists($poster) || empty($ev['poster_image'])) {
                            $poster = "uploads/default_poster.png";
                        }
                    ?>
                    <img src="<?php echo $poster; ?>" alt="poster" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($ev['title']); ?></strong>
                    <br><small style="color: #666;"><?php echo htmlspecialchars(substr($ev['description'], 0, 70)); ?>...</small>
                </td>
                <td><?php echo htmlspecialchars($ev['category']); ?></td>
                <td><?php echo date('d M Y', strtotime($ev['event_date'])); ?></td>
                <td>
                    <span style="padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; background: <?php echo $ev['status'] == 'open' ? '#d4edda' : '#eee'; ?>; color: <?php echo $ev['status'] == 'open' ? '#155724' : '#666'; ?>;">
                        <?php echo strtoupper($ev['status']); ?>
                    </span>
                </td>
                <td>
                    <a href="edit_event.php?id=<?php echo $ev['id']; ?>" style="color: blue; margin-right: 8px;">Edit</a>
                    <a href="delete_event.php?id=<?php echo $ev['id']; ?>" onclick="return confirm('Are you sure you want to delete this event?');" style="color: red;">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #888;">No events found in this category.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
