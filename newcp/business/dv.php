<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function dv($number) {
    $number = strrev(ereg_replace('\.|,|-| ', '', $number));
    $prime = array(3,7,13,17,19,23,29,37,41,43,47,53,59,67,71);
    $n = strlen($number);
    $accum = 0;
    for ($i=0; $i<$n; $i++) {
        $accum += $number[$i] * $prime[$i];
    }
    switch($accum % 11) {
        case 0  : return 0;
        case 1  : return 1;
        default : return 11 - ($accum % 11);
    }
}
?>
