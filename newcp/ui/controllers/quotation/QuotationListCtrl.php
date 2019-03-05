<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/ProjectDAO.php');
require_once includePath('dao/CachedListsDAO.php');
require_once includePath('business/Location.php');

ULang::Load('QuotationList');

$request = URequest::GetInstance();
if (isset($request->customerId)) {
    require_once includePath('dao/CustomerDAO.php');
    $view->customer = CustomerDAO::FindPersonById($request->customerId);
    $view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
    $view->scripts[] = 'ui/view/js/jquery.dgrid.js';
    $view->scripts[] = 'ui/view/QuotationList.js';
    $view->styles[] = 'ui/view/theme/dgrid.css';
    $view->projects = ProjectDAO::GetProjectsByCustomer($view->customer->id);
    $view->states = CachedListsDAO::GetStates(array('SS', 'PS'));

    $loc = new Location($view->customer->locationId);
    $view->location = $loc->getDescriptions();

    $view->pageTitle = L_QUOTLST_PAGE_TITLE;
    $view->flowStep = 2;
    $view->flowTotalSteps = 5;
    $view->flowStepTitle = L_QUOTLST_PAGE_TITLE;

    include(includePath('lib/util/web/UHtml.php'));
    include(includePath('ui/view/FlowState.php'));
    include(includePath('ui/view/QuotationList.php'));
}
else {
    // TODO: Error
    print 'Error ...';
}
?>