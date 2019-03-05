<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Location.php');

$request = URequest::GetInstance();
if (isset($request->id)) {
    require_once includePath('dao/CustomerDAO.php');
    $customer = CustomerDAO::FindPersonById($request->id);
    if (!$customer) {
        $customer =  new stdClass;
        $customer->id = $request->id;
    }
    ULang::Load('CustomerForm');

    $view->customer = $customer;
    $view->customerId = $customer->id;
    $view->pageTitle = L_CUST_VIEW_PAGE_TITLE;
    $view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
    $view->scripts[] = 'ui/view/customer/view.js';

    $loc = new Location($customer->locationId);
    $view->location = $loc->getDescriptions();

    $view->customerType = CustomerDAO::GetCustomerType($customer->customerTypeId);
    $view->customerCategory = CustomerDAO::GetCustomerCat($customer->categoryId);

    $view->messages = UMessages::Get();
    include(includePath('ui/view/customer/view.php'));
}
else {
    // TODO: Error
    print 'Error ....';
}
?>