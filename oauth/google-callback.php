<?php
require_once dirname(__DIR__) . "/db.php";
require_once dirname(__DIR__) . "/oauth/google.php";
session_start();
$db = new db();
$client = new googleOauth();

if (isset($_GET["login"]) && $_GET["login"] == "true") {
    header("Location: " . $client->getURL());
    exit();
}

if (isset($_GET["error"])) {
    // Clear the session if there is an error (e.g., user canceled login)
    session_unset();
    session_destroy();
    header("Location: /login");
    exit();
}

$token = $client->fetchToken($_GET["code"]);
if ($token == null) {
    die("Error: Unable to fetch access token.");
}

$oAuth = $client->generateOauth2();
$userInfo = $client->getUserInfo();
if ($userInfo == null || $oAuth == null) {
    die("Error: Unable to fetch user info.");
}

$first_name = $userInfo->givenName;
$last_name = $userInfo->familyName;
if (empty($last_name)){
    $last_name = " ";
}
$email = $userInfo->email;
$google_picture = $userInfo->picture;
$user = $db->getUser($email);
if ($user == null) {
    $isSuccess = $db->addUser($first_name, $last_name, $email, $google_picture);
    if (!$isSuccess) {
        die("Error: Unable to add user.");
    }
    $_SESSION["user_id"] = $db->getLastInsertId();
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
if (empty($user["password"])) {
    header("Location: /newPass");
    exit();
} else {
    header("Location: /dashboard");
    exit();
}
?>