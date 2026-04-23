<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap");

        * {
            box-sizing: border-box;
            font-family: "Inter", system-ui, sans-serif;
        }

        .task-shell {
            background: #1a1a1a;
            border: 1px solid #424242;
            color: #ffffff;
        }

        .task-shell-header {
            border-bottom: 1px solid #424242;
            color: #bdbdbd;
        }

        .task-shell-subheader {
            border-bottom: 1px solid #424242;
            color: #ffffff;
        }

        .task-title-text {
            color: #ffffff;
        }

        .task-date-text {
            color: #bdbdbd;
        }

        .task-subtask-panel {
            background: #141414;
            border-bottom: 1px solid #424242;
            color: #bdbdbd;
        }
    </style>
</head>
<div class="p-4 flex flex-col w-full gap-4 items-stretch">
    <div class="task-shell flex flex-col w-full min-h-32 lg:min-h-96 rounded-xl shadow-md flex-grow">
        <div class="task-shell-header flex w-full p-4">
            <?php
            require_once dirname(__DIR__) . "/db/tasks.php";
            $db = new Task();
            $taskCount = $db->getTaskCount($_SESSION["user_id"]);
            ?>
            <h3 class="text-xl">My Task (<span id="taskCount"><?php echo $taskCount; ?></span>)</h3>
            <div class="ml-auto flex gap-4">
                <button class="text-xl text-red-400" onclick="deleteMarkedTasks()">Delete</button>
                <button class="text-xl text-yellow-400">Mark as Read</button>
            </div>
        </div>
        <div class="flex flex-col w-full">
            <div class="task-shell-subheader flex w-full p-2">
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
                    <h3 class="text-xl text-white">Due Date</h3>
                </div>
            </div>
            <?php
            $tasks = $db->getTasks($_SESSION["user_id"]);
            if ($tasks == null){
                $tasks = [];
            }
            $parents = [];
            $childrenByParent = [];
            foreach ($tasks as $task) {
                if (!isset($task["parent_task_id"]) || $task["parent_task_id"] === null) {
                    $parents[] = $task;
                } else {
                    $pid = (int) $task["parent_task_id"];
                    if (!isset($childrenByParent[$pid])) {
                        $childrenByParent[$pid] = [];
                    }
                    $childrenByParent[$pid][] = $task;
                }
            }

            foreach ($parents as $task) {

                $currentDate = new DateTime('now', new DateTimeZone('Asia/Manila'));
                $finishDate = new DateTime($task["finish_date"], new DateTimeZone('Asia/Manila'));
                $formattedFinishDate = $finishDate->format("m-d-y h:i A");
                $isToday = $finishDate->format('Y-m-d') === $currentDate->format('Y-m-d');
                $isPastDue = $finishDate < $currentDate;
                $hasChildren = isset($childrenByParent[(int) $task["id"]]) && count($childrenByParent[(int) $task["id"]]) > 0;
                $isCompleted = isset($task["is_completed"]) && (int) $task["is_completed"] === 1;

                echo '<div class="flex w-full border-b border-[#424242] p-2 task" data-task-row="' . htmlspecialchars($task["id"]) . '">';
                echo '<div class="flex gap-4 items-center justify-center">';
                echo '<button type="button" class="text-lg font-bold ml-2 toggle-subtasks" data-parent-id="' . htmlspecialchars($task["id"]) . '" style="width:1.5rem;">' . ($hasChildren ? '▶' : '') . '</button>';
                echo '<input type="checkbox" name="Task' . htmlspecialchars($task["id"]) . '" id="Task' . htmlspecialchars($task["id"]) . '" class="peer task-checkbox" data-task-id="' . htmlspecialchars($task["id"]) . '" ' . ($isCompleted ? 'checked' : '') . ' />';
                echo '<label for="Task' . htmlspecialchars($task["id"]) . '" class="text-xl cursor-pointer task-title-text ' . ($isCompleted ? 'line-through text-gray-500' : ($isPastDue ? 'line-through text-gray-500' : 'peer-checked:line-through peer-checked:text-gray-500')) . '">';
                echo htmlspecialchars($task["title"]);
                echo '</label>';
                echo '</div>';
                echo '<div class="ml-auto">';
                echo '<h3 class="text-xl ' . ($isToday || $isPastDue ? 'text-red-400' : 'task-date-text') . ' mr-8">' . htmlspecialchars($formattedFinishDate) . '</h3>';
                echo '</div>';
                echo '</div>';

                if ($hasChildren) {
                    echo '<div class="hidden flex flex-col w-full px-14 py-2 task-subtask-panel" data-subtasks="' . htmlspecialchars($task["id"]) . '">';
                    foreach ($childrenByParent[(int) $task["id"]] as $subtask) {
                        $subIsCompleted = isset($subtask["is_completed"]) && (int) $subtask["is_completed"] === 1;
                        echo '<div class="flex items-center gap-3 py-1" data-task-row="' . htmlspecialchars($subtask["id"]) . '">';
                        echo '<input type="checkbox" class="task-checkbox" data-task-id="' . htmlspecialchars($subtask["id"]) . '" ' . ($subIsCompleted ? 'checked' : '') . ' />';
                        echo '<span class="text-sm ' . ($subIsCompleted ? 'line-through text-gray-500' : 'text-gray-300') . '">' . htmlspecialchars($subtask["title"]) . '</span>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
        ?>
        </div>
    </div>
    <div class="task-shell flex flex-col w-full min-h-32 lg:min-h-96 rounded-xl shadow-md flex-grow">
        <div class="task-shell-header flex w-full p-4">
            <h3 class="text-xl">Latest News</h3>
        </div>
        <div class="flex flex-col w-full">
        </div>
    </div>
</div>
<script>
    // * Persist completion state (and auto-update parents) via API.
    function setTaskCompleted(taskId, completed) {
        return fetch("/api/tasks/toggle-complete.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ task_id: taskId, completed: completed })
        }).then(r => r.json());
    }

    document.addEventListener("change", (e) => {
        const el = e.target;
        if (!el.classList.contains("task-checkbox")) {
            return;
        }
        const taskId = parseInt(el.getAttribute("data-task-id"), 10);
        if (!taskId) {
            return;
        }
        const completed = el.checked === true;

        setTaskCompleted(taskId, completed).then((data) => {
            if (!data || !data.success) {
                el.checked = !completed;
                alert((data && data.message) ? data.message : "Could not update task status.");
                return;
            }
            // * Update label styling locally.
            const row = document.querySelector('[data-task-row="' + taskId + '"]');
            if (row) {
                const label = row.querySelector("label");
                if (label) {
                    if (completed) {
                        label.classList.add("line-through", "text-gray-500");
                    } else {
                        label.classList.remove("line-through", "text-gray-500");
                    }
                }
                const span = row.querySelector("span");
                if (span) {
                    if (completed) {
                        span.classList.add("line-through", "text-gray-500");
                    } else {
                        span.classList.remove("line-through", "text-gray-500");
                    }
                }
            }
        }).catch(() => {
            el.checked = !completed;
            alert("Could not update task status. Please try again.");
        });
    });

    // * Collapsible subtasks.
    document.addEventListener("click", (e) => {
        const btn = e.target;
        if (!btn.classList.contains("toggle-subtasks")) {
            return;
        }
        const parentId = btn.getAttribute("data-parent-id");
        const panel = document.querySelector('[data-subtasks="' + parentId + '"]');
        if (!panel) {
            return;
        }
        if (panel.classList.contains("hidden")) {
            panel.classList.remove("hidden");
            btn.textContent = "▼";
        } else {
            panel.classList.add("hidden");
            btn.textContent = "▶";
        }
    });

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
                        if (result.value.useAi) {
                            Swal.fire({
                                title: "Generating subtasks...",
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                            const aiBody = new FormData();
                            aiBody.append("task_title", result.value.title);
                            aiBody.append("parent_task_id", String(data.task_id));
                            fetch("/api/tasks/ai-breakdown.php", {
                                method: "POST",
                                body: aiBody
                            })
                            .then(r => r.json())
                            .then(aiData => {
                                if (aiData && aiData.success) {
                                    Swal.fire("Success!", "Task and AI-generated subtasks created. Refresh to see them.", "success");
                                } else {
                                    Swal.fire("Task created", "Main task saved. AI subtasks were not generated.", "info");
                                }
                            })
                            .catch(() => {
                                Swal.fire("Task created", "Main task saved. AI subtasks could not be generated.", "info");
                            });
                        } else {
                            Swal.fire('Success!', 'Task created successfully. Refresh to see it.', 'success');
                        }
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