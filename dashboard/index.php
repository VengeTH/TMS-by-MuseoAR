<!-- updated -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Management by MuseoAR</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
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
    ?>
    </div>
    <div class="underWelcome">
        <div class="logoutCont">
            <a href="/user/logout" class="logoutButton">Logout</a>
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
                const interval = setInterval(updateTime, 1000);
                window.addEventListener('beforeunload', () => {
                    clearInterval(interval);
                });
                delete updateTime; // cleanup
            </script>
        </div>
    </div>
</body>
</html>