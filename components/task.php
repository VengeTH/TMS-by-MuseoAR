<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<div class="p-4 flex flex-col w-full gap-4 items-stretch">
    <div class="flex flex-col w-full bg-white min-h-32 lg:min-h-96 rounded-xl shadow-md flex-grow">
        <div class="flex w-full border-b-2 border-gray-500 p-4">
            <h3 class="text-xl">My Task (06)</h3>
            <div class="ml-auto flex gap-4">
                <button class="text-xl">Delete</button>
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
            require_once dirname(__DIR__) . "/db/tasks.php";
            $db = new task();
            $tasks = $db->getTasks($_SESSION["user_id"]);
            foreach ($tasks as $task) {
                $finishDate = new DateTime($task["finish_date"]);
                $formattedFinishDate = $finishDate->format("m-d-y h:i A");
                $isToday = $finishDate->format('Y-m-d') === (new DateTime())->format('Y-m-d');

                echo '<div class="flex w-full border-b-2 border-black p-2">';
                echo '<div class="flex gap-4 items-center justify-center">';
                echo '<input type="checkbox" name="Task' . htmlspecialchars($task["id"]) . '" id="Task' . htmlspecialchars($task["id"]) . '" class="peer" />';
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