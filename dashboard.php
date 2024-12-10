<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <div class="welcome">
    <?php
    session_start();
    require_once __DIR__ . "/db.php";
    $db = new db();

    if (!isset($_SESSION["user_id"])) {
        header("Location: /");
        exit();
    }

    $user = $db->getUserById($_SESSION["user_id"]);

    if (empty($user["password"])) {
        header("Location: /newPass");
        exit();
    }

    echo "Welcome, " . htmlspecialchars($_SESSION["first_name"]) . "!";
    ?>
    </div>
    <div class="underWelcome">
        <div class="logoutCont">
            <form method="POST" action="dashboard.php">
                <button type="submit" name="logout" class="logoutButton">Logout</button>
            </form>
            <?php
            if (isset($_POST["logout"])) {
                session_unset();
                session_destroy();
                header("Location: /index.php");
                exit();
            }
            ?>
        </div>
    </div>
    <div class="upperTab">
        <div class="searchBar">
            <input type="text" placeholder="Search" class="searchInput">
            <button class="searchButton">Search</button>
        </div>
        <div class="newTaskButton">+ New Task</div>
        <div class="time">
            <p id="currentTime"></p>
            <script>
                function updateTime() {
                    const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                    document.getElementById(`currentTime`).textContent = currentTime;
                }
                updateTime();
                setInterval(updateTime, 1000);
            </script>
        </div>
    </div>
</body>
</html>