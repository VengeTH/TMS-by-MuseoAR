<?php
session_start();
require_once dirname(__DIR__) . "/db/db.php";
$db = new db();
$loggedIn = $db->checkUser($_SESSION["user_id"], $_SESSION["first_name"]);
if (!$loggedIn) {
	header("Location: /");
	exit();
}
?>
