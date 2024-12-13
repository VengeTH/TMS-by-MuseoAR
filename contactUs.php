<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="stylesheet" href="/css/contactUs.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <?php include "headerWhite.php" ?>
    <div class="contactUsUpper">
        <h1>Contact Us</h1>
        <p>We’d love to hear from you! If you have any questions, feedback, or suggestions about OrgaNiss, feel free to reach out using the form below.</p>
    </div>
    <div class="getInTouch">
        <h1>Get in Touch</h1>
        <div class="containerAddress">
            <img src="/img/location.png" alt="Location Marker">
            <h2>Address</h2>
            <p>STI Academic Center, Alabang-Zapote Road, corner V.Guinto, Las Piñas, 1740 Metro Manila</p>
        </div>
        <div class="containerContact">
            <img src="/img/telephone.png" alt="Phone Icon">
            <h2>Contact Number</h2>
            <p>+63 912 3456 789</p>
        </div>
        <div class="containerEmail">
            <img src="/img/email.png" alt="Email Icon">
            <h2>E-Mail:</h2>
            <a href="mailto:museoar2024@gmail.com">
                <p>museoar2024@gmail.com</p>
            </a>
        </div>
        <div class="containerFollowUs">
            <h2>Follow Us:</h2>
            <div class="socialMedia">
                <a href="https://www.facebook.com/STIMuseoAR" target="_blank">
                    <img src="/img/facebook.png" alt="Facebook Icon">
                </a>
                <a href="https://twitter.com/STIMuseoAR" target="_blank">
                    <img src="/img/twitter.png" alt="Twitter Icon">
                </a>
                <a href="https://www.instagram.com/stimuseoar/" target="_blank">
                    <img src="/img/instagram.png" alt="Instagram Icon">
                </a>
            </div>
        </div>
    </div>
    <div class="contactForm">
        <form action="" method="post">
            <h1>Send a Message</h1>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">E-mail address</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" required></textarea>

            <input type="checkbox" id="notice" name="notice" value="notice">
            <label for="notice">By submitting this form, you agree to the processing of your personal information in accordance with our Privacy Policy. Your data will be used solely to respond to your inquiries and provide the assistance you requested. We value your privacy and ensure the protection of your information.</label>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
            <?php
            include "footer2.php";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $message = htmlspecialchars($_POST['message']);

                $to = "museoar2024@gmail.com";
                $subject = "Contact Us Form Submission";
                $body = "Name: $name\nEmail: $email\nMessage: $message";
                $headers = "From: $email";

                if (mail($to, $subject, $body, $headers)) {
                    echo "Email successfully sent.";
                } else {
                    echo "Email sending failed.";
                }
            }
            ?>
</html>
</div>