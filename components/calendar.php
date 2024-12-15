<?php
require_once __DIR__ . "/../vendor/autoload.php";
use benhall14\phpCalendar\Calendar as Calendar;

// Create the calendar object
$calendar = new Calendar;

// Set the starting day and day names
$calendar->useSundayStartingDate();
$calendar->useInitialDayNames();

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