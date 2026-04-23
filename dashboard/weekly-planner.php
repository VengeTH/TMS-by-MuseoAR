<?php
require_once dirname(__DIR__) . "/helpers/sessionHandler.php";
require_once dirname(__DIR__) . "/db/tasks.php";

$db = new Task();
$tasks = $db->getTasks($_SESSION["user_id"]);
if ($tasks === null) {
    $tasks = [];
}

$plannerParentTasks = [];
foreach ($tasks as $task) {
    if (!isset($task["parent_task_id"]) || $task["parent_task_id"] === null || $task["parent_task_id"] === "") {
        $plannerParentTasks[] = $task;
    }
}

$plannerDemoMode = count($plannerParentTasks) === 0;
$plannerModeLabel = $plannerDemoMode ? "Demo tasks loaded" : "Live tasks loaded";
$plannerSelectionHint = $plannerDemoMode ? "Demo tasks are already selected for you." : "Select at least one task before generating a plan.";
$plannerAccountLabel = $plannerDemoMode ? "4 sample tasks" : "Live account data";
$plannerAvailableCount = $plannerDemoMode ? 4 : count($plannerParentTasks);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Weekly Planner - OrgaNiss</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap");

        :root {
            color-scheme: dark;
        }

        body {
            background:
                radial-gradient(circle at top, rgba(255, 193, 7, 0.12), transparent 30%),
                linear-gradient(180deg, #111111 0%, #0f0f0f 100%);
            color: #ffffff;
            font-family: "Inter", system-ui, sans-serif;
        }

        .planner-shell {
            border: 1px solid rgba(255, 255, 255, 0.06);
            background: rgba(26, 26, 26, 0.92);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }

        .planner-card {
            border: 1px solid #424242;
            background: #1a1a1a;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.18);
        }

        .planner-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border-radius: 999px;
            border: 1px solid #424242;
            background: #161616;
            color: #bdbdbd;
            padding: 0.45rem 0.8rem;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .planner-chip::before {
            content: "";
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 999px;
            background: #ffc107;
            box-shadow: 0 0 0 5px rgba(255, 193, 7, 0.14);
        }

        .planner-task {
            border: 1px solid #424242;
            border-radius: 0.9rem;
            background: linear-gradient(180deg, #242424 0%, #1a1a1a 100%);
            transition: transform 150ms ease, border-color 150ms ease, background 150ms ease;
        }

        .planner-task:hover {
            transform: translateY(-1px);
            border-color: #ffc107;
            background: #232323;
        }

        .planner-task input[type="checkbox"] {
            accent-color: #ffc107;
            width: 1rem;
            height: 1rem;
        }

        .planner-task .planner-task-meta {
            color: #bdbdbd;
            font-size: 0.78rem;
        }

        .planner-alert {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 60;
        }

        .planner-alert.is-hidden {
            display: none;
        }

        .planner-alert__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.62);
            backdrop-filter: blur(6px);
        }

        .planner-alert__dialog {
            position: relative;
            width: min(100%, 32rem);
            border-radius: 1.2rem;
            border: 1px solid #424242;
            background: linear-gradient(180deg, #242424 0%, #171717 100%);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.45);
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .planner-alert__icon {
            width: 3rem;
            height: 3rem;
            border-radius: 999px;
            display: grid;
            place-items: center;
            color: #0f0f0f;
            font-weight: 800;
            flex: 0 0 auto;
        }

        .planner-alert__body {
            flex: 1;
        }

        .planner-alert__eyebrow {
            margin: 0;
            font-family: "Space Grotesk", "Inter", sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
        }

        .planner-alert__text {
            margin-top: 0.35rem;
            color: #bdbdbd;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .planner-alert__button {
            margin-top: 1rem;
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0.8rem;
            background: #ffc107;
            color: #0f0f0f;
            padding: 0.7rem 1.1rem;
            font-weight: 700;
            cursor: pointer;
        }

        .planner-alert__button:hover {
            background: #ffb300;
        }

        .planner-badge {
            border: 1px solid rgba(255, 193, 7, 0.35);
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border-radius: 999px;
            padding: 0.4rem 0.7rem;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="min-h-screen bg-[#0f0f0f] text-white">
    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="planner-shell mb-6 rounded-2xl px-5 py-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="/img/logo.png" alt="OrgaNiss" class="h-10 w-10 object-contain">
                    <div>
                        <h1 class="text-2xl font-bold" style="font-family: 'Space Grotesk', 'Inter', sans-serif;">AI Weekly Planner</h1>
                        <p class="text-sm text-[#bdbdbd]">Plan your week using real tasks, or use the built-in demo tasks when your account is empty.</p>
                    </div>
                </div>
                <div class="planner-chip">Brand-aligned planner</div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <div class="planner-card rounded-2xl p-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] text-[#bdbdbd]">Generate Plan</p>
                            <h2 class="mt-1 text-xl font-semibold">Select the tasks to distribute across the week</h2>
                            <p class="mt-2 text-sm text-[#bdbdbd]">
                                AI will arrange the selected items from Monday to Sunday.
                                When no real tasks exist, demo tasks are preselected so you can visualize the workflow immediately.
                            </p>
                        </div>
                        <div class="planner-badge"><?php echo $plannerModeLabel; ?></div>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-3 text-sm text-[#bdbdbd]">
                        <span id="selectionSummary">0 tasks selected</span>
                        <span><?php echo $plannerAvailableCount; ?> available</span>
                    </div>
                    <div class="mt-4 flex max-h-72 flex-col gap-3 overflow-y-auto pr-1" id="taskSelection">
                        <!-- Filled by JS -->
                    </div>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-[#bdbdbd]">
                            <?php echo $plannerSelectionHint; ?>
                        </p>
                        <button
                            id="generatePlanBtn"
                            class="px-4 py-3 bg-[#ffc107] text-[#0f0f0f] rounded-lg hover:bg-[#ffb300] text-sm font-semibold transition-colors"
                        >
                            Generate Weekly Plan with AI
                        </button>
                    </div>
                </div>

                <div class="planner-card rounded-2xl p-5">
                    <h2 class="text-lg font-semibold mb-4">Weekly Plan</h2>
                    <div id="planEmpty" class="text-sm text-[#bdbdbd]">
                        No plan generated yet. Use the button above to create one.
                    </div>
                    <div id="planBoard" class="hidden grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-3 text-sm">
                        <!-- Columns rendered by JS -->
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button
                            id="applyPlanBtn"
                            class="px-4 py-2 bg-[#ffc107] text-[#0f0f0f] rounded-lg hover:bg-[#ffb300] text-sm font-semibold hidden transition-colors"
                        >
                            Apply Plan to Tasks
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="planner-card rounded-2xl p-5">
                    <h2 class="text-lg font-semibold mb-3">Planner Snapshot</h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-[#424242] bg-[#161616] p-4">
                            <div class="text-xs uppercase tracking-[0.14em] text-[#bdbdbd]">Suggested Flow</div>
                            <div class="mt-2 text-lg font-semibold text-[#ffc107]">Focus early, review late</div>
                            <p class="mt-2 text-sm text-[#bdbdbd]">High-priority work is nudged into the early week while lighter review items stay toward Friday and Saturday.</p>
                        </div>
                        <div class="rounded-xl border border-[#424242] bg-[#161616] p-4">
                            <div class="text-xs uppercase tracking-[0.14em] text-[#bdbdbd]">Demo Mode</div>
                            <div class="mt-2 text-lg font-semibold text-white"><?php echo $plannerAccountLabel; ?></div>
                            <p class="mt-2 text-sm text-[#bdbdbd]">The planner works with sample content so you can preview the workflow without adding your own tasks first.</p>
                        </div>
                    </div>
                </div>

                <div class="planner-card rounded-2xl p-5">
                    <h2 class="text-lg font-semibold mb-3">Visualization Values</h2>
                    <div class="grid gap-3">
                        <div class="flex items-center justify-between rounded-xl border border-[#424242] bg-[#161616] px-4 py-3">
                            <span class="text-sm text-[#bdbdbd]">Estimated weekly focus</span>
                            <span class="text-sm font-semibold text-white">18 hours</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-[#424242] bg-[#161616] px-4 py-3">
                            <span class="text-sm text-[#bdbdbd]">Open planning slots</span>
                            <span class="text-sm font-semibold text-white">3 days</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-[#424242] bg-[#161616] px-4 py-3">
                            <span class="text-sm text-[#bdbdbd]">Suggested productivity score</span>
                            <span class="text-sm font-semibold text-[#ffc107]">82 / 100</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="plannerAlert" class="planner-alert is-hidden" aria-hidden="true">
        <div class="planner-alert__backdrop" data-alert-close></div>
        <div class="planner-alert__dialog" role="dialog" aria-modal="true" aria-labelledby="plannerAlertTitle" aria-describedby="plannerAlertText">
            <div class="planner-alert__icon" id="plannerAlertIcon">!</div>
            <div class="planner-alert__body">
                <p class="planner-alert__eyebrow" id="plannerAlertTitle">Notice</p>
                <p class="planner-alert__text" id="plannerAlertText"></p>
                <button type="button" id="plannerAlertConfirm" class="planner-alert__button">OK</button>
            </div>
        </div>
    </div>

    <script>
        const allTasks = <?php echo json_encode($tasks, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?> || [];
        const realParentTasks = allTasks.filter(t => !t.parent_task_id);
        const demoTasks = [
            { id: -1, title: "Prepare weekly roadmap", finish_date: "Friday", priority: 3, parent_task_id: null },
            { id: -2, title: "Client follow-up emails", finish_date: "Wednesday", priority: 2, parent_task_id: null },
            { id: -3, title: "Review overdue tasks", finish_date: "Thursday", priority: 2, parent_task_id: null },
            { id: -4, title: "Team stand-up notes", finish_date: "Tuesday", priority: 1, parent_task_id: null }
        ];
        const displayTasks = realParentTasks.length > 0 ? realParentTasks : demoTasks;
        const isDemoMode = realParentTasks.length === 0;

        const weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        const selectedTaskIds = new Set();
        let latestPlanByDay = {};
        let latestPlanIdsByDay = {};
        const plannerAlert = document.getElementById("plannerAlert");
        const plannerAlertTitle = document.getElementById("plannerAlertTitle");
        const plannerAlertText = document.getElementById("plannerAlertText");
        const plannerAlertIcon = document.getElementById("plannerAlertIcon");
        const plannerAlertConfirm = document.getElementById("plannerAlertConfirm");
        const alertToneMap = {
            success: { background: "#9cd67a", glyph: "✓" },
            error: { background: "#ff7070", glyph: "!" },
            warning: { background: "#ffc107", glyph: "!" },
            info: { background: "#6ea8fe", glyph: "i" }
        };

        function updateSelectionSummary() {
            const summary = document.getElementById("selectionSummary");
            if (!summary) {
                return;
            }
            summary.textContent = `${selectedTaskIds.size} task${selectedTaskIds.size === 1 ? "" : "s"} selected`;
        }

        function showSystemAlert(icon, title, text) {
            const tone = alertToneMap[icon] || alertToneMap.info;
            plannerAlertTitle.textContent = title;
            plannerAlertText.textContent = text;
            plannerAlertIcon.textContent = tone.glyph;
            plannerAlertIcon.style.background = tone.background;
            plannerAlert.classList.remove("is-hidden");
            plannerAlert.setAttribute("aria-hidden", "false");
        }

        function hideSystemAlert() {
            plannerAlert.classList.add("is-hidden");
            plannerAlert.setAttribute("aria-hidden", "true");
        }

        function renderTaskSelection() {
            const container = document.getElementById("taskSelection");
            container.innerHTML = "";

            if (isDemoMode) {
                const note = document.createElement("div");
                note.className = "text-xs text-[#bdbdbd] mb-1";
                note.textContent = "No real tasks found. Showing demo tasks for visualization.";
                container.appendChild(note);
            }

            displayTasks.forEach(task => {
                    const wrapper = document.createElement("label");
            wrapper.className = "planner-task flex items-start gap-3 p-3 text-sm cursor-pointer";

                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.value = String(task.id);
                    checkbox.checked = selectedTaskIds.has(task.id) || isDemoMode;
                    if (isDemoMode) {
                        selectedTaskIds.add(task.id);
                    }
                    checkbox.addEventListener("change", () => {
                        if (checkbox.checked) {
                            selectedTaskIds.add(task.id);
                        } else {
                            selectedTaskIds.delete(task.id);
                        }
                    });

                    const info = document.createElement("div");
                    info.className = "flex-1";

                    const title = document.createElement("div");
                    title.className = "font-semibold text-white";
                    title.textContent = task.title || "";

                    const finishDate = document.createElement("div");
                    finishDate.className = "planner-task-meta mt-1";
                    const priorityLabel = task.priority === 3 ? "High" : task.priority === 2 ? "Medium" : "Low";
                    finishDate.textContent = `${priorityLabel} priority${task.finish_date ? " • due " + task.finish_date : ""}`;

                    info.appendChild(title);
                    info.appendChild(finishDate);

                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(info);
                    container.appendChild(wrapper);
                });

            updateSelectionSummary();
        }

        function buildTasksPayload() {
            if (isDemoMode && selectedTaskIds.size === 0) {
                displayTasks.forEach((task) => selectedTaskIds.add(task.id));
            }

            const payloadTasks = [];

            displayTasks.forEach(task => {
                if (!selectedTaskIds.has(task.id)) {
                    return;
                }
                const priority = task.priority === 3 ? "high"
                    : task.priority === 2 ? "medium"
                    : "low";
                payloadTasks.push({
                    id: task.id,
                    title: task.title,
                    priority: priority,
                    estimated_time: "1 hour",
                    deadline: task.finish_date || ""
                });
            });

            return payloadTasks;
        }

        function renderPlan(plan) {
            const board = document.getElementById("planBoard");
            const empty = document.getElementById("planEmpty");
            const applyBtn = document.getElementById("applyPlanBtn");

            board.innerHTML = "";
            latestPlanByDay = {};
            latestPlanIdsByDay = {};

            weekdays.forEach(day => {
                const col = document.createElement("div");
                col.className = "flex flex-col bg-[#2b2b2b] rounded-lg p-2 min-h-32 border border-[#424242]";

                const header = document.createElement("div");
                header.className = "font-semibold mb-2";
                header.textContent = day;

                const list = document.createElement("div");
                list.className = "flex flex-col gap-1";

                const titles = Array.isArray(plan[day]) ? plan[day] : [];
                latestPlanByDay[day] = titles;

                const mappedIds = [];

                titles.forEach(title => {
                    const item = document.createElement("div");
                    item.className = "px-2 py-1 bg-[#1a1a1a] rounded border border-[#424242] text-white";
                    item.textContent = title;
                    list.appendChild(item);

                    const match = displayTasks.find(t => t.title === title);
                    if (match) {
                        mappedIds.push(match.id);
                    }
                });

                latestPlanIdsByDay[day] = mappedIds;

                if (titles.length === 0) {
                    const emptyItem = document.createElement("div");
                    emptyItem.className = "text-xs text-[#bdbdbd]";
                    emptyItem.textContent = "No tasks";
                    list.appendChild(emptyItem);
                }

                col.appendChild(header);
                col.appendChild(list);
                board.appendChild(col);
            });

            empty.classList.add("hidden");
            board.classList.remove("hidden");
            applyBtn.classList.remove("hidden");
        }

        document.getElementById("generatePlanBtn").addEventListener("click", () => {
            const tasksPayload = buildTasksPayload();
            if (tasksPayload.length === 0) {
                showSystemAlert("warning", "No Task Selected", "Select at least one task to include in the plan.");
                return;
            }

            fetch("/api/ai/weekly-plan.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ tasks: tasksPayload })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.plan) {
                    renderPlan(data.plan);
                } else {
                    showSystemAlert("error", "Plan Generation Failed", data.message || "AI could not generate a weekly plan.");
                }
            })
            .catch(() => {
                showSystemAlert("error", "Service Unavailable", "Failed to contact AI service. Please try again later.");
            });
        });

        document.getElementById("applyPlanBtn").addEventListener("click", () => {
            if (isDemoMode) {
                showSystemAlert("info", "Demo Mode", "This plan is using demo tasks, so there is nothing to apply to your account yet.");
                return;
            }

            const realPlan = {};
            Object.keys(latestPlanIdsByDay).forEach((day) => {
                realPlan[day] = (latestPlanIdsByDay[day] || []).filter(id => Number(id) > 0);
            });

            fetch("/api/ai/apply-weekly-plan.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ plan: realPlan })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSystemAlert("success", "Plan Applied", "Weekly plan applied to task deadlines.");
                } else {
                    showSystemAlert("error", "Apply Failed", data.message || "Could not apply weekly plan.");
                }
            })
            .catch(() => {
                showSystemAlert("error", "Service Unavailable", "Failed to apply weekly plan. Please try again later.");
            });
        });

        plannerAlertConfirm.addEventListener("click", hideSystemAlert);
        plannerAlert.addEventListener("click", (event) => {
            if (event.target && event.target.hasAttribute("data-alert-close")) {
                hideSystemAlert();
            }
        });
        window.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                hideSystemAlert();
            }
        });

        renderTaskSelection();
    </script>
</body>
</html>

