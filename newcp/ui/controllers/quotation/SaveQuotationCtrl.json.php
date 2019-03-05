<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/QuotationDAO.php');
require_once includePath('dao/CustomerDAO.php');
require_once includePath('business/Quotation.php');

$request = URequest::GetInstance();
$session = USession::GetInstance();

$quotation = unserialize($session->quotationInProcess);
$quotation->projectId = $request->getInt('projectId');
$quotation->comments = $request->description;

$customer = CustomerDAO::FindPersonById($request->customerId);
$quotation->addressId = $customer->addressId;

$response = new stdClass;
$response->success = false;
$response->messages = array();

ULang::Load('QuotationForm');

// Validate non empty quotations
if ($quotation->getNumItems() == 0) {
    $response->messages[] = L_VAL_0_PRODUCTS;
}

// Validate only one dispatch type per quotation. Plan Separe requirement.
$dispatchTypeId = $quotation->items[0]->dispatchTypeId;
foreach ($quotation->items as $item) {
    if ($item->dispatchTypeId != $dispatchTypeId) {
        $response->messages[] = L_VAL_MUL_DISPATCH_TYPE;
        break;
    }
}

if (count($response->messages) == 0) {
    try {
        QuotationDAO::SaveQuotation($quotation);
        $response->success = true;
    }
    catch (Exception $ex) {
        $response->success = false;
        $response->messages[] = $ex->getMessage();
    }
}

$response->quotationId = $quotation->id;
$session->quotationInProcess = serialize($quotation);
print json_encode($response);
?>