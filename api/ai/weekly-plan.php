<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
require_once dirname(__DIR__, 2) . "/helpers/ai.php";
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

$userId = (int) $_SESSION["user_id"];

$raw = file_get_contents("php://input");
/** @var mixed $decoded */
$decoded = json_decode($raw, true);

if (!is_array($decoded) || !isset($decoded["tasks"]) || !is_array($decoded["tasks"])) {
    echo json_encode(["success" => false, "message" => "Invalid payload."]);
    exit();
}

$tasksInput = [];
foreach ($decoded["tasks"] as $task) {
    if (!is_array($task) || !isset($task["title"])) {
        continue;
    }
    $title = trim((string) $task["title"]);
    if ($title === "" || mb_strlen($title) > 200) {
        continue;
    }
    $tasksInput[] = [
        "title" => $title,
        "priority" => isset($task["priority"]) ? (string) $task["priority"] : "normal",
        "estimated_time" => isset($task["estimated_time"]) ? (string) $task["estimated_time"] : "unspecified time",
        "deadline" => isset($task["deadline"]) ? (string) $task["deadline"] : "no strict deadline",
    ];
    if (count($tasksInput) >= 50) {
        break;
    }
}

if (count($tasksInput) === 0) {
    echo json_encode(["success" => false, "message" => "No valid tasks provided."]);
    exit();
}

if (!canUseAiToday($userId, 20)) {
    echo json_encode([
        "success" => false,
        "message" => "Daily AI limit reached. Please try again tomorrow.",
        "plan" => new stdClass(),
    ]);
    exit();
}

$plan = generateWeeklyPlan($tasksInput);

if (count($plan) === 0) {
    echo json_encode([
        "success" => false,
        "message" => "AI could not generate a weekly plan.",
        "plan" => new stdClass(),
    ]);
    exit();
}

incrementAiUsage($userId);

echo json_encode([
    "success" => true,
    "plan" => $plan,
]);

?>

