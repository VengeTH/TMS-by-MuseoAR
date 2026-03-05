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

if (!is_array($decoded) || !isset($decoded["task_id"], $decoded["completed"])) {
    echo json_encode(["success" => false, "message" => "Invalid payload."]);
    exit();
}

$taskId = (int) $decoded["task_id"];
$completed = (bool) $decoded["completed"];

if ($taskId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid task id."]);
    exit();
}

$db = new Task();
$conn = $db->getConnection();

// * Ensure task exists and belongs to user.
$stmt = $conn->prepare("SELECT id, parent_task_id FROM tasks WHERE id = ? AND user_id = ?");
if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Database error."]);
    exit();
}
$stmt->bind_param("ii", $taskId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!is_array($row)) {
    echo json_encode(["success" => false, "message" => "Task not found."]);
    exit();
}

$parentTaskId = $row["parent_task_id"] === null ? null : (int) $row["parent_task_id"];

// * Update task completion state.
$completedAt = $completed ? (new DateTime("now"))->format("Y-m-d H:i:s") : null;

if ($completed) {
    $update = $conn->prepare("UPDATE tasks SET is_completed = 1, completed_at = ? WHERE id = ? AND user_id = ?");
    if ($update === false) {
        echo json_encode(["success" => false, "message" => "Database error."]);
        exit();
    }
    $update->bind_param("sii", $completedAt, $taskId, $userId);
    $update->execute();
    $update->close();
} else {
    $update = $conn->prepare("UPDATE tasks SET is_completed = 0, completed_at = NULL WHERE id = ? AND user_id = ?");
    if ($update === false) {
        echo json_encode(["success" => false, "message" => "Database error."]);
        exit();
    }
    $update->bind_param("ii", $taskId, $userId);
    $update->execute();
    $update->close();
}

// * If this is a parent task, apply the same completion state to its subtasks for a predictable UX.
if ($parentTaskId === null) {
    if ($completed) {
        $childUpdate = $conn->prepare("UPDATE tasks SET is_completed = 1, completed_at = ? WHERE user_id = ? AND parent_task_id = ?");
        if ($childUpdate !== false) {
            $childUpdate->bind_param("sii", $completedAt, $userId, $taskId);
            $childUpdate->execute();
            $childUpdate->close();
        }
    } else {
        $childUpdate = $conn->prepare("UPDATE tasks SET is_completed = 0, completed_at = NULL WHERE user_id = ? AND parent_task_id = ?");
        if ($childUpdate !== false) {
            $childUpdate->bind_param("ii", $userId, $taskId);
            $childUpdate->execute();
            $childUpdate->close();
        }
    }
} else {
    // * If this is a subtask, auto-update the parent: completed if and only if all subtasks are completed.
    $check = $conn->prepare("SELECT COUNT(*) AS total, SUM(is_completed) AS done FROM tasks WHERE user_id = ? AND parent_task_id = ?");
    if ($check !== false) {
        $check->bind_param("ii", $userId, $parentTaskId);
        $check->execute();
        $counts = $check->get_result()->fetch_assoc();
        $check->close();

        $total = isset($counts["total"]) ? (int) $counts["total"] : 0;
        $done = isset($counts["done"]) ? (int) $counts["done"] : 0;

        if ($total > 0 && $done === $total) {
            $parentCompletedAt = (new DateTime("now"))->format("Y-m-d H:i:s");
            $parentUpdate = $conn->prepare("UPDATE tasks SET is_completed = 1, completed_at = ? WHERE id = ? AND user_id = ?");
            if ($parentUpdate !== false) {
                $parentUpdate->bind_param("sii", $parentCompletedAt, $parentTaskId, $userId);
                $parentUpdate->execute();
                $parentUpdate->close();
            }
        } else {
            $parentUpdate = $conn->prepare("UPDATE tasks SET is_completed = 0, completed_at = NULL WHERE id = ? AND user_id = ?");
            if ($parentUpdate !== false) {
                $parentUpdate->bind_param("ii", $parentTaskId, $userId);
                $parentUpdate->execute();
                $parentUpdate->close();
            }
        }
    }
}

echo json_encode(["success" => true]);

?>

