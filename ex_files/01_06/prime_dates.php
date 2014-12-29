<?php
$startDate = new DateTime('2001-01-12');
$endDate = new DateTime('2010-05-26');
$oneDay = new DateInterval('P1D');
try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    $stmt = $db->prepare('INSERT INTO environmental_data (date_recorded) VALUE (:date)');
    while ($startDate <= $endDate) {
        /* Use the PDO bindValue() method instead of bindParam() to avoid strict error messages.
         * The bindParam() method should be used only with variables.
         */
        $stmt->bindValue(':date', $startDate->format('Y-m-d'));
        $stmt->execute();
        $startDate->add($oneDay);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
echo 'Done';