<?
$pag_ini = '../adm_mod/adm_mod_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_mod" );

/**********************************************************************************************/

function up_modulo( $orden_ant, $mod_id, $orden, $mod_id_ant ) {
    $query_1 = "update modulos set mod_orden = $orden_ant where mod_id = $mod_id";
    tep_db_query($query_1);
    $query_1 = "update modulos set mod_orden = $orden where mod_id = $mod_id_ant";
    tep_db_query($query_1);
    header( "Location: adm_mod_01.php" );
    tep_exit();
}

function delete_modulo( $mod_id ) {
    $query_1 = "delete from modulos where mod_id = $mod_id";
    tep_db_query($query_1);
    // borramos las referencias en la tabla permisosxmodulo
    $query_1 = "delete from permisosxmodulo where pemo_mod_id = $mod_id";
    tep_db_query($query_1);
    header( "Location: adm_mod_01.php" );
    tep_exit();
}

function listado_modulos() {
    global $ses_usr_id;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_2);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
    $MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);
    $MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    //$barra_her = kid_href( 'adm_mod_01.php', '', BOTON_LISTAR_IMG, TEXT_LISTAR, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= " " . kid_href( 'adm_mod_01.php', 'action=ins', BOTON_AGREGAR_IMG, TEXT_AGREGAR, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_mod/listado.html");

    // Recuperamos los modulos de la base
    $MiTemplate->set_block("main", "Modulos", "PBLModulos");

    //$query = "select mod_id, mod_nombre, mod_orden, mod_padre_id from modulos order by mod_orden";
    $query = "select m1.mod_id, m1.mod_nombre, m1.mod_orden, m1.mod_padre_id, ifnull(m2.mod_nombre,'') as padre from modulos m1 left join modulos as m2 on m2.mod_id = m1.mod_padre_id order by m1.mod_orden";
    $rq = tep_db_query($query);
    $res = mysql_fetch_assoc( $rq );
    $arr_k = array_keys ($res);

    $rq = tep_db_query($query);
    $primero = 1;
    while( $res = tep_db_fetch_array( $rq ) ) {
        for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
            $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
        }
        $msg_aux = '';
        if( $primero == 0 )
            $msg_aux = kid_href( 'adm_mod_01.php', 'action=up&mod_id='.$res['mod_id'].'&orden='.$res['mod_orden']."&mod_id_ant=$mod_id_2&orden_ant=$orden_2", BOTON_UP_IMG, '', '' ) . ' ';
        else
            $primero = 0;
        $mod_id_2 = $res['mod_id'];
        $orden_2 = $res['mod_orden'];

        if( se_puede( 'u', PERMISOS_MOD ) ) {
            $msg_aux .= kid_href( 'adm_mod_01.php', "action=upd&mod_id=".$res['mod_id'], BOTON_MODIFICAR_IMG, TEXT_TAG_MODIFICAR, '' ) . " ";
        }
        if( se_puede( 'd', PERMISOS_MOD ) ) {
            $msg_aux .= kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_MOD.'", "adm_mod_01.php?action=del&mod_id='.$res['mod_id'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_TAG_ELIMINAR, '' );
        }

        $MiTemplate->set_var('ACCIONES',$msg_aux);
        $MiTemplate->parse("PBLModulos", "Modulos", true);
    }

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function formulario_modulo( $mod_id, $action ) {

    global $ses_usr_id;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);
    $MiTemplate->set_var("CAMPO_NUMERICO",MOD_CAMPO_NUMERICO);

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
    $MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
    $MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);

    if( $action == "upd" ) {
        $MiTemplate->set_var("SUBTITULO1",TEXT_MOD_1);
        $MiTemplate->set_var("ACTION",'upd1');
        $MiTemplate->set_var("act_mod_id",$mod_id);
        $MiTemplate->set_var("BOTON",BOTON_MODIFICAR);
    }
    else {
        $MiTemplate->set_var("SUBTITULO1",TEXT_AGR_1);
        $MiTemplate->set_var("ACTION",'ins1');
        $MiTemplate->set_var("act_mod_id",'');
        $MiTemplate->set_var("BOTON",BOTON_AGREGAR);
    }

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'adm_mod_01.php', '', BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= " " . kid_href( 'adm_mod_01.php', 'action=ins', BOTON_AGREGAR_IMG, TEXT_AGREGAR, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    $mod_padre_id = 0;
    if( $action == 'upd' ) {
        // Recuperamos los datos del modulo
        $query = "select mod_padre_id, mod_nombre, mod_descripcion, mod_url, mod_orden, mod_estado from modulos where mod_id = $mod_id";
        $rq = tep_db_query($query);
        $res = mysql_fetch_assoc( $rq );
        $arr_k = array_keys ($res);

        $rq = tep_db_query($query);
        while( $res = tep_db_fetch_array( $rq ) ) {
            for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
            }
            if( $res['mod_estado'] == 2 ) {
                $MiTemplate->set_var('chec1','');
                $MiTemplate->set_var('chec2','checked');
            }
            else {
                $MiTemplate->set_var('chec1','checked');
                $MiTemplate->set_var('chec2','');
            }
            if( $res['mod_padre_id'] != '' )
                $mod_padre_id = $res['mod_padre_id'];

        }
    }
    else {
        $MiTemplate->set_var('chec1','checked');
        $MiTemplate->set_var('chec2','');
        $MiTemplate->set_var('mod_nombre','');
        $MiTemplate->set_var('mod_descripcion','');
        $MiTemplate->set_var('mod_url','');

        $query_1 = "select max( mod_orden ) as max from modulos";
        $rq_1 = tep_db_query($query_1);
        $res_1 = tep_db_fetch_array( $rq_1 );
        $MiTemplate->set_var('mod_orden',$res_1['max']+10);
    }

    // Agregamos el main
    $MiTemplate->set_file("main","adm_mod/form_mod.html");

    // Lista para los padres
    $MiTemplate->set_block("main", "Padres", "PBLPadres");
    $query = "select mod_id as padre_mod_id, mod_nombre as padre_mod_nombre, if( mod_id = $mod_padre_id, 'selected', '' ) as selected from modulos where mod_padre_id is NULL order by mod_nombre";
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLPadres', 'Padres' );

    // Recuperamos los modulos de la base
    $MiTemplate->set_block("main", "Modulos", "PBLPadres");

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function update_modulo( $mod_id ) {
    global $ses_usr_id;
    global $c_1, $c_2, $c_3, $c_4, $c_5, $c_6;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $c_6 == '---' )
        $c_6 = 'NULL';

    $query_1 = "update modulos set mod_estado = $c_3, mod_nombre = '$c_1', mod_descripcion = '$c_2', mod_url = '$c_4', mod_orden = $c_5, mod_padre_id = $c_6, mod_usr_mod = '$usr_nombre', mod_fec_mod = now() where mod_id = $mod_id";
    tep_db_query($query_1);

    header( "Location: adm_mod_01.php" );
    tep_exit();
}

