<?php
require_once __DIR__ . "/../vendor/autoload.php";
use benhall14\phpCalendar\Calendar as Calendar;
require_once dirname(__DIR__) . "/db/tasks.php";

// Create the calendar object
$calendar = new Calendar;

// Set the starting day and day names
$calendar->useSundayStartingDate();
$calendar->useInitialDayNames();

$tasks = $db->getTasks($_SESSION["user_id"]);
if ($tasks !==NULL) {
    $events = array();
    foreach ($tasks as $task) {
        $events[] = array(
            "title" => $task["title"],
            "start" => $task["created_at"],
            "end" => $task["finish_date"],
            'summary' => $task["title"],

        );
    }
    $calendar->addEvents($events);
}

// Get the current date or the date from the query parameter
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$currentDate = new DateTime($date);

// Handle navigation
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'prev') {
        $currentDate->modify('-1 month');
    } elseif ($_GET['action'] == 'next') {
        $currentDate->modify('+1 month');
    }
}

// Create options array
$options = [
    'date' => $currentDate->format('Y-m-d'),
    'color' => 'grey'
];

echo $calendar->display($options);
?>