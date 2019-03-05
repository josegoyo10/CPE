<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/LocationDAO.php');
require_once includePath('lib/util/data/UJSON.php');

$request = URequest::GetInstance();
$cityId = $request->getIntArray('val');
$list = LocationDAO::GetNeighbourhoodsByCity($cityId[0], $cityId[1], $cityId[2]);
UJSON::EncodeRowsToOut($list);
?>