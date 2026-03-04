<?php
// * AI helper functions for task breakdown and weekly planning using Google Gemini API.

require_once __DIR__ . "/env.php";
require_once dirname(__DIR__) . "/db/db.php";

/**
 * Generates a list of subtasks for a given task title using the Gemini API.
 *
 * @param string $taskTitle The title of the main task.
 * @param int $maxItems Maximum number of subtasks to return.
 * @return array<int, string> List of subtasks. Returns empty array on error.
 */
function generateTaskBreakdown(string $taskTitle, int $maxItems = 8): array
{
    $cleanTitle = trim($taskTitle);
    if ($cleanTitle === "" || mb_strlen($cleanTitle) > 200) {
        return [];
    }

    $apiKey = $_ENV["GEMINI_API_KEY"] ?? getenv("GEMINI_API_KEY");
    if (!is_string($apiKey) || $apiKey === "") {
        return [];
    }

    $model = "gemini-1.5-flash";

    $prompt = "Break down this task into 4–8 small actionable steps.\n" .
        "Return only a bullet list, one item per line, no extra commentary.\n" .
        "Task: \"" . $cleanTitle . "\"";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . rawurlencode($model) . ":generateContent";
    $url .= "?key=" . rawurlencode($apiKey);

    $payload = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $prompt,
                    ],
                ],
            ],
        ],
    ];

    $responseBody = callGemini($url, $payload);
    if ($responseBody === null) {
        return [];
    }

    /** @var mixed $decoded */
    $decoded = json_decode($responseBody, true);
    if (!is_array($decoded)) {
        return [];
    }

    $text = "";
    if (
        isset($decoded["candidates"][0]["content"]["parts"][0]["text"]) &&
        is_string($decoded["candidates"][0]["content"]["parts"][0]["text"])
    ) {
        $text = $decoded["candidates"][0]["content"]["parts"][0]["text"];
    }

    if ($text === "") {
        return [];
    }

    $lines = preg_split("/\r\n|\r|\n/", $text);
    if ($lines === false) {
        return [];
    }

    $subtasks = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === "") {
            continue;
        }

        $trimmed = preg_replace("/^[\-\*\•]\s*/u", "", $trimmed);
        if (!is_string($trimmed)) {
            $trimmed = "";
        }

        $trimmed = preg_replace("/^[0-9]{1,3}\.\s*/", "", $trimmed);
        if (!is_string($trimmed)) {
            $trimmed = "";
        }

        $trimmed = trim($trimmed);
        if ($trimmed === "") {
            continue;
        }

        $subtasks[] = $trimmed;

        if (count($subtasks) >= $maxItems) {
            break;
        }
    }

    return $subtasks;
}

/**
 * Generates a weekly plan for the given tasks using the Gemini API.
 *
 * @param array<int, array<string, mixed>> $tasks Tasks with title, priority, estimated_time, and deadline.
 * @return array<string, array<int, string>> Weekly plan keyed by weekday name.
 */
function generateWeeklyPlan(array $tasks): array
{
    if (count($tasks) === 0) {
        return [];
    }

    $apiKey = $_ENV["GEMINI_API_KEY"] ?? getenv("GEMINI_API_KEY");
    if (!is_string($apiKey) || $apiKey === "") {
        return [];
    }

    $model = "gemini-1.5-flash";

    $taskLines = [];
    foreach ($tasks as $task) {
        if (!isset($task["title"])) {
            continue;
        }
        $title = (string) $task["title"];
        $priority = isset($task["priority"]) ? (string) $task["priority"] : "normal";
        $estimatedTime = isset($task["estimated_time"]) ? (string) $task["estimated_time"] : "unspecified time";
        $deadline = isset($task["deadline"]) ? (string) $task["deadline"] : "no strict deadline";

        if (mb_strlen($title) > 200) {
            $title = mb_substr($title, 0, 200);
        }

        $taskLines[] = "- " . $title . " (priority: " . $priority . ", time: " . $estimatedTime . ", deadline: " . $deadline . ")";
        if (count($taskLines) >= 50) {
            break;
        }
    }

    if (count($taskLines) === 0) {
        return [];
    }

    $tasksBlock = implode("\n", $taskLines);

    $prompt = "Organize the following tasks into a weekly plan from Monday to Sunday.\n" .
        "Return ONLY a valid JSON object with exactly these keys: " .
        "\"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\", \"Friday\", \"Saturday\", \"Sunday\".\n" .
        "Each key must map to an array of task titles.\n\n" .
        "Tasks:\n" . $tasksBlock;

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . rawurlencode($model) . ":generateContent";
    $url .= "?key=" . rawurlencode($apiKey);

    $payload = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $prompt,
                    ],
                ],
            ],
        ],
    ];

    $responseBody = callGemini($url, $payload);
    if ($responseBody === null) {
        return [];
    }

    /** @var mixed $decoded */
    $decoded = json_decode($responseBody, true);
    if (!is_array($decoded)) {
        return [];
    }

    $rawText = "";
    if (
        isset($decoded["candidates"][0]["content"]["parts"][0]["text"]) &&
        is_string($decoded["candidates"][0]["content"]["parts"][0]["text"])
    ) {
        $rawText = $decoded["candidates"][0]["content"]["parts"][0]["text"];
    }

    if ($rawText === "") {
        return [];
    }

    $json = extractJsonObject($rawText);
    if ($json === null) {
        return [];
    }

    /** @var mixed $plan */
    $plan = json_decode($json, true);
    if (!is_array($plan)) {
        return [];
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

    $normalized = [];
    foreach ($weekdays as $day) {
        $value = $plan[$day] ?? [];
        if (!is_array($value)) {
            $normalized[$day] = [];
            continue;
        }
        $titles = [];
        foreach ($value as $entry) {
            if (is_string($entry) && $entry !== "") {
                $titles[] = $entry;
            }
        }
        $normalized[$day] = $titles;
    }

    return $normalized;
}

