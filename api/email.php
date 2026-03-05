<?php
require_once dirname(__DIR__) . "/db/db.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method.",
    ]);
    exit();
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized.",
    ]);
    exit();
}

/**
 * * Validates password strength for account security.
 *
 * @param string $password
 * @return bool
 */
function isStrongPassword(string $password): bool
{
    if (mb_strlen($password) < 8) {
        return false;
    }
    $hasLetter = (bool) preg_match("/[A-Za-z]/", $password);
    $hasDigit = (bool) preg_match("/\d/", $password);
    return $hasLetter && $hasDigit;
}

// * Optional CSRF protection for form-based requests.
if (isset($_SESSION["csrf_token"])) {
    $csrf = isset($_POST["csrf_token"]) ? (string) $_POST["csrf_token"] : "";
    if ($csrf === "" || !hash_equals((string) $_SESSION["csrf_token"], $csrf)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid CSRF token.",
        ]);
        exit();
    }
}

$sessionUserId = (int) $_SESSION["user_id"];
$id = isset($_POST["id"]) ? (int) $_POST["id"] : $sessionUserId;

if ($id <= 0 || $id !== $sessionUserId) {
    echo json_encode([
        "success" => false,
        "message" => "You can only change your own password.",
    ]);
    exit();
}

$newPassword = isset($_POST["password"]) ? (string) $_POST["password"] : "";

if ($newPassword === "") {
    echo json_encode([
        "success" => false,
        "message" => "Password is required.",
    ]);
    exit();
}

if (!isStrongPassword($newPassword)) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 8 characters long and contain both letters and numbers.",
    ]);
    exit();
}

$db = new db();
$updatePassword = $db->updatePassword($id, $newPassword);

if ($updatePassword) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Unable to update password.",
    ]);
}
?>