<?php
    require_once dirname(__DIR__) . "/db/tasks.php";
    $db = new task();
?>
<script>
    var tasks = <?php echo json_encode($db->getTasks($_SESSION['user_id'])); ?> ?? [];
    function loadTasks() {
        const taskContainer = document.getElementById('taskContainer');
        taskContainer.innerHTML = `
            <div class="taskHeader" style="display: flex; justify-content: space-between; font-weight: bold; padding: 10px; border-bottom: 2px solid #ccc;">
                <div style="flex: 2;">Title</div>
                <div style="flex: 3;">Details</div>
                <div style="flex: 1;">Finish Date</div>
                <div style="flex: 1;">Priority</div>
            </div>
        `;
        taskContainer.style.maxHeight = '28rem'; // Set max height for scrollable area
        taskContainer.style.overflowY = 'auto'; // Enable vertical scrolling
        tasks.sort((a, b) => b.priority - a.priority);
        tasks.forEach((task, index) => {
            const taskElement = document.createElement('div');
            taskElement.classList.add('task');
            taskElement.style.display = 'flex';
            taskElement.style.justifyContent = 'space-between';
            taskElement.style.border = '1px solid #ccc';
            taskElement.style.padding = '10px';
            taskElement.style.marginBottom = '10px';
            taskElement.style.borderRadius = '5px';
            taskElement.style.backgroundColor = '#f9f9f9';
            if (index === 0) {
                taskElement.style.marginTop = '1rem'; // Lower the first task
            }
            const priorityText = task.priority === 1 ? 'Low' : task.priority === 2 ? 'Medium' : 'High';
            taskElement.innerHTML = `
                <div class="taskTitle" style="flex: 2;">${task.title}</div>
                <div class="taskDetails" style="flex: 3;">${task.details}</div>
                <div class="taskFinishDate" style="flex: 1;">${task.finish_date}</div>
                <div class="taskPriority" style="flex: 1;">${priorityText} Priority</div>
            `;
            taskContainer.appendChild(taskElement);
        });
    }
    window.onload = loadTasks;

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
                            priority: result.value.priority === 'high' ? 3 : result.value.priority === 'medium' ? 2 : 1
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