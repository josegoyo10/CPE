<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Encode/Decode Json Format.
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UJSON {

    public static function EncodeToString($obj) {
        return json_encode($obj);
    }

    public static function EncodeToOut($obj) {
        print json_encode($obj);
    }

    public static function Decode($json) {
        return json_decode($json);
    }

    public static function EncodeRowToOut($row, $rownum = 0) {
        print "{'#':$rownum";
        foreach ($row as $k => $v) {
            print ",'$k':'$v'";
        }
        print "}";
    }

    public static function EncodeRowsToOut($rows) {
        print "[";
        $count = count($rows);
        $start = 0;
        if ($count >= 1) {
            UJSON::EncodeRowToOut($rows[0]);
            $start++;
        }
        for ($i=$start; $i<$count; $i++) {
            print ",";
            UJSON::EncodeRowToOut($rows[$i], $i);
        }
        print "]";
    }
    
}
?>
