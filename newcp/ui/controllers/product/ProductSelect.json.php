<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/ProductDAO.php');
$request = URequest::GetInstance();
$session = USession::GetInstance();
$products = ProductDAO::FindProduct($request->code, $request->type, $session->loggedUser->storeId);
$response = new stdClass();
if (count($products) > 0) {
    $response->success = true;
    $response->items = $products;
}
else {
    $response->success = false;
}
print json_encode($response);
?>