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

$view->quotation = QuotationDAO::GetQuotation($request->id);
$view->iva = QuotationDAO::GetIVA($request->id);
$view->customer = CustomerDAO::FindPersonById($view->quotation->customerId);
$clocation = new Location($view->customer->locationId);
$view->customer->locationDescriptions = $clocation->getDescriptions();
$view->address = CustomerDAO::GetAddress($view->quotation->addressId);
$view->loggedUser = $session->loggedUser;

$view->pageTitle = L_QUOTSUM_PAGE_TITLE;
$view->overrideTheme = 'ui/view/theme/report.php';
include(includePath('ui/view/barcode.php'));
include(includePath('ui/view/plansepare/PrintedQuotationForm.php'));
?>