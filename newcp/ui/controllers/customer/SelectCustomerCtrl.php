<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

$request = URequest::GetInstance();
$custId = $request->getInt('cust:id');

if ($custId) {
    URequest::Redirect("?q=/customer/view&id=$custId");
}
else {
    ULang::Load('SelectCustomer');
    $view->flowStep = 0;
    $view->flowTotalSteps = 5;
    $view->flowStepTitle = L_SEL_CUST_PAGE_TITLE;
    $view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
    include(includePath('ui/view/SelectCustomer.php'));
}
?>