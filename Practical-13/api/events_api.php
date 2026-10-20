<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../db.php";

$method = $_SERVER['REQUEST_METHOD'];

try {
    
    if ($method === 'GET') {
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $stmt = $conn->prepare("SELECT * FROM events_api WHERE title LIKE :q OR category LIKE :q ORDER BY event_date ASC");
            $stmt->execute([':q' => "%$q%"]);
        } else {
            $stmt = $conn->query("SELECT * FROM events_api ORDER BY event_date ASC");
        }
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $events]);
        exit();
    }

    
    if ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $title = trim($data["title"] ?? "");
        $category = $data["category"] ?? "Workshop";
        $event_date = $data["event_date"] ?? "";
        $description = trim($data["description"] ?? "");

        if (empty($title) || empty($event_date)) {
            echo json_encode(["status" => "error", "message" => "Title and date are required!"]);
            exit();
        }

        $sql = "INSERT INTO events_api (title, category, event_date, description) VALUES (:t, :c, :d, :desc)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":t" => $title,
            ":c" => $category,
            ":d" => $event_date,
            ":desc" => $description
        ]);

        echo json_encode(["status" => "success", "message" => "Event created successfully!"]);
        exit();
    }

    
    if ($method === 'DELETE') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM events_api WHERE id = :id");
            $stmt->execute([":id" => $id]);
            echo json_encode(["status" => "success", "message" => "Event deleted successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid event ID."]);
        }
        exit();
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit();
}
?>
