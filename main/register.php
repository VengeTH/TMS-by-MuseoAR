<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="icon" href="../img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/register.css">
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
                <input type="checkbox" name="agreement" required>
                <p>I agree to the <a href="#" id="termsLink">Terms and Conditions</a></p>
                <div id="termsModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <iframe src="termsAndConditions.html" width="100%" height="400px"></iframe>
                    </div>
                </div>
                <script>
                    var modal = document.getElementById("termsModal");
                    var link = document.getElementById("termsLink");
                    var span = document.getElementsByClassName("close")[0];

                    link.onclick = function(event) {
                        event.preventDefault();
                        modal.style.display = "block";
                    }

                    span.onclick = function() {
                        modal.style.display = "none";
                    }

                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                </script>
            </div>
            <button type="submit" class="createAccButton">Create Account</button>
            <p>----------------------- Or register with -----------------------</p>
            <div class="altContainer">
                <a href="google-login.php">
                <div class="googleButton">Google</div></a>
                <a href="#">
                <div class="facebookButton">Facebook</div></a>
            </div>
        </form>
    </div>
    <?php
include 'db.php'; // Include the database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form submission detected. ";
    print_r($_POST); // Show form inputs to ensure they're being received.
    // Check if the terms checkbox is checked
    if (!isset($_POST['agreement'])) {
        die("You must agree to the terms and conditions.");
    }

    // Sanitize and validate inputs
    $first_name = trim($_POST['firstName']);
    $last_name = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        die("This email is already registered.");
    }
    $check_stmt->close();

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        // Start a session and store user info
        session_start();
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['first_name'] = $first_name;

        // Registration successful, redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
</body>
</html>