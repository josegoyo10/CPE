<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of ULang
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class ULang {

    public static function Load($module) {
        global $APP_LANG;
        if (!isset($APP_LANG)) $APP_LANG = 'es_CO';
        $file = includePath("lang/$APP_LANG/$module.lang.php");
        if (file_exists($file)) {
            require_once($file);
        }
    }

    public static function FormatNumber($number) {
        return number_format($number, L_NUM_DECIMAL_NUM, L_NUM_DECIMAL_SEP, L_NUM_THOUSANDS_SEP);
    }

    public static function FormatCurrency($number) {
        return '$' . number_format($number, L_CUR_DECIMAL_NUM, L_CUR_DECIMAL_SEP, L_CUR_THOUSANDS_SEP);
    }

    public static function FormatDate($date = null, $format = L_DATE_FORMAT) {
        if ($date) {
            $mysqldate = array();
            if (is_numeric($date)) {
                return date($format, $date);
            }
            else if (preg_match('/(\d{4})-(\d{2})-(\d{2}).*/', $date, $mysqldate)) {
                return date($format, mktime(0, 0, 0, $mysqldate[2], $mysqldate[3], $mysqldate[1]));
            }
            else {
                return date($format, strtotime($date));
            }
        }
        else {
            return date($format);
        }
    }

    public static function FormatTime($date = null) {
        return ULang::FormatDate($date, L_TIME_FORMAT);
    }

    public static function FormatDateTime($date = null) {
        return ULang::FormatDate($date, L_DATE_FORMAT .' '. L_TIME_FORMAT);
    }

}
?>
