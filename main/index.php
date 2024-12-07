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
    <div class="welcome">
        <h1>Welcome</h1>
        <p>to your personal task Manager</p>
    </div>
    <div class="lagayan">
        <div class="bilog"></div>
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
                    <p>Incorrect email or password.</p>
                </div>
            </form>
        </div>
    </div>
</body>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Dummy credentials for demonstration
    $correct_email = 'user@example.com';
    $correct_password = 'password123';

    if ($email === $correct_email && $password === $correct_password) {
        $_SESSION['email'] = $email;

        if ($remember) {
            setcookie('email', $email, time() + (86400 * 30), "/"); // 30 days
            setcookie('password', $password, time() + (86400 * 30), "/"); // 30 days
        }
        header('Location: dashboard.php');
        exit();
    } else {
        echo '<script>document.getElementById("errorMessage").style.display = "block";</script>';
    }
}

if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];
}
?>
<footer>
</footer>
</html>