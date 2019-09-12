<?php

class CodeGenerator {
    private $code;
    private $code_mask;
    private $mask = [
        [0,0,0,0],
        [0,0,0,0],
        [0,0,0,0],
        [0,0,0,0],
    ];
    private $range;

    public function setMask($code)
    {
        $this->mask = $code;
    }

    public function generate()
    {
        $this->code = $this->mask;
        $this->range = range(5,20);

        while ($this->complementValues($this->code)) {};
        $this->rangeReduce();

        $this->code_mask = $this->code;

        $this->computePermutations($this->range);
    }

    public function rangeReduce()
    {
        $merge = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($this->code)), 0);
        $this->range = array_values(array_diff($this->range, $merge));
    }

    public function complementValues(&$code)
    {
        // diagonal
        for ($i = 0; $i < 4; $i++) {
            $d1[] = $code[$i][$i];
            $d2[] = $code[3-$i][$i];
        }

        $merge = array_intersect_assoc($d1, array_fill(0,4,0));
        if (count($merge) == 1) {
            $i = array_search(0, $merge);
            $code[$i][$i] = 50 - array_sum($d1);
            return true;
        }

        $merge = array_intersect_assoc($d2, array_fill(0,4,0));
        if (count($merge) == 1) {
            $i = array_search(0, $merge);
            $code[3-$i][$i] = 50 - array_sum($d2);
            return true;
        }

        // row
        for ($i = 0; $i < 4; $i++) {
            $merge = array_intersect_assoc($code[$i], [0,0,0,0]);

            if (count($merge) == 1) {
                $code[$i][array_search(0, $merge)] = 50 - array_sum($code[$i]);
                return true;
            }
        }

        // column
        for ($i = 0; $i < 4; $i++) {
            $col = array_column($code, $i);
            $merge = array_intersect_assoc($col, [0,0,0,0]);

            if (count($merge) == 1) {
                $code[array_search(0, $merge)][$i] = 50 - array_sum($col);
                return true;
            }
        }

        return false;
    }

    protected function computePermutations($array)
    {
        $result = [];
        $done = false;

        $recurse = function($array, $start_i = 0) use (&$result, &$recurse, &$done) {

            if ($done) {
                return;
            }

            if ($start_i === count($array)-1) {
                //array_push($result, $array);
                $done = $this->checkSuccess($array);
            }

            for ($i = $start_i; $i < count($array); $i++) {

                //Swap array value at $i and $start_i
                $t = $array[$i]; $array[$i] = $array[$start_i]; $array[$start_i] = $t;

                //Recurse
                $recurse($array, $start_i + 1);

                //Restore old order
                $t = $array[$i]; $array[$i] = $array[$start_i]; $array[$start_i] = $t;
            }
        };

        $recurse($array);

        return $result;
    }

    protected function checkSuccess($range)
    {
        $this->code = $this->code_mask;

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                if ($this->code[$i][$j] == 0) {
                    $this->code[$i][$j] = array_pop($range);
                }
            }
        }

        $result = true;

        // row
        foreach ($this->code as $row) {
            $result = $result && (50 == array_sum($row));
        }

        // column
        for ($i = 0; $i < count($this->code[0]); $i++) {
            $column = array_column($this->code, $i);
            $result = $result && (50 == array_sum($column));
        }

        // diagonal
        for ($i = 0; $i < 4; $i++) {
            $d1[] = $this->code[$i][$i];
            $d2[] = $this->code[3-$i][$i];
        }

        $result = $result && (50 == array_sum($d1));
        $result = $result && (50 == array_sum($d2));

        return $result;
    }

    public function printCode()
    {
        echo PHP_EOL;

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                printf("%2d ", $this->code[$i][$j]);
            }
            echo PHP_EOL;
        }
    }

    public function printRange()
    {
        echo PHP_EOL;
        foreach ($this->range as $item) {
            printf("%2d ", $item);
        }
        echo PHP_EOL;
    }

    public function getCode()
    {
        return $this->code;
    }
}
