<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/LocationDAO.php');
require_once includePath('dao/CachedListsDAO.php');
require_once includePath('lib/util/data/UJSON.php');

$session = USession::GetInstance();
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
    $view->pageTitle = L_CUST_FORM_PAGE_TITLE;
    $view->scripts[] = 'ui/view/js/jquery-1.2.6.min.js';
    $view->scripts[] = 'ui/view/js/jquery.cascade.js';
    $view->scripts[] = 'ui/view/js/jquery.cascade.ext.js';
    $view->scripts[] = 'ui/view/js/forms.js';
    $view->scripts[] = 'ui/view/customer/form.js';

    $view->departments = LocationDAO::GetAllDepartments();
    $view->cities = LocationDAO::GetCitiesByDepartment($view->customer->departmentId);
    $cityPk = explode(',', $view->customer->cityId);
    $view->neighbourhoods = LocationDAO::GetNeighbourhoodsByCity($cityPk[0], $cityPk[1], $cityPk[2]);
    $view->genders = CachedListsDAO::GetGenders();
    $view->custTypes = CachedListsDAO::GetCustomerTypes();
    $view->custCategories = CachedListsDAO::GetCustomerCategories();
    $view->taxContributionTypes = CachedListsDAO::GetTaxContributionTypes();

    $view->overrideTheme = 'ui/view/theme/popupForm.php';
    $view->messages = UMessages::Get();
    include(includePath('ui/view/customer/form.php'));
}
else {
    // TODO: Error
    print 'Error ....';
}
?>