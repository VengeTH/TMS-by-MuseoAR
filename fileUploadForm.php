<!-- <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
    <label for="profile_picture">Upload Profile Picture:</label>
    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
    <input type="submit" value="Upload">
</form>
to be input

<?php
// Assuming you have the user ID in the session
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($profile_picture);
$stmt->fetch();
$stmt->close();
?>

<img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
display the user's profile picture -->