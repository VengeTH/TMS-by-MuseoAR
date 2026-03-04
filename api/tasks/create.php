<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
session_start();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit();
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit();
}

$title = isset($_POST["title"]) ? trim((string) $_POST["title"]) : "";
$details = isset($_POST["details"]) ? trim((string) $_POST["details"]) : "";
$finishDate = isset($_POST["finishDate"]) ? trim((string) $_POST["finishDate"]) : "";
$priority = isset($_POST["priority"]) ? trim((string) $_POST["priority"]) : "low";
$userId = (int) $_SESSION["user_id"];

if ($title === "" || mb_strlen($title) > 200) {
    echo json_encode(["success" => false, "message" => "Task title is required and must be at most 200 characters."]);
    exit();
}

if ($finishDate === "") {
    echo json_encode(["success" => false, "message" => "Finish date is required."]);
    exit();
}

if (!in_array($priority, ["low", "medium", "high"], true)) {
    $priority = "low";
}

$db = new Task();
$success = $db->addTask($title, $details, $finishDate, $priority, $userId);

if ($success) {
    $taskId = $db->getLastInsertId();
    echo json_encode(["success" => true, "task_id" => $taskId]);
} else {
    echo json_encode(["success" => false, "message" => "Unable to create task."]);
}
?>