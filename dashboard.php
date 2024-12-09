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
    if (!isset($_SESSION["user_id"])) {
    	header("Location: /");
    	exit();
    }

    echo "Welcome, " . htmlspecialchars($_SESSION["first_name"]) . "!";
    ?>
    </div>
    <div class="underWelcome">
        <div class="logoutCont">
        <button type="submit" class="logoutButton">Logout</div>
            <?php if (isset($_GET["logout"])) {
            	session_unset();
            	session_destroy();
            	header("Location: /");
            	exit();
            } ?> 
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
    <script>
        document.querySelector('.logoutButton').addEventListener('click', function() {
            window.location.href = '/dashboard?&logout=true';
        });
    </script>
    

</body>
</html>
