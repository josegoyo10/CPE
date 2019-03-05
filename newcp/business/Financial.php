<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function calculateCreditWithFixedQuota($total, $initial, $rate, $periods, $taxRate) {
    $iq = floatval($initial);
    $n = intval($periods);
    $v = floatval($total);
    $i = floatval($rate);
    $amount = $v - $iq;
    if ($amount < 0) { $amount = 0.0; }

    $quota1 = $amount * $i * pow(1+$i, $n)/(pow(1+$i, $n)-1);
    $interest = ($quota1 * $n) - $amount;
    $interestTax = ($interest * $taxRate) / (1 + $taxRate);

    $quota = ($amount + $interest) / $n;
    $result = new stdClass;
    $result->initialQuota = $iq;
    $result->numQuotas = $n;
    $result->total = $v;
    $result->financedValue = $amount;
    $result->quota = round($quota);
    $result->rate = $i;
    $result->interests = $interest;
    $result->interestsTax = $interestTax;
    return $result;
}
?>
