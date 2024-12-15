<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/newPass.css">
    <script  src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    session_start();
    require_once dirname(__DIR__) . "/db/db.php";
    $db = new db();
    $id = null;
    if (isset($_SESSION["user_id"])) {
    	$id = $_SESSION["user_id"];
    	$user = $db->getUserById($id);
    	if (!empty($user["password"])) {
    		header("Location: /dashboard");
    		exit();
    	}
    } else {
    	header("Location: /");
    	exit();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    	$newPassword = $_POST["newPassword"];
    	$confirmPassword = $_POST["confirmPassword"];

    	if ($newPassword === $confirmPassword) {
    		$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    		$stmt = $db->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    		$stmt->bind_param("si", $hashedPassword, $id);

    		if ($stmt->execute()) {
    			echo "<script>
                    Swal.fire('Success', 'Password changed successfully', 'success').then(() => {
                        window.location.href = '/dashboard';
                    });
                </script>";
    		} else {
    			echo "<script>
                    Swal.fire('Error', 'Failed to change password', 'error');
                </script>";
    		}

    		$stmt->close();
    	} else {
    		echo "<script>
                Swal.fire('Error', 'Passwords do not match', 'error');
            </script>";
    	}
    }
    ?>
<div class="header">
    <div class="logo">
        <img src="/img/logo.png" class="logo" width=6% height=6%>
    </div>
    <div class="titleBesideLogo">
        <h1>ORGANISS</h1>
    </div>
</div>
<div class="upperlogo">
    <img src="/img/logo.png" class="logo" width=6% height=6%>
    <h1>Change Password</h1>
</div>
<div class="textInfo">
    <h1>Create a strong password</h1>
    <p>Create a new, strong password that you don’t use for other websites.</p>
</div>
<div class="form">
    <form method="POST">
        <div class="password-container">
            <label for="newPassword">Create Password</label>
            <input type="password" id="newPassword" name="newPassword" required>
        </div>
        <br>
        <div class="password-container">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
        </div>
        <br>
        <div class="buttons">
        <button type="button" id="logoutButton" onclick="window.location.href='/user/logout.php'">Cancel</button>
        <button type="submit" id="submitButton">Change Password</button>
        </div>
    </form>
</div>
    <?php require_once dirname(__DIR__) . "/components/newFooter.php"; ?>
</body>
</html>