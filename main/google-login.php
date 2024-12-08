<?php
require 'vendor/autoload.php';

$client = new Google\Client();
$client->setClientId('353540925058-rjjiqh9293el9qqn73100t8am2ahc4cm.apps.googleusercontent.com'); // Replace with your Client ID
$client->setClientSecret('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T'); // Replace with your Client Secret
$client->setRedirectUri('http://localhost/Task%20Management/main/google-callback.php'); // Update to match your setup
$client->addScope('email');
$client->addScope('profile');

// Redirect user to Google's OAuth 2.0 server
$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));