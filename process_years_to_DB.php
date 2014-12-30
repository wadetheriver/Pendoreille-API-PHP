<?php
ini_set('max_execution_time', '0');
$start = microtime(true); //Return current Unix timestamp with microseconds
try {
    $db = new PDO('mysql:host=localhost;dbname=pendoreille', 'pendadmin', 'lynda');
    $stmt = $db->prepare('UPDATE environmental_data SET air_temp = :air,
                          bar_press = :bar, wind_speed = :wind
                          WHERE date_recorded = :date');
    $errorInfo = $stmt->errorInfo();
    if (isset($errorInfo[2])) { //3 element will be set if error in statement
        echo $errorInfo[2];
    }
    for ($year = 2001; $year <= 2010; $year++) {
        $w = $year < 2007 ? 1 : 7;
        $a = $year < 2007 ? 4 : 1;
        $b = $year < 2007 ? 6 : 2;
        $file = fopen('rawdata/' . $year . '.txt', 'r');
        $firstline = fgets($file);
        $dataset = array();
        while (($line = fgetcsv($file, 500, "\t")) !== false) {
            $date = substr($line[0], 0, 10);
            $dataset[$date]['air_temp'][] = ltrim($line[$a]);
            $dataset[$date]['bar_press'][] = ltrim($line[$b]);
            $dataset[$date]['wind_speed'][] = ltrim($line[$w]);
        }

        fclose($file); //close the file for this year

        foreach ($dataset as $date => $subarrays) {
            /* When binding the values to the named parameters, the PDO bindParam()
             * method should be used only with variables. The date is stored as a
             * variable so using bindParam() is correct. However, to avoid strict
             * error messages, the other values should be bound using bindValue().
             */
            $stmt->bindParam(':date', $date);
            //store the data as one big comma separated list
            $stmt->bindValue(':air', implode(',', $subarrays['air_temp']));
            $stmt->bindValue(':bar', implode(',', $subarrays['bar_press']));
            $stmt->bindValue(':wind', implode(',', $subarrays['wind_speed']));

            $stmt->execute(); //execute the prepared statement for each year
        }
        echo "$year done<br>";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
$end = microtime(true);
echo 'Time taken: ' . ($end - $start ) . ' seconds';