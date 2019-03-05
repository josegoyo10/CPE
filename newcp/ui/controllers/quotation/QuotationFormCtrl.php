<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/CustomerDAO.php');
require_once includePath('dao/ProjectDAO.php');
require_once includePath('dao/DispatchDAO.php');
require_once includePath('dao/CachedListsDAO.php');
require_once includePath('business/Quotation.php');

ULang::Load('QuotationForm');

$session = USession::GetInstance();
$request = URequest::GetInstance();
if (isset($session->quotationInProcess)) {
    $quotation = unserialize($session->quotationInProcess);
    $customer = CustomerDAO::FindPersonById($quotation->customerId);
}
else if (isset($request->customerId)) {
    $customer = CustomerDAO::FindPersonById($request->customerId);
    $quotation = new Quotation();
    $quotation->customerId = $customer->id;
    $quotation->userId = $session->loggedUser->id;
    $quotation->login = $session->loggedUser->login;
    $quotation->storeId = $session->loggedUser->storeId;
    $quotation->addressId = $customer->addressId;
    $session->quotationInProcess = serialize($quotation);
}

$view->customer = $customer;
$view->quotation = $quotation;

$view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
$view->scripts[] = 'ui/view/js/jquery.dgrid.js';
$view->scripts[] = 'ui/view/js/jqModal.js';
$view->scripts[] = 'ui/view/QuotationForm.js';

$view->styles[] = 'ui/view/theme/dgrid.css';
$view->styles[] = 'ui/view/theme/jqModal.css';

$view->projects = ProjectDAO::GetProjectsByCustomer($view->customer->id);
$view->dispatchTypes = DispatchDAO::GetDispatchTypes(array(1,3));

$view->pageTitle = L_PROD_OS_PAGE_TITLE;
$view->flowStep = 3;
$view->flowTotalSteps = 5;
$view->flowStepTitle = L_PROD_OS_PAGE_TITLE;

include(includePath('lib/util/web/UHtml.php'));
include(includePath('ui/view/FlowState.php'));
include(includePath('ui/view/QuotationForm.php'));
?>