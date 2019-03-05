<?php
$pag_ini = '../consulta_precios/index.php';
include "../../includes/aplication_top.php";
include_idioma_mod( $ses_idioma, "sp" );

/**********************************************************************************************/


/****************************************************************
 *
 * Despliega Listado Búsqueda
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $radiobutton1, $radiobutton2;
    global $bus_1, $bus_2;
    global $select_pag;
    global $orden, $filtro;

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

    $MiTemplate->set_var("bus_1",$bus_1);
    $MiTemplate->set_var("bus_2",$bus_2);
    $MiTemplate->set_var("radiobutton1",$radiobutton1);
    $MiTemplate->set_var("radiobutton2",$radiobutton2);

    $MiTemplate->set_var("select_pag",$select_pag);

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    //
    if ($radiobutton1 == 1)
        $MiTemplate->set_var("chec_rad1_1","checked");
    else if ($radiobutton1 == 2)
        $MiTemplate->set_var("chec_rad1_2","checked");
    else
        $MiTemplate->set_var("chec_rad1_3","checked");

    //
    if ($radiobutton2 == 2)
        $MiTemplate->set_var("chec_rad2_2","checked");
    else
        $MiTemplate->set_var("chec_rad2_1","checked");

    //Orden
    if ( $filtro == "" || $filtro == "cod" )
        $order_aux = "ORDER BY cod_prod1 $orden";
    else if( $filtro == "des" )
        $order_aux = "ORDER BY p.des_corta $orden";

    if ( $orden == "" || $orden == "asc"){
        $MiTemplate->set_var("orden",'desc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_DOWN);
    }
    else if ( $orden == "desc" ){
        $MiTemplate->set_var("orden",'asc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_UP);
    }

    //Filtros
    $where_aux ='';
    if ( $bus_1){
        //Primera opción de búsqueda
        if ($radiobutton1 == 1){
            $where_aux="WHERE p.cod_prod1 ='".$bus_1."'";
        }
        else if ($radiobutton1 == 2){
            $where_aux="JOIN codbarra cb ON cb.id_producto = p.id_producto AND cb.cod_barra like'%".$bus_1."%'";
        }
        else if ($radiobutton1 == 3){
            $where_aux="WHERE p.des_larga like ('%".$bus_1."%')";
        }

    }
    if ( $bus_2 ){
        //Segunda opción de búsqueda
        if ($radiobutton2 == 1){
            $where_aux="JOIN prodxprov pxv ON pxv.id_producto = p.id_producto
                        JOIN proveedores pv ON pv.id_proveedor = pxv.id_proveedor
                        AND pv.nom_prov like ('%".$bus_2."%')";
        }
        else if ($radiobutton2 == 2){
            $where_aux="JOIN prodxprov pxv ON pxv.id_producto = p.id_producto
                        JOIN proveedores pv ON pv.id_proveedor = pxv.id_proveedor
                        AND pv.rut_prov =".($bus_2+0);
        }
    }

    if ( $bus_1 || $bus_2 ){

        // Agregamos el main
        $MiTemplate->set_file("main","consulta_precios/listado.html");

        $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite = 20;

        $query_count="SELECT count(*) as cuenta
                FROM productos p
                $where_aux";
        $rq_count = tep_db_query($query_count);
        $res_count = tep_db_fetch_array($rq_count);
        $total = ceil($res_count['cuenta'] / $limite);
        //echo $query_count;
        if ($select_pag == ""){
            $select_pag = 1;
        }
        $desde = ( $select_pag -1 ) * $limite;

        if ($total>0){
            for ($i=1;$i<=$total;$i++){
                if ($select_pag == $i)
                    $MiTemplate->set_var('selected','selected');
                else
                    $MiTemplate->set_var('selected','');
                $MiTemplate->set_var('pag',$i);
                $MiTemplate->parse("PBLPaginas", "Paginas", true);
            }
        }

        $MiTemplate->set_block("main", "listado", "PBLlistado");
        $query="SELECT p.id_producto
                FROM productos p
                $where_aux
                LIMIT ".($desde+0).",".($limite+0);
        $db = tep_db_query($query);
        $lista = "0,";
        while( $res = tep_db_fetch_array( $db ) ) {
            $lista .= $res['id_producto'] . ',';
        }
        $lista = substr( $lista, 0, strlen($lista)-1 );


        $query="SELECT p.id_producto, p.cod_prod1, p.des_corta, p.des_larga, p.prod_tipo, p.prod_subtipo
                FROM productos p
                WHERE p.id_producto IN ( $lista )
                $order_aux";

        if ( $rq = tep_db_query($query) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
                $MiTemplate->set_var('id_producto',tohtml( $res['id_producto']));
                $MiTemplate->set_var('cod_prod1',tohtml( $res['cod_prod1']));
                $MiTemplate->set_var('des_corta',tohtml( $res['des_corta']));
                $MiTemplate->set_var('des_larga',tohtml( $res['des_larga']));
                $MiTemplate->set_var('prod_tipo',tohtml( $res['prod_tipo']));
                $MiTemplate->set_var('prod_subtipo',tohtml( $res['prod_subtipo']));
                $MiTemplate->set_var('link_info', "<a href='index.php?req=frmed&id_prod=".(tohtml( $res['cod_prod1'])+0)."'>Ver</a>");
                $MiTemplate->set_var('espacio','&nbsp;');


                $MiTemplate->parse("PBLlistado", "listado", true);
            }
        }
    }
    else{
        // Agregamos el main
        $MiTemplate->set_file("main","consulta_precios/listado.html");

        //$MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //$MiTemplate->set_var('pag',1);
        //$MiTemplate->parse("PBLPaginas", "Paginas", true);

        $MiTemplate->set_block("main", "Grilla", "PBLGrilla");
        // $MiTemplate->set_var('pag',1);
        // $MiTemplate->parse("PBLGrilla", "Paginas", true);
    }

    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


/****************************************************************
 *
 * Despliega Formulario de Edición
 *
 ****************************************************************/
