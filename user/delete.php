<?php
require_once dirname(__DIR__) . "/helpers/sessionHandler.php";
require_once dirname(__DIR__) . "/db/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /pages/delete-account");
    exit();
}

$token = $_POST["csrf_token"] ?? "";
$expectedToken = $_SESSION["delete_account_csrf"] ?? "";
if ($token === "" || $expectedToken === "" || !hash_equals($expectedToken, $token)) {
    $_SESSION["delete_account_error"] = "Invalid request token. Please try again.";
    header("Location: /pages/delete-account");
    exit();
}

$confirmText = trim((string) ($_POST["confirm_text"] ?? ""));
$password = (string) ($_POST["password"] ?? "");

if (strtoupper($confirmText) !== "DELETE") {
    $_SESSION["delete_account_error"] = "Type DELETE exactly to confirm account removal.";
    header("Location: /pages/delete-account");
    exit();
}

$db = new db();
$user = $db->getUserById($_SESSION["user_id"]);

if (!$user) {
    $_SESSION["delete_account_error"] = "User account was not found.";
    header("Location: /pages/delete-account");
    exit();
}

$hasPassword = !empty($user["password"]);
if ($hasPassword && !$db->verifyUserPassword((int) $_SESSION["user_id"], $password)) {
    $_SESSION["delete_account_error"] = "Password is incorrect.";
    header("Location: /pages/delete-account");
    exit();
}

$deleted = $db->deleteUserAccountById((int) $_SESSION["user_id"]);
if (!$deleted) {
    $_SESSION["delete_account_error"] = "Could not delete account right now. Please try again.";
    header("Location: /pages/delete-account");
    exit();
}

session_unset();
session_destroy();
header("Location: /?account_deleted=1");
exit();
