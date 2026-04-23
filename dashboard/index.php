<?php
    // session_start(); // Remove this line
    if (!isset($_GET['tab'])) {
        header("Location: /dashboard?tab=dashboard");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OrgaNiss</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel=stylesheet href="/css/content.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (() => {
            const key = "th.dashboard.preferences.v1";
            const defaults = {
                interfaceTheme: "heedful-dark",
                sidebarDensity: "comfortable",
                emailUpdates: "daily",
                weekendMode: true,
                sessionTimeout: "30",
                taskDueAlerts: true
            };

            let preferences = defaults;
            try {
                const raw = localStorage.getItem(key);
                if (raw) {
                    preferences = { ...defaults, ...JSON.parse(raw) };
                }
            } catch (_error) {
                preferences = defaults;
            }

            document.documentElement.dataset.interfaceTheme = preferences.interfaceTheme;
            document.documentElement.dataset.sidebarDensity = preferences.sidebarDensity;
            document.documentElement.dataset.emailUpdates = preferences.emailUpdates;
            document.documentElement.dataset.weekendMode = preferences.weekendMode ? "on" : "off";
            document.documentElement.dataset.sessionTimeout = String(preferences.sessionTimeout || "30");
            document.documentElement.dataset.taskDueAlerts = preferences.taskDueAlerts ? "on" : "off";
        })();
    </script>
</head>
<body style="display:flex; min-height:100vh;">
    <!-- left side -->
    <div class="nilalaman">
        <!-- welcome text -->
        <div class="welcome">
            <?php
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
        <a href="?tab=weeklyPlanner" class="helpButton links <?php echo ($_GET["tab"] == 'weeklyPlanner') ? 'active' : ''; ?>">
                AI Weekly Planner
        </a>
        <a href="?tab=help" class="helpButton links <?php echo ($_GET["tab"] == 'help') ? 'active' : ''; ?>">
                Help
        </a>
        <a href="?tab=settings" class="settingsButton links <?php echo ($_GET["tab"] == 'settings') ? 'active' : ''; ?>">
                Settings
        </a>
            <a href="/user/logout" class="logoutButton">Logout</a>
    </div>
    <main class="container-right">
        <?php $active = $_GET["tab"] ?? "dashboard"; ?>

        <?php if ($active !== "dashboard" && $active !== "weeklyPlanner"): ?>
            <div class="upperTab upperTab--plain">
                <div class="upperTab__title"><?php echo htmlspecialchars(ucfirst($active)); ?></div>
                <a href="?tab=profile" class="user-profile" title="Profile &amp; Account">
                    <?php $profilePicture = !empty($user["profile_picture"]) ? htmlspecialchars($user["profile_picture"]) : "/img/defaultPFP.png"; ?>
                    <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture">
                </a>
                <span class="time" id="currentTime">--:--</span>
            </div>
        <?php endif; ?>

        <div class="tab-content">
            <?php
                if ($active == "dashboard") {
                    require_once dirname(__DIR__)."/components/dashboard.php";
                } else if ($active == "myTask") {
                    require_once dirname(__DIR__)."/components/task.php";
                } else if ($active == "help") {
                    require_once dirname(__DIR__)."/components/help.php";
                } else if ($active == "weeklyPlanner") {
                    require_once dirname(__DIR__)."/components/weekly-planner-tab.php";
                } else if ($active == "profile") {
                    require_once dirname(__DIR__)."/components/profile.php";
                } else if ($active == "settings") {
                    require_once dirname(__DIR__)."/components/settings.php";
                }
            ?>
        </div>
    </main>

    <script>
        (() => {
            const timeEl = document.getElementById("currentTime");
            if (!timeEl) {
                return;
            }
            const tick = () => {
                timeEl.textContent = new Date().toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: true,
                });
            };
            tick();
            setInterval(tick, 1000);
        })();

        (() => {
            let timeoutId = null;

            const getTimeoutMs = () => {
                const minutesRaw = document.documentElement.dataset.sessionTimeout || "30";
                const minutes = parseInt(minutesRaw, 10);
                if (!Number.isFinite(minutes) || minutes <= 0) {
                    return 0;
                }
                return minutes * 60 * 1000;
            };

            const resetTimer = () => {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                const timeoutMs = getTimeoutMs();
                if (timeoutMs <= 0) {
                    return;
                }
                timeoutId = setTimeout(() => {
                    window.location.href = "/user/logout";
                }, timeoutMs);
            };

            ["mousemove", "keydown", "mousedown", "touchstart", "scroll"].forEach((eventName) => {
                window.addEventListener(eventName, resetTimer, { passive: true });
            });

            window.addEventListener("th-preferences-updated", () => {
                resetTimer();
            });

            resetTimer();
        })();
    </script>
</body>
</html>