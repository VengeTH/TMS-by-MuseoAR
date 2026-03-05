<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
require_once dirname(__DIR__, 2) . "/helpers/auth.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

$userId = requireAuthJson();

$data = json_decode(file_get_contents("php://input"), true);
$taskIds = is_array($data) && isset($data["taskIds"]) && is_array($data["taskIds"]) ? $data["taskIds"] : [];

if (!empty($taskIds)) {
    $db = new Task();
    $success = $db->deleteTasks($taskIds, $userId);
    echo json_encode(["success" => $success]);
} else {
    echo json_encode(["success" => false, "message" => "No tasks selected for deletion"]);
}
?>
