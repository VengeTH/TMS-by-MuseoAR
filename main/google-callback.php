<?php
require_once "vendor/autoload.php";
session_start();
$client = new Google_Client();
$client->setClientId('353540925058-rjjiqh9293el9qqn73100t8am2ahc4cm.apps.googleusercontent.com'); // Replace with actual Client ID
$client->setClientSecret('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'); // Replace with actual Client Secret
$client->setRedirectUri('http://localhost/Task%20Management/main/google-callback.php'); // Replace with actual Redirect URI
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

if (isset($_GET['code']) ) {
    //error 1
    $token =  $client->fetchAccessTokenWithAuthCode($_GET['code']);
    echo 'token:'.$token;
    if (isset($token['error'])) {
        die('Error fetching the access token: ' . htmlspecialchars($token['error']));
    }
    $client->setAccessToken($token);
    // error 2
    if ($client->getAccessToken()) {
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        // Extract user information
        $first_name = $userInfo->givenName;
        $last_name = $userInfo->familyName;
        $email = $userInfo->email;

        // Connect to the database
        include 'db.php';

        // Check if the user already exists
        $check_stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // User exists
            $check_stmt->bind_result($user_id, $db_first_name);
            $check_stmt->fetch();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['first_name'] = $db_first_name;

            header('Location: dashboard.php');
            exit();
        } else {
            // Register the new user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $first_name, $last_name, $email);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['first_name'] = $first_name;

                header('Location: dashboard.php');
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
} else {
    echo "Error: No code parameter found in the callback.";
}
?>