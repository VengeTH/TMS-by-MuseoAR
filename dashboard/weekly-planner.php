<?php
require_once dirname(__DIR__) . "/helpers/sessionHandler.php";
require_once dirname(__DIR__) . "/db/tasks.php";

$db = new Task();
$tasks = $db->getTasks($_SESSION["user_id"]);
if ($tasks === null) {
    $tasks = [];
}
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
    </style>
</head>
<body class="min-h-screen bg-[#0f0f0f] text-white">
    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold" style="font-family: 'Space Grotesk', 'Inter', sans-serif;">AI Weekly Planner</h1>
            <a href="/dashboard?tab=dashboard" class="text-yellow-400 hover:underline text-sm">← Back to Dashboard</a>
        </div>

        <div class="bg-[#1a1a1a] rounded-xl shadow mb-6 p-4 border border-[#424242]">
            <h2 class="text-lg font-semibold mb-2">Generate Plan</h2>
            <p class="text-sm text-[#bdbdbd] mb-4">
                Select which tasks to include in your weekly plan. AI will distribute them from Monday to Sunday.
            </p>
            <div class="flex flex-col gap-2 max-h-64 overflow-y-auto mb-4" id="taskSelection">
                <!-- Filled by JS -->
            </div>
            <button
                id="generatePlanBtn"
                class="px-4 py-2 bg-[#ffc107] text-[#0f0f0f] rounded hover:bg-[#ffb300] text-sm font-semibold"
            >
                Generate Weekly Plan with AI
            </button>
        </div>

        <div class="bg-[#1a1a1a] rounded-xl shadow p-4 border border-[#424242]">
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
                    class="px-4 py-2 bg-[#ffc107] text-[#0f0f0f] rounded hover:bg-[#ffb300] text-sm font-semibold hidden"
                >
                    Apply Plan to Tasks
                </button>
            </div>
        </div>
    </div>

    <script>
        const allTasks = <?php echo json_encode($tasks, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?> || [];

        const weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        const selectedTaskIds = new Set();
        let latestPlanByDay = {};
        let latestPlanIdsByDay = {};

        function renderTaskSelection() {
            const container = document.getElementById("taskSelection");
            container.innerHTML = "";

            allTasks
                .filter(t => !t.parent_task_id)
                .forEach(task => {
                    const wrapper = document.createElement("label");
                    wrapper.className = "flex items-center gap-2 text-sm cursor-pointer";

                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.value = String(task.id);
                    checkbox.checked = selectedTaskIds.has(task.id);
                    checkbox.addEventListener("change", () => {
                        if (checkbox.checked) {
                            selectedTaskIds.add(task.id);
                        } else {
                            selectedTaskIds.delete(task.id);
                        }
                    });

                    const info = document.createElement("span");
                    const title = task.title || "";
                    const finishDate = task.finish_date || "";
                    info.textContent = title + (finishDate ? " (" + finishDate + ")" : "");

                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(info);
                    container.appendChild(wrapper);
                });
        }

        function buildTasksPayload() {
            const payloadTasks = [];

            allTasks.forEach(task => {
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

                    const match = allTasks.find(t => t.title === title);
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
                alert("Select at least one task to include in the plan.");
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
                    alert(data.message || "AI could not generate a weekly plan.");
                }
            })
            .catch(() => {
                alert("Failed to contact AI service. Please try again later.");
            });
        });

        document.getElementById("applyPlanBtn").addEventListener("click", () => {
            fetch("/api/ai/apply-weekly-plan.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ plan: latestPlanIdsByDay })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert("Weekly plan applied to task deadlines.");
                } else {
                    alert(data.message || "Could not apply weekly plan.");
                }
            })
            .catch(() => {
                alert("Failed to apply weekly plan. Please try again later.");
            });
        });

        renderTaskSelection();
    </script>
</body>
</html>

