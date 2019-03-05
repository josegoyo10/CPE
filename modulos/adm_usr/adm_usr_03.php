<?
$pag_ini = '../adm_usr/adm_usr_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_usr" );

/**********************************************************************************************/

function form_per_usr_1( $id_padre ) {

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("TEXT_AGREGAR_TITULO",TEXT_AGREGAR_TITULO);
    $MiTemplate->set_var("TEXT_AGREGAR_TEXT_1",TEXT_AGREGAR_TEXT_1);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_PERFIL",TEXT_AGREGAR_CAMPO_PERFIL);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_USUARIO",TEXT_AGREGAR_CAMPO_USUARIO);
    $MiTemplate->set_var("BOTON_CONTINUAR",BOTON_CONTINUAR);
    $MiTemplate->set_var("ID_PADRE",$id_padre);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function form_per_usr_2( $id_padre ) {

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("TEXT_AGREGAR_TITULO_PERUSR",TEXT_AGREGAR_TITULO_PERUSR);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_PERFIL",TEXT_AGREGAR_CAMPO_PERFIL);
    $MiTemplate->set_var("BOTON_AGREGAR_PERFIL",BOTON_AGREGAR_PERFIL);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_PERFIL_1",TEXT_AGREGAR_CAMPO_PERFIL_1);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_PERFIL_2",TEXT_AGREGAR_CAMPO_PERFIL_2);
    $MiTemplate->set_var("ID_PADRE",$id_padre);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_1.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function insert_perfil( $nombre_in, $descripcion_in, $id_padre_in ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $id_padre_in == '' )
        $id_padre = "NULL";
    else
        $id_padre = $id_padre_in;

    $query_1 = "insert into perfiles( per_nombre, per_descripcion, per_padre, per_usr_crea, per_fec_crea ) values ( '$nombre_in', '$descripcion_in', $id_padre, '$usr_nombre', now() )";
    tep_db_query($query_1);


    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("ID_PADRE",$id_padre);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_2.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function form_per_usr_3( $id_padre ) {

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);
    $MiTemplate->set_var("TEXT_AGREGAR_TITULO_PERUSR",TEXT_AGREGAR_TITULO_PERUSR);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_USUARIO",TEXT_AGREGAR_CAMPO_USUARIO);
    $MiTemplate->set_var("TEXT_BUSCAR_CAMPO_1",TEXT_BUSCAR_CAMPO_1);
    $MiTemplate->set_var("BOTON_BUSCAR_USUARIO",BOTON_BUSCAR_USUARIO);
    $MiTemplate->set_var("ID_PADRE",$id_padre);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_3.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function search_usuarios( $id_padre, $c_1 ) {
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);
    $MiTemplate->set_var("TEXT_AGREGAR_TITULO_PERUSR",TEXT_AGREGAR_TITULO_PERUSR);
    $MiTemplate->set_var("TEXT_AGREGAR_CAMPO_USUARIO",TEXT_AGREGAR_CAMPO_USUARIO);
    $MiTemplate->set_var("TEXT_BUSCAR_CAMPO_1",TEXT_BUSCAR_CAMPO_1);
    $MiTemplate->set_var("BOTON_BUSCAR_USUARIO",BOTON_BUSCAR_USUARIO);
    $MiTemplate->set_var("ID_PADRE",$id_padre);
    $MiTemplate->set_var("c_1",$c_1);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_3.html");

    if( $id_padre == "" )
        $id_padre = "NULL";
    $not_in = "";
    $tag = "";
    $query_1 = "select peus_usr_id from perfilesxusuario where peus_per_id = $id_padre";
    $rq_1 = tep_db_query($query_1);
    while( $res_1 = tep_db_fetch_array( $rq_1 ) ) {
        $not_in .= $tag . $res_1['peus_usr_id'];
        if( $tag == "" )
            $tag = ",";
    }
    if( $not_in != "" ) {
        $and_not_in = " and usr_id not in ( $not_in )";
    }
    $query = "select usr_id, usr_login, usr_apellidos, usr_nombres from usuarios where usr_login like '%$c_1%' $and_not_in and usr_estado = 1 order by usr_login";
    $MiTemplate->set_block("main", "Usuarios", "PBLUsuarios");
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLUsuarios', 'Usuarios' );

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");
}

function asocia_usuario( $id_padre, $usr_id ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $id_padre == "" )
        $id_padre = "NULL";

    $query_1 = "insert into perfilesxusuario( peus_usr_id, peus_per_id, peus_usr_crea, peus_fec_crea ) values ( $usr_id, $id_padre, '$usr_nombre', now() )";
    tep_db_query($query_1);

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("ID_PADRE",$id_padre);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_2.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

/**********************************************************************************************/

if( $action == 'addperusr' && $tipo == 1 )
    form_per_usr_2( $id_padre );
else if( $action == 'addperusr' && $tipo == 2 )
    form_per_usr_3( $id_padre );
else if( $action == 'addperfil' )
    insert_perfil( $nombre_in, $descripcion_in, $id_padre );
else if( $action == 'seausr' )
    search_usuarios( $id_padre, $c_1 );
else if( $action == 'addusr' )
    asocia_usuario( $id_padre, $usr_id );
else
    form_per_usr_1( $id_padre );

/**********************************************************************************************/


include "../../includes/application_bottom.php";
?>