function insert_modulo() {
    global $ses_usr_id;
    global $c_1, $c_2, $c_3, $c_4, $c_5, $c_6;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $c_2 == '' ) $c_2 = 'NULL'; else $c_2 = "'$c_2'";
    if( $c_4 == '' ) $c_4 = 'NULL'; else $c_4 = "'$c_4'";
    if( $c_6 == '---' ) $c_6 = 'NULL';

    $query_1 = "insert into modulos( mod_estado, mod_nombre, mod_descripcion, mod_url, mod_orden, mod_padre_id, mod_usr_crea, mod_fec_crea ) values ( $c_3, '$c_1', $c_2, $c_4, $c_5, $c_6, '$usr_nombre', now() )";
    tep_db_query($query_1);

    header( "Location: adm_mod_01.php" );
    tep_exit();

}

/**********************************************************************************************/

if( $action == 'del' )
    delete_modulo( $mod_id );
else if( $action == 'up' )
    up_modulo( $orden_ant, $mod_id, $orden, $mod_id_ant );
else if( $action == 'upd' )
    formulario_modulo( $mod_id, $action );
else if( $action == 'upd1' )
    update_modulo( $mod_id );
else if( $action == 'ins' )
    formulario_modulo( $mod_id, $action );
else if( $action == 'ins1' )
    insert_modulo();
else
    listado_modulos();

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>