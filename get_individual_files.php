<?php
ini_set('max_execution_time', 0);
$start = microtime(true);

require_once 'Foundationphp/PendOreille/OneDay.php';

use Foundationphp\PendOreille\OneDay;

try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    $table = 'environmental_data';
    $months = range(6, 12);
    $months30 = array(4, 6, 9, 11);
    foreach ($months as $month) {
        if (in_array($month, $months30)) {
            $days = range(1, 30);
        } elseif ($month == 2) {
            $days = range(1, 28);
        } else {
            $days = range(1, 31);
        }
        foreach ($days as $day) {
            $pend = new OneDay(new DateTime("2010/$month/$day"), $db, $table);
            $messages[] = $pend->getMessages();
        }
    }
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