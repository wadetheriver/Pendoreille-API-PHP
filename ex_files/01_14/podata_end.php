<?php
require_once 'Foundationphp/Calculate/Average.php';

use Foundationphp\Calculate\Average as Avg;

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
$yesterday = new DateTime('yesterday');
if (!$start) {
    $error = array('error' =>
        'Invalid request. Permitted parameters are startDate and endDate in YYYYMMDD format.');
} elseif ($start > $yesterday->format('Y-m-d')) {
    $error = array('error' => 'Start date cannot be greater than yesterday.');
} elseif ($start < '2001-01-12' || ($end && $end < '2001-01-12')) {
    $error = array('error' => 'Out of range. No data available before 20010112.');
} elseif ($start && $startOnly) {
    $sql = "SELECT * FROM environmental_data WHERE date_recorded = '$start'";
} elseif ($start && $end) {
    if ($end < $start) {
        $error = array('error' => 'End date is before start date.');
    } else {
        $sql = "SELECT * FROM environmental_data WHERE date_recorded
                BETWEEN '$start' AND '$end'";
    }
}

if (!$error) {
    try {
        $db = new PDO ('mysql:host=localhost;dbname=pendoreille', 'penduser',
            'lynda');
        $air = '';
        $bar = '';
        $wind = '';
        $missing = array();
        foreach ($db->query($sql) as $row) {
            if (is_null($row['air_temp'])) {
                $missing[] = $row['date_recorded'];
            } else {
                $air .= $row['air_temp'] . ',';
                $bar .= $row['bar_press'] . ',';
                $wind .= $row['wind_speed'] . ',';
            }
        }
        $errorInfo = $db->errorInfo();
        if ($missing) {
            $remarks = 'No data for ' . implode(', ', $missing);
        }
        if ($air) {
            $finalComma = strrpos($air, ',');
            $air = substr($air, 0, $finalComma);
            $finalComma = strrpos($bar, ',');
            $bar = substr($bar, 0, $finalComma);
            $finalComma = strrpos($wind, ',');
            $wind = substr($wind, 0, $finalComma);
            $air_stats = new Avg(explode(',', $air));
            $bar_stats = new Avg(explode(',', $bar));
            $wind_stats = new Avg(explode(',', $wind));
            $output =
                array('airTemperature' =>
                           array('mean' => $air_stats->getMean(),
                                 'median' => $air_stats->getMedian()),
                      'barometricPressure' =>
                          array('mean' => $bar_stats->getMean(),
                              'median' => $bar_stats->getMedian()),
                      'windSpeed' =>
                          array('mean' => $wind_stats->getMean(),
                              'median' => $wind_stats->getMedian()));
            if (isset($remarks)) {
                $output['remarks'] = $remarks;
            }
        } else {
            $output = array('remarks' => $remarks);
        }
        echo 'var jsonReturnData=' . json_encode($output) . ';';
    } catch (Exception $e) {
        $exception = $e->getMessage();
        $error = array('error' => 'Problem connecting to database.');
    }
} else {
    echo 'var jsonReturnData=' . json_encode($error) . ';';
}

// For debugging
if (isset($errorInfo[2])) {
    echo $errorInfo[2];
}
if (isset($exception)) {
    echo $exception;
}