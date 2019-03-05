<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Financial.php');
require_once includePath('dao/ConfigDAO.php');

$session = USession::GetInstance();
$view->rows = $session->financiationProducts;
$view->storeId = $session->loggedUser->storeId;
$view->storeName = $session->loggedUser->storeName;
$view->datetime = mktime();
$view->customerId = $session->finCustomerId;
$view->customerName = $session->finCustomerName;

$request = URequest::GetInstance();
$finConfig = ConfigDAO::GetGroupConfiguration(CONFIG_GROUP_CHEQ_POS);
$view->credit = calculateCreditWithFixedQuota(
    $request->total,
    $request->initialQuota,
    $finConfig->CHEQ_POS_INTERES,
    $request->numQuotas,
    $finConfig->CHEQ_POS_IVA);

$view->text = $finConfig->CHEQ_POS_TEXT;
$view->overrideTheme = 'ui/view/theme/report.php';
include includePath('ui/view/financialInterests/PrintedForm.php');
?>
