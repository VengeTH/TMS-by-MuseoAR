<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - OrgaNiss</title>
<?php
$seo_title = "About Us - OrgaNiss";
$seo_description = "Learn about OrgaNiss, the task management system by The Heedful. Organize tasks, reminders, and AI planning for students and professionals.";
$seo_canonical = "/pages/about";
require_once dirname(__DIR__) . "/components/seo-meta.php";
require_once dirname(__DIR__) . "/components/json-ld-organization.php";
?>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/aboutUs.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <?php include dirname(__DIR__) . "/components/header.php"; ?>

    <div class="wrapper">
        <div class="aboutUsUpper">
            <h1>About The Heedful</h1>
            <p>Building Tomorrow's Leading Technology Company</p>
        </div>

        <div class="about-content">
            <section class="about-section">
                <h2>Our Mission</h2>
                <p>The Heedful is a technology startup focused on becoming a successful, well-known company across all industries through high-quality software development and innovative solutions. We build cutting-edge applications, provide exceptional services, and create positive impact—including environmental initiatives like tree planting programs—while establishing ourselves as leaders in technology.</p>
            </section>

            <section class="about-section highlight-section">
                <h2>The Founder</h2>
                <div class="founder-card">
                    <div class="founder-info">
                        <h3>Prinze Mikhail Sadsad</h3>
                        <p class="role">Founder & Chief Executive Officer</p>
                        <p>Prinze Mikhail Sadsad guides company direction, product vision, and long-term strategy. From teenage curiosity to purpose-driven innovation, Prinze leads The Heedful to build solutions that truly matter. With expertise spanning Web, Mobile, and Enterprise development, he architects systems designed for reliability, scale, and measurable business impact.</p>
                        <a href="https://vengeth.theheedful.me" target="_blank" class="portfolio-link">View Portfolio</a>
                    </div>
                </div>
            </section>

            <section class="about-section">
                <h2>Our Ecosystem</h2>
                <div class="ecosystem-grid">
                    <div class="eco-card">
                        <h3>SmartHR360</h3>
                        <p>Comprehensive HR management system featuring GPS-based attendance tracking, MFA security, and payroll.</p>
                    </div>
                    <div class="eco-card">
                        <h3>Fambi</h3>
                        <p>Community reporting and emergency response platform built to keep citizens safe and engaged.</p>
                    </div>
                    <div class="eco-card">
                        <h3>WellLink</h3>
                        <p>Digital platform focused on employee and organizational well-being and healthcare workflows.</p>
                    </div>
                    <div class="eco-card">
                        <h3>OrgaNiss</h3>
                        <p>An intelligent productivity platform utilizing AI for task breakdown and structured weekly planning.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include dirname(__DIR__) . "/components/footer.php"; ?>
</body>
</html>

