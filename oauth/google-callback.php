<?php
require_once dirname(__DIR__) . "/db.php";
require_once dirname(__DIR__) . "/oauth/google.php";
session_start();
$db = new db();
$client = new googleOauth();
if (isset($_GET["login"]) && $_GET["login"] == "true") {
	header("Location: " . $client->getURL());
}
$token = $client->fetchToken($_GET["code"]);
if ($token == null) {
	die("Error: Unable to fetch access token.");
}
$oAuth = $client->generateOauth2(); // use this if you want to use ouath on a different way.
$userInfo = $client->getUserInfo();
if ($userInfo == null || $oAuth == null) {
	die("Error: Unable to fetch user info.");
}
$first_name = $userInfo->givenName;
$last_name = $userInfo->familyName;
$email = $userInfo->email;
$google_picture = $userInfo->picture; // Google profile picture
$user = $db->getUser($email);
if ($user == null) {
	$isSuccess = $db->addUser($first_name, $last_name, $email, $google_picture);
	if (!$isSuccess) {
		die("Error: Unable to add user.");
	}
	$_SESSION["user_id"] = $stmt->insert_id;
	$_SESSION["first_name"] = $first_name;
	$_SESSION["profile_picture"] = $google_picture;
	header("Location: /newPass");
	exit();
}
if (
	$user["profile_picture_source"] == "google" &&
	!$db->updateUserImage($user["id"], $google_picture)
) {
	die("Error: Unable to update user image.");
}
$_SESSION["user_id"] = $user["id"];
$_SESSION["first_name"] = $user["first_name"];
$_SESSION["profile_picture"] = $google_picture;
header("Location: /newPass");
exit();
?>
