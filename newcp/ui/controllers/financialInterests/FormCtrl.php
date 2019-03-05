<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/CustomerDAO.php');

ULang::Load('FinancialInterestsForm');
ULang::Load('SearchProductPopup');

$request = URequest::GetInstance();
$session = USession::GetInstance();

if (isset($request->customerId)) {
    $view->customerId = $request->customerId;
    $view->customerName = CustomerDAO::GetName($request->customerId);
    $view->customerFound = ($view->customerName) ? true : false;
    $session->finCustomerId = $view->customerId;
    $session->finCustomerName = $view->customerName;
}
else {
    $session->financiationProducts = array();
}

$view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
$view->scripts[] = 'ui/view/js/jquery.dgrid.js';
$view->scripts[] = 'ui/view/js/jqModal.js';
$view->scripts[] = 'ui/view/financialInterests/Form.js';
$view->scripts[] = 'ui/view/product/SearchProductPopup.js';
$view->styles[] = 'ui/view/theme/dgrid.css';
$view->styles[] = 'ui/view/theme/jqModal.css';

$view->pageTitle = L_INTERESTS_PAGE_TITLE;

include(includePath('lib/util/web/UHtml.php'));
include(includePath('ui/view/financialInterests/Form.php'));

?>
