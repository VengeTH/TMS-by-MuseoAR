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
<body style="display:flex; min-height:100vh;">

    <!-- left side -->
    <div class="container">
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
        <a href="?tab=dashboard" class="dashboardButton links active">
                Dashboard
        </a>
        <a href="?tab=myTask" class="myTaskButton links">
                My Task
        </a>
        <span class="otherText">
            OTHER
        </span>
        <a href="?tab=help" class="helpButton links">
                Help
        </a>
        <a href="?tab=settings" class="settingsButton links">
                Settings
        </a>
            <a href="/user/logout" class="logoutButton">Logout</a>
    </div>
    <!-- right-side -->
     <div class="container-right">
        <div class="upperTab">
            <img src="/img/logo.png" alt="" srcset="" style="min-height:1rem; max-height:5rem;">
            <div class="searchBar">
                <input type="text" placeholder="Search" class="searchInput">
                <button class="searchButton">
                    <img src="/img/Search.png" alt="" srcset="" style="max-width:2rem;">
                </button>
            </div>
            <div style="margin-left:auto; margin-right:3rem;display:flex;align-items:center;gap:1rem;">
                <button class="newTaskButton" onclick="showNewTaskModal()">+ New Task</button>
                <a href="#" class="user-profile">
                        <?php $profilePicture = !empty($user["profile_picture"])
                        	? htmlspecialchars($user["profile_picture"])
                        	: "/img/defaultPFP.png"; ?>
                        <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture">
                </a>
                <span class="time" id="currentTime">
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
                </span>
            </div>

        </div>
        <div class="middleContent">
            <div class="firstChild">
                <div class="date">
                    <?php include "../components/calendar.php"; ?>
                </div>
                <div class="stats">
                    stats
                </div>
            </div>
            <div class="secondChild">
                <div class="tasks">
                    <?php
                    $tasks = $db->getTasks();
                    if ($tasks->num_rows > 0) {
                        while ($task = $tasks->fetch_assoc()) {
                            $finishDate = new DateTime($task["finish_date"]);
                            $formattedFinishDate = $finishDate->format("F d, Y h:i A"); // Format to include month name
                            echo "<div class='task'>";
                            echo "<p>" .
                                htmlspecialchars($task["title"]) .
                                " - " .
                                htmlspecialchars($task["details"]) .
                                " - Priority: " .
                                htmlspecialchars($task["priority"]) .
                                " - Finish Date: " .
                                htmlspecialchars($formattedFinishDate) .
                                "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No tasks found</p>";
                    }
                    ?>
                </div>
                <div class="news">
                    news
                </div>
            </div>
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