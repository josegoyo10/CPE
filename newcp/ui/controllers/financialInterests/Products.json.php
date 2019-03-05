<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
$session = USession::GetInstance();
if (isset($session->financiationProducts)) {
    $n = count($session->financiationProducts);
    $result = array();
    $total = 0;
    for ($i=0; $i<$n; $i++) {
        $item = $session->financiationProducts[$i];
        $result[] = array(
            $item->productId,
            $item->productBarcode,
            $item->productDescription,
            $item->quantity,
            $item->price,
            $item->quantity * $item->price,
            $item->editablePrice
        );
        $total = $total + ($item->quantity * $item->price);
    }
    $response = new stdClass();
    $response->total = ULang::FormatCurrency($total);
    $response->rawTotal = $total;
    $response->records = $result;
    print json_encode($response);
}
else {
    print '[total: "-", records: []]';
}

?>
