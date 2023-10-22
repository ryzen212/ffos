<?php

$arr = [6, 2, 3, 4, 5, 1];
$n = 6;
for ($i = 0; $i < (count($arr)); $i++) {
    for ($j = $i; $j < (count($arr) ); $j++) {
        if ($arr[$i] > $arr[$j]) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $temp;
        }
    }
}

for ($i = 0; $i < (count($arr)); $i++) {
    echo $arr[$i] . ' ';

}
?>