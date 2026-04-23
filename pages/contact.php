<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - OrgaNiss</title>
<?php
$seo_title = "Contact Us - OrgaNiss";
$seo_description = "Get in touch with the OrgaNiss team. Questions, feedback, or support for our task management system. STI Academic Center, Las Piñas.";
$seo_canonical = "/pages/contact.php";
require_once dirname(__DIR__) . "/components/seo-meta.php";
require_once dirname(__DIR__) . "/components/json-ld-organization.php";
?>
    <link rel="stylesheet" href="/css/contactUs.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <?php include dirname(__DIR__) . "/components/header.php" ?>
    
    <div class="page-wrapper">
        <div class="contactUsUpper">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you! If you have any questions, feedback, or suggestions about <strong>OrgaNiss</strong>, feel free to reach out using the form below.</p>
        </div>

        <div class="contact-content-grid">
            <div class="getInTouch">
                <h2>Get in Touch</h2>
                
                <div class="info-group">
                    <img src="/img/location.png" alt="Location Marker" class="info-icon">
                    <div class="info-details">
                        <h3>Address</h3>
                        <p>Alabang, Muntinlupa</p>
                    </div>
                </div>

                <div class="info-group">
                    <img src="/img/telephone.png" alt="Phone Icon" class="info-icon">
                    <div class="info-details">
                        <h3>Contact Number</h3>
                        <p>+639937172004</p>
                    </div>
                </div>

                <div class="info-group">
                    <img src="/img/email.png" alt="Email Icon" class="info-icon">
                    <div class="info-details">
                        <h3>E-Mail</h3>
                        <p><a href="mailto:prinzesadsad@theheeful.me">prinzesadsad@theheeful.me</a></p>
                    </div>
                </div>

                <div class="info-group">
                    <div class="info-details">
                        <h3>Follow Us</h3>
                        <div class="socialMedia">
                            <a href="https://www.facebook.com/TheHeedfulPH" target="_blank"><img src="/img/facebook.png" alt="Facebook Icon"></a>
                            <a href="https://github.com/VengeTH" target="_blank"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg" alt="Github Icon" style="filter: invert(1);"></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contactForm">
                <form action="" method="post" onsubmit="return validateForm()">
                    <h2>Send a Message</h2>
                    
                    <div class="form-wrapper">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-text" required>
                    </div>

                    <div class="form-wrapper">
                        <label for="email">E-mail address</label>
                        <input type="email" id="email" name="email" class="form-text" required>
                    </div>

                    <div class="form-wrapper">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-text" required></textarea>
                    </div>

                    <label class="rememberMe policy-label" for="notice">
                        <input type="checkbox" id="notice" name="notice" value="notice" required>
                        <span>By submitting this form, you agree to our Privacy Policy. Your data will be used solely to respond to your inquiries.</span>
                    </label>

                    <button type="submit" class="ContinueButton">Submit Message</button>
                </form>
            </div>
        </div>
    </div>

    <?php include dirname(__DIR__) . "/components/footer.php"; ?>

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

