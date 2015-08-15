<?php
//example data from 2001
//lpo.dt.navy.mil/data/DM/Environmental_Data_2001.txt
ini_set('max_execution_time', '0');
$url = 'http://lpo.dt.navy.mil/data/DM/Environmental_Data_';
//single text files for these years include all the data
for ($year = 2001; $year <= 2010; $year++) {
    if ($data = file_get_contents($url . $year . '.txt')) {
        if (file_put_contents('rawdata_new/' . $year . '.txt', $data)) {
            echo "Saved $year<br>";
        } else {
            echo "Cannot write file for $year<br>";
        }
    } else {
        echo "Problem getting file for $year<br>";
    }
}
 