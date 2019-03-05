<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('ui/controllers/customer/CustomerValidator.php');
require_once includePath('dao/CustomerDAO.php');
require_once includePath('dao/LocationDAO.php');

$request = URequest::GetInstance();
$cust = $request->getObject('cust');

ULang::Load('Common');

$messages = array();
$validator = CustomerValidator::GetInstance();
$id = $cust->id;

$response = new stdClass;
$response->customerId = $id;

if ($validator->validate($cust, $messages)) {
    try {
        $customer = CustomerDAO::SavePerson($cust);
        $response->customerId = $customer->id;
        $response->success = true;
    }
    catch (Exception $ex) {
        $response->messages = array('__global' => $ex->getMessage());
        $response->success = false;
    }
}
else {
    $response->messages = $messages;
    $response->success = false;
}

print json_encode($response);

?>