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
        <a href="#">
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
    <div class="date">
        <?php
        include '../components/calendar.php';
        ?>
    </div>
    <div class="tasks">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum et urna ex. Donec sed vestibulum lectus, sed convallis augue. Quisque condimentum ac lorem eget tincidunt. Fusce at venenatis turpis. Aenean quis tincidunt tellus, sed rhoncus lectus. Proin convallis risus ut ligula pretium porttitor. Aliquam eleifend ac mauris sed efficitur.

Phasellus nec ante quis neque dignissim ullamcorper. Nullam ornare leo vitae ornare ullamcorper. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam ornare pretium ante eu venenatis. Proin tempus sed ipsum hendrerit tempus. Cras tempor feugiat blandit. Integer ultricies venenatis velit, vitae pretium enim suscipit vitae. Quisque vehicula, tortor vitae varius molestie, urna est lacinia augue, non rutrum leo eros ac mi. Mauris finibus facilisis est eu tincidunt. Duis sed arcu dictum, ultrices erat eget, convallis lacus. Nulla eu placerat lorem, vel rutrum felis.

Sed id ante ac mauris dictum congue dapibus interdum ligula. In tincidunt et sem quis scelerisque. Proin convallis nulla non lectus porta porta. Mauris aliquet enim est, sed rhoncus augue lacinia et. Morbi varius faucibus erat, eget tincidunt nibh laoreet sit amet. Fusce sit amet arcu quis lacus fermentum condimentum at elementum felis. Sed sollicitudin luctus augue ut ultrices. In sit amet feugiat tortor. Mauris porta turpis consectetur faucibus tempus. In iaculis justo at elit consequat, ac hendrerit massa eleifend. Cras ac urna suscipit, tempus dolor ut, eleifend felis. Quisque at ipsum non est ultricies molestie.

Duis eleifend neque nibh, non faucibus mauris pretium eget. Phasellus orci metus, iaculis vitae ultrices eget, laoreet ac erat. Sed leo enim, volutpat sit amet feugiat non, pharetra vel eros. Aliquam a leo tempus, sollicitudin ante at, tempus erat. Suspendisse potenti. Integer id lectus sit amet est rutrum tincidunt ut in metus. In mauris risus, hendrerit in tempus eu, fringilla quis nibh. Aliquam erat volutpat. Etiam at eros tellus. Aliquam consequat varius libero in laoreet. Aenean ac sapien libero. Suspendisse quis lorem sed felis mollis blandit. Suspendisse purus mi, sodales quis elit quis, sollicitudin suscipit felis. Duis blandit sem id dolor bibendum tempus. Nullam eget urna et dolor ornare blandit. Nullam elementum vitae nulla eu dignissim.

Integer ut laoreet velit, vitae vestibulum nibh. Mauris sed libero ut est laoreet tincidunt pellentesque eu nisl. Duis justo libero, rhoncus quis tortor eu, accumsan pretium quam. Maecenas auctor ac ipsum congue malesuada. Etiam cursus orci non sapien ullamcorper, eget tincidunt velit sagittis. Ut ut sapien non purus fringilla euismod. Mauris in nulla eu nisi pulvinar pulvinar. Etiam tempor cursus posuere. Nam faucibus augue sem. Proin ultricies velit enim, ut porta felis egestas a. Fusce laoreet tempus rhoncus. Donec pretium tellus vel eros fermentum, et gravida libero iaculis. Mauris accumsan aliquam lacus, pulvinar suscipit dolor gravida sit amet. Sed eget nunc facilisis, feugiat nibh sit amet, vehicula lectus. Vivamus pretium porttitor fringilla. Integer imperdiet nunc ut ligula vestibulum pulvinar.
    </div>
    <div class="stats">
        stats
    </div>
    <div class="news">
        news
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