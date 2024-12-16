<?php
require_once dirname(__DIR__, 2) . "/db/db.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'];
$details = $data['details'];
$finishDate = $data['finishDate'];
$priority = $data['priority'];
$userId = $_SESSION['user_id'];

$db = new db();
$success = $db->addTask($userId, $title, $details, $finishDate, $priority);

header("Content-Type: application/json; charset=utf-8");
if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Unable to create task."]);
}
?>