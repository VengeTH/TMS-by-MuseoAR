<?php
require_once dirname(__DIR__) . "/db/tasks.php";

if (!isset($db) || !($db instanceof Task)) {
    $db = new Task();
}

$appTimeZone = new DateTimeZone("Asia/Manila");
$requestedDate = $_GET["calendar_date"] ?? (new DateTimeImmutable("now", $appTimeZone))->format("Y-m-01");
$currentMonth = DateTimeImmutable::createFromFormat("Y-m-d", $requestedDate, $appTimeZone);
if (!$currentMonth instanceof DateTimeImmutable) {
    $currentMonth = new DateTimeImmutable((new DateTimeImmutable("now", $appTimeZone))->format("Y-m-01"), $appTimeZone);
}
$currentMonth = $currentMonth->setDate((int) $currentMonth->format("Y"), (int) $currentMonth->format("m"), 1);

$previousMonth = $currentMonth->modify("-1 month");
$nextMonth = $currentMonth->modify("+1 month");

$tasks = $db->getTasks($_SESSION["user_id"]);
if (!is_array($tasks)) {
    $tasks = [];
}

$eventsByDate = [];
foreach ($tasks as $task) {
    if (empty($task["finish_date"])) {
        continue;
    }
    $eventDate = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", (string) $task["finish_date"], $appTimeZone);
    if (!$eventDate instanceof DateTimeImmutable) {
        $eventDate = new DateTimeImmutable((string) $task["finish_date"], $appTimeZone);
    }
    $dateKey = $eventDate->format("Y-m-d");
    if (!isset($eventsByDate[$dateKey])) {
        $eventsByDate[$dateKey] = [];
    }
    $eventsByDate[$dateKey][] = (string) ($task["title"] ?? "Task");
}

$demoMode = count($tasks) === 0;
if ($demoMode) {
    $eventsByDate[$currentMonth->modify("+3 days")->format("Y-m-d")][] = "Plan priorities";
    $eventsByDate[$currentMonth->modify("+8 days")->format("Y-m-d")][] = "Send reminders";
    $eventsByDate[$currentMonth->modify("+14 days")->format("Y-m-d")][] = "Clear overdue items";
}

$daysInMonth = (int) $currentMonth->format("t");
$firstDayWeekIndex = (int) $currentMonth->format("w");
$leadingCells = $firstDayWeekIndex;
$totalCells = $leadingCells + $daysInMonth;
$trailingCells = (7 - ($totalCells % 7)) % 7;

$baseQuery = ["tab" => "dashboard"];
$prevQuery = array_merge($baseQuery, ["calendar_date" => $previousMonth->format("Y-m-01")]);
$nextQuery = array_merge($baseQuery, ["calendar_date" => $nextMonth->format("Y-m-01")]);

$prevUrl = "/dashboard?" . http_build_query($prevQuery);
$nextUrl = "/dashboard?" . http_build_query($nextQuery);
$monthLabel = $currentMonth->format("F Y");
$todayDate = (new DateTimeImmutable("now", $appTimeZone))->format("Y-m-d");

$dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
?>
<div class="calendar-shell">
    <div class="calendar-shell__header">
        <div>
            <div class="calendar-shell__eyebrow">Calendar</div>
            <h2><?php echo htmlspecialchars($monthLabel); ?></h2>
        </div>
        <div class="calendar-shell__actions">
            <a class="calendar-shell__button" href="<?php echo htmlspecialchars($prevUrl); ?>">Previous</a>
            <a class="calendar-shell__button" href="<?php echo htmlspecialchars($nextUrl); ?>">Next</a>
        </div>
    </div>

    <?php if ($demoMode): ?>
        <div class="calendar-shell__note">Demo events are shown because your account does not have scheduled tasks yet.</div>
    <?php endif; ?>

    <div class="calendar-shell__body">
        <table class="th-calendar-grid" role="grid" aria-label="Monthly Calendar">
            <thead>
                <tr>
                    <?php foreach ($dayNames as $index => $dayName): ?>
                        <?php $isWeekendCol = ($index === 0 || $index === 6) ? "1" : "0"; ?>
                        <th data-weekend="<?php echo $isWeekendCol; ?>"><?php echo htmlspecialchars($dayName); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php for ($i = 0; $i < $leadingCells; $i++): ?>
                        <?php $isWeekendCol = ($i === 0 || $i === 6) ? "1" : "0"; ?>
                        <td class="is-padding" data-weekend="<?php echo $isWeekendCol; ?>"></td>
                    <?php endfor; ?>

                    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                        <?php
                        $cellDate = $currentMonth->setDate((int) $currentMonth->format("Y"), (int) $currentMonth->format("m"), $day);
                        $dayIndex = (int) $cellDate->format("w");
                        $dateKey = $cellDate->format("Y-m-d");
                        $isWeekend = ($dayIndex === 0 || $dayIndex === 6);
                        $isToday = ($dateKey === $todayDate);
                        $events = $eventsByDate[$dateKey] ?? [];
                        $eventJson = htmlspecialchars(json_encode($events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, "UTF-8");
                        ?>
                        <td
                            data-weekend="<?php echo $isWeekend ? "1" : "0"; ?>"
                            data-date="<?php echo htmlspecialchars($dateKey); ?>"
                            data-events="<?php echo $eventJson; ?>"
                            class="<?php echo $isToday ? "is-today" : ""; ?>"
                        >
                            <div class="th-calendar-grid__day"><?php echo $day; ?></div>
                            <div class="th-calendar-grid__events">
                                <?php if (count($events) > 0): ?>
                                    <span><?php echo htmlspecialchars($events[0]); ?></span>
                                    <?php if (count($events) > 1): ?>
                                        <span>+<?php echo count($events) - 1; ?> more</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>

                        <?php if ((($leadingCells + $day) % 7) === 0 && $day !== $daysInMonth): ?>
                            </tr><tr>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php for ($i = 0; $i < $trailingCells; $i++): ?>
                        <?php
                        $columnIndex = ($leadingCells + $daysInMonth + $i) % 7;
                        $isWeekendCol = ($columnIndex === 0 || $columnIndex === 6) ? "1" : "0";
                        ?>
                        <td class="is-padding" data-weekend="<?php echo $isWeekendCol; ?>"></td>
                    <?php endfor; ?>
                </tr>
            </tbody>
        </table>

        <div class="calendar-day-panel" data-calendar-day-panel>
            <h3>Day Details</h3>
            <p>Select a day to view scheduled items.</p>
        </div>
    </div>
</div>

<script>
(() => {
    const shell = document.querySelector(".calendar-shell");
    if (!shell) {
        return;
    }

    const shellBody = shell.querySelector(".calendar-shell__body");
    const dayPanel = shell.querySelector('[data-calendar-day-panel]');
    const calendarCells = shell.querySelectorAll(".th-calendar-grid td[data-date]");

    if (shellBody) {
        shellBody.addEventListener("dblclick", (event) => {
            const target = event.target;
            if (target && target.closest(".calendar-shell__button")) {
                return;
            }
            shell.classList.toggle("is-expanded");
        });
    }

    calendarCells.forEach((cell) => {
        cell.addEventListener("click", () => {
            calendarCells.forEach((item) => item.classList.remove("is-selected"));
            cell.classList.add("is-selected");

            if (!dayPanel) {
                return;
            }

            const dateText = cell.getAttribute("data-date") || "";
            let events = [];
            try {
                events = JSON.parse(cell.getAttribute("data-events") || "[]");
            } catch (_error) {
                events = [];
            }

            const prettyDate = new Date(dateText + "T00:00:00").toLocaleDateString([], {
                year: "numeric",
                month: "long",
                day: "numeric",
            });

            if (!Array.isArray(events) || events.length === 0) {
                dayPanel.innerHTML = `<h3>${prettyDate}</h3><p>No scheduled tasks for this day.</p>`;
                return;
            }

            const items = events
                .map((eventTitle) => `<li>${String(eventTitle).replace(/</g, "&lt;").replace(/>/g, "&gt;")}</li>`)
                .join("");

            dayPanel.innerHTML = `<h3>${prettyDate}</h3><ul>${items}</ul>`;
        });
    });
})();
</script>
