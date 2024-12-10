<?php
require_once "db.php";
session_start(); // Start the session
$login_error = "";
$db = new db();
$email = "";

// Check if the user is already logged in
if (isset($_SESSION["first_name"])) {
	// error_log("User is already logged in: " . $_SESSION["first_name"]);
	header("Location: dashboard.php"); // Redirect to dashboard if already logged in
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST["email"];
	$password = $_POST["password"];
	$login_error = $db->loginUser($email, $password);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" href="./img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include "header.php";
// Include the header
?>
    <div class="lagayan">
        <img src="./img/logo.png" class="bilog">
        <div class="title">
            <h1>ORGANISS</h1>
        </div>
        <div class="LoginForm">
            <form action="" method="post">
                <div class="emailCont">
                    Email
                    <input type="text" name="email" class="EmailBox" value="<?php echo htmlspecialchars(
                    	$email,
                    ); ?>" required>
                </div>
                <div class="passCont">
                    Password
                    <input type="password" name="password" class="PasswordBox" required>
                </div>
                <div class="rememberMe">
                    <input type="checkbox" name="remember">
                    <p>Remember me</p>
                </div>
                <button type="submit" class="ContinueButton">Continue</button>
                <a href="register.php" class="Register">Don't have an account</a>
            </form>
        </div>
    </div>
    <?php
    include "footer.php"; // Include the footer

    if (!empty($login_error)) {
    	echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '$login_error',
            });
        </script>";
    }
    ?>
</body>
</html>