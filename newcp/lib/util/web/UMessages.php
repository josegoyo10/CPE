<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of UMessages
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UMessages {

    public static function Init() {
        if (isset($_SESSION['UMSG_MESSAGES'])) {
            $_REQUEST['UMSG_MESSAGES'] = $_SESSION['UMSG_MESSAGES'];
        }
        else {
            $_REQUEST['UMSG_MESSAGES'] = array();
        }
        $_SESSION['UMSG_MESSAGES'] = array('global'=>array());
    }

    public static function Get() {
        return $_REQUEST['UMSG_MESSAGES'];
    }
    
    public static function Add($arg0, $arg1 = null) {
        if (is_array($arg0) && count($arg0) > 0) {
            $_SESSION['UMSG_MESSAGES'] = array_merge($_SESSION['UMSG_MESSAGES'], $arg0);
        }
        else if ($arg1 != null) {
            $_SESSION['UMSG_MESSAGES'][$arg0] = $arg1;
        }
        else {
            $_SESSION['UMSG_MESSAGES']['global'][] = $arg0;
        }
    }
}
?>
