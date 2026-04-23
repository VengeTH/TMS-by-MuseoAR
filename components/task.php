<?php
require_once dirname(__DIR__) . "/db/tasks.php";

$db = new Task();
$tasks = $db->getTasks($_SESSION["user_id"]);
if ($tasks === null) {
    $tasks = [];
}

$isDemoTaskMode = count($tasks) === 0;
if ($isDemoTaskMode) {
    $tasks = [
        [
            "id" => -1,
            "title" => "Prepare weekly roadmap",
            "details" => "Set sprint priorities and identify blockers.",
            "finish_date" => date("Y-m-d H:i:s", strtotime("Friday 6 pm")),
            "priority" => 3,
            "parent_task_id" => null,
            "is_completed" => 0,
        ],
        [
            "id" => -2,
            "title" => "Client follow-up emails",
            "details" => "Reply to open threads and confirm next steps.",
            "finish_date" => date("Y-m-d H:i:s", strtotime("Wednesday 3 pm")),
            "priority" => 2,
            "parent_task_id" => null,
            "is_completed" => 0,
        ],
        [
            "id" => -3,
            "title" => "Review overdue tasks",
            "details" => "Close completed items and reschedule pending work.",
            "finish_date" => date("Y-m-d H:i:s", strtotime("Thursday 10 am")),
            "priority" => 2,
            "parent_task_id" => null,
            "is_completed" => 0,
        ],
        [
            "id" => -4,
            "title" => "Team stand-up notes",
            "details" => "Summarize blockers and share the daily plan.",
            "finish_date" => date("Y-m-d H:i:s", strtotime("Tuesday 9 am")),
            "priority" => 1,
            "parent_task_id" => null,
            "is_completed" => 0,
        ],
    ];
}

$taskCountDisplay = count($tasks);
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
?>

<div class="task-page">
    <section class="task-panel task-panel--main">
        <header class="task-panel__header">
            <h2>My Task (<?php echo $taskCountDisplay; ?>)</h2>
            <div class="task-panel__actions">
                <button class="task-action task-action--danger" onclick="deleteMarkedTasks()">Delete</button>
                <button class="task-action">Mark as Read</button>
            </div>
        </header>

        <?php if ($isDemoTaskMode): ?>
            <div class="task-demo-note">
                Demo tasks are loaded so you can visualize the system before adding real work.
            </div>
        <?php endif; ?>

        <div class="task-table task-table--head">
            <div>Task</div>
            <div>Details</div>
            <div>Due Date</div>
        </div>

        <div class="task-table-wrap">
            <?php foreach ($parents as $task): ?>
                <?php
                $currentDate = new DateTime('now', new DateTimeZone('Asia/Manila'));
                $finishDate = new DateTime($task["finish_date"], new DateTimeZone('Asia/Manila'));
                $formattedFinishDate = $finishDate->format("m-d-y h:i A");
                $isToday = $finishDate->format('Y-m-d') === $currentDate->format('Y-m-d');
                $isPastDue = $finishDate < $currentDate;
                $hasChildren = isset($childrenByParent[(int) $task["id"]]) && count($childrenByParent[(int) $task["id"]]) > 0;
                $isCompleted = isset($task["is_completed"]) && (int) $task["is_completed"] === 1;
                ?>
                <article class="task-item-row" data-task-row="<?php echo htmlspecialchars((string) $task["id"]); ?>">
                    <div class="task-item-col task-item-col--title">
                        <button type="button" class="task-toggle-subtasks" data-parent-id="<?php echo htmlspecialchars((string) $task["id"]); ?>">
                            <?php echo $hasChildren ? "▶" : ""; ?>
                        </button>
                        <input
                            type="checkbox"
                            class="task-checkbox"
                            data-task-id="<?php echo htmlspecialchars((string) $task["id"]); ?>"
                            <?php echo $isCompleted ? "checked" : ""; ?>
                        >
                        <span class="task-item-title <?php echo ($isCompleted || $isPastDue) ? "is-done" : ""; ?>">
                            <?php echo htmlspecialchars((string) $task["title"]); ?>
                        </span>
                    </div>
                    <div class="task-item-col task-item-col--details">
                        <?php echo htmlspecialchars((string) ($task["details"] ?? "")); ?>
                    </div>
                    <div class="task-item-col task-item-col--date <?php echo ($isToday || $isPastDue) ? "is-alert" : ""; ?>">
                        <?php echo htmlspecialchars($formattedFinishDate); ?>
                    </div>
                </article>

                <?php if ($hasChildren): ?>
                    <div class="task-subtasks is-hidden" data-subtasks="<?php echo htmlspecialchars((string) $task["id"]); ?>">
                        <?php foreach ($childrenByParent[(int) $task["id"]] as $subtask): ?>
                            <?php $subIsCompleted = isset($subtask["is_completed"]) && (int) $subtask["is_completed"] === 1; ?>
                            <div class="task-subtask-row" data-task-row="<?php echo htmlspecialchars((string) $subtask["id"]); ?>">
                                <input
                                    type="checkbox"
                                    class="task-checkbox"
                                    data-task-id="<?php echo htmlspecialchars((string) $subtask["id"]); ?>"
                                    <?php echo $subIsCompleted ? "checked" : ""; ?>
                                >
                                <span class="<?php echo $subIsCompleted ? "is-done" : ""; ?>">
                                    <?php echo htmlspecialchars((string) $subtask["title"]); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="task-panel">
        <header class="task-panel__header">
            <h3>Latest News</h3>
        </header>
        <div class="task-news-list">
            <article class="task-news-item">
                <div class="task-news-tag">Planner</div>
                <h4>Weekly planning now supports demo tasks</h4>
                <p>Sample tasks are available when your account has no current workload.</p>
            </article>
            <article class="task-news-item">
                <div class="task-news-tag">UI</div>
                <h4>Dashboard layout rebuilt for consistency</h4>
                <p>The shell now keeps navigation fixed while content panels scroll independently.</p>
            </article>
            <article class="task-news-item">
                <div class="task-news-tag">Profile</div>
                <h4>Profile and settings scrolling behavior improved</h4>
                <p>Account pages now stay accessible with stable spacing across desktop and mobile.</p>
            </article>
        </div>
    </section>
