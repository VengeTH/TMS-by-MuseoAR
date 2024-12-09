<?php
include 'db.php'; // Include the database connection
session_start(); // Start the session
$login_error = '';
$email = '';

// Check if the user is already logged in
if (isset($_SESSION['first_name'])) {
    error_log("User is already logged in: " . $_SESSION['first_name']);
    header("Location: dashboard.php"); // Redirect to dashboard if already logged in
    exit();
} else{
    error_log("User is not logged in");
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $first_name, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Store the user ID in the session
            $_SESSION['user_id'] = $user_id;
            // Store the first name in the session
            $_SESSION['first_name'] = $first_name;
            header("Location: dashboard.php"); // Redirect to dashboard
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    include 'header.php'; // Include the header
    ?>
    <div class="lagayan">
        <img src="../img/logo.png" class="bilog">
        <div class="title">
            <h1>ORGANISS</h1>
        </div>
        <div class="LoginForm">
            <form action="" method="post">
                <div class="emailCont">
                    Email
                    <input type="text" name="email" class="EmailBox" value="<?php echo htmlspecialchars($email);?>" required>
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
    include 'footer.php'; // Include the footer

    // Display SweetAlert2 alert if there is a login error
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