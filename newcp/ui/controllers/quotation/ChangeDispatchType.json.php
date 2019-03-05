<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Quotation.php');
require_once includePath('dao/DispatchDAO.php');
$request = URequest::GetInstance();
$session = USession::GetInstance();
$response = new stdClass;
$response->success = false;
$dispatch = DispatchDAO::FindById($request->dispatchTypeId);
if ($dispatch) {
    $quotation = unserialize($session->quotationInProcess);
    foreach ($quotation->items as $item) {
        $item->dispatchTypeId = $dispatch->id;
        $item->dispatchTypeDescription = $dispatch->description;
    }
    $session->quotationInProcess = serialize($quotation);
    $response->success = true;
}
print json_encode($response);
?>
