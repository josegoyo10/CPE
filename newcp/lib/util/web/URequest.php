<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Provides abstract access to $_REQUEST
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class URequest {

    public static function Restore() {
        if (isset($_REQUEST['USTR_'])) {
            $reqid = $_REQUEST['USTR_'];
            $oldRequest =& $_SESSION[$reqid];
            foreach ($oldRequest as $k => $v) {
                $_REQUEST[$k] = $v;
            }
            unset($_SESSION[$reqid]);
            unset($_REQUEST['USTR_']);
        }
    }

    public static function GetInstance() {
        URequest::Restore();
        return new URequest();
    }

    public static function Redirect($location) {
        @header('Location: ' . $location);
        die();
    }

    public static function StatefulRedirect($location, $getParams = false) {
        if ($getParams) {
            $params = '';
            foreach ($_GET as $k => $v) {
                $params = $params . $k . "=" . urlencode($v) . '&';
            }
            if (strpos($location, '?') === false) {
                $params = '?' . $params;
            }
            else {
                $params = '&' . $params;
            }
            @header('Location: ' . $location . $params);
            die();
        }
        else {
            $reqid = uniqid('USTR');
            $_SESSION[$reqid] = $_REQUEST;
            if (strpos($location, '?') === false) {
                $reqid = '?USTR_=' . $reqid;
            }
            else {
                $reqid = '&USTR_=' . $reqid;
            }
            @header('Location: ' . $location . $reqid);
            die();
        }
    }

    public function getInt($name, $default = 0) {
        if (isset($_REQUEST[$name])) {
            return intval($_REQUEST[$name]);
        }
        else {
            return $default;
        }
    }

    public function getDouble($name, $default = 0) {
        if (isset($_REQUEST[$name])) {
            return doubleval($_REQUEST[$name]);
        }
        else {
            return $default;
        }
    }

    public function getArray($name, $sep = '/') {
        if (isset($_REQUEST[$name])) {
            return explode($sep, $_REQUEST[$name]);
        }
        else {
            return array();
        }
    }

    public function getIntArray($name, $sep = ',') {
        if (isset($_REQUEST[$name])) {
            $s = explode($sep, $_REQUEST[$name]);
            $ret = array();
            foreach ($s as $part) {
                $ret[] = intval($part);
            }
            return $ret;
        }
        else {
            return array();
        }
    }

    public function getObject($name) {
        $obj = &new stdClass;
        foreach ($_REQUEST as $k => $v) {
            $fqn = explode(':', $k, 2);
            if (count($fqn) == 2 && $fqn[0] == $name) {
                $attr = $fqn[1];
                $obj->$attr = $v;
            }
        }
        return $obj;
    }

    public function __get($name) {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        else {
            return null;
        }
    }

    public function __set($name, $value) {
        $_REQUEST[$name] = $value;
    }

    public function __isset($name) {
        return isset($_REQUEST[$name]);
    }

    public function __unset($name) {
        unset($_REQUEST[$name]);
    }

}
?>
