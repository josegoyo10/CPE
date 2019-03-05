<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Easy :: CP :: <?= $view->pageTitle ?></title>
        <link rel="stylesheet" href="ui/view/theme/style.css" type="text/css"/>
        <!-- Additional styles -->
        <?foreach ($view->styles as $style) {
            print "<link type='text/css' rel='stylesheet' href='$style' />\n";
        }?>
        <!-- IE 6 hacks -->
        <!--[if lt IE 7]>
        <?foreach ($view->iestyles as $style) {
            print "<link type='text/css' rel='stylesheet' href='$style' media='screen' />\n";
        }?>
        <![endif]-->
        <!-- Scripts -->
        <?foreach ($view->scripts as $script) {
            print "<script type='text/javascript' src='$script'></script>\n";
        }?>
    </head>
    <body id="mainBody">
        <div id="appOuterBody">
            <div id="appHeader">
                <div class="easy-logo">
                    <div class="cp-logo">
                        <div id="appMenuBar"></div>
                    </div>
                </div>
            </div>
            <div id="appInnerBody">
                <? print $view->content ?>
            </div>
            <div id="appFooter">
                Usuario: <span class="output1"><?= $loggedUser->login . ' (' . $loggedUser->id . ')' ?></span>
                | Local: <span class="output1"><?= $loggedUser->storeName ?></span>
                | Fecha: <span class="output1"><?= ULang::FormatDate() ?></span>
            </div>
        </div>
        <!-- DEBUG -->
        <?if ($view->debug) { include(dirname(__FILE__) . '/debug.php'); }?>
    </body>
</html>
