<?
$pag_ini = '../adm_usr/adm_usr_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_usr" );

?>
<html>
<head>
<title></title>

<script type="text/javascript" src="mtmcode.js">
</script>

<script type="text/javascript">
// Morten's JavaScript Tree Menu
// version 2.3.0, dated 2001-04-30
// http://www.treemenu.com/

// Copyright (c) 2001, Morten Wang & contributors
// All rights reserved.

// This software is released under the BSD License which should accompany
// it in the file "COPYING".  If you do not have this file you can access
// the license through the WWW at http://www.treemenu.com/license.txt

/******************************************************************************
* User-configurable options.                                                  *
******************************************************************************/

// Menu table width, either a pixel-value (number) or a percentage value.
var MTMTableWidth = "100%";

// Name of the frame where the menu is to appear.
var MTMenuFrame = "menuusr";

// variable for determining whether a sub-menu always gets a plus-sign
// regardless of whether it holds another sub-menu or not
var MTMSubsGetPlus = "Always";

// variable that defines whether the menu emulates the behaviour of
// Windows Explorer
var MTMEmulateWE = false;

// Directory of menu images/icons
var MTMenuImageDirectory = "img/menu-images/";

// Variables for controlling colors in the menu document.
// Regular BODY atttributes as in HTML documents.
var MTMBGColor = "<?=COLOR_IFRAME_FONDO?>";
var MTMBackground = "";
var MTMTextColor = "#000000";

// color for all menu items
var MTMLinkColor = "#000000";

// Hover color, when the mouse is over a menu link
var MTMAhoverColor = "#FF0000";

// Foreground color for the tracking & clicked submenu item
var MTMTrackColor ="#000000";
var MTMSubExpandColor = "#FF0000";
var MTMSubClosedColor = "#FF0000";

// All options regarding the root text and it's icon
var MTMRootIcon = "menu_new_root.gif";
var MTMenuText = "<?=TEXT_TITULO_ARBOL?>";
var MTMRootColor = "#000000";
var MTMRootFont = "Arial, Helvetica, sans-serif";
var MTMRootCSSize = "84%";
var MTMRootFontSize = "-1";

// Font for menu items.
var MTMenuFont = "Arial, Helvetica, sans-serif";
var MTMenuCSSize = "84%";
var MTMenuFontSize = "-1";

// Variables for style sheet usage
// 'true' means use a linked style sheet.
var MTMLinkedSS = false;
var MTMSSHREF = "style/menu.css";

// Additional style sheet properties if you're not using a linked style sheet.
// See the documentation for details on IDs, classes & elements used in the menu.
// Empty string if not used.
var MTMExtraCSS = "";

// Header & footer, these are plain HTML.
// Leave them to be "" if you're not using them

var MTMHeader = "";
var MTMFooter = "";

// Whether you want an open sub-menu to close automagically
// when another sub-menu is opened.  'true' means auto-close
var MTMSubsAutoClose = false;

// This variable controls how long it will take for the menu
// to appear if the tracking code in the content frame has
// failed to display the menu. Number if in tenths of a second
// (1/10) so 10 means "wait 1 second".
var MTMTimeOut = 1;

// Cookie usage.  First is use cookie (yes/no, true/false).
// Second is cookie name to use.
// Third is how many days we want the cookie to be stored.

var MTMUseCookies = false;
var MTMCookieName = "MTMCookie";
var MTMCookieDays = 3;

// Tool tips.  A true/false-value defining whether the support
// for tool tips should exist or not.
var MTMUseToolTips = true;

/******************************************************************************
* User-configurable list of icons.                                            *
******************************************************************************/

var MTMIconList = null;
MTMIconList = new IconList();
MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre"));
MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post"));
MTMIconList.addIcon(new MTMIcon("usuario_txt.gif", "usr", "pre"));

/******************************************************************************
* User-configurable menu.                                                     *
******************************************************************************/

// Main menu.
var menu = null;

menu = new MTMenu();

<?

// recuperamos los perfiles y los dejamos en un arreglo para no consultar la base de datos siempre
$arr_perfiles = array();
$query_1 = "select per_nombre, per_id, per_descripcion, per_padre from perfiles order by per_nombre";
$db_1 = tep_db_query($query_1);
$i = 0;
while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
    $arr_perfiles[$i][0] = $res_1['per_id'];
    $arr_perfiles[$i][1] = $res_1['per_nombre'];
    $arr_perfiles[$i][2] = $res_1['per_descripcion'];
    $arr_perfiles[$i][3] = $res_1['per_padre'];
    $i++;
}

