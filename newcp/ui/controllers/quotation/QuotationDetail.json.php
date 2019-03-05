<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Quotation.php');
$session = USession::GetInstance();
if (isset($session->quotationInProcess)) {
    $quotation = unserialize($session->quotationInProcess);
    $n = $quotation->getNumItems();
    $result = array();
    for ($i=0; $i<$n; $i++) {
        $item = $quotation->getItem($i);
        $result[] = $item->getAsArray($i);
    }
    $response = new stdClass();
    $response->total = ULang::FormatCurrency($quotation->getTotal());
    $response->records = $result;
    print json_encode($response);
}
else {
    print '[total: "-", records: []]';
}
?>
