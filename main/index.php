<?php
session_start(); // Start the session
include 'db.php'; // Include the database connection

// Check if the user is already logged in
if (isset($_SESSION['first_name'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if already logged in
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT first_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($first_name, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Store the first name in the session
            $_SESSION['first_name'] = $first_name;
            // Login successful, redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "No user found with that email.";
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../img/logo.png" type="image/x-icon">
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="../img/logo.png" class="logo" width=6% height=6%>
        </div>
        <div class="titleBesideLogo">
            <h1>ORGANISS</h1>
        </div>
        <div class="menu">
            <a href="index.php"><button>Sign in</button></a>
            <a href="register.php"><button>Register</button></a>
        </div>
    </div>
    <div class="welcome">
        <h1>Welcome</h1>
        <p>to your personal task Manager</p>
    </div>
    <div class="lagayan">
        <img src="../img/logo.png" class="bilog">
        <div class="title">
            <h1>ORGANISS</h1>
        </div>
        <div class="LoginForm">
            <form action="" method="post">
                <div class="emailCont">
                    Email
                    <input type="text" name="email" class="EmailBox" required>
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
                <div class="errorMessage" id="errorMessage">
                    <?php
                        echo "<p> $login_error </p>";
                    ?>
                </div>
            </form>
        </div>
    </div>
    <div class="footer">
        <a href="aboutUs.php"><p>About Us</p></a>
        <p>MuseoAR Developers</p>
        <a href="contactUs.php"><p>Contact Us</p></a>
    </div>
</body>
</html>