<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management by MuseoAR</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <?php
        echo "<h1 class='welcome'>Welcome, _SESSION['username']</h1>"
    ?>
    <div class="underWelcome">

    </div>
    <div class="upperTab">
        <div class="searchBar">
            <input type="text" placeholder="Search" class="searchInput">
            <button class="searchButton">Search</button>
        </div>
        <div class="newTaskButton">+ New Task</div>
        <div class="time">
            <p id="currentTime"></p>
            <?php
                echo "<script>function updateTime() {
                var currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                document.getElementById(`currentTime`).textContent = currentTime;
                }setInterval(updateTime, 1000);</script>";
            ?>
        </div>
    </div>
</body>
</html>