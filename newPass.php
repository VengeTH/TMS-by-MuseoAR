<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/howToAppendData.js"></script>
</head>
<body>
    <form id="resetPasswordForm" >
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required><br><br>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br><br>
        <button type="submit">Insert Password</button>
    </form>
    <?php session_start(); ?>
    <script>
        fetch("getEmail.php")
            .then((response) => response.json())
            .then((data) => {
                const email = data.email

                fetch("resetPassword.php", {
                    method: "POST",
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Password has been reset!",
                            }).then(() => {
                                window.location.href = "dashboard"
                            })
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: data.message,
                            })
                        }
                    })
                    .catch((error) => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred!",
                        })
                    })
            })
            .catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to get email!",
                })
            })

    </script>
</body>
</html>