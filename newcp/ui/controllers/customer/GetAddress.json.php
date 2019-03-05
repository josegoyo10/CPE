<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/CustomerDAO.php');
$request = URequest::GetInstance();
$addressId = $request->addressId;
$address = CustomerDAO::GetAddress($addressId);
print json_encode($address);
?>
