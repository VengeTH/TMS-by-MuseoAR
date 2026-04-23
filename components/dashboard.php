<?php
    require_once dirname(__DIR__) . "/db/tasks.php";
    $db = new Task();
    $completedToday = $db->getCompletedTodayCount($_SESSION["user_id"]);
    $streakDays = $db->getCompletionStreakDays($_SESSION["user_id"]);
?>
<script>
    var tasks = <?php echo json_encode($db->getTasks($_SESSION['user_id'])); ?> ?? [];
    var filteredTasks = tasks;

    function buildTaskGroups() {
        const parents = [];
        const childrenByParent = {};

        (tasks || []).forEach(task => {
            if (task.parent_task_id === null || typeof task.parent_task_id === "undefined") {
                parents.push(task);
                return;
            }
            const pid = task.parent_task_id;
            if (!childrenByParent[pid]) {
                childrenByParent[pid] = [];
            }
            childrenByParent[pid].push(task);
        });

        parents.sort((a, b) => b.priority - a.priority);

        return { parents, childrenByParent };
    }

    function loadTasks() {
        const taskContainer = document.getElementById('taskContainer');
        taskContainer.innerHTML = `
            <div class="taskHeader" style="display: flex; justify-content: space-between; font-weight: bold; padding: 10px; border-bottom: 1px solid #424242; color: #bdbdbd;">
                <div style="flex: 2;">Title</div>
                <div style="flex: 3;">Details</div>
                <div style="flex: 1;">Finish Date</div>
                <div style="flex: 1;">Priority</div>
            </div>
        `;
        taskContainer.style.maxHeight = '28rem'; // Set max height for scrollable area
        taskContainer.style.overflowY = 'auto'; // Enable vertical scrolling
        const { parents, childrenByParent } = buildTaskGroups();

        parents.forEach((task, index) => {
            const wrapper = document.createElement('div');
            wrapper.classList.add('task-group');
            wrapper.style.border = '1px solid #424242';
            wrapper.style.borderRadius = '5px';
            wrapper.style.marginBottom = '10px';
            wrapper.style.backgroundColor = '#1a1a1a';
            if (index === 0) {
                wrapper.style.marginTop = '1rem';
            }

            const header = document.createElement('div');
            header.style.display = 'flex';
            header.style.justifyContent = 'space-between';
            header.style.padding = '10px';
            header.style.cursor = 'pointer';
            header.style.color = '#ffffff';

            const left = document.createElement('div');
            left.style.display = 'flex';
            left.style.alignItems = 'center';
            left.style.gap = '0.5rem';

            const toggle = document.createElement('span');
            toggle.textContent = childrenByParent[task.id] && childrenByParent[task.id].length > 0 ? '>' : '';
            toggle.style.fontWeight = 'bold';
            toggle.style.color = '#ffc107';

            const titleEl = document.createElement('div');
            titleEl.textContent = task.title;
            titleEl.style.flex = '2';

            const detailsEl = document.createElement('div');
            detailsEl.textContent = task.details || '';
            detailsEl.style.flex = '3';

            const finishEl = document.createElement('div');
            finishEl.textContent = task.finish_date || '';
            finishEl.style.flex = '1';

            const priorityText = task.priority === 1 ? 'Low' : task.priority === 2 ? 'Medium' : 'High';
            const priorityEl = document.createElement('div');
            priorityEl.textContent = `${priorityText} Priority`;
            priorityEl.style.flex = '1';

            left.appendChild(toggle);
            left.appendChild(titleEl);
            header.appendChild(left);
            header.appendChild(detailsEl);
            header.appendChild(finishEl);
            header.appendChild(priorityEl);

            const children = document.createElement('div');
            children.style.display = 'none';
            children.style.padding = '0 1.5rem 0.5rem 2.5rem';
            children.style.color = '#bdbdbd';

            (childrenByParent[task.id] || []).forEach(sub => {
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.padding = '4px 0';

                const subTitle = document.createElement('div');
                subTitle.textContent = `- ${sub.title}`;
                subTitle.style.flex = '3';

                const subFinish = document.createElement('div');
                subFinish.textContent = sub.finish_date || '';
                subFinish.style.flex = '1';

                row.appendChild(subTitle);
                row.appendChild(subFinish);
                children.appendChild(row);
            });

            if ((childrenByParent[task.id] || []).length > 0) {
                header.addEventListener('click', () => {
                    if (children.style.display === 'none') {
                        children.style.display = 'block';
                        toggle.textContent = 'v';
                    } else {
                        children.style.display = 'none';
                        toggle.textContent = '>';
                    }
                });
            }

            wrapper.appendChild(header);
            wrapper.appendChild(children);
            taskContainer.appendChild(wrapper);
        });
    }

    function filterTasks() {
        const searchInput = document.querySelector('.searchInput').value.toLowerCase();
        filteredTasks = (tasks || []).filter(task => task.title.toLowerCase().includes(searchInput));
        loadTasks();
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
                <label style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;font-size:0.9rem;">
                    <input type="checkbox" id="useAiBreakdown" />
                    <span>Generate subtasks using AI</span>
                </label>
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
                    priority: document.getElementById('taskPriority').value,
                    useAi: document.getElementById('useAiBreakdown').checked
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
                        const newTask = {
                            id: data.task_id,
                            title: result.value.title,
                            details: result.value.details,
                            finish_date: result.value.finishDate,
                            priority: result.value.priority === 'high' ? 3 : result.value.priority === 'medium' ? 2 : 1,
                            parent_task_id: null
                        };
                        tasks.push(newTask);
                        filteredTasks = tasks;

                        if (result.value.useAi) {
                            Swal.fire({
                                title: 'Generating subtasks...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            const aiBody = new FormData();
                            aiBody.append('task_title', result.value.title);
                            aiBody.append('parent_task_id', String(data.task_id));
                            fetch('/api/tasks/ai-breakdown.php', {
                                method: 'POST',
                                body: aiBody
                            })
                            .then(r => r.json())
                            .then(aiData => {
                                if (aiData.success && Array.isArray(aiData.subtasks)) {
                                    aiData.subtasks.forEach(sub => {
                                        tasks.push({
                                            id: null,
                                            title: sub,
                                            details: '',
                                            finish_date: result.value.finishDate,
                                            priority: newTask.priority,
                                            parent_task_id: data.task_id
                                        });
                                    });
                                    filteredTasks = tasks;
                                    Swal.fire('Success!', 'Task and AI-generated subtasks created.', 'success');
                                    loadTasks();
                                } else {
                                    Swal.fire('Task created', 'Main task saved. AI subtasks were not generated: ' + (aiData.message || ''), 'info');
                                    loadTasks();
                                }
                            })
                            .catch(() => {
                                Swal.fire('Task created', 'Main task saved. AI subtasks could not be generated.', 'info');
                                loadTasks();
                            });
                        } else {
                            Swal.fire('Success!', 'Task created successfully', 'success');
                            loadTasks();
                        }
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
                <input type="text" placeholder="Search" class="searchInput" oninput="filterTasks()">
            </div>
            <div style="margin-left:auto; margin-right:3rem;display:flex;align-items:center;gap:1rem;">
                <button class="newTaskButton" onclick="showNewTaskModal()">+ New Task</button>
                <a href="?tab=profile" class="user-profile" title="Profile & Account">
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
                    <div class="stats-grid">
                        <div class="dashboard-stat-card">
                            <div class="dashboard-stat-label">AI Weekly Planner</div>
                            <div class="dashboard-stat-row">
                                <div class="dashboard-stat-text">Let AI organize your week.</div>
                                <a href="/dashboard/weekly-planner.php" class="dashboard-stat-link">Open</a>
                            </div>
                        </div>
                        <div class="dashboard-stat-card">
                            <div class="dashboard-stat-label">Tasks Completed Today</div>
                            <div class="dashboard-stat-value"><?php echo (int) $completedToday; ?></div>
                        </div>
                        <div class="dashboard-stat-card">
                            <div class="dashboard-stat-label">Productivity Score</div>
                            <div class="dashboard-stat-value"><?php echo min(100, max(0, ((int) $completedToday * 10) + ((int) $streakDays * 5))); ?></div>
                        </div>
                        <div class="dashboard-stat-card">
                            <div class="dashboard-stat-label">Streak Counter</div>
                            <div class="dashboard-stat-value"><?php echo (int) $streakDays; ?> days</div>
                        </div>
                    </div>
                </div>
                <div class="news">
                    <div class="news-title">Latest News</div>
                    <div class="news-body">No updates yet. Announcements and product improvements will appear here.</div>
                </div>
            </div>
        </div>
    </div>
