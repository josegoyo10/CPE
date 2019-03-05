<?
$pag_ini = '../adm_usr/adm_usr_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_usr" );

/**********************************************************************************************/

function form_1( $id_perfil ) {

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("ID_PERFIL",$id_perfil);
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);
    $MiTemplate->set_var("TEXT_ADMIN_TITULO",TEXT_ADMIN_TITULO);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_PERFIL_1",TEXT_AGREGAR_CAMPO_PERFIL_1);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_PERFIL_2",TEXT_AGREGAR_CAMPO_PERFIL_2);
    $MiTemplate->set_var("BOTON_MODIFICAR_PERFIL",BOTON_MODIFICAR_PERFIL);
    $MiTemplate->set_var("TEXT_ADMIN_TITULO_ELIMINAR",TEXT_ADMIN_TITULO_ELIMINAR);
    $MiTemplate->set_var("TEXT_ADMIN_TITULO_ASOCIADOS",TEXT_ADMIN_TITULO_ASOCIADOS);
    $MiTemplate->set_var("TEXT_ADMIN_TITULO_NOASOCIADOS",TEXT_ADMIN_TITULO_NOASOCIADOS);
    $MiTemplate->set_var("TEXT_ADMIN_AGREGAR_IMG",TEXT_ADMIN_AGREGAR_IMG);
    $MiTemplate->set_var("TEXT_ADMIN_ELIMINAR_IMG",TEXT_ADMIN_ELIMINAR_IMG);
    $MiTemplate->set_var("TEXT_ADMIN_MODIFICAR_IMG",TEXT_ADMIN_MODIFICAR_IMG);

    $query = "select per_nombre, per_descripcion from perfiles where per_id = $id_perfil";
    query_to_set_var( $query, &$MiTemplate, 0, '', '' );

    // Revisamos si se puede eliminar el perfil
    $query_1 = "select count(*) as cont from perfiles where per_padre = $id_perfil";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    $items = $res_1['cont'];
    $query_1 = "select count(*) as cont from perfilesxusuario where peus_per_id = $id_perfil";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    $items += $res_1['cont'];

    if( $items > 0 ) {
        $msg = TEXT_NOELIMINAR_PERFIL;
    }
    else {
        // recuperamos el perfil anterior al que vamos a eliminar
        $query_1 = "select per_padre from perfiles where per_id = $id_perfil";
        $rq_1 = tep_db_query($query_1);
        $res_1 = tep_db_fetch_array( $rq_1 );
        $msg = kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_USR.'", "adm_usr_05.php?action=delper&id_perfil='.$id_perfil.'&per_o='.$res_1['per_padre'].'" ); //', '', TEXT_ELIMINAR_PERFIL, TEXT_ELIMINAR_PERFIL, '' );
    }
    $MiTemplate->set_var("TEXTO_ELIMINACION",$msg);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_05_1.html");

    $MiTemplate->set_block("main", "Modulos", "PBLModulos");
    $query = "select pemo_mod_id, mod_nombre, if( mod_padre_id is null, '', '&nbsp;&nbsp;&nbsp;&nbsp;' ) as espacios from permisosxmodulo, modulos where pemo_tipo = 1 and pemo_mod_id = mod_id and pemo_per_id = $id_perfil order by mod_orden";
    $rq = tep_db_query($query);
    if( tep_db_num_rows($rq) > 0 ) {
        $res = mysql_fetch_assoc( $rq );
        $arr_k = array_keys ($res);

        $i = 0;
        $not_in = "";
        $tag = "";
        $rq = tep_db_query($query);
        while( $res = tep_db_fetch_array( $rq ) ) {
            for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
            }
            $MiTemplate->parse("PBLModulos", "Modulos", true);
            $not_in .= $tag . $res['pemo_mod_id'];
            if( $tag == "" )
                $tag = ", ";
            $i++;
        }
    }
    $not_in .= $tag . "-1";
    
    $query = "select mod_id as pemo_mod_id, mod_nombre, if( mod_padre_id is null, '', '&nbsp;&nbsp;&nbsp;&nbsp;' ) as espacios from modulos where mod_id not in ( $not_in ) order by mod_orden";
    $MiTemplate->set_block("main", "Modulos_na", "PBLModulos_na");
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLModulos_na', 'Modulos_na' );
  
    //echo '<pre>';    print_r($arr_pemo);    echo '</pre>';
    //echo '<pre>';    print_r($arr_pemo_new);    echo '</pre>';
    //query_to_set_var( $query, &$MiTemplate, 1, 'PBLModulos', 'Modulos' );

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");
}

