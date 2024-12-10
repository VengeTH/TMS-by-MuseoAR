<?php
require_once dirname(__DIR__) . "/db.php";
session_start();
$id = $_POST["id"];
$newPassword = $_POST["password"];
$db = new db();
$updatePassword = $db->updatePassword($id, $newPassword);

header("Content-Type: application/json; charset=utf-8");
if ($updatePassword) {
	echo json_encode(["success" => true]);
} else {
	echo json_encode(["success" => false, "message" => "Unable to update password."]);
}
?>
