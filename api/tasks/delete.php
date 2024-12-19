<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $taskIds = $data['taskIds'] ?? [];
    $userId = $_SESSION['user_id'];

    if (!empty($taskIds)) {
        $db = new Task();
        $success = $db->deleteTasks($taskIds, $userId);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No tasks selected for deletion']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
