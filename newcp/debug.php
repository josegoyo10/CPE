<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
define('_APP_BOOTSTRAP_', 1);
define('_BASE_PATH_', dirname(__FILE__) . '/');

// --- Utility path resolver
function includePath($path) {
    return _BASE_PATH_ . $path;
}

require_once 'config/web.php';
require_once includePath('lib/adodb5/adodb-exceptions.inc.php');
require_once includePath('lib/adodb5/adodb.inc.php');
require_once includePath('lib/util/data/UDataSource.php');
require_once includePath('config/datasource.php');
require_once includePath('lib/util/web/URequest.php');
require_once includePath('lib/util/web/URequestDispatcher.php');
require_once includePath('lib/util/web/USession.php');
require_once includePath('lib/util/debug/UDebug.php');
require_once includePath('lib/util/lang/ULang.php');
require_once includePath('lib/util/web/UMessages.php');

session_start();
?>
<html>
    <head>
        <title>Debug</title>
    </head>
    <body>
        <?
        
        include('sdo/FreightService.php');
        $fs = FreightService::GetInstance();

        $source = new Location('11001000010096');
        $target = new Location('11001000010096');

        $response = $fs->getFreightFare($source, $target, "Test Address", 2, 1500, 1, 0);
        print '<pre>';
        //print_r($source);
        print_r($response);
        print '</pre>';
        
        
        if (defined('APP_DEBUG')) {
            print '<h1>Debug</h1>';
            print '<pre>';
            print_r($GLOBALS['APP_DEBUG_LOG']);
            print '</pre>';
            print '<h1>Globals</h1>';
            print '<pre>';
            print_r($GLOBALS);
            print '</pre>';
        }
        else {
            print "Debug mode is disabled.";
        }
        ?>
    </body>
</html>
