<?php
ini_set('max_execution_time', 0);
date_default_timezone_set("America/Los_Angeles");
//keep the DB up to date
//using shell script in this example
//Id like this to always be up to date
//And if user requests the current day go to DM site
//And display it or store it or both

require_once 'src/MyFoundationphp/PendOreille/OneDay.php';

use MyFoundationphp\PendOreille\OneDay;

//single instance of the one day class
try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    $table = 'environmental_data';

    $pend = new OneDay(new DateTime("yesterday"), $db, $table);
    $messages = $pend->getMessages();

} catch(Exception $e) {
    $error = $e->getMessage();
}

echo '<ul>';
foreach ($messages as $message) {
    echo "<li>$message</li>";
}
echo '</ul>';