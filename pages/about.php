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
    <link rel="stylesheet" href="/css/aboutUs.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <?php
        include dirname(__DIR__) . "/components/headerWhite.php";
    ?>
    content here
    <?php
        include dirname(__DIR__) . "/components/footer2.php";
    ?>
</body>
</html>

