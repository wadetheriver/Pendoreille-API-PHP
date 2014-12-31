<?php
ini_set('max_execution_time', 0);
//default timezone not set, produced no errors, very difficult to debug
date_default_timezone_set("America/Los_Angeles");
$start = microtime(true);

require_once 'src/MyFoundationphp/PendOreille/OneDay.php';

use MyFoundationphp\PendOreille\OneDay;
try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    $table = 'environmental_data';
    //raw data for entire years stored in single text files ends on 5/27/2010
    //last full day was 5/26
    //days are now stored in single text files
    //simple usage of class, last few days of month of may
    $month = 5; //may
    $days = range(27,31);
    foreach($days as $day){
        $pend = new OneDay(new DateTime("2010/$month/$day"), $db, $table);
        //why storing messages in array unless adding new array elements here?
        //iterate multi dim array below

        $messages[] = $pend->getMessages();
    }


//    $months = range(6, 12);
//    $months30 = array(4, 6, 9, 11);
//    foreach ($months as $month) {
//        if (in_array($month, $months30)) {
//            $days = range(1, 30);
//        } elseif ($month == 2) {
//            $days = range(1, 28);
//        } else {
//            $days = range(1, 31);
//        }
//        foreach ($days as $day) {
//            $pend = new OneDay(new DateTime("2010/$month/$day"), $db, $table);
//            $messages[] = $pend->getMessages();
//        }
//    }
} catch(Exception $e) {
    $error = $e->getMessage();

}

$end = microtime(true);
echo 'Time taken: ' . ($end - $start) . ' seconds<br>';

echo '<ul>';
foreach ($messages as $message) {
    foreach ($message as $item) {
        echo "<li>$item</li>";
    }
}
echo '</ul>';