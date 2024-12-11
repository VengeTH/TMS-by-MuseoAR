<?php
// Initialize the session
session_start();
// Update the following variables
$facebook_oauth_app_id = "582728114360758";
$facebook_oauth_app_secret = "b1a43ff61b5ee96571613dc08a1dab60";
// Must be the direct URL to the facebook.php file
$facebook_oauth_redirect_uri = "http://localhost/oauth/facebook.php";
$facebook_oauth_version = "v18.0";

// Include the database connection file
require_once __DIR__ . "/../db.php"; // Update the path to db.php
$db = new db();

// If the captured code param exists and is valid
if (isset($_GET["code"]) && !empty($_GET["code"])) {
    $params = [
        "client_id" => $facebook_oauth_app_id,
        "client_secret" => $facebook_oauth_app_secret,
        "redirect_uri" => $facebook_oauth_redirect_uri,
        "code" => $_GET["code"],
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/oauth/access_token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);

    // Handle the response and store the access token in the session
    if (isset($response["access_token"])) {
        $_SESSION["facebook_access_token"] = $response["access_token"];

        // Fetch user information from Facebook
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?fields=id,first_name,last_name,email,picture&access_token=" . $response["access_token"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_info = curl_exec($ch);
        curl_close($ch);
        $user_info = json_decode($user_info, true);

        // Check if the user exists in the database
        $user = $db->getUser($user_info["email"]);
        if ($user == null) {
            // Add the user to the database
            $isSuccess = $db->addUser($user_info["first_name"], $user_info["last_name"], $user_info["email"], $user_info["picture"]["data"]["url"]);
            if (!$isSuccess) {
                die("Error: Unable to add user.");
            }
            $_SESSION["user_id"] = $db->getLastInsertId();
            $_SESSION["first_name"] = $user_info["first_name"];
            $_SESSION["profile_picture"] = $user_info["picture"]["data"]["url"];
            header("Location: /newPass");
            exit();
        } else {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["profile_picture"] = $user["profile_picture"];
            header("Location: /dashboard");
            exit();
        }
    } else {
        echo "Error: Unable to obtain access token.";
    }
} else {
    // Define params and redirect to Facebook OAuth page
    $params = [
        "client_id" => $facebook_oauth_app_id,
        "redirect_uri" => $facebook_oauth_redirect_uri,
        "response_type" => "code",
        "scope" => "email",
    ];
    header("Location: https://www.facebook.com/dialog/oauth?" . http_build_query($params));
    exit();
}
?>