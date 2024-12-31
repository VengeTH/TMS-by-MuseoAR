<!-- updated -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Management by MuseoAR</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel=stylesheet href="/css/content.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="display:flex; min-height:100vh;">
    <?php
        // session_start(); // Remove this line
        if(!isset($_GET['tab'])) {
            header("Location: /dashboard?tab=dashboard");
        }
    ?>
    <!-- left side -->
    <div class="nilalaman">
        <!-- welcome text -->
        <div class="welcome">
            <?php
            require "../vendor/autoload.php";
            use benhall14\phpCalendar\Calendar as Calendar;
            require_once dirname(__DIR__) . "/helpers/sessionHandler.php";
            require_once dirname(__DIR__) . "/db/db.php";
            $db = new db();
            $user = $db->getUserById($_SESSION["user_id"]);
            if (empty($user["password"])) {
            	header("Location: /verify/password");
            	exit();
            }
            echo "Welcome, " . htmlspecialchars($_SESSION["first_name"]) . "!";
            $activeTab = isset($_GET["tab"]) ? $_GET["tab"] : "dashboard";
            ?>
        </div>
        <!-- links -->

        <a href="?tab=dashboard" class="dashboardButton links <?php echo ($_GET["tab"]== 'dashboard') ? ' active' : ''; ?>">
                Dashboard
        </a>

        <a href="?tab=myTask" class="myTaskButton links <?php echo ($_GET["tab"] == 'myTask') ? 'active' : ''; ?>">
                My Task
        </a>
        <span class="otherText">
            OTHER
        </span>
        <a href="?tab=help" class="helpButton links <?php echo ($_GET["tab"] == 'help') ? 'active' : ''; ?>">
                Help
        </a>
        <a href="?tab=settings" class="settingsButton links <?php echo ($_GET["tab"] == 'settings') ? 'active' : ''; ?>">
                Settings
        </a>
            <a href="/user/logout" class="logoutButton">Logout</a>
    </div>
    <!-- right-side -->
     <?php
        $active = $_GET["tab"] ?? "dashboard";
        if ($active == "dashboard") {
            require_once dirname(__DIR__)."/components/dashboard.php";
        }
         else if ($active == "myTask") {
            require_once dirname(__DIR__)."/components/task.php";
        } else if ($active == "help") {
            require_once dirname(__DIR__)."/components/help.php";
        } else if ($active == "settings") {
            require_once dirname(__DIR__)."/components/settings.php";
        }
     ?>
</body>
</html>