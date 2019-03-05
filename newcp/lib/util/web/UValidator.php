<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

ULang::Load('Validators');

/**
 * Description of UValidator
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UValidator {

    public function checkRequired($obj, $attr, &$messages) {
        if (isset($obj->$attr)) {
            if (trim($obj->$attr) == '') {
                $messages[$attr] = VALID_REQUIRED;
                return false;
            }
        }
        else {
            $messages[$attr] = VALID_REQUIRED;
            return false;
        }
        return true;
    }

    public function checkInt($obj, $attr, &$messages) {
        if (isset($obj->$attr)) {
            if (!is_numeric($obj->$attr)) {
                $messages[$attr] = VALID_FORMAT;
                return false;
            }
            else {
                $obj->$attr = intval($obj->$attr);
            }
        }
        return true;
    }

    public function checkNumber($obj, $attr, &$messages) {
        if (isset($obj->$attr)) {
            if (!is_numeric($obj->$attr)) {
                $messages[$attr] = VALID_FORMAT;
                return false;
            }
            else {
                $obj->$attr = doubleval($obj->$attr);
            }
        }
        return true;
    }

    public function checkIntRange($obj, $attr, $min, $max, &$messages) {
        if (isset($obj->$attr)) {
            if (!is_numeric($obj->$attr)) {
                $messages[$attr] = VALID_FORMAT;
                return false;
            }
            else {
                $obj->$attr = intval($obj->$attr);
                if ($min > $obj->$attr || $max < $obj->$attr) {
                    $messages[$attr] = sprintf(VALID_RANGE, array($min, $max));
                    return false;
                }
            }
        }
        return true;
    }

}
?>
