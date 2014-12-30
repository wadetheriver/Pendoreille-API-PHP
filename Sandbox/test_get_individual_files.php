<?php
//For debugging and testing retrieval of data from DM station with wadetheriver\pendeoreiile\OneDay

date_default_timezone_set("America/Los_Angeles");
use Wadetheriver\PendOreille\OneDay;
require_once '../src/Wadetheriver/PendOreille/OneDay.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>

<?php $test_day = new OneDay(new DateTime("2014/01/01"));?>

<?php

$messages = $test_day->getMessages();

echo '<ul>';
foreach ($messages as $message) {

        echo "<li>$message</li>";

}
echo '</ul>';

?>


</body>
</html>