function DisplayDetalle(){

    global $ses_usr_id;
    global $id_prod;

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

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","consulta_precios/detalle.html");


    // Detalle del Producto
    $query = "SELECT p.id_producto, p.cod_prod1, p.des_corta, p.des_larga, p.prod_tipo, p.estadoactivo, p.prod_subtipo, pe.espec_tecnicas, pe.stock_proveedor
            FROM productos p
            LEFT JOIN productos_ext pe ON pe.id_producto = p.id_producto
            WHERE p.cod_prod1 = ".($id_prod+0);
    $rq = tep_db_query($query);
    $res = tep_db_fetch_array( $rq );

    $MiTemplate->set_var('id_producto',tohtml( $res['id_producto']));
    $MiTemplate->set_var('cod_prod1',tohtml( $res['cod_prod1']));
    $MiTemplate->set_var('des_corta',tohtml( $res['des_corta']));
    $MiTemplate->set_var('des_larga',tohtml( $res['des_larga']));
    $MiTemplate->set_var('prod_tipo',tohtml( $res['prod_tipo']));
    $MiTemplate->set_var('prod_subtipo',tohtml( $res['prod_subtipo']));
    $MiTemplate->set_var('stock_proveedor',tohtml( $res['stock_proveedor']));
    $MiTemplate->set_var('espec_tecnicas',tohtml( $res['espec_tecnicas']));

    if( $res['estadoactivo']=='C')
    	$MiTemplate->set_var('estadoactivo','Activo');
    else
    	$MiTemplate->set_var('estadoactivo','Eliminado');

    if( file_exists(DIR_PROD_IMG."img_".$res['id_producto'].".jpg") )
        $MiTemplate->set_var("imagen",DIR_PROD_IMG."img_".$res['id_producto'].".jpg");
    else
        $MiTemplate->set_var("imagen",DIR_PROD_IMG."default.gif");


    // Listado de Códigos de barra
    $MiTemplate->set_block("main", "Codigos_barra", "PBL_Codigos_barra");

    $query="SELECT cod_barra, unid_med, estadoactivo
			FROM codbarra
			WHERE cod_prod1 = '".($id_prod+0)."' ";

    $codBarra = "<table  border='0' class='userinput'> ";
    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $codBarra = $codBarra . "<tr>";
        	if($res['estadoactivo'] == 'C')
        	$activo = "Activo ";
        	else
        	$activo = "Borrado ";

        	$codBarra = $codBarra . "<td width='90' >" . $res['cod_barra'] . "</td>" . "<td>" .  $activo . "</td>";
            $codBarra = $codBarra . "</tr>";

            $auxum=$res['unid_med'].' <br>'.$auxum;

        }
        $codBarra = $codBarra . " </table>";
    }
    else
        $MiTemplate->parse("PBL_Codigos_barra", "Codigos_barra", true);
        $MiTemplate->set_var('unid_med',tohtml( $auxum));
		$MiTemplate->set_var('cod_barra',tohtml($codBarra));
   		$MiTemplate->parse("PBL_Codigos_barra", "Codigos_barra", true);
    // Listado de Precios por Local
    $query="SELECT l.cod_local, nom_local, prec_valor, prec_costo, stock, p.estadoactivo
            FROM precios p
            LEFT JOIN locales l on l.id_local = p.id_local
            WHERE p.cod_prod1 = ".($id_prod+0);
    //echo $query;
    $MiTemplate->set_block("main", "Precios", "PBLprecios");

    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('cod_local',tohtml( $res['cod_local']));
            $MiTemplate->set_var('nom_local',tohtml( $res['nom_local']));
            $MiTemplate->set_var('prec_valor',tohtml( $res['prec_valor']));
            $MiTemplate->set_var('stock',tohtml( $res['stock']));

            if( $res['estadoactivo']=='C')
    		$MiTemplate->set_var('estadoactivoPrecio','Activo');
    		else
    		$MiTemplate->set_var('estadoactivoPrecio','Borrado');

            $MiTemplate->parse("PBLprecios", "Precios", true);
        }
    }

    // Agregamos el footer
    include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}


/**********************************************************************************************/
switch($req){
	case "frmed":
		DisplayDetalle();
		break;
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>