<?php
require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

use MyFoundationphp\Calculate\Average as Avg ; //filename is Average.php
require_once('src/MyFoundationphp/Average.php');

$app = new \Slim\Slim();


date_default_timezone_set("America/Los_Angeles");
// Initialize variables
$start = null;
$end = null;
$startOnly = false;
$error = null;
$siteInfo = null;


$app->get('/api/:startDate/:endDate', function ($startDate, $endDate) {
    global $start;
    global $end;

    $start = verifyDate($startDate);
    $end = verifyDate($endDate);

});

$app->get('/api/:startDate/', function ($startDate) {
    global $start;
    global $startOnly;

    $start = verifyDate($startDate);
    $startOnly=true;
});


$app->run();



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

//check most current date actually in the database
//because I'm not running a cron job
//at some point make it update when user logs in or something
$sql = "SELECT MAX(date_recorded) AS max_date FROM environmental_data";
try{
    $db = new PDO('mysql:host=localhost;dbname=pendoreille','penduser','lynda');
    //getting only one row...
    foreach($db->query($sql) as $row) {
        $last_updated = $row['max_date'];
    }


}catch (Exception $e) {
    $exception = $e-> getMessage();
}


//$yesterday = new DateTime("yesterday");
// yesterday is assuming we have cron job running
if (isset($last_updated)) {

//    echo 'last updated:'. $last_updated. '<br>';

    $siteInfo = "Data is current until:  " . $last_updated;
    $yesterday = new DateTime($last_updated); //yesterday is whenever site last updated

    echo '<br>';
} else {
    $yesterday = new DateTime("yesterday");
    $siteInfo = "Cannot determine the last day the API has been updated";
}


if(!$start) {
    //store errors in array, need to send as JSON
    $error = array('error' => 'Invalid Request. Permitted paramaters are startDate and endDate in YYYYMMDD format');

} elseif ($start > $yesterday->format("Y-m-d"))  {
    $error = array('error' => 'Start date cannot be greater than ' . $yesterday->format("Y-m-d"));

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

        $errorInfo = $db->errorInfo();

        if($missing) {
            $remarks = "No data recorded for:  " . implode(', ', $missing);
        }

        if ($air) {
            //strpos finds first instance of needle, strrpos finds last
            $final_comma = strrpos($air, ','); //final comma in data string produce and empty array value...
            $air = explode(',', substr($air, 0, $final_comma));
            $air_stats = new Avg($air);

            $final_comma = strrpos($bar, ',');
            $bar = explode(',', substr($bar, 0, $final_comma));
            $bar_stats = new Avg($bar);

            $final_comma = strrpos($wind, ',');
            $wind = explode(',', substr($wind, 0, $final_comma));
            $ind_stats = new Avg($wind);

            $output = array(
                'airTemperature' =>
                array('mean' => $air_stats->getMean(),
                    'median' => $air_stats->getMedian()),

                'barometricPressure' =>
                array('mean'=>$bar_stats->getMean(),
                    'median' => $bar_stats->getMedian()),

                'windSpeed' =>
                array('mean' => $bar_stats->getMean(),
                    'median' => $bar_stats->getMedian())

        ); // end output array

            if (isset($remarks)) {
                $output['remarks'] = $remarks;// append output, dates not available
            }
            if (isset($siteInfo)) {
                $output['siteInfo'] = $siteInfo;// append output, last time site was updated
            }

        } else {
            $output = array('remarks' => $remarks);// no data in any selected date
        }
        echo 'var jsonReturnData=' . json_encode($output) . ';';

    } catch (Exception $e){
        $exception = $e->getMessage();
        $error = array('error' => 'Database must be down or something!');
    }

} else { //input error
    echo 'var jsonReturnData=' . json_encode($error) . ';';

}


