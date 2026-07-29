<?php
function echoEvenInArray(array $array) {
    for ($i = 0; $i < count($array); $i++) {
        $item = $array[$i];
        if (gettype($item) != "integer") {
            continue;
        }

        if ($item % 2 == 0) {
            echo $item . "<br>";
        }
    }
}