/**
 * Performs a POST request to the Gemini API.
 *
 * @param string $url Target endpoint including query parameters.
 * @param array<string, mixed> $payload JSON serializable payload.
 * @return string|null Raw response body, or null on error.
 */
function callGemini(string $url, array $payload): ?string
{
    $ch = curl_init($url);
    if ($ch === false) {
        return null;
    }

    curl_setopt_array(
        $ch,
        [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 20,
        ]
    );

    $responseBody = curl_exec($ch);
    if ($responseBody === false) {
        curl_close($ch);
        return null;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        return null;
    }

    return $responseBody;
}

/**
 * Extracts the first JSON object found in a text blob.
 *
 * @param string $text Model output that may contain JSON.
 * @return string|null JSON substring or null if not found.
 */
function extractJsonObject(string $text): ?string
{
    $start = strpos($text, "{");
    $end = strrpos($text, "}");
    if ($start === false || $end === false || $end <= $start) {
        return null;
    }

    $json = substr($text, $start, $end - $start + 1);
    if (!is_string($json) || $json === "") {
        return null;
    }

    return $json;
}

/**
 * Checks whether the given user is allowed to perform another AI generation today.
 *
 * @param int $userId The ID of the user.
 * @param int $dailyLimit Maximum number of generations allowed per day.
 * @return bool True if allowed, false otherwise.
 */
function canUseAiToday(int $userId, int $dailyLimit = 20): bool
{
    $db = new db();
    $conn = $db->getConnection();

    $today = (new DateTime("now", new DateTimeZone("UTC")))->format("Y-m-d");

    $select = $conn->prepare("SELECT requests FROM ai_usage WHERE user_id = ? AND date = ?");
    if ($select === false) {
        return false;
    }

    $select->bind_param("is", $userId, $today);
    if (!$select->execute()) {
        $select->close();
        return false;
    }

    $result = $select->get_result();
    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $select->close();
        if (!is_array($row) || !isset($row["requests"])) {
            return false;
        }
        return (int) $row["requests"] < $dailyLimit;
    }

    $select->close();
    return true;
}

/**
 * Increments the AI usage counter for the given user for today.
 *
 * @param int $userId The ID of the user.
 * @return void
 */
function incrementAiUsage(int $userId): void
{
    $db = new db();
    $conn = $db->getConnection();

    $today = (new DateTime("now", new DateTimeZone("UTC")))->format("Y-m-d");

    $select = $conn->prepare("SELECT requests FROM ai_usage WHERE user_id = ? AND date = ?");
    if ($select === false) {
        return;
    }

    $select->bind_param("is", $userId, $today);
    if (!$select->execute()) {
        $select->close();
        return;
    }

    $result = $select->get_result();
    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $select->close();
        if (!is_array($row) || !isset($row["requests"])) {
            return;
        }
        $requests = (int) $row["requests"] + 1;

        $update = $conn->prepare("UPDATE ai_usage SET requests = ? WHERE user_id = ? AND date = ?");
        if ($update === false) {
            return;
        }
        $update->bind_param("iis", $requests, $userId, $today);
        $update->execute();
        $update->close();
    } else {
        $select->close();
        $requests = 1;
        $insert = $conn->prepare("INSERT INTO ai_usage (user_id, date, requests) VALUES (?, ?, ?)");
        if ($insert === false) {
            return;
        }
        $insert->bind_param("isi", $userId, $today, $requests);
        $insert->execute();
        $insert->close();
    }
}

?>

