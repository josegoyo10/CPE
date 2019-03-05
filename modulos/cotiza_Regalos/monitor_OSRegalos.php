<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "monitor_OSRegalos" );
$USR_LOGIN = get_nombre_usr( $ses_usr_id );

/**********************************************************************************************/
function listado_cotizaciones() {
    global $ses_usr_id;
    global   $idLista, $select_pag, $select_local, $bus_List, $bus_rut, $accion, $filtro, $orden, $rad_selec, $rut;
	$local = get_local_name( $ses_usr_id );
	$USR_LOGIN = get_nombre_usr( $ses_usr_id );
	$Fecha = date('d/m/Y'); 

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
    $MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);
    $MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
    
    $MiTemplate->set_var("Regresar",Regresar);
    $MiTemplate->set_var("select_pag",$select_pag);
    $MiTemplate->set_var("select_local",$select_local);
    $MiTemplate->set_var("bus_List",$bus_List);
    $MiTemplate->set_var("bus_rut",$bus_rut);
    $MiTemplate->set_var("rut",$rut);   
    $MiTemplate->set_var("idLista",$idLista);
  
    $MiTemplate->set_var("Usuario",$USR_LOGIN);
    $MiTemplate->set_var("Local",$local);
    $MiTemplate->set_var("Fecha",$Fecha);
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","cotiza_Regalos/monitor_OSRegalos.html");    

	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT o.id_local as id_local, o.nom_local as nom_local, if('$select_local'=o.id_local, 'selected', '') 'selected' FROM locales o WHERE 1 
			ORDER BY nom_local";
					
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   	
	// Fin de la recuperacion

    if ($rad_selec == 1 || $rad_selec == ""){
        $MiTemplate->set_var("checkr1",'checked');
		$MiTemplate->set_var("Val_check1",'document.form_search1.bus_List.onkeypress = NumberIsKey');
    }
    else if($rad_selec == 2)
        $MiTemplate->set_var("checkr2",'checked');
    else if($rad_selec == 3)
        $MiTemplate->set_var("checkr3",'checked');
    else if($rad_selec == 4){
        $MiTemplate->set_var("checkr3",'checked');
        $MiTemplate->set_var("Val_check1",'document.form_search1.bus_List.onkeypress = RutIsKey');
    }

	$MiTemplate->set_var("rad_selec",$rad_selec);

	//Filtros
    $where_aux ='';
  
    
    if($select_local)
        $where_auxl = " and L.OS_local = ".( $select_local + 0 ); 
    if($rad_selec == 1 && $bus_List)
            $where_aux = " and L.idLista_OS_enc = ".( $bus_List + 0 );
    else if($rad_selec == 2 && $bus_List)
            $where_aux = " and C.clie_nombre like '$bus_List%'";
    else if($rad_selec == 3 && $bus_List)
            $where_aux = " and C.clie_paterno like '$bus_List%'";
    else if($rad_selec == 4 && $bus_List)
            $where_aux = " and C.clie_rut = ".( $bus_List + 0 );                    
            
    if ( $orden == "" || $orden == "asc"){
    	$order_aux = "ORDER BY L.idLista_OS_enc $orden";
        $MiTemplate->set_var("orden",'desc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_DOWN);
    }
    else if ( $orden == "desc" ){
    	$order_aux = "order by L.idLista_OS_enc $orden";
        $MiTemplate->set_var("orden",'asc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_UP);
    }
          

    /************************************para la paginacion***********************************/    
		 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite = MONITOR_LI_COT;
		$query_count = "SELECT count(*) as cuenta  FROM list_os_enc L
				          INNER JOIN clientes C ON C.clie_rut = L.OS_clieRut
				          INNER JOIN estados E ON E.id_estado = L.OS_estado
				          INNER JOIN locales T ON T.id_local = L.OS_local
				          WHERE  1 AND L.idLista_enc = $idLista
						$where_aux  $where_auxl  $order_aux LIMIT ".($desde+0).",".($limite+0);
		$rq_count = tep_db_query($query_count);        
        $res_count = tep_db_fetch_array($rq_count);
        $total = ceil($res_count['cuenta'] / $limite);

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

    $query = "SELECT L.idLista_OS_enc, date_format(L.OS_fecCrea, '%d/%m/%Y' ) AS fecCrea, E.esta_nombre, T.nom_local, L.invitado, LR.id_Estado,
    		  if( ('SE'=L.OS_estado), '1', '0') 'Anular',
    		  if( ('SE'=L.OS_estado), '1', '0') 'Pagar'
    		  FROM list_os_enc L
    		  LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = L.idLista_OS_enc
    		  LEFT JOIN list_regalos_enc LR ON LR.idLista = LE.idLista_enc
	          INNER JOIN clientes C ON C.clie_rut = L.OS_clieRut
	          INNER JOIN estados E ON E.id_estado = L.OS_estado
	          INNER JOIN locales T ON T.id_local = L.OS_local
	          WHERE  1 AND L.idLista_enc = $idLista
			  $where_aux  $where_auxl  $order_aux LIMIT ".($desde+0).",".($limite+0);			
	if ( $rq = tep_db_query($query) ){
        
		while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('idLista_OS_enc', ($res['nom_local']?$res['idLista_OS_enc']:"N/I"));
            $MiTemplate->set_var('fec_creacion', ($res['fecCrea']?$res['fecCrea']:"N/I"));
            $MiTemplate->set_var('estado', ($res['esta_nombre']?$res['esta_nombre']:"N/I"));
            $MiTemplate->set_var('local', ($res['nom_local']?$res['nom_local']:"N/I"));
            $MiTemplate->set_var('invitado', ($res['invitado']?$res['invitado']:"N/I"));
          
            $Anular = "<a href=\"javascript:validaAnl('".$res['idLista_OS_enc']."')\" class=\"link1\">Anular</a>";
			$Pagar = "<a href=\"javascript:validaPago('".$res['idLista_OS_enc']."')\" class=\"link1\">Pagar</a>";
			
			if($res['id_Estado'] != 'LA'){
				$MiTemplate->set_var('anular', (($res['Anular'] == '1')?$Anular: " ") );
	        	$MiTemplate->set_var('pagar', (($res['Pagar'] == '1')?$Pagar:" ") );
			}
            $MiTemplate->parse("PBLModulos", "Modulos", true);   
        } 
		         
    }

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}
/**********************************************************************************************/

