<?php

include "db.php";

$sql = "SELECT 
            registrations.id,
            students.name AS student_name,
            students.email,
            events.title AS event_name,
            events.event_date
        FROM registrations
        INNER JOIN students 
            ON registrations.student_id = students.id
        INNER JOIN events 
            ON registrations.event_id = events.id";

$stmt = $conn->prepare($sql);

$stmt->execute();

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>StudentHub - Student Events</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

    </style>

</head>

<body>

<h1>StudentHub - Student Event Registrations</h1>

<table>

    <tr>
        <th>Registration ID</th>
        <th>Student Name</th>
        <th>Email</th>
        <th>Event</th>
        <th>Event Date</th>
    </tr>

    <?php foreach ($records as $row) { ?>

    <tr>

        <td><?php echo $row['id']; ?></td>

        <td><?php echo $row['student_name']; ?></td>

        <td><?php echo $row['email']; ?></td>

        <td><?php echo $row['event_name']; ?></td>

        <td><?php echo $row['event_date']; ?></td>

    </tr>

    <?php } ?>

</table>

</body>

</html>