<!-- updated -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Management by MuseoAR</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="welcome">
    <?php
    require '../vendor/autoload.php';
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
    $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
    ?>
    </div>
    <div class="container">
        <a href="?tab=dashboard" class="dashboardButton">
            <div class="dashboardDiv <?php echo $activeTab === 'dashboard' ? 'active' : ''; ?>" style="color: <?php echo $activeTab === 'dashboard' ? '#fff' : '#321c24'; ?>;">
                Dashboard
            </div>
        </a>
        <a href="?tab=myTask" class="myTaskButton">
            <div class="myTaskDiv <?php echo $activeTab === 'myTask' ? 'active' : ''; ?>" style="color: <?php echo $activeTab === 'myTask' ? '#fff' : '#321c24'; ?>;">
                My Task
            </div>
        </a>
        <div class="otherText">
            OTHER
        </div>
        <a href="?tab=help" class="helpButton">
            <div class="help <?php echo $activeTab === 'help' ? 'active' : ''; ?>" style="color: <?php echo $activeTab === 'help' ? '#fff' : '#321c24'; ?>;">
                Help
            </div>
        </a>
        <a href="?tab=settings" class="settingsButton">
            <div class="settings <?php echo $activeTab === 'settings' ? 'active' : ''; ?>" style="color: <?php echo $activeTab === 'settings' ? '#fff' : '#321c24'; ?>;">
                Settings
            </div>
        </a>
        <div class="logoutCont">
            <a href="/user/logout" class="logoutButton">Logout</a>
        </div>
    </div>
    <div class="upperTab">
        <div class="searchBar">
            <input type="text" placeholder="Search" class="searchInput">
            <button class="searchButton">Search</button>
        </div>
        <div class="newTaskButton" onclick="showNewTaskModal()">+ New Task</div>
        <a href="#kalokohan">
        <div class="profile">
            <?php
            $profilePicture = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : '/img/defaultPFP.png';
            ?>
            <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture">
        </div>
        </a>
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
    <div class="middleContent">
        <div class="date">
            <?php
            include '../components/calendar.php';
            ?>
        </div>
        <div class="tasks">
            <?php
            $tasks = $db->getTasks();
            if ($tasks->num_rows > 0) {
                while($task = $tasks->fetch_assoc()) {
                    $finishDate = new DateTime($task["finish_date"]);
                    $formattedFinishDate = $finishDate->format('F d, Y h:i A'); // Format to include month name
                    echo "<div class='task'>";
                    echo "<p>" . htmlspecialchars($task["title"]) . " - " . htmlspecialchars($task["details"]) . " - Priority: " . htmlspecialchars($task["priority"]) . " - Finish Date: " . htmlspecialchars($formattedFinishDate) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No tasks found</p>";
            }
            ?>
        </div>
        <div class="stats">
            stats
        </div>
        <div class="news">
            news
        </div>
    </div>
    <script>
function showNewTaskModal() {
    Swal.fire({
        title: 'New Task',
        html: `
            <input type="text" id="taskTitle" class="swal2-input" placeholder="Task Title *" required>
            <textarea id="taskDetails" class="swal2-textarea" placeholder="Task Details"></textarea>
            <input type="datetime-local" id="finishDate" class="swal2-input" required>
            <select id="taskPriority" class="swal2-select">
                <option value="low">Low Priority</option>
                <option value="medium">Medium Priority</option>
                <option value="high">High Priority</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const title = document.getElementById('taskTitle').value;
            const finishDate = document.getElementById('finishDate').value;

            if (!title.trim()) {
                Swal.showValidationMessage('Task title is required');
                return false;
            }
            if (!finishDate) {
                Swal.showValidationMessage('Finish date is required');
                return false;
            }

            return {
                title: title,
                details: document.getElementById('taskDetails').value,
                finishDate: finishDate,
                priority: document.getElementById('taskPriority').value
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Send data to server
            fetch('/api/tasks/create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Success!', 'Task created successfully', 'success');
                    loadTasks();
                } else {
                    Swal.fire('Error!', data.message || 'Failed to create task', 'error');
                }
            });
        }
    });
}
</script>
</body>
</html>