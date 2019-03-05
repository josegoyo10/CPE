<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
if (isset($_SESSION['ses_usr_id'])) {
    $session = USession::GetInstance();
    if (!isset($session->loggedUser) || $session->loggedUser->id != $_SESSION['ses_usr_id']) {
        require_once includePath('dao/UserDAO.php');
        $_loggedUser = UserDAO::FindById($_SESSION['ses_usr_id']);
        if ($_loggedUser && $_loggedUser->canSelect) {
            $session->loggedUser = $_loggedUser;
            //URequest::Redirect('?q=/customer/select');
        }
        else {
            URequest::Redirect('ui/view/theme/errors/error_403.php');
        }
    }
}
else {
    URequest::Redirect('ui/view/theme/errors/error_403.php');
}
include 'int2_trackhack.php';
?>