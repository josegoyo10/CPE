<?
header('HTTP/1.1 403 Forbidden');
session_start();
?>
<!--
Copyright 2008 Cencosud S.A.
Original Author Frank D. Martinez M.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Easy :: CP :: <?= $view->pageTitle ?></title>
        <link rel="stylesheet" href="../style.css" type="text/css"/>
    </head>
    <body id="mainBody">
        <div id="appOuterBody">
            <div id="appHeader">
                <div class="easy-logo">
                    <div class="cp-logo">
                        <div id="appMenuBar">
                        </div>
                    </div>
                </div>
            </div>
            <div id="appInnerBody">
                <table align="center" cellpadding="5">
                    <tr>
                        <td>
                            <img src="../images/secshield.jpg"/>
                        </td>
                        <td>
                            No tiene permisos para usar este m&oacute;dulo.
                            <br />
                            Fecha: <? print date("Y-m-d"); ?>
                            <br />
                            Hora: <? print date("H:i:s A"); ?>
                            <br />
                            Usuario: <?= $_SESSION['ses_usr_id'] ?>
                            <br />
                            IP: <?= $REMOTE_ADDR ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
