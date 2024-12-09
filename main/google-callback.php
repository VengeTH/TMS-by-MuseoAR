<?php
require_once "vendor/autoload.php";
session_start();
$client = new Google_Client();
$client->setClientId('353540925058-rjjiqh9293el9qqn73100t8am2ahc4cm.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/Task%20Management/main/google-callback.php');
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die('Error fetching the access token: ' . htmlspecialchars($token['error']));
    }
    $client->setAccessToken($token);

    if ($client->isAccessTokenExpired()) {
        die('Access token expired. Please login again.');
    }

    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $first_name = $userInfo->givenName;
    $last_name = $userInfo->familyName;
    $email = $userInfo->email;
    $google_picture = $userInfo->picture; // Google profile picture

    include 'db.php';

    $check_stmt = $conn->prepare("SELECT id, first_name, profile_picture, profile_picture_source FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->bind_result($user_id, $db_first_name, $current_picture, $picture_source);
        $check_stmt->fetch();

        // Retain the uploaded profile picture if it exists
        if ($picture_source === 'google') {
            $update_stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $update_stmt->bind_param("si", $google_picture, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            $current_picture = $google_picture; // Update the session picture to Google picture
        }

        $_SESSION['user_id'] = $user_id;
        $_SESSION['first_name'] = $db_first_name;
        $_SESSION['profile_picture'] = $current_picture;

        header('Location: dashboard.php');
        exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, profile_picture, profile_picture_source) VALUES (?, ?, ?, ?, 'google')");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $google_picture);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['profile_picture'] = $google_picture;

            header('Location: dashboard.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
} else {
    echo "Error: No code parameter found in the callback.";
}
?>