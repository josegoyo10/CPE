<?
$pag_ini = '../pe_precio_pend/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "pr_precio_pend" );

/**********************************************************************************************/


/****************************************************************
 *
 * Despliega Listado Monitor PE Precio Pend
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $select_tipo, $select_local, $bus_os, $filtro, $orden;

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
/*
    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
*/    
    $MiTemplate->set_var("select_tipo",$select_tipo);
    $MiTemplate->set_var("select_local",$select_local);
    $MiTemplate->set_var("bus_os",$bus_os);
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","pe_precio_pend/listado.html");

	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND os.id_local = $mylocal ";
	else
	    $MiTemplate->set_var("ALL","<option value=''>TODAS</option>"); 

	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT os.id_local as id_local, os.nom_local as nom_local, if('$select_local'=os.id_local, 'selected', '') 'selected' 
			FROM locales os WHERE 1 
			$where_aux_local
			ORDER BY nom_local";
	query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   
    //echo $queryL;

    //Filtros
    $where_aux ='';
    if ( $select_tipo == 0 || $select_tipo == "" ) 
        $where_aux = ""; //" and osde_precio IS NULL";  // JSE: 07092005
    else if ( $select_tipo == 1 ) {
        $where_aux = ""; //" and osde_precio IS NOT NULL"; // JSE: 07092005
	    $MiTemplate->set_var("select1","selected"); // JSE: 07092005
	}
	else if ( $select_tipo == 2 ){ 
        $where_aux = " ";
		$MiTemplate->set_var("select2","selected");
	}
    if( $select_local )
        $where_aux = " and l.id_local = ".( $select_local + 0 );
    if ( $bus_os ) 
        $where_aux = " and osd.id_os = ".( $bus_os + 0 );
	
	//Orden
    if ( $filtro == "" || $filtro == "os" )
        $order_aux = "order by osd.id_os $orden";        
    else if( $filtro == "fc" )
        $order_aux = "order by os_fechacreacion $orden";        
                
    if ( $orden == "" || $orden == "asc"){
        $MiTemplate->set_var("orden",'desc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_DOWN);
    }
    else if ( $orden == "desc" ){
        $MiTemplate->set_var("orden",'asc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_UP);
    }
    
    $MiTemplate->set_block("main", "listado", "PBLlistado");
    
    $query = "SELECT id_os_detalle, osd.id_os as id_os, osde_tipoprod, osde_descripcion, osde_preciocosto,
					 osde_precio, osde_cantidad,date_format(os_fechacreacion, '%d/%m/%Y') os_fechacreacion , os.id_local, nom_local
            FROM os_detalle osd
				JOIN os ON os.id_os = osd.id_os
				JOIN locales l ON l.id_local = os.id_local
			WHERE osde_tipoprod = 'PE' AND  osde_subtipoprod='GE' and  id_estado='SI' AND os.origen='C' /*estado PENDIENTE*/
			$where_aux_local $where_aux  $order_aux";
	//echo $query;
    //query_to_set_var( $query, $MiTemplate, 1, 'PBLModulos', 'Modulos' );   
    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_os',tohtml( $res['id_os']));
            $MiTemplate->set_var('local',tohtml( $res['nom_local']));
            $MiTemplate->set_var('os_fechacreacion',tohtml( $res['os_fechacreacion']));
            $MiTemplate->set_var('desc_prod',tohtml( $res['osde_descripcion']));
            $MiTemplate->set_var('idosde',tohtml( $res['id_os_detalle']));
            if ( strlen($res['osde_preciocosto']) == 0 )
				$MiTemplate->set_var('costo','-');
            else 
				$MiTemplate->set_var('costo',formato_precio(tohtml( $res['osde_preciocosto'])));
            if ( strlen($res['osde_precio']) == 0 )
				$MiTemplate->set_var('precio','-');
            else 
				$MiTemplate->set_var('precio',formato_precio(tohtml( $res['osde_precio'])));
            
            
            $MiTemplate->parse("PBLlistado", "listado", true);   
        }           
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
function DisplayFormEdit(){

    global $ses_usr_id;
    global $idosde;

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
    $MiTemplate->set_file("main","pe_precio_pend/edit.html");

	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND os.id_local = $mylocal ";

    $query = "SELECT id_os_detalle, osd.id_os as id_os, osde_tipoprod, osde_descripcion, osde_preciocosto,
					 osde_precio, osde_cantidad, os_fechacreacion, os.id_local, nom_local, osde_especificacion,
					 osd.cod_barra as codbarra, osd.cod_sap, observacion
            FROM os_detalle osd
				JOIN os on os.id_os = osd.id_os
				JOIN locales l on l.id_local = os.id_local
			WHERE id_os_detalle = $idosde
				AND osde_tipoprod = 'PE'
			$where_aux_local $where_aux $order_aux";

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	$MiTemplate->set_var('id_os',tohtml($res['id_os']));
	$MiTemplate->set_var('idosde',tohtml($res['id_os_detalle'])); 
	$MiTemplate->set_var('local',tohtml( $res['nom_local']));
	$MiTemplate->set_var('os_fechacreacion',tohtml( $res['os_fechacreacion']));
	$MiTemplate->set_var('codbarra',tohtml($res['codbarra']));
	$MiTemplate->set_var('cod_sap',tohtml($res['cod_sap']));
	$MiTemplate->set_var('desc_prod',tohtml($res['osde_descripcion']));
	$MiTemplate->set_var('especificacion',tohtml( $res['osde_especificacion']));
	$MiTemplate->set_var('costo',tohtml( $res['osde_preciocosto']));
	$MiTemplate->set_var('precio',tohtml( $res['osde_precio']));
	$MiTemplate->set_var('idosde',tohtml( $res['id_os_detalle']));
	$MiTemplate->set_var('obs',tohtml( $res['observacion']));

	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}



/****************************************************************
 *
 * Proceso de Actualización
 *
 ****************************************************************/
function Upd_H(){
	global $idosde, $precio, $costo, $obs,$id_os;
	$query = "UPDATE os_detalle
			  SET    osde_preciocosto	= '$costo',
					 osde_precio		= '$precio',
					 observacion		= '$obs'
			  WHERE  id_os_detalle		= $idosde
			  ";

	if (!tep_db_query($query))
		echo "error: ".$query;
	else{
        /*llama a la funcion historial*/
            if ($precioant!=$precio){
				/* saca nonbre del producto que se ha actualizado el precio*/
				$querN="select osde_descripcion, osde_preciocosto, osde_precio from os_detalle where id_os_detalle = $idosde ";
				$estaN = tep_db_query($querN);
				$estaN = tep_db_fetch_array( $estaN );
				$esta_nombre=$estaN['osde_descripcion'];
				$esta_precioventa=$estaN['osde_precio']+0;
				$esta_preciocosto=$estaN['osde_preciocosto']+0;

				insertahistorial("Se ha actualizado el precio pendiente (precio costo: $esta_preciocosto, precio venta: $esta_precioventa) del producto $esta_nombre en la OS ".($id_os+0));
			}
			header( "Location: index.php" );
        }
}

/**********************************************************************************************/
switch($req){
	case "frmed":
		DisplayFormEdit();
		break;
	case "upd":
		Upd_H();
		break;	
    default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>