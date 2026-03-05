<?php
// Include the database connection file
require_once dirname(__DIR__) . "/db/db.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Initialize the database connection
$db = new db();
$conn = $db->getConnection();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["auth_code"])) {
    // Get the authentication code from the POST request
    $authCode = trim((string) $_POST["auth_code"]);

    // Validate the authentication code
    if (isset($_SESSION["auth_code"]) && hash_equals((string) $_SESSION["auth_code"], $authCode)) {
        // Get the new email and current email from the session
        $newEmail = isset($_SESSION["new_email"]) ? (string) $_SESSION["new_email"] : "";
        $currentEmail = isset($_SESSION["current_email"]) ? (string) $_SESSION["current_email"] : "";

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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="auth_code">Verification Code:</label>
        <input type="text" id="auth_code" name="auth_code" required><br><br>
        <button type="submit">Verify</button>
    </form>
</body>
</html>