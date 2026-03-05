<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
require_once dirname(__DIR__, 2) . "/helpers/auth.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method.",
    ]);
    exit();
}

$userId = requireAuthJson();
$orderBasis = isset($_GET["orderBasis"]) ? (string) $_GET["orderBasis"] : "priority";
$order = isset($_GET["order"]) ? (string) $_GET["order"] : "DESC";

$db = new Task();
$tasks = $db->getTasks($userId, $orderBasis, $order);

echo json_encode([
    "success" => true,
    "tasks" => $tasks !== null ? $tasks : [],
]);
?>