function update_perfil( $nombre_in, $descripcion_in, $id_perfil ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query_1 = "update perfiles set per_nombre = '$nombre_in', per_descripcion = '$descripcion_in', per_usr_mod = '$usr_nombre', per_fec_mod = now() where per_id = $id_perfil";
    tep_db_query($query_1);

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("ID_PADRE",$id_perfil);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_2.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function delete_perfil( $id_perfil, $per_o ) {
    $query_1 = "delete from perfiles where per_id = $id_perfil";
    tep_db_query($query_1);

    $query_1 = "delete from permisosxmodulo where pemo_tipo = 1 & pemo_per_id = $id_perfil";
    tep_db_query($query_1);

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("ID_PADRE",$per_o);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_2.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function desasociar_modulo( $id_perfil, $pemo_mod_id ) {
    $query_1 = "delete from permisosxmodulo where pemo_tipo = 1 and pemo_per_id = $id_perfil and pemo_mod_id = $pemo_mod_id";
    tep_db_query($query_1);
    header( "Location: adm_usr_05.php?id_perfil=$id_perfil" );
    tep_exit();
}

function form_asociar_modulo( $id_perfil, $pemo_mod_id, $action ) {

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("ID_PERFIL",$id_perfil);
    $MiTemplate->set_var("PEMO_MOD_ID",$pemo_mod_id);
    $MiTemplate->set_var("TEXT_ADMIN_TITULO",TEXT_ADMIN_TITULO);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_1",TEXT_ASOCIAR_CAMPO_1);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_2",TEXT_ASOCIAR_CAMPO_2);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_3",TEXT_ASOCIAR_CAMPO_3);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_4",TEXT_ASOCIAR_CAMPO_4);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_5",TEXT_ASOCIAR_CAMPO_5);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_6",TEXT_ASOCIAR_CAMPO_6);

    if( $action == "f_asociar" ) {
        $MiTemplate->set_var("BOTON",BOTON_ASOCIAR_MOD);
        $MiTemplate->set_var("ACTION",'asociar_mod');
    }
    else {
        $MiTemplate->set_var("BOTON",BOTON_MODIFICAR_MOD);
        $MiTemplate->set_var("ACTION",'mod_asociar_mod');

        $query = "select pemo_insert, pemo_update, pemo_delete, pemo_select from permisosxmodulo where pemo_tipo = 1 and pemo_mod_id = $pemo_mod_id and pemo_per_id = $id_perfil";
        $rq = tep_db_query($query);
        $res = tep_db_fetch_array( $rq );
        if( $res['pemo_insert'] == 1 ) $MiTemplate->set_var("ch1",'checked');
        if( $res['pemo_delete'] == 1 ) $MiTemplate->set_var("ch2",'checked');
        if( $res['pemo_update'] == 1 ) $MiTemplate->set_var("ch3",'checked');
    }

    $query = "select per_nombre from perfiles where per_id = $id_perfil";
    query_to_set_var( $query, &$MiTemplate, 0, '', '' );

    $query = "select mod_nombre from modulos where mod_id = $pemo_mod_id";
    query_to_set_var( $query, &$MiTemplate, 0, '', '' );

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_05_2.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");
}

function asociar_modulo( $id_perfil, $pemo_mod_id ) {
    global $ses_usr_id;
    global $insert_in, $delete_in, $update_in, $select_in;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $insert_in == '' )
        $insert_in = 0;
    if( $delete_in == '' )
        $delete_in = 0;
    if( $update_in == '' )
        $update_in = 0;
    if( $select_in == '' )
        $select_in = 0;

    $query_1 = "insert into permisosxmodulo( pemo_per_id, pemo_mod_id, pemo_tipo, pemo_insert, pemo_delete, pemo_update, pemo_select, pemo_usr_crea, pemo_fec_crea ) values ( $id_perfil, $pemo_mod_id, 1, $insert_in, $delete_in, $update_in, $select_in, '$usr_nombre', now() )";
    tep_db_query($query_1);
    header( "Location: adm_usr_05.php?id_perfil=$id_perfil" );
    tep_exit();
}

function mod_asociar_modulo( $id_perfil, $pemo_mod_id ) {
    global $ses_usr_id;
    global $insert_in, $delete_in, $update_in, $select_in;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $insert_in == '' )
        $insert_in = 0;
    if( $delete_in == '' )
        $delete_in = 0;
    if( $update_in == '' )
        $update_in = 0;
    if( $select_in == '' )
        $select_in = 0;

    $query_1 = "update permisosxmodulo set pemo_insert = $insert_in, pemo_update = $update_in, pemo_delete = $delete_in, pemo_select = $select_in, pemo_usr_mod = '$usr_nombre', pemo_fec_mod = now() where pemo_per_id = $id_perfil and pemo_mod_id = $pemo_mod_id and pemo_tipo = 1";
    tep_db_query($query_1);
    header( "Location: adm_usr_05.php?id_perfil=$id_perfil" );
    tep_exit();
}

/**********************************************************************************************/

if( $action == 'updper' )
    update_perfil( $nombre_in, $descripcion_in, $id_perfil );
else if( $action == 'delper' )
    delete_perfil( $id_perfil, $per_o );
else if( $action == 'f_asociar' )
    form_asociar_modulo( $id_perfil, $pemo_mod_id, $action );
else if( $action == 'f_mod_asociar' )
    form_asociar_modulo( $id_perfil, $pemo_mod_id, $action );
else if( $action == 'asociar_mod' )
    asociar_modulo( $id_perfil, $pemo_mod_id );
else if( $action == 'mod_asociar_mod' )
    mod_asociar_modulo( $id_perfil, $pemo_mod_id );
else if( $action == 'desasociar' )
    desasociar_modulo( $id_perfil, $pemo_mod_id );
else
    form_1( $id_perfil );

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>