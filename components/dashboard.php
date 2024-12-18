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
                const postBody = new FormData();
                if (result.isConfirmed) {
                    FormData.append('title', result.value.title);
                    FormData.append('details', result.value.details);
                    FormData.append('finishDate', result.value.finishDate);
                    FormData.append('priority', result.value.priority);
                    // Send data to server
                    fetch('/api/tasks/create.php', {
                        method: 'POST',
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