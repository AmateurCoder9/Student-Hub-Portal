<?php
session_start();
require_once "db.php";

$id = $_GET["id"] ?? 0;

if ($id > 0) {
    try {
        $stmt = $conn->prepare("DELETE FROM students_crud WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $_SESSION['msg'] = "Student deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error deleting student: " . $e->getMessage();
    }
}

header("Location: students.php");
exit();
?>
