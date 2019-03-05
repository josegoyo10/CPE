<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */

// --- Define application constants
define('_APP_BOOTSTRAP_', 1);
define('_BASE_PATH_', dirname(__FILE__) . '/');

// --- Define permissions
define('PERM_SELECT', 1);
define('PERM_INSERT', 2);
define('PERM_UPDATE', 4);
define('PERM_DELETE', 8);

// --- Utility path resolver
function includePath($path) {
    return _BASE_PATH_ . $path;
}

// --- Load Main Configuration
include(includePath('config/web.php'));

// --- Import minimal dependencies
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

// --- Start Session: Important this line must be here.
session_start();
/*
UDebug::Log(print_r($_SESSION, true));
if (!isset($_SESSION['__ALIVE__']) || $_SESSION['__ALIVE__'] + SESSION_TIMEOUT < time()) {
    $_SESSION['__ALIVE__'] = time();
    URequest::Redirect('index.php');
    exit();
}
*/

# *** Start Integration Point: Get authenticated user from session vars
require_once 'legacy/int1_security_check.php';
# *** End Integration Point.

// -- Load basic localizations
ULang::Load('Locale');

// --- Restore Stateful Request if needed
URequest::Restore();
UMessages::Init();

// --- Restore module configuration
$session = USession::GetInstance();
if (!isset($session->psConfig)) {
    require_once(includePath('dao/ConfigDAO.php'));
    $session->psConfig = ConfigDAO::GetGroupConfiguration();
}
unset($session);

// --- Route Actions
$request = URequest::GetInstance();
URequestDispatcher::Dispatch($request->q);

?>