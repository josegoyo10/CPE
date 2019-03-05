<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/ProductDAO.php');

$request = URequest::GetInstance();
$session = USession::GetInstance();
$response = new stdClass;
$response->success = false;

$product = ProductDAO::FindProduct($request->barcode, 'UPC', $session->loggedUser->storeId);
if (is_array($product) && count($product) == 1) {
    $response->success = true;
    $item = new stdClass;
    $item->productId = $product[0]['id'];
    $item->productDescription = $product[0]['description'];
    $item->productBarcode = $product[0]['barcode'];
    $item->productType = $product[0]['type'];
    $item->productSubType = $product[0]['subType'];
    $item->productSAPId = $product[0]['sapId'];
    $item->editablePrice = $product[0]['editablePrice'] > 0;
    if ($product[0]['editablePrice']) {
        $item->price = floatval($request->price);
    }
    else {
        $item->price = $product[0]['price'];
    }
    $item->quantity = floatval($request->quantity);
    $products = $session->financiationProducts;
    if (!$products) {
        $products = array();
    }
    if (isset($request->rowKey) && is_numeric($request->rowKey)) {
        $products[$request->rowKey] = $item;
    }
    else {
        $products[] = $item;
    }
    $session->financiationProducts = $products;
}
else {
    $response->message = 'TODO: Item no permitido';
}
print json_encode($response);
?>