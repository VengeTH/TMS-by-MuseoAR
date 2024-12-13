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
                    swal.fire('Success', 'Password changed successfully', 'success').then(() => {
                        window.location.href = '/dashboard';
                    });
                </script>";
    		} else {
    			echo "<script>
                    swal.fire('Error', 'Failed to change password', 'error');
                </script>";
    		}

    		$stmt->close();
    	} else {
    		echo "<script>
                swal.fire('Error', 'Passwords do not match', 'error');
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
<div class="form">
    <form method="POST" >
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required><br><br>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br><br>
        <button type="submit" id="submitButton">Change Password</button>
        <button type="button" id="logoutButton" onclick="window.location.href='logout.php'">Cancel</button>
    </form>
</div>

    <?php require_once dirname(__DIR__) . "/components/footer.php"; ?>
</body>
</html>