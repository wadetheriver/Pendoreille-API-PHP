<?php
//process the raw data downloaded with get_full_years.php
//http://lpo.dt.navy.mil/data/DM/
//Make it friendly for the API I'm about to build

for ($year = 2001; $year <= 2010; $year++) {
    //format changes in 2008
    $ws = $year < 2007 ? 1 : 7;
    $at = $year < 2007 ? 4 : 1;
    $bp = $year < 2007 ? 6 : 2;
    $file = fopen('rawdata/' . $year . '.txt', 'r');
    $firstline = fgets($file); //gets first line and moves pointer to second where the actual data is

    //data is in a tab separated list
    //date and time are both in the first column
//    $line = fgetcsv($file, 500, "\t");
//    print_r($line);

    //create our own data array of the data
    $dataset = array();
    while (($line = fgetcsv($file, 500, "\t")) !== false) {
        $date = substr($line[0], 0, 10); //first element in data is date, 10 characters long
//        $time = substr($line[0], 12, 10); //get even more complex data by adding [$date][$time]
        $dataset[$date]['air_temp'][] = ltrim($line[$at]); //line[index]
        $dataset[$date]['bar_press'][] = ltrim($line[$bp]);
        $dataset[$date]['wind_speed'][] = ltrim($line[$ws]);
    }

    fclose($file);

}

echo"<pre>";
print_r($dataset);
echo"<pre>";

?>


 