<?php
ini_set('max_execution_time', 0);
$start = microtime(true);

try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    $table = 'environmental_data';
} catch(Exception $e) {
    $error = $e->getMessage();
}

$end = microtime(true);
echo 'Time taken: ' . ($end - $start) . ' seconds<br>';
