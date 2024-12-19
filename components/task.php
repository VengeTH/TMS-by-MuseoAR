<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *{
            font-family: "Righteous", sans-serif;
        }
    </style>
</head>
<div class="p-4 flex flex-col w-full gap-4 items-stretch">
    <div class="flex flex-col w-full bg-white min-h-32 lg:min-h-96 rounded-xl shadow-md flex-grow">
        <div class="flex w-full border-b-2 border-gray-500 p-4">
            <?php
            require_once dirname(__DIR__) . "/db/tasks.php";
            $db = new Task();
            $taskCount = $db->getTaskCount($_SESSION["user_id"]);
            ?>
            <h3 class="text-xl">My Task (<span id="taskCount"><?php echo $taskCount; ?></span>)</h3>
            <div class="ml-auto flex gap-4">
                <button class="text-xl" onclick="deleteMarkedTasks()">Delete</button>
                <button class="text-xl">Mark as Read</button>
            </div>
        </div>
        <div class="flex flex-col w-full">
            <div class="flex w-full border-b-2 border-black p-2">
                <div class="flex gap-4 items-center justify-center">
                <!-- Checkbox -->
                <!-- Label for the checkbox -->
                <label
                    for="Task1"
                    class="text-xl cursor-pointer peer-checked:line-through peer-checked:text-gray-500 ml-8"
                >
                    Task/s
                </label>
                </div>
                <div class="ml-auto">
                    <h3 class="text-xl text-black">Due Date</h3>
                </div>
            </div>
            <?php
            $tasks = $db->getTasks($_SESSION["user_id"]);
            foreach ($tasks as $task) {
                $finishDate = new DateTime($task["finish_date"]);
                $formattedFinishDate = $finishDate->format("m-d-y h:i A");
                $isToday = $finishDate->format('Y-m-d') === (new DateTime())->format('Y-m-d');

                echo '<div class="flex w-full border-b-2 border-black p-2 task">';
                echo '<div class="flex gap-4 items-center justify-center">';
                echo '<input type="checkbox" name="Task' . htmlspecialchars($task["id"]) . '" id="Task' . htmlspecialchars($task["id"]) . '" class="peer task-checkbox" data-task-id="' . htmlspecialchars($task["id"]) . '" />';
                echo '<label for="Task' . htmlspecialchars($task["id"]) . '" class="text-xl cursor-pointer peer-checked:line-through peer-checked:text-gray-500">';
                echo htmlspecialchars($task["title"]);
                echo '</label>';
                echo '</div>';
                echo '<div class="ml-auto">';
                echo '<h3 class="text-xl ' . ($isToday ? 'text-red-800' : 'text-black') . ' mr-8">' . htmlspecialchars($formattedFinishDate) . '</h3>';
                echo '</div>';
                echo '</div>';
            }
        ?>
        </div>
    </div>
    <div class="flex flex-col w-full bg-white min-h-32 lg:min-h-96 rounded-xl shadow-md flex-grow">
        <div class="flex w-full border-b-2 border-gray-500 p-4">
            <h3 class="text-xl">Latest News</h3>
        </div>
        <div class="flex flex-col w-full">
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

    function deleteMarkedTasks() {
        const checkboxes = document.querySelectorAll('.task-checkbox:checked');
        const taskIds = Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-task-id'));

        if (taskIds.length > 0) {
            fetch('/api/tasks/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ taskIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkboxes.forEach(checkbox => {
                        const taskElement = checkbox.closest('.task');
                        taskElement.remove(); // Remove the entire task element
                    });
                    alert('Tasks deleted successfully');
                    updateTaskCount(-taskIds.length); // Update task count
                } else {
                    alert('Failed to delete tasks');
                }
            });
        } else {
            alert('No tasks selected for deletion');
        }
    }

    function updateTaskCount(change) {
        const taskCountElement = document.getElementById('taskCount');
        const currentCount = parseInt(taskCountElement.textContent);
        taskCountElement.textContent = currentCount + change;
    }
</script>