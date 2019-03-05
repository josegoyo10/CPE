<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

// Set debug mode
// define('APP_DEBUG', 1);

// Define Dispatcher options
define('DISPATCHER_BASE_PATH', 'ui/');
define('DISPATCHER_MAIN_VIEW', 'ui/view/theme/main.php');

// General Options
define('TRACKHACK_DAYS', -5);
define('MODULE_ID', 92);
define('CONFIG_GROUP', 5);
define('CONFIG_GROUP_CHEQ_POS', 6);

// Web Services
define('WS_ESB_HOST', '172.20.5.73');
define('WS_ESB_PORT', '80');
//define('WS_ESB_HOST', '172.20.4.64');
//define('WS_ESB_PORT', '9080');

// Session timeout
define('SESSION_TIMEOUT', 5000000);

/**
 * Application Controllers Mapping
 */
$APP_CONTROLLERS = array(

    /* =========================================================================
     * Customer
     * =========================================================================
     */
    array(
        'path' => '/customer/select',
        'ctrl' => 'controllers/customer/SelectCustomerCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/view',
        'ctrl' => 'controllers/customer/ShowCustomerCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/edit',
        'ctrl' => 'controllers/customer/EditCustomerCtrl.php',
        'perm' => PERM_UPDATE),
    array(
        'path' => '/customer/save',
        'ctrl' => 'controllers/customer/SaveCustomerCtrl.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),

    /* =========================================================================
     * Locations
     * =========================================================================
     */
    array(
        'path' => '/location/cities',
        'ctrl' => 'controllers/location/CityList.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/location/neighbourhoods',
        'ctrl' => 'controllers/location/NeighbourhoodList.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),

    /* =========================================================================
     * Quotation list
     * =========================================================================
     */
    array(
        'path' => '/customer/oslist',
        'ctrl' => 'controllers/quotation/QuotationListCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/oslist/data',
        'ctrl' => 'controllers/quotation/QuotationList.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),

    /* =========================================================================
     * Step 4 Quotation Form
     * =========================================================================
     */

    array(
        'path' => '/customer/os/new',
        'ctrl' => 'controllers/quotation/QuotationFormCtrl.php',
        'perm' => PERM_INSERT),
    array(
        'path' => '/customer/projects/json',
        'ctrl' => 'controllers/project/ProjectList.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/projects/create',
        'ctrl' => 'controllers/project/CreateProject.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT),
    array(
        'path' => '/product/list1',
        'ctrl' => 'controllers/product/ProductList1.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/quotation/detail',
        'ctrl' => 'controllers/quotation/QuotationDetail.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/product/select',
        'ctrl' => 'controllers/product/ProductSelect.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/quotation/detail/addProduct',
        'ctrl' => 'controllers/quotation/AddProduct.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/customer/quotation/detail/removeProduct',
        'ctrl' => 'controllers/quotation/RemoveProduct.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/customer/quotation/detail/saveProductSpecs',
        'ctrl' => 'controllers/quotation/SaveProductSpecs.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/customer/quotation/detail/changeDispatchType',
        'ctrl' => 'controllers/quotation/ChangeDispatchType.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/quotation/save',
        'ctrl' => 'controllers/quotation/SaveQuotationCtrl.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),

    /* =========================================================================
     * Step 5 Quotation Summary
     * =========================================================================
     */
    array(
        'path' => '/quotation/view',
        'ctrl' => 'controllers/quotation/QuotationSummaryCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/quotation/print',
        'ctrl' => 'controllers/quotation/PrintQuotationCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/quotation/contract/print',
        'ctrl' => 'controllers/quotation/PrintContractCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/quotation/finiquito/print',
        'ctrl' => 'controllers/quotation/PrintFiniquitoCtrl.php',
        'perm' => PERM_SELECT),
    array(
        'path' => '/customer/address',
        'ctrl' => 'controllers/customer/GetAddress.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/quotation/freight/gen',
        'ctrl' => 'controllers/quotation/GenerateFreight.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),

    /* =========================================================================
     * Financial Interests
     * =========================================================================
     */
    array(
        'path' => '/financial/interests/form',
        'ctrl' => 'controllers/financialInterests/FormCtrl.php',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/financial/interests/printedForm',
        'ctrl' => 'controllers/financialInterests/PrintedFormCtrl.php',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/financial/interests/addProduct',
        'ctrl' => 'controllers/financialInterests/AddProduct.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/financial/interests/products',
        'ctrl' => 'controllers/financialInterests/Products.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT),
    array(
        'path' => '/financial/interests/removeProduct',
        'ctrl' => 'controllers/financialInterests/RemoveProduct.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_INSERT | PERM_UPDATE),
    array(
        'path' => '/financial/basicFinanciation',
        'ctrl' => 'controllers/financialInterests/Financiation.json.php',
        'type' => 'ajax',
        'content-type' => 'text/x-json',
        'perm' => PERM_SELECT)

);

?>
