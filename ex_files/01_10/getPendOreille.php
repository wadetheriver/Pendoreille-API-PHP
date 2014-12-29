<?php
require_once 'Foundationphp/PendOreille/OneDay.php';

use Foundationphp\PendOreille\OneDay;

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
    echo "<li>$item</li>";
}
echo '</ul>';