<?php
require_once __DIR__ . "/db/db.php"; // Include the database file
session_start(); // Start the session
$login_error = "";
$db = new db();
$email = "";

// Check if the user is already logged in
if (isset($_SESSION["user_id"])) {
	$user = $db->getUserById($_SESSION["user_id"]);
	if (!empty($user["password"])) {
		header("Location: /dashboard"); // Redirect to dashboard if already logged in and has a password
		exit();
	} else {
		header("Location: /verify/password"); // Redirect to newPass if logged in but no password set
		exit();
	}
}

// Check if "Remember Me" cookies are set
if (isset($_COOKIE["user_id"]) && isset($_COOKIE["first_name"])) {
	$_SESSION["user_id"] = $_COOKIE["user_id"];
	$_SESSION["first_name"] = $_COOKIE["first_name"];
	header("Location: /dashboard");
	exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST["email"];
	$password = $_POST["password"];
	$remember = isset($_POST["remember"]) ? $_POST["remember"] : 0;
	$login_error = $db->loginUser($email, $password);
	if (empty($login_error)) {
		// Set session variables
		$user = $db->getUser($email);
		$_SESSION["user_id"] = $user["id"];
		$_SESSION["first_name"] = $user["first_name"];

		// Set cookies based on "Remember Me" checkbox
		if ($remember) {
			setcookie("user_id", $_SESSION["user_id"], time() + 86400 * 30, "/"); // 30 days
			setcookie("first_name", $_SESSION["first_name"], time() + 86400 * 30, "/"); // 30 days
		} else {
			// Clear any existing cookies
			setcookie("user_id", "", time() - 3600, "/");
			setcookie("first_name", "", time() - 3600, "/");
		}

		// Redirect to dashboard if login is successful
		header("Location: /dashboard");
		exit();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="index">
    <?php
    include_once __DIR__ . "/components/header.php"; // Include the header
    include_once __DIR__ . "/components/welcomeMessage.php";
    ?>
    <!-- culprit of why there is a scroll...  -->
    <div class="lagayan">
        <img src="/img/logo.png" class="bilog">
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
                    <input type="checkbox" name="remember" value="1">
                    <p>Remember me</p>
                </div>
                <button type="submit" class="ContinueButton">Continue</button>
                <a href="/user/register" class="Register">Don't have an account</a>
            </form>
        </div>
    </div>
    <?php
    include_once __DIR__ . "/components/footer.php"; // Include the footer

    if (!empty($login_error)) {
    	echo "<script>
            Swal.fire({
                title: 'Error',
                text: '" .
    		$login_error .
    		"',
                icon: 'error'
            });
        </script>";
    }
    ?>
</body>
</html>