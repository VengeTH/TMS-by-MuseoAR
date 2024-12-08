<?php
require 'vendor/autoload.php';
session_start();

$client = new Google\Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID'); // Replace with your Client ID
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET'); // Replace with your Client Secret
$client->setRedirectUri('http://localhost/your-folder-path/google-callback.php'); // Update to match your setup

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    if ($client->getAccessToken()) {
        $oauth2 = new Google\Service\Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        // Extract user information
        $first_name = $userInfo['givenName'];
        $last_name = $userInfo['familyName'];
        $email = $userInfo['email'];

        // Connect to the database
        include 'db.php';

        // Check if the user already exists
        $check_stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // User exists, start a session for them
            $check_stmt->bind_result($user_id, $db_first_name);
            $check_stmt->fetch();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['first_name'] = $db_first_name;

            header('Location: dashboard.php'); // Redirect to dashboard
            exit();
        } else {
            // Register the new user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $first_name, $last_name, $email);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['first_name'] = $first_name;

                header('Location: dashboard.php'); // Redirect to dashboard
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