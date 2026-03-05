<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - OrgaNiss</title>
<?php
$seo_title = "Contact Us - OrgaNiss";
$seo_description = "Get in touch with the OrgaNiss team. Questions, feedback, or support for our task management system. STI Academic Center, Las Piñas.";
$seo_canonical = "/pages/contact";
require_once dirname(__DIR__) . "/components/seo-meta.php";
require_once dirname(__DIR__) . "/components/json-ld-organization.php";
?>
    <link rel="stylesheet" href="/css/contactUs.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <?php include dirname(__DIR__) . "/components/headerWhite.php" ?>
    <div class="contactUsUpper">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! If you have any questions, feedback, or suggestions about <strong>OrgaNiss</strong>, feel free to reach out using the form below.</p>
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
            <a href="mailto:prinzesadsad@theheeful.me">
                <p>prinzesadsad@theheeful.me</p>
            </a>
        </div>
        <div class="containerFollowUs">
            <h2>Follow Us:</h2>
            <div class="socialMedia">
                <a href="https://www.facebook.com/theheedful" target="_blank">
                    <img src="/img/facebook.png" alt="Facebook Icon">
                </a>
                <a href="https://twitter.com/theheedful" target="_blank">
                    <img src="/img/twitter.png" alt="Twitter Icon">
                </a>
                <a href="https://www.instagram.com/theheedful/" target="_blank">
                    <img src="/img/instagram.png" alt="Instagram Icon">
                </a>
            </div>
        </div>
    </div>
    <div class="contactForm">
        <form action="" method="post" onsubmit="return validateForm()">
            <h1>Send a Message</h1>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">E-mail address</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" required></textarea>

            <input type="checkbox" id="notice" name="notice" value="notice" required>
            <label for="notice">By submitting this form, you agree to the processing of your personal information in accordance with our Privacy Policy. Your data will be used solely to respond to your inquiries and provide the assistance you requested. We value your privacy and ensure the protection of your information.</label>

            <button type="submit">Submit</button>
        </form>
    </div>
    <script>
        function validateForm() {
            var checkbox = document.getElementById('notice');
            if (!checkbox.checked) {
                alert('You must agree to the privacy policy before submitting.');
                return false;
            }
            return true;
        }
    </script>
    <?php
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;

        require dirname(__DIR__) . "/vendor/autoload.php";
        require_once dirname(__DIR__) . "/helpers/env.php";

        include dirname(__DIR__) . "/components/footer2.php";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = htmlspecialchars($_POST["name"]);
            $email = htmlspecialchars($_POST["email"]);
            $message = htmlspecialchars($_POST["message"]);

            $smtpHost = safeEnv("SMTP_HOST", "smtp.gmail.com");
            $smtpUser = safeEnv("SMTP_USER", "organizzbymuseoar@gmail.com");
            $smtpPass = safeEnv("SMTP_PASS", "juli-anne2024");
            $smtpPort = (int) safeEnv("SMTP_PORT", "587");
            $toAddress = safeEnv("CONTACT_TO_EMAIL", "prinzesadsad@theheeful.me");

            $mail = new PHPMailer(true);
            try {
                // * Server settings
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUser;
                $mail->Password = $smtpPass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $smtpPort;

                // * Recipients
                $mail->setFrom($email, $name);
                $mail->addAddress($toAddress);

                // * Content
                $mail->isHTML(true);
                $mail->Subject = "Contact Us Form Submission";
                $mail->Body    = "Name: " . $name . "<br>Email: " . $email . "<br>Message: " . $message;

                $mail->send();
                echo "Email successfully sent.";
            } catch (Exception $e) {
                echo "Email sending failed. Mailer Error: " . $mail->ErrorInfo;
            }
        }
    ?>
</body>
</html>

