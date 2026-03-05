<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
require_once dirname(__DIR__, 2) . "/helpers/ai.php";
require_once dirname(__DIR__, 2) . "/helpers/auth.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit();
}

$userId = requireAuthJson();

$title = isset($_POST["task_title"]) ? trim((string) $_POST["task_title"]) : "";
$parentTaskId = isset($_POST["parent_task_id"]) ? (int) $_POST["parent_task_id"] : 0;

if ($title === "" || mb_strlen($title) > 200) {
    echo json_encode(["success" => false, "message" => "Invalid task title."]);
    exit();
}

if ($parentTaskId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid parent task id."]);
    exit();
}

if (!canUseAiToday($userId, 20)) {
    echo json_encode([
        "success" => false,
        "message" => "Daily AI limit reached. Please try again tomorrow.",
        "subtasks" => [],
    ]);
    exit();
}

$db = new Task();
$parentTask = $db->getTask($parentTaskId, $userId);

if ($parentTask === null) {
    echo json_encode(["success" => false, "message" => "Parent task not found.", "subtasks" => []]);
    exit();
}

$subtasks = generateTaskBreakdown($title);

if (count($subtasks) === 0) {
    echo json_encode([
        "success" => false,
        "message" => "AI could not generate subtasks.",
        "subtasks" => [],
    ]);
    exit();
}

incrementAiUsage($userId);

$created = [];
$finishDate = isset($parentTask["finish_date"]) ? (string) $parentTask["finish_date"] : "";
$parentPriority = isset($parentTask["priority"]) ? (int) $parentTask["priority"] : 1;

$priorityText = "low";
if ($parentPriority === 3) {
    $priorityText = "high";
} elseif ($parentPriority === 2) {
    $priorityText = "medium";
}

foreach ($subtasks as $subTitle) {
    $subTitleClean = trim($subTitle);
    if ($subTitleClean === "" || mb_strlen($subTitleClean) > 200) {
        continue;
    }
    $ok = $db->addTask($subTitleClean, "", $finishDate, $priorityText, $userId, $parentTaskId);
    if ($ok) {
        $created[] = $subTitleClean;
    }
}

echo json_encode([
    "success" => true,
    "message" => "",
    "subtasks" => $created,
]);

?>

