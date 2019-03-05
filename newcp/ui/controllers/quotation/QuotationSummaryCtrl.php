<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/QuotationDAO.php');
require_once includePath('dao/CustomerDAO.php');
require_once includePath('business/Location.php');

ULang::Load('QuotationSummary');

$request = URequest::GetInstance();
$session = USession::GetInstance();
if (isset($session->quotationInProcess)) { unset($session->quotationInProcess); }


$view->quotation = QuotationDAO::GetQuotation($request->id);
$view->customer = CustomerDAO::FindPersonById($view->quotation->customerId);
$clocation = new Location($view->customer->locationId);
$view->customer->locationDescriptions = $clocation->getDescriptions();
$view->address = CustomerDAO::GetAddress($view->quotation->addressId);
$view->addresses = CustomerDAO::GetAddresses($view->quotation->customerId);
$view->loggedUser = $session->loggedUser;

$view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
$view->scripts[] = 'ui/view/js/jquery.dgrid.js';
$view->scripts[] = 'ui/view/js/jqModal.js';
$view->scripts[] = 'ui/view/QuotationSummary.js';
$view->styles[] = 'ui/view/theme/dgrid.css';
$view->styles[] = 'ui/view/theme/jqModal.css';

$view->pageTitle = L_QUOTSUM_PAGE_TITLE;
$view->flowStep = 4;
$view->flowTotalSteps = 5;
$view->flowStepTitle = L_QUOTSUM_PAGE_TITLE;

include(includePath('lib/util/web/UHtml.php'));
include(includePath('ui/view/FlowState.php'));
include(includePath('ui/view/QuotationSummary.php'));
?>