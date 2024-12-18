<?php
    require_once dirname(__DIR__) . "/db/tasks.php";
    $db = new task();
?>
<script>
    var tasks = <?php echo json_encode($db->getTasks($_SESSION['user_id'])); ?> ?? [];
    function loadTasks() {
        const taskContainer = document.getElementById('taskContainer');
        taskContainer.innerHTML = '';
        tasks.sort((a, b) => b.priority - a.priority);
        tasks.forEach(task => {
            const taskElement = document.createElement('div');
            taskElement.classList.add('task');
            taskElement.innerHTML = `
                <div class="taskTitle">${task.title}</div>
                <div class="taskDetails">${task.details}</div>
                <div class="taskFinish
                Date">${task.finish_date}</div>
                <div class="taskPriority">${task.priority}</div>
            `;
            taskContainer.appendChild(taskElement);
        });
    }
    window.onload = loadTasks;
</script>
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
                <div class="tasks" id="taskContainer">
                </div>
            </div>
            <div class="secondChild">
                <div class="stats">
                    stats
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
                const postBody = new FormData();
                if (result.isConfirmed) {
                    postBody.append('title', result.value.title);
                    postBody.append('details', result.value.details);
                    postBody.append('finishDate', result.value.finishDate);
                    postBody.append('priority', result.value.priority);
                    // Send data to server
                    fetch('/api/tasks/create', {
                        method: 'POST',
                        body: postBody
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            tasks = [...tasks, {
                                title: result.value.title,
                                details: result.value.details,
                                finish_date: result.value.finishDate,
                                priority: result.value.priority
                            }];
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