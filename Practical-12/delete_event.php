<?php
session_start();
require_once "db.php";

$id = $_GET["id"] ?? 0;

if ($id > 0) {
    try {
        
        $stmt = $conn->prepare("SELECT poster_image FROM events_crud WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            
            $del = $conn->prepare("DELETE FROM events_crud WHERE id = :id");
            $del->execute([":id" => $id]);

            
            if ($event['poster_image'] != "default_poster.png") {
                $file = "uploads/" . $event['poster_image'];
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            $_SESSION['msg'] = "Event deleted successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error deleting event: " . $e->getMessage();
    }
}

header("Location: events.php");
exit();
?>
