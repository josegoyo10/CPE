<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/QuotationDAO.php');

$request = URequest::GetInstance();
$list = QuotationDAO::searchCustomerQuotations(
    $request->customerId, $request->projectFilter, $request->statusFilter);

$items = array();
foreach($list as $row) {
    $item = array();
    $item[] = $row['id'];
    $item[] = $row['projectName'];
    $item[] = $row['description'];
    $item[] = ULang::FormatDate(time($row['dateCreated']));
    $item[] = $row['statusName'];
    $item[] = getActions($row['statusId'], $row['id']);
    $items[] = $item;
}
print json_encode($items);

function getActions($status, $id) {
    $actions = "<a href='?q=/quotation/view&id=$id'>Ver</a>";
    return $actions;
}
?>