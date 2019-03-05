<div id="__debug">
<h1>PHP Debug Window. (remove line "define('APP_DEBUG',1)" in config/web.php to hide this window)</h1>
<?php
    if (isset($view->exception)) {
        print '<pre>';
        print print_r($view->exception);
        print '</pre>';
    }
    print '<pre>';
    UDebug::Dump();
    print '</pre>';
?>
</div>