<?
$pag_ini = '../monitor_cotizaciones/monitor_cotizaciones_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

/**********************************************************************************************/
/* Declaracion de variables GLOBALES para la creacion de cotizaciones*/
function listado_cotizaciones() {
    global $ses_usr_id;
    global $select_pag, $select_estado, $select_local, $bus_os, $bus_rut, $accion, $filtro, $orden, $rad_selec;

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
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
    $MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
    $MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
    $MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
    
	$MiTemplate->set_var("select_pag",$select_pag);
	$MiTemplate->set_var("select_estado",$select_estado);
    $MiTemplate->set_var("select_local",$select_local);
    $MiTemplate->set_var("bus_os",$bus_os);
    $MiTemplate->set_var("bus_rut",$bus_rut);
    
	$MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","monitor_cotizaciones/monitor_cotizaciones_00.html");
	// la primera vez que imngresa lo hace con el filtro en cotizadas

	if(!$select_estado &&!strlen($select_estado) && !$bus_rut && !$bus_os){
		$select_estado='SE';
		$MiTemplate->set_var("select_estado",$select_estado);
	}
    //Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_esta");

    $query_E = "select id_estado, esta_nombre as estado, if('$select_estado'=id_estado, 'selected', '') 'selected' from estados where esta_tipo = 'SS' order by esta_nombre";
	$MiTemplate->set_var("select_estado",$select_estado);
    query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_esta', 'Estados' );   
    
	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND o.id_local = $mylocal ";
	else
	    $MiTemplate->set_var("ALL","<option value='0'>TODAS</option>"); 

	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT o.id_local as id_local, o.nom_local as nom_local, if('$select_local'=o.id_local, 'selected', '') 'selected' 
			FROM locales o WHERE 1 
			$where_aux_local
			ORDER BY nom_local";
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

    if ($rad_selec == 1 || $rad_selec == "")
        $MiTemplate->set_var("checkr1",'checked');
    else if($rad_selec == 2)
        $MiTemplate->set_var("checkr2",'checked');
    else if($rad_selec == 3)
        $MiTemplate->set_var("checkr3",'checked');

	        $MiTemplate->set_var("rad_selec",$rad_selec);
    //Filtros
    $where_aux ='';

	if ( $select_estado != "" &&  $select_estado != "0" && $bus_rut )
        $where_auxe = " and o.id_estado = '".$select_estado."'";
    if($select_local)
        $where_auxl = " and o.id_local = ".( $select_local + 0 );
	if ( $select_estado != "" &&  $select_estado != "0" && !$bus_rut && !$bus_os  )
        $where_aux = " and o.id_estado = '".$select_estado."'";
    else if ($bus_rut)
        $where_aux = " and c.clie_rut = ".(substr($bus_rut, 0, strlen($bus_rut)-1)+0)."";
    else if($rad_selec == 1)
            $where_aux = " and o.id_os = ".( $bus_os + 0 );
    else if($rad_selec == 2)
            $where_aux = " and clie_nombre like '$bus_os%'";
    else if($rad_selec == 3)
            $where_aux = " and clie_paterno = '$bus_os'";
    /*    
	//versión anterior de búsqueda de local
	if ($mylocal)
        $where_aux2 = " and o.id_local = ".( $mylocal + 0 );
	*/

    //Orden
    if ( $filtro == "" || $filtro == "os" )
        $order_aux = "order by o.id_os $orden";        
    else if( $filtro == "fc" )
        $order_aux = "order by o.os_fechacreacion $orden";        
                
    if ( $orden == "" || $orden == "asc"){
        $MiTemplate->set_var("orden",'desc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_DOWN);
    }
    else if ( $orden == "desc" ){
        $MiTemplate->set_var("orden",'asc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_UP);
    }
    /************************************para la paginacion***********************************/    
		 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite = MONITOR_LI_COT;

	$query_count = "SELECT count(*) as cuenta 
            FROM os o
            join clientes c on c.clie_rut = o.clie_rut
            join estados e on e.id_estado = o.id_estado and o.id_os > 0 
			WHERE $where_aux_local $where_aux  $where_auxl  $where_auxe $order_aux";
		
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
			if ($res_count['cuenta']<$limite){
				$MiTemplate->set_var('inicio','<!--');
				$MiTemplate->set_var('fin','-->');
			}
        }
        else{
                $MiTemplate->set_var('pag',1);
				$MiTemplate->set_var('inicio','<!--');
				$MiTemplate->set_var('fin','-->');
                $MiTemplate->parse("PBLPaginas", "Paginas", true);
        }
    /*****************************************************************************************/        
	$MiTemplate->set_block("main", "Modulos", "PBLModulos");
    
    $query = "SELECT o.id_os, o.os_descripcion, date_format( o.os_fechacreacion, '".DATE_FORMAT_S."' ) as os_fechacreacion, c.clie_rut, c.clie_nombre, c.clie_paterno, c.clie_telefonocasa, e.esta_nombre
            FROM os o
            join clientes c on c.clie_rut = o.clie_rut
            join estados e on e.id_estado = o.id_estado and o.id_os > 0 
			WHERE $where_aux_local $where_aux  $where_auxl $where_auxe  $order_aux  LIMIT ".($desde+0).",".($limite+0);
			
    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_os',tohtml( $res['id_os']));
            $MiTemplate->set_var('os_comentarios',tohtml( $res['os_descripcion']));
            $MiTemplate->set_var('os_fechacreacion',tohtml( $res['os_fechacreacion']));
            $MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']));
            $MiTemplate->set_var('clie_nombre',tohtml( $res['clie_nombre']));
            $MiTemplate->set_var('clie_paterno',tohtml( $res['clie_paterno']));
            $MiTemplate->set_var('clie_telcontacto2',tohtml( $res['clie_telefonocasa']));
            $MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));
            $MiTemplate->set_var('dig_ver', dv($res['clie_rut']) );
            
            $MiTemplate->parse("PBLModulos", "Modulos", true);   
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

listado_cotizaciones();

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>