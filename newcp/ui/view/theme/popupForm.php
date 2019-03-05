<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Easy :: CP :: <?= $view->pageTitle ?></title>
        <link rel="stylesheet" href="ui/view/theme/style.css" type="text/css" media="all"/>
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
        <div style="background: #ffffff; padding: 5px;">
            <? print $view->content ?>
        </div>
    </body>
</html>
