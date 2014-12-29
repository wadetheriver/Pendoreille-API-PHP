<?php
namespace Foundationphp\Calculate;

class Average
{
    protected $array;
    protected $array_length;

    public function __construct(array $values)
    {
        $this->array = $values;
        $this->array_length = count($values);
    }

    public function getMean($precision = null)
    {
        $mean = array_sum($this->array) / $this->array_length;
        if ($precision) {
            return round($mean, $precision);
        } else {
            return $mean;
        }
    }

    public function getMedian($precision = 2)
    {
        sort($this->array);
        $med = $this->array_length/2;
        if ($this->array_length % 2) {
            $med = floor($med);
            return $this->array[$med];
        } else {
            $midvals = $this->array[$med-1] + $this->array[$med];
            return round($midvals/2, $precision);
        }
    }
}