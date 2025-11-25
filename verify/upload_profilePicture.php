<?php
session_start();
require_once dirname(__DIR__) . "/db/db.php"; // * Include your database connection
//! recommend using api here.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Check if a file was uploaded
	if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
		$userId = $_SESSION["user_id"]; // Assuming you have the user ID in the session
		$file = $_FILES["profile_picture"];
		$uploadDir = "uploads/"; // Directory to save uploaded files
		$fileName = basename($file["name"]);
		$targetFilePath = $uploadDir . $fileName;

		// Check if the file is an image
		$check = getimagesize($file["tmp_name"]);
		if ($check !== false) {
			// Move the uploaded file to the target directory
			if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
				// Update the database with the file path
				$stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
				$stmt->bind_param("si", $targetFilePath, $userId);
				$stmt->execute();
				$stmt->close();
				echo "Profile picture uploaded successfully.";
			} else {
				echo "Error uploading the file.";
			}
		} else {
			echo "File is not an image.";
		}
	} else {
		echo "No file uploaded or there was an upload error.";
	}
}
?>
