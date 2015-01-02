<?php
// test this median stuff
// prepping for Average class

$myarray = range(3,491, 7);
$myarray[] = 2096;
$myarray_length = count($myarray);

echo "My Array: " . implode($myarray, ',');
echo "</br>";
echo "Is this long: " . $myarray_length;
echo "</br>";

sort($myarray);

$med = $myarray_length/2;
echo "divided by two has: " .$med . " elements<br/>";
//if there is a remainder % returns 1 true
if ($myarray_length % 2) {
$med = floor($med); //med is x.5 floor rounds down
echo "And the Median is: " . $myarray[$med];

} else {

$midvals = $myarray[$med-1] + $myarray[$med];

echo "And the Median is: " . round($midvals/2, 2);
}