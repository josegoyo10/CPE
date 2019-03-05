<?
$pag_ini = '../lista_Regalos/monitor_ListRegalos.php';
include "../../includes/aplication_top.php";

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
include_idioma_mod( $ses_idioma, "monitor_ListRegalos" );

/**********************************************************************************************/
function listado_cotizaciones() {
	actualizaEstado();
    global $ses_usr_id;
    global   $select_pag, $select_estado, $select_local, $select_evento, $select_fecha, $bus_List, $bus_rut, $accion, $filtro, $orden, $rad_selec, $rut;

    $local_name = get_local_name( $ses_usr_id );
    $usr_login = get_login_usr( $ses_usr_id );

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
    $MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
    $MiTemplate->set_var("TEXT_CAMPO_13",TEXT_CAMPO_13);
    $MiTemplate->set_var("TEXT_CAMPO_14",TEXT_CAMPO_14);
    $MiTemplate->set_var("TEXT_CAMPO_15",TEXT_CAMPO_15);
    $MiTemplate->set_var("TEXT_CAMPO_16",TEXT_CAMPO_16);
    
    $MiTemplate->set_var("select_pag",$select_pag);
    $MiTemplate->set_var("select_estado",$select_estado);
    $MiTemplate->set_var("select_evento",$select_evento);
    $MiTemplate->set_var("select_local",$select_local);
    $MiTemplate->set_var("select_fecha",$select_fecha);
    $MiTemplate->set_var("bus_List",$bus_List);
    $MiTemplate->set_var("bus_rut",$bus_rut);
    $MiTemplate->set_var("rut",$rut);   
  
    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);
    
    $MiTemplate->set_var("local_name",$local_name); 
    $MiTemplate->set_var("usr_login",$usr_login); 
    $MiTemplate->set_var("fecha_hoy",date("m/d/Y", time())); 
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","lista_Regalos/monitor_ListRegalos.html");    

    //Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_esta");
	$query_E = "SELECT id_estado, esta_nombre AS estado, if('$select_estado'=id_estado, 'selected', '')'selected', if('id_estado'=0, '$todosEs', '') 'todosEs' FROM estados WHERE esta_tipo = 'LS' ORDER BY esta_nombre";
	$MiTemplate->set_var("todosEs",$todosEs );
    
	query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_esta', 'Estados' );   
  
	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT o.id_local as id_local, o.nom_local as nom_local, if('$select_local'=o.id_local, 'selected', '') 'selected' FROM locales o WHERE 1 
			ORDER BY nom_local";			
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

	//Recuperamos los Eventos*/
	$MiTemplate->set_block("main", "Eventos", "BLO_event");
	$todosEv='<option value="0"{selected}>Todos</option>';
	$queryEv = "SELECT idEvento, nombre, if('$select_evento'=idEvento, 'selected', '')'selected', if('idEvento'=0, '$todosEv', '') 'todosEv' FROM list_eventos ORDER BY nombre";
	$MiTemplate->set_var("todosEv",$todosEv );
	query_to_set_var( $queryEv, $MiTemplate, 1, 'BLO_event', 'Eventos' );

	// Recupera las fechas de entrega
	$MiTemplate->set_block("main", "Fec_entrega", "BLO_Fec_entrega");
	$todasFec='<option value="0"{selected}>Todas</option>';
	$queryEv = "SELECT  DISTINCT fec_Evento, date_format( fec_Evento, '%d/%m/%Y' ) AS fecha, if('$select_fecha'=fec_Evento, 'selected', '')'selected', if('fec_Evento'=0, '$todasFec', '') 'todasFec'  FROM list_regalos_enc ORDER BY fec_Evento DESC LIMIT 200;";
	$MiTemplate->set_var("todasFec",$todasFec );

	query_to_set_var( $queryEv, $MiTemplate, 1, 'BLO_Fec_entrega', 'Fec_entrega' );
	
	// Fin de la recuperacion
	
    if ($rad_selec == 1 || $rad_selec == ""){
        $MiTemplate->set_var("checkr1",'checked');
		$MiTemplate->set_var("Val_check1",'document.form_search3.bus_List.onkeypress = RutIsKey');
    }
    else if($rad_selec == 2)
        $MiTemplate->set_var("checkr2",'checked');
    else if($rad_selec == 3)
        $MiTemplate->set_var("checkr3",'checked');
    else if($rad_selec == 4){
        $MiTemplate->set_var("checkr3",'checked');
        $MiTemplate->set_var("Val_check1",'document.form_search3.bus_List.onkeypress = RutIsKey');
    }

	        $MiTemplate->set_var("rad_selec",$rad_selec);

	//Filtros
    $where_aux ='';
  
    if($select_local)
        $where_auxl = " and L.id_Local = ".( $select_local + 0 );
	if ( $select_estado != "" &&  $select_estado != "0" && !$bus_rut && !$bus_os  )
        $where_auxes = " and L.id_Estado = '".$select_estado."'";
    if ($select_evento)
        $where_auxev = " and L.id_Evento = " . $select_evento;
    if ($select_fecha)
        $where_auxfe = " and L.fec_Evento = '" .$select_fecha. "'";    
    else if($rad_selec == 1)
            $where_aux = " and L.idLista = ".( $bus_List + 0 );
    else if($rad_selec == 2)
            $where_aux = " and C.clie_nombre like '$bus_List%'";
    else if($rad_selec == 3)
            $where_aux = " and C.clie_paterno like '$bus_List%'";
    else if($rad_selec == 4)
            $where_aux = " and C.clie_rut = ".( $bus_List + 0 );

    // Filtra por Vendedor
	if($rut){
          $queryV="SELECT CONCAT(USR_NOMBRES,' ',USR_APELLIDOS)AS vendedor, USR_ID FROM usuarios WHERE USR_LOGIN='".$rut."'";
          $rq_vend = tep_db_query($queryV);        
          $res_vend = tep_db_fetch_array($rq_vend);
          $where_auxV = " and L.id_Usuario = ".$res_vend['USR_ID'];
          $MiTemplate->set_var("clien",$res_vend['vendedor']);
	 }	                    
                
    if ( $orden == "" || $orden == "asc"){
    	$order_aux = "ORDER BY L.idLista $orden";
        $MiTemplate->set_var("orden",'desc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_DOWN);
    }
    else if ( $orden == "desc" ){
    	$order_aux = "order by L.idLista $orden";
        $MiTemplate->set_var("orden",'asc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_UP);
    }

    /************************************para la paginacion***********************************/    
		 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite = MONITOR_LI_COT;
	$query_count = "SELECT count(*) as cuenta 
            FROM list_regalos_enc L
			INNER JOIN clientes C ON C.clie_rut = L.clie_Rut
			INNER JOIN estados E ON E.id_estado = L.id_estado AND L.idLista > 0
			INNER JOIN list_eventos Ev ON Ev.idEvento = L.id_Evento
			INNER JOIN locales Lc ON Lc.id_local=L.id_Local
			INNER JOIN usuarios U ON U.USR_ID=L.id_Usuario  
			WHERE 1
			$where_aux  $where_auxl  $where_auxes $where_auxev $where_auxV $where_auxfe";

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
   
    $anular ="  <a href=\"javascript:validadel(\'{idLista}\',\'monitor_ListRegalos.php?accion=Anl&idLista={idLista}&clie_rut=$clie_rut\')\">Anular</a>";
	
    $query = "SELECT L.idLista, C.clie_rut, date_format( L.fec_creacion, '%d/%m/%Y' ) as fec_creacion, date_format( L.fec_entrega, '%d/%m/%Y' ) as fec_entrega,
       		  Ev.nombre AS evento, C.clie_nombre, C.clie_paterno, C.clie_telefonocasa, E.esta_nombre, L.festejado, Lc.nom_local,
       		  if( ('NP'=L.id_Estado || 'CP'=L.id_Estado), '1', '0') 'Anular',
       		  if( (DATEDIFF( date_format(L.fec_Evento, '%Y-%m-%d'), date_format(now(), '%Y-%m-%d' ))<=".DIA_ALERT_ENT.") &&  (L.id_Estado='LC' || L.id_Estado='CC'), 'color: #EF1821; font-weight: bold;', 'Normal' ) AS DiasEvent
			  FROM list_regalos_enc L
    		  INNER JOIN clientes C ON C.clie_rut = L.clie_Rut
			  INNER JOIN estados E ON E.id_estado = L.id_estado AND L.idLista > 0
			  INNER JOIN list_eventos Ev ON Ev.idEvento = L.id_Evento
			  INNER JOIN locales Lc ON Lc.id_local=L.id_Local
			  INNER JOIN usuarios U ON U.USR_ID=L.id_Usuario
			  WHERE 1
			  $where_aux  $where_auxl  $where_auxes $where_auxev $where_auxV $where_auxfe $order_aux LIMIT ".($desde+0).",".($limite+0);			

	if ( $rq = tep_db_query($query) ){
        
		while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('idLista', ($res['idLista']?$res['idLista']:"N/I"));
            $MiTemplate->set_var('local', ($res['nom_local']?$res['nom_local']:"N/I"));
            $MiTemplate->set_var('fec_creacion', ($res['fec_creacion']?$res['fec_creacion']:"N/I"));
            $MiTemplate->set_var('evento', ($res['evento']?$res['evento']:"N/I"));
            $MiTemplate->set_var('festejado', ($res['festejado']?$res['festejado']:"N/I"));
            $MiTemplate->set_var('fec_entrega', ($res['fec_entrega']?$res['fec_entrega']:"N/I"));
            $MiTemplate->set_var('clie_rut', ($res['clie_rut']?$res['clie_rut']:"N/I"));
            $MiTemplate->set_var('cliente', ($res['clie_nombre']." ".$res['clie_paterno']));
            $MiTemplate->set_var('telefono', ($res['clie_telefonocasa']?$res['clie_telefonocasa']:"N/I"));
            $MiTemplate->set_var('estado', ($res['esta_nombre']?$res['esta_nombre']:"N/I"));
            $MiTemplate->set_var('DiasEvent', $res['DiasEvent']);
            
            $Anular = "<a href=\"javascript:validaAnl('".$res['idLista']."')\">Anular</a>";
            $MiTemplate->set_var('accion', (($res['Anular'] == '1')?$Anular:" ") );	
            
            $MiTemplate->parse("PBLModulos", "Modulos", true);   
        } 
		         
    }

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

function actualizaEstado(){
	$QryAct = "SELECT OE.idLista_OS_enc
				FROM  list_os_enc OE
				WHERE DATEDIFF(date_format(now(), '%Y-%m-%d'), date_format(OE.OS_fecCrea, '%Y-%m-%d' )) >= ".VAL_COTI_REG." AND OS_estado='SE'";
	$rqAct = tep_db_query($QryAct);        
    while ( $resAct = tep_db_fetch_array($rqAct) ){
    	$UpdtAct = "UPDATE list_os_enc SET OS_estado='SC' WHERE idLista_OS_enc='".$resAct['idLista_OS_enc']."' ";
    	tep_db_query($UpdtAct);
    	
    	$QryAct1 = "SELECT LE.idLista, LD.idLista_det, OD.idLista_OS_det, OD.OS_cantidad, LD.list_cantprod
					FROM list_os_det OD
					LEFT JOIN list_os_enc OE ON (OE.idLista_OS_enc=OD.idLista_OS_enc)
					LEFT JOIN list_regalos_enc LE ON (LE.idLista=OE.idLista_enc)
					LEFT JOIN  list_regalos_det LD ON (LD.idLista_det=OD.idLista_det)
					where OD.idLista_OS_enc='".$resAct['idLista_OS_enc']."' ";
    	$rqAct1 = tep_db_query($QryAct1);        
	    while ( $resAct1 = tep_db_fetch_array($rqAct1) ){
	    	$UpdtAct1 = "UPDATE list_regalos_det SET list_cantprod='".($resAct1['list_cantprod'] +$resAct1['OS_cantidad'])."' WHERE idLista_det='".$resAct1['idLista_det']."' ";
	    	tep_db_query($UpdtAct1);
	    }
    }
}
/**********************************************************************************************/

if ($accion=="Anl" && $idLista){
    $queryAnl = "UPDATE list_regalos_enc SET  id_Estado='LA' WHERE idLista = $idLista AND id_Estado IN ('NP','CP')";  
    tep_db_query($queryAnl);
    
    $QryOS = "SELECT OE.idLista_OS_enc FROM list_regalos_enc LE
				LEFT JOIN list_os_enc OE ON OE.idLista_enc=LE.idLista
				WHERE LE.idLista = ".$idLista." ";
    $rqOS = tep_db_query($QryOS);        
    while ( $resOS = tep_db_fetch_array($rqOS) ){
   		$queryAnl_OS = "UPDATE list_os_enc SET OS_estado='SN' WHERE idLista_OS_enc='".$resOS['idLista_OS_enc']."'";  
    	tep_db_query($queryAnl_OS);
    }
	insertahistorial("Se ha Anulado la Lista de Regalo N° $idLista");
    tep_exit();
}

listado_cotizaciones();

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
