<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Quotation.php');
$request = URequest::GetInstance();
$session = USession::GetInstance();
$response = new stdClass;
$response->success = false;
if (isset($request->all) && $request->all == 'true') {
    $quotation = unserialize($session->quotationInProcess);
    $quotation->removeAll();
    $session->quotationInProcess = serialize($quotation);
    $response->success = true;
}
else if (isset($request->rowKey) && is_numeric($request->rowKey)) {
    $quotation = unserialize($session->quotationInProcess);
    $quotation->removeItem($request->rowKey);
    $session->quotationInProcess = serialize($quotation);
    $response->success = true;
}
print json_encode($response);
?>
