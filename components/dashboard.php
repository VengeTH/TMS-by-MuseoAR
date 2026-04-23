<?php
require_once dirname(__DIR__) . "/db/tasks.php";

$db = new Task();
$completedToday = (int) $db->getCompletedTodayCount($_SESSION["user_id"]);
$streakDays = (int) $db->getCompletionStreakDays($_SESSION["user_id"]);
$isDemoMetrics = ($completedToday === 0 && $streakDays === 0);
$displayCompletedToday = $isDemoMetrics ? 3 : $completedToday;
$displayStreakDays = $isDemoMetrics ? 5 : $streakDays;
$displayProductivity = min(100, max(0, ($displayCompletedToday * 10) + ($displayStreakDays * 5)));
$taskPayload = $db->getTasks($_SESSION["user_id"]);
if (!is_array($taskPayload)) {
    $taskPayload = [];
}

$demoNewsItems = [
    [
        "tag" => "Planner",
        "title" => "Weekly planning now includes demo data",
        "body" => "Use the AI Weekly Planner immediately even when your account is empty. Demo tasks are preloaded for visual testing.",
    ],
    [
        "tag" => "Execution",
        "title" => "Task board redesigned for clearer priorities",
        "body" => "Priority, due dates, and workload now appear in one structured pane for faster planning decisions.",
    ],
    [
        "tag" => "TH",
        "title" => "Interface aligned with The Heedful visual system",
        "body" => "Dark-first surfaces, controlled yellow emphasis, and technical spacing now drive the dashboard shell.",
    ],
];
?>

<div class="upperTab">
    <img src="/img/logo.png" alt="OrgaNiss logo">
    <div class="searchBar">
        <input id="dashboardSearch" type="text" placeholder="Search tasks" class="searchInput" aria-label="Search tasks">
    </div>
    <button class="newTaskButton" onclick="showNewTaskModal()">+ New Task</button>
    <a href="?tab=profile" class="user-profile" title="Profile &amp; Account">
        <?php $profilePicture = !empty($user["profile_picture"]) ? htmlspecialchars($user["profile_picture"]) : "/img/defaultPFP.png"; ?>
        <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture">
    </a>
    <span class="time" id="currentTime">--:--</span>
</div>

<div class="dashboard-grid">
    <section class="dashboard-column dashboard-column--primary">
        <article class="dashboard-panel">
            <?php include __DIR__ . "/calendar.php"; ?>
        </article>

        <article class="dashboard-panel">
            <header class="dashboard-panel__header">
                <h2>Execution Metrics</h2>
                <span class="dashboard-pill">TH Signal</span>
            </header>
            <div class="stats-grid">
                <div class="dashboard-stat-card">
                    <div class="dashboard-stat-label">AI Weekly Planner</div>
                    <div class="dashboard-stat-row">
                        <div class="dashboard-stat-text">Let AI organize your week.</div>
                                <a href="/dashboard?tab=weeklyPlanner" class="dashboard-stat-link">Open</a>
                    </div>
                </div>
                <div class="dashboard-stat-card">
                    <div class="dashboard-stat-label">Tasks Completed Today</div>
                    <div class="dashboard-stat-value"><?php echo $displayCompletedToday; ?></div>
                </div>
                <div class="dashboard-stat-card">
                    <div class="dashboard-stat-label">Productivity Score</div>
                    <div class="dashboard-stat-value"><?php echo $displayProductivity; ?></div>
                </div>
                <div class="dashboard-stat-card">
                    <div class="dashboard-stat-label">Streak Counter</div>
                    <div class="dashboard-stat-value"><?php echo $displayStreakDays; ?> days</div>
                </div>
            </div>
            <?php if ($isDemoMetrics): ?>
                <div class="stats-demo-note">Showing demo values because your account has no completed tasks yet.</div>
            <?php endif; ?>
        </article>
    </section>

    <section class="dashboard-column dashboard-column--secondary">
        <article class="dashboard-panel dashboard-panel--scroll">
            <header class="dashboard-panel__header">
                <h2>Task Focus Board</h2>
                <span class="dashboard-pill dashboard-pill--muted" id="dashboardTaskCount">0 tasks</span>
            </header>

            <div class="dashboard-task-table">
                <div class="dashboard-task-table__head">
                    <span>Title</span>
                    <span>Details</span>
                    <span>Due Date</span>
                    <span>Priority</span>
                </div>
                <div id="dashboardTaskRows" class="dashboard-task-table__body"></div>
            </div>
        </article>

        <article class="dashboard-panel dashboard-panel--scroll">
            <header class="dashboard-panel__header">
                <h2>Latest News</h2>
            </header>
            <?php if ($isDemoMetrics): ?>
                <div class="news-list">
                    <?php foreach ($demoNewsItems as $newsItem): ?>
                        <article class="news-item">
                            <div class="news-item__tag"><?php echo htmlspecialchars($newsItem["tag"]); ?></div>
                            <div class="news-item__title"><?php echo htmlspecialchars($newsItem["title"]); ?></div>
                            <div class="news-item__body"><?php echo htmlspecialchars($newsItem["body"]); ?></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="news-body">No updates yet. Announcements and product improvements will appear here.</div>
            <?php endif; ?>
        </article>
    </section>
</div>

