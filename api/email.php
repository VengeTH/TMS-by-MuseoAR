<?php
require_once dirname(__DIR__) . "/db/db.php";
session_start();

$id = $_POST["id"];
$newPassword = $_POST["password"];
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

$db = new db();
$updatePassword = $db->updatePassword($id, $hashedPassword);

header("Content-Type: application/json; charset=utf-8");
if ($updatePassword) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Unable to update password."]);
}
?>