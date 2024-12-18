<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<div class="p-4 flex flex-col w-full gap-4 items-stretch">
    <div class="flex flex-col w-full bg-white min-h-32 lg:min-h-96 rounded-xl shadow-md flex-grow">
        <div class="flex w-full border-b-2 border-gray-500 p-4">
            <h3 class="text-xl">My Task (06)</h3>
            <div class="ml-auto flex gap-4">
                <h3 class="text-xl">Delete</h3>
                <h3 class="text-xl">Mark as Read</h3>
            </div>
        </div>
        <div class="flex flex-col w-full">
            <div class="flex w-full border-b-2 border-black p-2">
                <div class="flex gap-4 items-center justify-center">
                <!-- Checkbox -->
                <input 
                    type="checkbox" 
                    name="Task1" 
                    id="Task1" 
                    class="peer"
                />
                <!-- Label for the checkbox -->
                <label 
                    for="Task1" 
                    class="text-xl cursor-pointer peer-checked:line-through peer-checked:text-gray-500"
                >
                    Task 1
                </label>
                </div>
                <div class="ml-auto">
                    <h3 class="text-xl text-red-700">Today</h3>
                </div>
            </div>
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