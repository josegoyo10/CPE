<?
$query_1 = "select usr_nombres, usr_apellidos, usr_id from usuarios where usr_est_login = 1 and usr_id <> $ses_usr_id";
$db_1 = tep_db_query($query_1);
if( tep_db_num_rows( $db_1 ) > 0 ) {
    ?>
    <table width="160" border="0" cellpadding="0" cellspacing="0" background="../img/bot_fondo02.gif">
    <tr> 
      <td><img src="../img/1x1trans.gif" width="1" height="5"></td>
    </tr>
    </table>
    <table width="160" height="20" border="0" cellpadding="0" cellspacing="0" background="../img/bot_fondo01.gif">
    <tr>
      <td width="7">&nbsp;</td>
      <td class="link01"><?=TIT_BARRA_USUARIOS?></td>
    </tr>
    </table>

    <table width="160" height="20" border="0" cellpadding="0" cellspacing="0" background="../img/bot_fondo02.gif">
    <tr bgcolor="#666666"> 
      <td colspan="2"><img src="../img/1x1trans.gif" width="1" height="1"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="2"><img src="../img/1x1trans.gif" width="1" height="1"></td>
    </tr>
    <?
                $query_1 = "select usr_nombres, usr_apellidos, usr_id from usuarios where usr_est_login = 1";
                $db_1 = tep_db_query($query_1);
                while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
                    if( $ses_usr_id != $res_1['usr_id'] ) {
                    ?>
                    <tr> 
                      <td width="20" align="center"><img src="../img/bot_flecha01.gif" width="16" height="16"></td>
                      <td>
                        <?=kid_href( '../fic_per/fic_per_01.php', 'usr_f='.$res_1['usr_id'], ucwords(strtolower($res_1['usr_nombres'] . " " . $res_1['usr_apellidos'])), TEXT_VER_FICHA_TAG, 'link02' );?>
                        <?=kid_href( '../msg_pri/msg_pri_01.php', 'usr_f='.$res_1['usr_id'], TEXT_MSG_PRI, TEXT_MSG_PRI_TAG, 'link02' );?>
                      </td>
                    </tr>
                    <tr bgcolor="#666666"> 
                      <td colspan="2"><img src="../img/1x1trans.gif" width="1" height="1"></td>
                    </tr>
                    <tr bgcolor="#FFFFFF"> 
                      <td colspan="2"><img src="../img/1x1trans.gif" width="1" height="1"></td>
                    </tr>
                    <?
                    }
                }
                ?>
</table>
<table width="160" border="0" cellpadding="0" cellspacing="0" background="../img/bot_fondo02.gif">
<tr> 
  <td><img src="../img/1x1trans.gif" width="1" height="5"></td>
</tr>
</table>
<?
}
?>