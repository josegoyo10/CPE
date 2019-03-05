<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Provides abstract access to $_SESSION
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class USession {

    public static function Destroy() {
        if (session_id() != '') {
            session_destroy();
        }
    }

    public static function GetInstance() {
        return new USession();
    }

    public function getInt($name, $default = 0) {
        if (isset($_SESSION[$name])) {
            return intval($_SESSION[$name]);
        }
        else {
            return $default;
        }
    }

    public function getDouble($name, $default = 0) {
        if (isset($_SESSION[$name])) {
            return doubleval($_SESSION[$name]);
        }
        else {
            return $default;
        }
    }

    public function __get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        else {
            return null;
        }
    }

    public function __set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

    public function __unset($name) {
        unset($_SESSION[$name]);
    }

}
?>
