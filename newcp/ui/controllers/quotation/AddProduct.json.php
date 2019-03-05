<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Quotation.php');
require_once includePath('dao/ProductDAO.php');
require_once includePath('dao/DispatchDAO.php');

$request = URequest::GetInstance();
$session = USession::GetInstance();
$response = new stdClass;
$response->success = false;

$product = ProductDAO::FindProduct($request->barcode, 'UPC', $session->loggedUser->storeId);
if (is_array($product) && count($product) == 1) {
    $response->success = true;
    $dispatch = DispatchDAO::FindById($request->dispatchTypeId);
    $item = new QuotationItem();
    $item->productId = $product[0]['id'];
    $item->productDescription = $product[0]['description'];
    $item->productBarcode = $product[0]['barcode'];
    $item->productType = $product[0]['type'];
    $item->productSubType = $product[0]['subType'];
    $item->productSAPId = $product[0]['sapId'];
    $item->dispatchTypeId = $dispatch->id;
    $item->dispatchTypeDescription = $dispatch->description;
    if ($product[0]['editablePrice']) {
        $item->price = floatval($request->price);
    }
    else {
        $item->price = $product[0]['price'];
    }
    $item->quantity = floatval($request->quantity);
    $quotation = unserialize($session->quotationInProcess);
    if (isset($request->rowKey) && is_numeric($request->rowKey)) {
        $old = $quotation->getItem($request->rowKey);
        $item->specification = $old->specification;
        $quotation->setItem($request->rowKey, $item);
    }
    else {
        $quotation->addItem($item);
    }
    $session->quotationInProcess = serialize($quotation);
}
else {
    $response->message = 'TODO: Item no permitido';
}
print json_encode($response);
?>