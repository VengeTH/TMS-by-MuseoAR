<?php
// Include the database connection file
require_once dirname(__DIR__) . "/db/db.php";

// Initialize the database connection
$db = new db();
$conn = $db->getConnection();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['auth_code'])) {
    // Get the authentication code from the POST request
    $authCode = $_POST['auth_code'];

    // Start the session to access stored data
    session_start();

    // Validate the authentication code
    if ($authCode == $_SESSION['auth_code']) {
        // Get the new email and current email from the session
        $newEmail = $_SESSION['new_email'];
        $currentEmail = $_SESSION['current_email'];

        // Prepare the SQL statement to update the email
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE email = ?");
        $stmt->bind_param("ss", $newEmail, $currentEmail);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Email address updated successfully.";
        } else {
            echo "Error updating email address: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();

        // Clear the session data
        session_unset();
        session_destroy();
    } else {
        echo "Invalid authentication code.";
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
    <title>Verify Email</title>
</head>
<body>
    <form method="POST" action="verifyEmail.php">
        <label for="auth_code">Verification Code:</label>
        <input type="text" id="auth_code" name="auth_code" required><br><br>
        <button type="submit">Verify</button>
    </form>
</body>
</html>