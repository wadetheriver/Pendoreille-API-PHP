<?php

use MyFoundationphp\Calculate; //filename is Average.php
require_once('src/MyFoundationphp/Average.php');

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

if ($error) { //debugging
    print_r($error);
}

if(!$error) {
    try{
        $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');

        $air = '';
        $bar = '';
        $wind = '';
        $missing = array();

        ////PDO query can be used in foreach
        foreach ($db->query($sql) as $row) {
            //if air is null all data is null for that date
            if(is_null($row['air_temp'])) {
                $missing[] = $row['date_recorded'];
            } else {
                $air .= $row['air_temp'] . ","; //concatenate each row with data to the new variables
                $bar .= $row['bar_press'] . ",";
                $wind .= $row['wind_speed'] . ",";
            }

        }

        if($missing) {
            $remarks = "No data recorded for:  " . implode(', ', $missing);
            echo $remarks;
        }

       if($air) {
           //strpos finds first instance of needle, strrpos finds last
           $final_comma = strrpos($air, ','); //final comma in data string produce and empty array value...
           $air =  explode (',', substr($air, 0, $final_comma));
       }
        if($bar) {
            $final_comma = strrpos($bar, ',');
            $bar =  explode (',', substr($bar, 0, $final_comma));
        }
        if($wind) {
            $final_comma = strrpos($wind, ',');
            $wind =  explode (',', substr($wind, 0, $final_comma));
        }

        $dmdata = array(
            'Air Temperature' => $air,
            'Barometric Pressure' => $bar,
            'Wind Speed' => $wind
        );

        echo "<pre>";
        print_r($dmdata);
        echo "<pre>";




    } catch (Exception $e){
        $exception = $e->getMessage();
        $error = array('error' => 'Database must be down or something!');
    }
}