// se almacenan en una arreglo las área que se deben dejar abiertas cuando se opera con un área especifica
$arr_areas_abiertas = array();
$cont_a_a = 0;
$arr_areas_abiertas[$cont_a_a++] = $per_o;
function area_padre( $are_id ) {
    global $arr_perfiles, $arr_areas_abiertas, $cont_a_a;

    for( $i = 0; $i < sizeof( $arr_perfiles ); $i++ ) {
        if( $arr_perfiles[$i][0] == $are_id ) {
            if( $arr_perfiles[$i][3] != '' ) {
                area_padre( $arr_perfiles[$i][3] );
            }
            $arr_areas_abiertas[$cont_a_a++] = $arr_perfiles[$i][0];
            break;
        }
    }
}

area_padre( $per_o );

// recupera los usuarios en cada perfil
function usuarios( $per_id, $id_menu, $cont_in ) {

    $cont = 0;

    $query_1 = "select usr_id, usr_nombres, usr_apellidos from usuarios, perfiles, perfilesxusuario where per_id = peus_per_id and peus_usr_id = usr_id and per_id = $per_id and usr_estado <> 2 order by usr_nombres";
    $db_1 = tep_db_query($query_1);
    ?>
    <?=$id_menu?>.MTMAddItem(new MTMenuItem("<?=TEXT_ADMIN_PERUSR?>","adm_usr_05.php?sec=admin&id_perfil=<?=$per_id?>",'acciones','<?=TEXT_ADMIN_PERUSR_TAG?>','agregar_usr.gif'));
    <?=$id_menu?>.MTMAddItem(new MTMenuItem("<?=TEXT_AGREGAR_PERUSR?>","adm_usr_03.php?sec=agregar&id_padre=<?=$per_id?>",'acciones','<?=TEXT_AGREGAR_PERUSR_TAG?>','agregar_usr.gif'));
    <?
    while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
        ?>
        <?=$id_menu?>.MTMAddItem(new MTMenuItem("<?=ucwords(strtolower($res_1['usr_nombres']))?> <?=ucwords(strtolower($res_1['usr_apellidos']))?></a> <a href='adm_usr_08.php?usr_id=<?=$res_1['usr_id']?>&per_o=<?=$per_id?>' title='<?=TEXT_ELIMINAR_TAG_USR?>' target='acciones'><?=TEXT_ELIMINAR_A_USR?></a>","adm_usr_08.php?usr_id=<?=$res_1['usr_id']?>&per_o=<?=$per_id?>",'acciones','<?=TEXT_ELIMINAR_TAG_USR?>','usuario_txt.gif'));
        <?
        $cont++;
    }
    return $cont;
}

// recupera los perfiles hijos en forma recursiva
function hijos( $id_padre, $id_menu ) {
    global $arr_perfiles;

    $cont_especial = 0;
    for( $j = 0; $j < sizeof( $arr_perfiles ); $j++ ) {
        if( $arr_perfiles[$j][3] == $id_padre ) {
            ?>
            <?=$id_menu?>.MTMAddItem(new MTMenuItem("<?=$arr_perfiles[$j][1]?>"));
            <?
            papa( $arr_perfiles[$j][0], $id_menu, $cont_especial++ );
        }
    }
    return;
}

// recupera el segundo nivel del árbol
function papa( $id_padre, $id_menu, $cont_in ) {
    global $arr_perfiles, $arr_areas_abiertas;

    $cont = $cont_in;

    for( $i = 0; $i < sizeof( $arr_perfiles ); $i++ ) {
        if( $arr_perfiles[$i][0] == $id_padre ) {
            ?>
            var nivel_<?=$arr_perfiles[$i][0]?> = null;
            nivel_<?=$arr_perfiles[$i][0]?> = new MTMenu();
            <?
            hijos( $id_padre, "nivel_".$arr_perfiles[$i][0] );
            $cont_1 = usuarios( $id_padre, "nivel_".$arr_perfiles[$i][0], $cont );

            $abierto = 0;
            if( in_array( $arr_perfiles[$i][0], $arr_areas_abiertas) )
                $abierto = 1;
            ?>
            <?=$id_menu?>.items[<?=$cont?>].MTMakeSubmenu( nivel_<?=$arr_perfiles[$i][0]?>, <?=$abierto?> );
            <?
        }
    }
}

// recupera el primer nivel del árbol
$lugar_arreglo = 0;
for( $i = 0; $i < sizeof( $arr_perfiles ); $i++ ) {
    if( $arr_perfiles[$i][3] == '' ) {
        ?>
        menu.MTMAddItem(new MTMenuItem("<?=$arr_perfiles[$i][1]?>"));
        <?
        papa( $arr_perfiles[$i][0], "menu", $lugar_arreglo++ );
    }
}
?>
menu.MTMAddItem(new MTMenuItem("<?=TEXT_AGREGAR_PERUSR?>","adm_usr_03.php?sec=agregar&id_padre=<?=$per_id?>",'acciones','<?=TEXT_AGREGAR_PERUSR_TAG?>','agregar_usr.gif'));

</script>
</head>
<body onload="MTMStartMenu()" bgcolor="#FFFFFF" text="#000000" link="yellow" vlink="lime" alink="red">
</body>
</html>


