<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
$request = URequest::GetInstance();
$session = USession::GetInstance();
$response = new stdClass;
$response->success = false;
if (isset($request->all) && $request->all == 'true') {
    $response->success = true;
    $session->financiationProducts = array();
}
else if (isset($request->rowKey) && is_numeric($request->rowKey)) {
    $response->success = true;
    $products = $session->financiationProducts;
    if (array_key_exists($request->rowKey, $products)) {
        unset($products[$request->rowKey]);
        $tmp = array();
        foreach ($products as $item) {
            $tmp[] = $item;
        }
        $session->financiationProducts = $tmp;
    }
}
print json_encode($response);
?>
