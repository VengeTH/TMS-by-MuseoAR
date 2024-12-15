<?php
// Include the database connection file
include 'db.php';
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize the database connection
$db = new db();
$conn = $db->getConnection();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['current_email']) && isset($_POST['new_email'])) {
    // Get the current email and new email from the POST request
    $currentEmail = $_POST['current_email'];
    $newEmail = $_POST['new_email'];

    // Validate the inputs
    if (!empty($currentEmail) && !empty($newEmail) && filter_var($currentEmail, FILTER_VALIDATE_EMAIL) && filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        // Generate a random authentication code
        $authCode = rand(100000, 999999);

        // Store the authentication code and new email in the session
        session_start();
        $_SESSION['auth_code'] = $authCode;
        $_SESSION['new_email'] = $newEmail;
        $_SESSION['current_email'] = $currentEmail;

        // Send the authentication code to the new email address using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'organizzbymuseoar@gmail.com'; // SMTP username
            $mail->Password = 'juli-anne2024'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('no-reply@museoar.com', 'MuseoAR');
            $mail->addAddress($newEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Email Change Verification Code';
            $mail->Body = "Your verification code is: $authCode";

            $mail->send();
            echo "Verification code sent to $newEmail. Please check your email.";
        } catch (Exception $e) {
            echo "Failed to send verification code. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Invalid input.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Email</title>
</head>
<body>
    <form method="POST" action="changeEmail.php">
        <label for="current_email">Current Email:</label>
        <input type="email" id="current_email" name="current_email" required><br><br>
        <label for="new_email">New Email:</label>
        <input type="email" id="new_email" name="new_email" required><br><br>
        <button type="submit">Send Verification Code</button>
    </form>
</body>
</html>