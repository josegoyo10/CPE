<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/ProductDAO.php');

$request = URequest::GetInstance();
$session = USession::GetInstance();
$type = $request->getArray('searchKey', ',');
if (count($type) == 2) {
    switch ($type[0]) {
        case 'product':
            $productTypes = array('PS','PE');
        case 'service':
            if (!isset($productTypes)) {
                $productTypes = array('SV');
            }
            $loggedUser = $session->loggedUser;
            $list = ProductDAO::searchProduct1($type[1], $request->searchText, $loggedUser->storeId, 20, $productTypes);
            $items = array();
            foreach($list as $row) {
                $item = array();
                $item[] = $row['id'];
                $item[] = $row['sap'];
                $item[] = $row['description'];
                $item[] = ULang::FormatCurrency($row['sellPrice']);
                $item[] = $row['sap'];
                $items[] = $item;
            }
            print json_encode($items);
            break;
        case 'provider':
            $productTypes = array('PS','PE', 'SV');
            $loggedUser = $session->loggedUser;
            $list = ProductDAO::searchProductByProvider($type[1], $request->searchText, $loggedUser->storeId, 20, $productTypes);
            $items = array();
            foreach($list as $row) {
                $item = array();
                $item[] = $row['id'];
                $item[] = $row['sap'];
                $item[] = $row['description'];
                $item[] = ULang::FormatCurrency($row['sellPrice']);
                $item[] = $row['sap'];
                $items[] = $item;
            }
            print json_encode($items);
            break;
        default:
            print '[]';
    }
}
else {
    print '[]';
}
?>