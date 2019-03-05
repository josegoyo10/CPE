<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
$GLOBALS['APP_DEBUG_LOG'] = array();

/**
 * Description of UDebug
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UDebug {

    public static function Log($message) {
        global $APP_DEBUG_LOG;
        if (defined('APP_DEBUG')) {
            $APP_DEBUG_LOG[] = $message;
        }
    }

    public static function LogTrace() {
        $trace = debug_backtrace();
        $n = count($trace);
        UDebug::Log('Stack trace dump called from &lt;' . $trace[0]['file'] . ' line ' . $trace[0]['line'] . '&gt;');
        for ($i=$n-1; $i>0; $i--) {
            $item =& $trace[$i];
            $log = '';
            if (isset($item['class'])) {
                $log .= 'Method call: ' . $item['class'] . $item['type'] . $item['function'] . '(' . implode(', ', $item['args']) . ')';
            }
            else if (isset($item['function'])) {
                $log .= 'Function call: ' . $item['function'] . '(' . implode(', ', $item['args']) . ')';
            }
            $log .= ' &lt;' . $item['file'] . ' line ' . $item['line'] . '&gt;';
            UDebug::Log(str_repeat(' ', $n-$i) . $log);
        }
    }

    public static function Dump() {
        global $APP_DEBUG_LOG;
        if (defined('APP_DEBUG')) {
            foreach ($APP_DEBUG_LOG as $n => $line) {
                print "$n: $line\n";
            }
        }
    }
    
}
?>