if ($accion=="Anl" && $idLista){
	$Qrysel = "SELECT A.idLista_det, D.OS_cantidad, A.idLista_enc 
	           FROM list_os_det D
         	   JOIN list_regalos_det A ON (A.idLista_det = D.idLista_det)
			   WHERE D.idLista_OS_enc=".($idLista+0).";";
	$resltQry = tep_db_query($Qrysel);   
  
    while ($result = tep_db_fetch_array($resltQry)){
    		$QryUpd = "UPDATE list_regalos_det SET list_cantprod = (list_cantprod+".$result['OS_cantidad'].") WHERE idLista_det = ".$result['idLista_det'].";";    	
    		tep_db_query($QryUpd);
    		$idLista_enc = $result['idLista_enc'];
    }
    $queryAnl = "UPDATE list_os_enc SET OS_estado='SN' WHERE idLista_OS_enc = $idLista AND OS_estado IN ('SE')";   
    tep_db_query($queryAnl);
	writelog("Se ha eliminado del sistema la Lista de Regalo N° $queryAnl");
	insertahistorial_ListaReg("Se ha eliminado del sistema la Cotización N°.$idLista, correspondiente a la  Lista de Regalo N°.".$idLista_enc.".", $USR_LOGIN, null, $idLista_enc, $idLista, $tipo = 'SYS');
    tep_exit();
}

listado_cotizaciones();

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
