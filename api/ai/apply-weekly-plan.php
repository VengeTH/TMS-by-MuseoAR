<?php
require_once dirname(__DIR__, 2) . "/db/tasks.php";
require_once dirname(__DIR__, 2) . "/helpers/auth.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit();
}

$userId = requireAuthJson();

$raw = file_get_contents("php://input");
/** @var mixed $decoded */
$decoded = json_decode($raw, true);

if (!is_array($decoded) || !isset($decoded["plan"]) || !is_array($decoded["plan"])) {
    echo json_encode(["success" => false, "message" => "Invalid payload."]);
    exit();
}

$weekdays = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];

$today = new DateTimeImmutable("now");

/**
 * @param DateTimeImmutable $today
 * @param string $weekday
 * @return string
 */
function getNextWeekdayDate(DateTimeImmutable $today, string $weekday): string
{
    $target = clone $today;
    if (strtolower($weekday) !== strtolower($today->format("l"))) {
        $target = new DateTimeImmutable("next " . $weekday);
    }
    return $target->format("Y-m-d") . " 18:00:00";
}

$db = new Task();

foreach ($weekdays as $day) {
    if (!isset($decoded["plan"][$day]) || !is_array($decoded["plan"][$day])) {
        continue;
    }
    $taskIds = $decoded["plan"][$day];
    $finishDate = getNextWeekdayDate($today, $day);
    foreach ($taskIds as $taskId) {
        $taskIdInt = (int) $taskId;
        if ($taskIdInt <= 0) {
            continue;
        }
        $db->updateTaskFinishDate($userId, $taskIdInt, $finishDate);
    }
}

echo json_encode(["success" => true]);

?>

