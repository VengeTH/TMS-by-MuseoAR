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

// Handle 1 form submission
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
    <title>OrgaNiss - Task Management System</title>
<?php
$seo_title = "OrgaNiss - Task Management System";
$seo_description = "Sign in to OrgaNiss. Organize tasks, set reminders, and plan your week with AI. Your personal task manager.";
$seo_canonical = "/";
require_once __DIR__ . "/components/seo-meta.php";
require_once __DIR__ . "/components/json-ld-organization.php";
?>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="index">
    <?php include_once __DIR__ . "/components/header.php"; ?>
    <div class="wrapper">
        <?php include_once __DIR__ . "/components/welcomeMessage.php"; ?>
        <div class="right">
            <div class="right-inner">
                <img src="/img/logo.png" class="bilog" alt="OrgaNiss">
                <h1 class="title">ORGANISS</h1>
                <form action="" method="post" class="LoginForm">
                    <div class="form-wrapper">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-text" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-wrapper">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-text" placeholder="••••••••" required>
                    </div>
                    <label class="rememberMe">
                        <input type="checkbox" name="remember" checked>
                        <span>Remember me</span>
                    </label>
                    <button type="submit" class="ContinueButton">Continue</button>
                    <a href="/user/register.php" class="Register">Don&rsquo;t have an account? Register</a>
                </form>
            </div>
        </div>
    </div>
    <?php
    include_once __DIR__ . "/components/footer.php"; // Include the footer

    if (!empty($login_error)) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: '" . $login_error . "',
                icon: 'error'
            });
        </script>";
    }
    ?>
</body>
</html>