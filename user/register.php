<?php
require_once dirname(__DIR__) . "/db/db.php"; // Include the database file
session_start();
if (isset($_SESSION["user_id"])) {
	header("Location: /dashboard");
	exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$db = new db();
	if (!isset($_POST["agreement"])) {
		die("You must agree to the terms and conditions.");
	}
	$first_name = trim($_POST["firstName"]);
	$last_name = trim($_POST["lastName"]);
	$email = trim($_POST["email"]);
	$password = trim($_POST["password"]);
	if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
		die("All fields are required.");
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		die("Invalid email format.");
	}
	$emailExists = $db->getUser($email);
	if ($emailExists) {
		die("Email already exists.");
	}
	$user = $db->addUser($first_name, $last_name, $email, null, $password);
	print_r($user);
	if (!$user) {
		die("Error: Unable to add user.");
	}
	$_SESSION["user_id"] = $user;
	$_SESSION["first_name"] = $first_name;
	header("Location: /dashboard");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/register.css">
    <script src="/js/sweetalert.js"></script>
</head>
<body>
    <?php
    include dirname(__DIR__) . "/components/header.php"; // Include the header
    include dirname(__DIR__) . "/components/welcomeMessage.php";

//Include the welcome message
?>
    <div class="createAccText">
        <h1>Create Account</h1>
        <p>Already have an account? <a href="index.php">Log in</a></p>
    </div>
    <div class="infoContainer">
        <form action="" method="POST">
            <div class="nameCont">
                <div class="firstNameCont">
                    <input type="text" name="firstName" class="firstNameBox" placeholder="First Name" required>
                </div>
                <div class="lastNameCont">
                    <input type="text" name="lastName" class="lastNameBox" placeholder="Last Name" required>
                </div>
            </div>
            <div class="emailCont">
                <input type="text" name="email" class="emailBox" placeholder="Email" required>
            </div>
            <div class="passCont">
                <input type="password" name="password" class="passwordBox" placeholder="Password" required>
            </div>
            <div class="agreement">
                <input type="checkbox" name="agreement" id="agreementCheckbox" required disabled>
                <p>I agree to the <a href="#" id="termsLink">Terms and Conditions</a></p>
                <script>
                    document.getElementById('termsLink').addEventListener('click', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Terms and Conditions',
                            html: '<iframe src="termsAndConditions.html" width="100%" height="550px" style="border:none;"></iframe>',
                            width: '80%',
                            showCloseButton: true,
                            focusConfirm: false,
                            confirmButtonText: 'Accept'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('agreementCheckbox').checked = true;
                                document.getElementById('agreementCheckbox').disabled = false;
                            }
                        });
                    });
                </script>
            </div>
            <button type="submit" class="createAccButton">Create Account</button>
            <p>----------------------- Or register with -----------------------</p>
            <div class="altContainer">
                <a href="/oauth/google-callback?login=true">
                <div class="googleButton">Google</div></a>
                <a href="/oauth/facebook.php">
                <div class="facebookButton">Facebook</div></a>
            </div>
        </form>
    </div>
    <?php include_once dirname(__DIR__) . "/components/footer.php"; ?>
</body>
</html>