</div>

<script>
    function setTaskCompleted(taskId, completed) {
        if (taskId <= 0) {
            return Promise.resolve({ success: true });
        }
        return fetch("/api/tasks/toggle-complete.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ task_id: taskId, completed: completed })
        }).then((r) => r.json());
    }

    document.addEventListener("change", (e) => {
        const el = e.target;
        if (!el.classList.contains("task-checkbox")) {
            return;
        }

        const taskId = parseInt(el.getAttribute("data-task-id"), 10);
        const completed = el.checked === true;

        setTaskCompleted(taskId, completed).then((data) => {
            if (!data || !data.success) {
                el.checked = !completed;
                return;
            }

            const row = document.querySelector('[data-task-row="' + taskId + '"]');
            if (!row) {
                return;
            }

            const title = row.querySelector(".task-item-title, span");
            if (!title) {
                return;
            }

            title.classList.toggle("is-done", completed);
        }).catch(() => {
            el.checked = !completed;
        });
    });

    document.addEventListener("click", (e) => {
        const btn = e.target;
        if (!btn.classList.contains("task-toggle-subtasks")) {
            return;
        }

        const parentId = btn.getAttribute("data-parent-id");
        const panel = document.querySelector('[data-subtasks="' + parentId + '"]');
        if (!panel) {
            return;
        }

        const hidden = panel.classList.contains("is-hidden");
        panel.classList.toggle("is-hidden", !hidden);
        btn.textContent = hidden ? "▼" : "▶";
    });

    function deleteMarkedTasks() {
        const checkboxes = document.querySelectorAll('.task-checkbox:checked');
        const taskIds = Array.from(checkboxes)
            .map((checkbox) => parseInt(checkbox.getAttribute('data-task-id'), 10))
            .filter((id) => id > 0);

        if (taskIds.length === 0) {
            return;
        }

        fetch('/api/tasks/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ taskIds })
        })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                return;
            }
            window.location.reload();
        });
    }
</script>
