<?php
date_default_timezone_set("America/Los_Angeles"); //need to set this in php.ini cuz I keep getting this error

//The Raw data is missing some days (technical errors and such
//This will populate the date column with all dates

$startDate = new DateTime('2001-01-12');
$endDate = new DateTime('2010-05-26');
$oneDay = new DateInterval('P1D'); //knows days in month and if is a leap year. One day interval
try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    //bind variables (named parameter) in prepared statement..
    $stmt = $db->prepare('INSERT INTO environmental_data (date_recorded) VALUE (:date)');
    while ($startDate <= $endDate) {
        /* Use the PDO bindValue() method instead of bindParam() to avoid strict error messages.
         * The bindParam() method should be used only with variables.
         */
        $stmt->bindValue(':date', $startDate->format('Y-m-d'));
        $stmt->execute();
        $startDate->add($oneDay);

        echo $startDate->format('Y-m-d') . "Done <br/>";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
echo 'All Done!';