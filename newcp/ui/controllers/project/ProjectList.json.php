<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/ProjectDAO.php');
require_once includePath('lib/util/data/UJSON.php');

$request = URequest::GetInstance();
$customerId = $request->getInt('val');
$list = ProjectDAO::GetProjectsByCustomer($customerId);
UJSON::EncodeRowsToOut($list);
?>