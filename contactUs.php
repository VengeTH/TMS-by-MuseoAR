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
        <!-- input address -->
        <p>Address: 1234 Main St, Springfield, IL 62701</p>
        <!-- input phone number -->
        <p>Phone: (217) 555-5555</p>
        <!-- input email -->
        <p>Email:
            <a href="mailto:museoar2024@gmail.com">
            </a>
        </p>
    </div>
    <div class="contactForm">
        <form action="" method="post">
            <h1>Send a Message</h1>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
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