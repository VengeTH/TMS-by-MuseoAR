<?php
// Start session to use session variables if needed
session_start();

// Include your database configuration file
include 'db.php';

header('Content-Type: application/json');

try {
    // Check if user is logged in or session data is available
    if (!isset($_SESSION['email'])) {
        echo json_encode(['error' => 'User is not logged in.']);
        exit;
    }

    // Retrieve email from the session
    $email = $_SESSION['email'];

    // Query to check if the email exists in the database
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email found, return as JSON response
        echo json_encode(['email' => $email]);
    } else {
        echo json_encode(['error' => 'Email not found in the database.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}

// Close the database connection
$conn->close();
?>
