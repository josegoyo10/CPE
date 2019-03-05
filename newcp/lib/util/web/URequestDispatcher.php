<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of URequestDispatcher
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class URequestDispatcher {
    public static function Dispatch($path) {
        global $view, $loggedUser, $APP_CONTROLLERS;
        isset($APP_CONTROLLERS) || die('config/web.php not loaded.');
        $session = USession::GetInstance();
        $loggedUser = $session->loggedUser;
        // Create the view
        $view = &new stdClass;
        $view->scripts = array();
        $view->styles = array();
        $view->iestyles = array();
        $view->debug = defined('APP_DEBUG');
        $view->ajax = false;
        $view->contentType = 'text/html';
        $granted = false;
        // Select controller
        foreach ($APP_CONTROLLERS as $controller) {
            if ($controller['path'] == $path) {
                $view->ajax = isset($controller['type']) && $controller['type'] == 'ajax';
                if (isset($controller['content-type'])) {
                    $view->contentType = $controller['content-type'];
                    if ($view->contentType == 'text/x-json') {
                        require_once includePath('lib/json.php');
                    }
                }
                if (!isset($controller['perm']) || ($controller['perm'] & $loggedUser->permissions)) {
                    $granted = true;
                    ob_start();
                    try {
                        include(includePath(DISPATCHER_BASE_PATH . $controller['ctrl']));
                    }
                    catch (Exception $e) {
                        $view->exception = $e;
                    }
                    $view->content = ob_get_clean();
                }
                else {
                    $granted = false;
                }
                break;
            }
        }
        // Merge the theme
        if ($view->ajax) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
            header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
            header("Cache-Control: no-cache, must-revalidate" );
            header("Pragma: no-cache" );
            header("Content-type: $view->contentType");
            if ($granted) {
                print $view->content;
            }
        }
        else {
            if ($granted) {
                if ($view->overrideTheme) {
                    include(includePath($view->overrideTheme));
                }
                else {
                    include(includePath(DISPATCHER_MAIN_VIEW));
                }
            }
            else {
                URequest::Redirect('ui/view/theme/errors/error_403.php');
            }
        }
    }
}
?>
