<?php
// Start output buffering to prevent premature output issues

include 'db.php'; // Include the database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate terms and conditions agreement
    if (!isset($_POST['agreement'])) {
        die("You must agree to the terms and conditions.");
    }

    // Sanitize and validate form inputs
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

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for duplicate email
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        die("This email is already registered.");
    }
    $check_stmt->close();

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        // Start the session and redirect
        session_start();
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['first_name'] = $first_name;

        header("Location: dashboard.php");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
    $stmt->close();
}
$conn->close();
ob_end_flush(); // End output buffering
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="icon" href="../img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/register.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'header.php'; ?>
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
                <a href="google-login.php">
                <div class="googleButton">Google</div></a>
                <a href="#">
                <div class="facebookButton">Facebook</div></a>
            </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>