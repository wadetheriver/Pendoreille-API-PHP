<?php
date_default_timezone_set("America/Los_Angeles");
// Initialize variables
$start = null;
$end = null;
$startOnly = false;
$error = null;

// Get the start and end dates from the URL
if (isset($_GET['startDate'])) {
    $start = verifyDate($_GET['startDate']);
}
if (isset($_GET['endDate'])) {
    $end = verifyDate($_GET['endDate']);
} else {
    $startOnly = true;
}

// Check that the submitted date is valid
function verifyDate($date) {
    $date = trim($date);
    if (!is_numeric($date) || strlen($date) != 8) {
        return false;
    }
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6);
    if (!checkdate($month, $day, $year)) {
        return false;
    } else {
        return "$year-$month-$day";
    }
}

$yesterday = new DateTime("yesterday");
if(!$start) {
    //store errors in array, need to send as JSON
    $error = array('error' => 'Invalid Request. Permitted paramaters are startDate and endDate in YYYYMMDD format');

} elseif ($start > $yesterday->format("Y-m-d"))  {
    $error = array('error' => 'Start date cannot be greater than yesterday');

} elseif ($start < '2001-01-12' || $end && $end < '2001-01-12') {
    $error = array('error' => 'Out of range. No data available before 20010112.');
} elseif ($start && $startOnly) {
    //just get one day
    $sql = "SELECT * FROM environmental_data WHERE date_recorded = '$start'";
} elseif ($start && $end) {
    if ($end < $start) {
        $error = array('error' => 'End date is before start date.');
    } else {
        $sql = "SELECT * FROM environmental_data WHERE date_recorded
                BETWEEN '$start' AND '$end'";
    }
}