<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/QuotationDAO.php');

ULang::Load('PrintedContractForm');

$request = URequest::GetInstance();
$view->quotation = QuotationDAO::GetSimplifiedQuotation($request->id);
$view->pageTitle = "TODO: Title";
$view->overrideTheme = 'ui/view/theme/report.php';

$view->copy = L_CLIENT_COPY;
include(includePath('ui/view/plansepare/PrintedContractForm.php'));

$view->copy = L_STORE_COPY;
include(includePath('ui/view/plansepare/PrintedContractForm.php'));

?>