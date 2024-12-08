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
        <form>
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
                <p>I agree to the <a href="termsAndConditions.php">Terms and Conditions</a></p>
            </div>
            <button type="submit" class="createAccButton">Create Account</button>
        </form>
    </div>
</body>
</html>