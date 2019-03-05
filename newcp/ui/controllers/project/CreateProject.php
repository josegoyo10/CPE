<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('dao/ProjectDAO.php');
$request = URequest::GetInstance();
if ($request->customerId) {
    if ($request->projectName) {
        $newId = ProjectDAO::CreateProject($request->customerId, $request->projectName);
        if ($newId) {
            print "{success: true, id: $newId}";
        }
        else {
            print '{success: false}';
        }
    }
    else {
        print '{success: false}';
    }
}
else {
    print '{success: false}';
}
?>
