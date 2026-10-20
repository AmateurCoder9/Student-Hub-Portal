<?php
require_once "db.php";

$search = trim($_GET['search'] ?? '');
$course = trim($_GET['course'] ?? '');

$query = "SELECT id, name, email, course, semester, mobile FROM students_crud WHERE 1=1";
$params = [];

if ($search != "") {
    $query .= " AND (name LIKE :s OR email LIKE :s OR mobile LIKE :s)";
    $params[':s'] = "%" . $search . "%";
}

if ($course != "") {
    $query .= " AND course = :c";
    $params[':c'] = $course;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students_list.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, ['ID', 'Name', 'Email', 'Course', 'Semester', 'Mobile']);

foreach ($rows as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