<script>
(() => {
    const rawTasks = <?php echo json_encode($taskPayload); ?> || [];
    const demoTasks = [
        { id: -1, title: "Prepare weekly roadmap", details: "Set sprint priorities and identify blockers.", finish_date: "2026-04-24 18:00:00", priority: 3, parent_task_id: null },
        { id: -2, title: "Client follow-up emails", details: "Reply to open threads and confirm next steps.", finish_date: "2026-04-29 15:00:00", priority: 2, parent_task_id: null },
        { id: -3, title: "Review overdue tasks", details: "Close completed items and reschedule pending work.", finish_date: "2026-04-23 10:00:00", priority: 2, parent_task_id: null },
        { id: -4, title: "Team stand-up notes", details: "Summarize blockers and share the daily plan.", finish_date: "2026-04-28 09:00:00", priority: 1, parent_task_id: null }
    ];

    const allTasks = (Array.isArray(rawTasks) && rawTasks.length > 0 ? rawTasks : demoTasks)
        .filter((task) => task.parent_task_id === null || typeof task.parent_task_id === "undefined");

    const rowsEl = document.getElementById("dashboardTaskRows");
    const countEl = document.getElementById("dashboardTaskCount");
    const searchEl = document.getElementById("dashboardSearch");

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getPriorityLabel(priority) {
        if (Number(priority) >= 3) return "High";
        if (Number(priority) === 2) return "Medium";
        return "Low";
    }

    function formatDueDate(rawValue) {
        const date = new Date(rawValue);
        if (Number.isNaN(date.getTime())) {
            return String(rawValue || "-");
        }
        return date.toLocaleString([], {
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
        });
    }

    function isOverdue(rawValue) {
        const date = new Date(rawValue);
        if (Number.isNaN(date.getTime())) {
            return false;
        }
        return date.getTime() < Date.now();
    }

    function renderTasks(query = "") {
        const normalized = query.trim().toLowerCase();
        const filtered = allTasks.filter((task) => {
            if (normalized === "") {
                return true;
            }
            return String(task.title || "").toLowerCase().includes(normalized)
                || String(task.details || "").toLowerCase().includes(normalized);
        });

        countEl.textContent = `${filtered.length} task${filtered.length === 1 ? "" : "s"}`;

        if (filtered.length === 0) {
            rowsEl.innerHTML = '<div class="dashboard-task-empty">No tasks match your search.</div>';
            return;
        }

        rowsEl.innerHTML = filtered.map((task) => {
            const dueAlertsOn = document.documentElement.dataset.taskDueAlerts !== "off";
            const dueClass = (dueAlertsOn && isOverdue(task.finish_date)) ? " dashboard-task-row__due--alert" : "";
            const priorityLabel = getPriorityLabel(task.priority);
            return `
                <article class="dashboard-task-row">
                    <div class="dashboard-task-row__title">${escapeHtml(task.title || "Untitled task")}</div>
                    <div class="dashboard-task-row__details">${escapeHtml(task.details || "No details")}</div>
                    <div class="dashboard-task-row__due${dueClass}">${escapeHtml(formatDueDate(task.finish_date))}</div>
                    <div class="dashboard-task-row__priority">${escapeHtml(priorityLabel)}</div>
                </article>
            `;
        }).join("");
    }

    function tickTime() {
        const timeEl = document.getElementById("currentTime");
        if (!timeEl) {
            return;
        }
        timeEl.textContent = new Date().toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
        });
    }

    function showNewTaskModal() {
        Swal.fire({
            title: "New Task",
            html: `
                <input type="text" id="taskTitle" class="swal2-input" placeholder="Task Title *" required>
                <textarea id="taskDetails" class="swal2-textarea" placeholder="Task Details"></textarea>
                <input type="text" id="finishDate" class="swal2-input" placeholder="Pick date and time" required>
                <select id="taskPriority" class="swal2-select">
                    <option value="low">Low Priority</option>
                    <option value="medium">Medium Priority</option>
                    <option value="high">High Priority</option>
                </select>
            `,
            background: "#1a1a1a",
            color: "#ffffff",
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#ffc107",
            cancelButtonColor: "#424242",
            customClass: {
                popup: "th-swal-popup",
                title: "th-swal-title",
                confirmButton: "th-swal-confirm",
                cancelButton: "th-swal-cancel",
            },
            didOpen: () => {
                const dateInput = document.getElementById("finishDate");
                if (dateInput && typeof flatpickr === "function") {
                    flatpickr(dateInput, {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i:S",
                        time_24hr: false,
                        minuteIncrement: 5,
                        defaultDate: new Date(),
                    });
                }
            },
            preConfirm: () => {
                const title = document.getElementById("taskTitle").value;
                const finishDate = document.getElementById("finishDate").value;

                if (!title.trim()) {
                    Swal.showValidationMessage("Task title is required");
                    return false;
                }
                if (!finishDate) {
                    Swal.showValidationMessage("Finish date is required");
                    return false;
                }

                return {
                    title,
                    details: document.getElementById("taskDetails").value,
                    finishDate,
                    priority: document.getElementById("taskPriority").value,
                };
            },
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            const postBody = new FormData();
            postBody.append("title", result.value.title);
            postBody.append("details", result.value.details);
            postBody.append("finishDate", result.value.finishDate);
            postBody.append("priority", result.value.priority);

            fetch("/api/tasks/create", {
                method: "POST",
                body: postBody,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        Swal.fire("Error", data.message || "Failed to create task", "error");
                        return;
                    }
                    Swal.fire("Success", "Task created successfully", "success").then(() => {
                        window.location.reload();
                    });
                })
                .catch(() => {
                    Swal.fire("Error", "Failed to create task", "error");
                });
        });
    }

    window.showNewTaskModal = showNewTaskModal;

    if (searchEl) {
        searchEl.addEventListener("input", () => {
            renderTasks(searchEl.value);
        });
    }

    renderTasks();
    tickTime();
    setInterval(tickTime, 1000);
})();
</script>
