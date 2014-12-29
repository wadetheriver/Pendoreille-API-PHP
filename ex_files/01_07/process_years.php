<?php
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
    fclose($file);
}
 