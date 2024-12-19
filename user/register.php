<?php
require_once dirname(__DIR__) . "/db/db.php"; // Include the database file
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: /dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/register.css">
    <script src="/js/sweetalert.js"></script>
</head>
<body class="index">
    <?php include dirname(__DIR__) . "/components/header.php"; ?>
    <div class="wrapper">
        <?php include dirname(__DIR__) . "/components/welcomeMessage.php"; ?>
        <div class="right">
            <div class="createAccText">
                <h1>Create Account</h1>
                <p>Already have an account? <a href="/">Log in</a></p>
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
                                    html: '<iframe id="termsFrame" src="/termsAndConditions.html" width="100%" height="550px" style="border:none;"></iframe>',
                                    width: '80%',
                                    showCloseButton: true,
                                    focusConfirm: false,
                                    confirmButtonText: 'Accept',
                                    didOpen: () => {
                                        const termsFrame = document.getElementById('termsFrame');
                                        termsFrame.addEventListener('load', () => {
                                            termsFrame.contentWindow.addEventListener('scroll', () => {
                                                const scrollHeight = termsFrame.contentDocument.documentElement.scrollHeight;
                                                const scrollTop = termsFrame.contentDocument.documentElement.scrollTop;
                                                const clientHeight = termsFrame.contentDocument.documentElement.clientHeight;
                                                if (scrollTop + clientHeight >= scrollHeight) {
                                                    Swal.getConfirmButton().disabled = false;
                                                }
                                            });
                                        });
                                    },
                                    preConfirm: () => {
                                        document.getElementById('agreementCheckbox').checked = true;
                                        document.getElementById('agreementCheckbox').disabled = false;
                                    }
                                });
                                Swal.getConfirmButton().disabled = true;
                            });
                        </script>
                    </div>
                    <button type="submit" class="createAccButton">Create Account</button>
                    <p>----------------------- Or register with -----------------------</p>
                    <div class="altContainer">
                        <a href="/oauth/google-callback?login=true">
                            <div class="googleButton">Google</div>
                        </a>
                        <a href="/oauth/facebook.php">
                            <div class="facebookButton">Facebook</div>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include_once dirname(__DIR__) . "/components/footer.php"; ?>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            fetch(form.action, {
                method: form.method,
                body: formData
            }).then(response => response.text())
            .then(html => {
                document.open();
                document.write(html);
                document.close();
            });
        });
    </script>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new db();
    if (!isset($_POST["agreement"])) {
        echo "<script>
            Swal.fire('Error', 'You must agree to the terms and conditions.', 'error');
        </script>";
        exit();
    }
    $first_name = trim($_POST["firstName"]);
    $last_name = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo "<script>
            Swal.fire('Error', 'All fields are required.', 'error');
        </script>";
        exit();
    }
    if (!preg_match("/^[a-zA-Z]+$/", $first_name)) {
        echo "<script>
            Swal.fire('Error', 'First name can only contain letters.', 'error');
        </script>";
        exit();
    }
    if (!preg_match("/^[a-zA-Z]+$/", $last_name)) {
        echo "<script>
            Swal.fire('Error', 'Last name can only contain letters.', 'error');
        </script>";
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            Swal.fire('Error', 'Invalid email format.', 'error');
        </script>";
        exit();
    }
    if (strlen($password) < 8) {
        echo "<script>
            Swal.fire('Error', 'Password must be at least 8 characters long.', 'error');
        </script>";
        exit();
    }
    $emailExists = $db->getUser($email);
    if ($emailExists) {
        echo "<script>
            Swal.fire('Error', 'Email already exists.', 'error');
        </script>";
        exit();
    }
    $user = $db->addUser($first_name, $last_name, $email, null, $password); // Ensure password is passed
    if (!$user) {
        echo "<script>
            Swal.fire('Error', 'Error: Unable to add user.', 'error');
        </script>";
        exit();
    }
    $_SESSION["user_id"] = $db->getLastInsertId();
    $_SESSION["first_name"] = $first_name;
    echo "<script>
        Swal.fire('Success', 'Registration successful', 'success').then(() => {
            window.location.href = '/dashboard';
        });
    </script>";
    exit();
}
?>