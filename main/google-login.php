<?php
require 'vendor/autoload.php';
$client = new Google_Client();
$client->setClientId('353540925058-rjjiqh9293el9qqn73100t8am2ahc4cm.apps.googleusercontent.com'); // Replace with your Client ID
$client->setClientSecret('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'); // Replace with your Client Secret
$client->setRedirectUri('http://localhost/Task%20Management/main/google-callback.php'); // Update to match your setup
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

// Redirect user to Google's OAuth 2.0 server
$authUrl = $client->createAuthUrl();
//Check if the authUrl is not empty
if (!empty($authUrl)) {
    // Redirect the user to the Google login page
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
} else {
    // Display an error message
    echo 'Error generating the Google login URL';
}