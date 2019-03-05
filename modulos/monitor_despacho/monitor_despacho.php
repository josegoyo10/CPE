<?php
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";
$_SESSION['reimpresion'] = 0;
$_SESSION['id_ot_old'] = 0;
$_SESSION['result_reimpresines'] = 0;

//include_idioma_mod( $ses_idioma, "monitor_despacho" );

/****************************************************************
 *
 * Despliega Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $texto_osot,$radioE,$busqueda,$select_origen,$select_tipo,$select_tipo,$select_des,$select_estado,$fecha_inicio,$fecha_inicio1,
	$select4,$orden,$select_fecha,$impresion,$where0,$radioE,$checkedS,$checkedT,$select_pag,$limite,$fecha_termino,$fecha_termino1, $variable;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' -  NOTAS POR PICKEAR');
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_2);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);
    
    $where_fecha = "";
    if ($fecha_inicio) {
		$aux = split("/", $fecha_inicio);
		$fec_ini_qry = $aux[2]."-".$aux[1]."-".$aux[0];
	} else {
		  $fec_ini_qry = date("Y-m-d", strtotime("-1 month"));
		  $fecha_inicio = date("d/m/Y", strtotime("-1 month"));
	}
	$where_fecha .= " and IF(os.os_fechaboleta IS NULL, ot_fechacreacion, os.os_fechaboleta) >= '".$fec_ini_qry.' 00:00:00'."' ";
    
    if ($fecha_termino) {
		$aux = split("/", $fecha_termino);
		$fec_ter_qry = $aux[2]."-".$aux[1]."-".$aux[0];
	} else {
		$fec_ter_qry = date("Y-m-d");
		$fecha_termino = date("d/m/Y");
	}
	$where_fecha .= " and IF(os.os_fechaboleta IS NULL, ot_fechacreacion, os.os_fechaboleta) <= '".$fec_ter_qry.' 23:59:59'."' ";
	
	$MiTemplate->set_var("fecha_inicio", $fecha_inicio);
	$MiTemplate->set_var("fecha_termino", $fecha_termino);

	
	if ($select4) {
		$order1 = " ORDER BY " . $select4 . " DESC";
		$MiTemplate->set_var("selected4" . $select4, "selected");
	} else {
		$order1 = "ORDER BY 10 DESC";
		$MiTemplate->set_var("selected410", "selected");
	}
	
	
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","monitor_despacho/monitor_despacho.htm");
	//busqueda de os u ot, con número
	    $MiTemplate->set_var("texto_osot",$texto_osot);
	    $MiTemplate->set_var("radioE",$radioE);

	/* recupera los valores en la primera búsqueda*/	
	if ($radioE=="os"){
		$checkedS="checked";
		$MiTemplate->set_var("checkedS",$checkedS);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " and ot.id_os=".$texto_osot;
	}	
	if ($radioE=="ot"){
		$checkedT="checked";
		$MiTemplate->set_var("checkedT",$checkedT);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " and ot.ot_id= ".$texto_osot;
	}

	/* si viene vacio el radio*/	/* si viene vacio el texto*/
	if (($radioE=='')||($texto_osot=='')){
			$MiTemplate->set_var("checkedS","checked");
			$where0= " ";
	}
	$MiTemplate->set_var("select_estado",$select_estado);
	$MiTemplate->set_var("id_estado",$id_estado);

	$aux=$select_estado;
	$a = split(",", $aux);
	count($a);
	if ($a[0] && $a[1]){
		$uno="'".$a[0]."'";
		$dos="'".$a[1]."'";
		$select_es="(".$uno.",".$dos.")";
		$valor=true;
	}
	if ($a[0] || $a[1]){
		$uno="'".$a[0]."'";
		$dos="'".$a[1]."'";
		$select_es="".$uno.",".$dos."";
		$valor=true;
	}
	/* si select_estado no tiene valor toma vacio*/
	if ((!$valor)){
		$select_es="'".$select_estado."'";
	}
	if ($select_estado=='0'){
		$select_es="''";
	}
	
  $MiTemplate->set_var("num_bus1",$texto_osot);
  $MiTemplate->set_var("radio_bus1",$radioE);
  $MiTemplate->set_var("orden",$select4);
  $MiTemplate->set_var("selected4".$select4,"selected");
  $MiTemplate->set_var("select_des",$select_des);
  $MiTemplate->set_var("select_fecha",$select_fecha);
  $MiTemplate->set_var("id_tipodespacho",$id_tipodespacho);
  $MiTemplate->set_var("select_tipo",$select_tipo);
  $MiTemplate->set_var("select_origen",$select_origen);

	/* para la búsqueda por tipo*/
	if ($select_tipo=='PS'){
		$MiTemplate->set_var("selected_ps","selected");
	}
	if ($select_tipo=='PE'){
		$MiTemplate->set_var("selected_pe","selected");
	}

	//Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_estados");
	
	
	$query_E="SELECT if(e2.esta_nombre IS NULL, concat('', e1.id_estado, ''), concat('',e1.id_estado, ',', e2.id_estado, '')) id_estado, e1.esta_nombre,
                     if((e1.id_estado IN ($select_es)), 'selected', '') 'seleccionado' 
              FROM   estados e1 LEFT JOIN estados e2 on e1.esta_nombre = e2.esta_nombre
                                AND e2.esta_tipo = 'TE'
                                AND e2.id_estado NOT IN ('EC', 'EP', 'ET', 'ER', 'EM')
              WHERE  e1.esta_tipo = 'TP' 
              AND    e1.id_estado NOT IN ('PM')
              UNION
              SELECT if(e1.esta_nombre IS NULL, concat('', e2.id_estado, ''), concat('', e1.id_estado, ',', e2.id_estado, '')) id_estado, e2.esta_nombre,
                     if((e2.id_estado in($select_es)), 'selected', '') 'seleccionado' 
              FROM   estados e1 RIGHT JOIN estados e2 on e1.esta_nombre=e2.esta_nombre
              AND    e1.esta_tipo = 'TP'
              AND    e1.id_estado NOT IN ('PM')
              WHERE  e2.esta_tipo = 'TE' AND e2.id_estado NOT IN ('EC', 'EP', 'ET', 'ER', 'EM')";
	
	query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_estados', 'Estados' );
	
	//Deja los Botones con las acciones visibles segun el estado
	if($aux == 'PC'){
		$MiTemplate->set_var("picking","<input type='button' name='Button' value='Imprimir Ordenes de Picking' onClick='PrintPickin(selectedCheck()); refrescar();' style='color: rgb(255, 0, 0); display:compact;'>");
	}
	if($aux == 'PG,EG'){
		$MiTemplate->set_var("guia","<input type='button' name='Button' value='Impr Guia de Despacho' onClick='PrintGuia(selectedCheck()); refrescar();' style='color: rgb(255, 0, 0); display:compact;'/>");
	}
	if($aux == 'ER'){
		$MiTemplate->set_var("guia","<input type='button' name='Button' value='Impr Guia de Despacho' onClick='PrintGuia(selectedCheck()); refrescar();' style='color: rgb(255, 0, 0); display:compact;'/>");
	}


     //Recuperamos los origenes
    $MiTemplate->set_block("main", "Origenes", "BLO_org");
    $query_Org = "select id_origen, nom_origen as origen , if('$select_origen'=id_origen, 'selected', '') 'selected' from origenusr order by nom_origen";
    query_to_set_var( $query_Org, $MiTemplate, 1, 'BLO_org', 'Origenes' );  

	

    
     //MANTIS 4969
    $perfilquery = "SELECT PER_ID FROM perfiles WHERE PER_NOMBRE IN ('Operador Despachos','Centro de Servicios','Jefe Centro de Servicios');";
    $rq_count = tep_db_query($perfilquery);        
   
     while(  $res_count = tep_db_fetch_array($rq_count) ){
        if(usuario_perfil($res_count['PER_ID'])){
            $SINper = "";
            break;
        }else{
            $SINper = " WHERE nombre != 'Express' ";
            $sinPER2 = true;
        }
    }
        
   //Recuperamos los despachos
    $MiTemplate->set_block("main", "Despachos", "BLO_des");
    $query_Des = "select id_tipodespacho, nombre , if('$select_des'=id_tipodespacho, 'selected', '') 'selected' from tipos_despacho $SINper order by nombre";
    query_to_set_var( $query_Des, $MiTemplate, 1, 'BLO_des', 'Despachos' );   


	
	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND os.id_local = $mylocal ";
	$select_ti="'".$select_tipo."'";
	$where1 = ($select_tipo)?" and ot_tipo = " . ($select_ti) :"";
	if($select_des){
            $where2 =  " and ot.id_tipodespacho = " . ($select_des);
        }else{
        	$where2 = ($sinPER2)?" and ot.id_tipodespacho != (SELECT id_tipodespacho FROM tipos_despacho where nombre = 'Express')" :"";
        }
	if ($select_es!="''"){
	$select_es="(".$select_es.")";
	$where3 = ($select_es)?" and ot.id_estado in " . ($select_es) :" ";
	}
	
	$where4 = ($select_fecha)?" and os.os_fechaboleta >= '$select_fecha 00:00:00' and os.os_fechaboleta <= '$select_fecha 23:59:59'":"";
	$where5= " and ot.id_estado not in ('EC', 'EP', 'ET', 'ER')";
	
	// Sin origen
    $query2 = "select id_origen FROM origenusr WHERE nom_origen='Sin Origen'";
    $hResult2 = tep_db_query($query2);        
    $hDataset2 = tep_db_fetch_array($hResult2); 
	
	if($select_origen!='0' && trim($select_origen)!='')
    $where6 = ($select_origen)?" and os.USR_ORIGEN = " . ($select_origen) :"";
    
                  
  if($select_origen==$hDataset2['id_origen'])
  {  
    $where6 = " and (os.USR_ORIGEN IS NULL or os.USR_ORIGEN = 0 or os.USR_ORIGEN = " . ($select_origen).")";
  }
  
  $where7 = " and e.esta_tipo in ('TE','TP') and e.id_estado not in ('EC', 'EP', 'ET', 'ER', 'EM', 'PM') ";
  
/*////////////////////// Para la paginacion //////////////////////*/    
		 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite =MONITOR_LI_DE;

	$query_count = "SELECT count(*) as cuenta 
	FROM ot JOIN os on os.id_os = ot.id_os 
	JOIN estados e on e.id_estado = ot.id_estado 
	JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
	where ot_tipo in ('PE','PS') and os.origen in ('C','S','V', 'W') 
	$where0 $where1 $where2 $where3 $where4 $where5 $where6 $where7 $where_fecha $where_aux_local";

	//echo $query_count."<br>";
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
/*//////////////////////////////////////////////////////////////////////////////////////////*/  

/*
     //QUERY ORIGINAL CON LA FECHA DE CREACION DE LA OS
	$MiTemplate->set_block("main", "LISTA_OT", "BLO_lista");
	$query="SELECT ot.ot_id, ot.id_os as id_os, ot_tipo, td.nombre as nombre_despacho , ot_fechacreacion,esta_nombre,ot.id_estado,date_format(ot_fechacreacion, '%d/%m/%Y  %H:%m:%s')OTF, os.id_local,  nom_local,ot.id_tipodespacho as tipoDes,ot.id_estado,esta_tipo,ot.id_estado
	FROM ot JOIN os on os.id_os = ot.id_os 
	JOIN estados e on e.id_estado = ot.id_estado 
	JOIN locales l on l.id_local = os.id_local 
	JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
	where ot_tipo in ('PE','PS') and os.origen in ('C','S','V') $where0 $where1 $where2 $where3 $where4 $where5 $auxosot $where_aux_local $order1 LIMIT ".($desde+0).",".($limite+0);
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_lista', 'LISTA_OT' ); 
*/
     
    //MANTIS 4969
    $perfilquery = "SELECT PER_ID FROM perfiles ";
    $rq_count = tep_db_query($perfilquery);        
    $res_count = tep_db_fetch_array($rq_count);
    if(usuario_perfil($res_count['PER_ID'])){
        $permisos = "IF(td.nombre = 'Express', 'disabled' , '') as permisos," ;
        $permisos2 = "IF(td.nombre = 'Express', 'display:none;' , '') as permisos2,";
    }else{
        $permisos = "" ;
    }
        
        
        
        
        
// case WHEN (date_format(os.os_fechaboleta, '%H') > 12) then 'estilo1' else 'estilo2' end estilo
$color1 = 'bgcolor="#00CC66"'; // Verde
$color2 = 'bgcolor="#FFFF66"'; // Amarillo

	$MiTemplate->set_block("main", "LISTA_OT", "BLO_lista");
	//ESTE QUERY TRAE LA FECHA DE PAGO DE LA COTIZACION
	$query="SELECT ot.ot_id, 
	               ot.id_os as id_os, 
				   ot_tipo, 
                                   $permisos
                                   $permisos2
				   td.nombre as nombre_despacho, 
				   ot_fechacreacion, 
				   esta_nombre, 
				   ot.id_estado, 
	               IF(ot.numero_impresiones = 0, '&nbsp;', '<img src=\"../img/printOk.png\">') impresiones, 
	               IF(os.os_fechaboleta IS NULL, date_format(ot_fechacreacion, '%d/%m/%Y %T'), date_format(os.os_fechaboleta, '%d/%m/%Y %T')) OTF, 
				   IF(os.os_fechaboleta IS NULL, ot_fechacreacion, os.os_fechaboleta) OTFORDEN, 
	               os.id_local, 
				   ot.id_tipodespacho as tipoDes, 
				   ot.id_estado, 
				   esta_tipo, 
				   ot.id_estado, 
	               IF (DATE(now( )) = date_format( os_fechaboleta, '%Y-%m-%d' ) AND ot.id_estado IN ( 'PC','PD','PE','PG','EE','EG', 'ED', 'ER'), IF (date_format(os.os_fechaboleta, '%H') >= 12, 'estilo1', 'estilo2' ),  IF ( ( ot.id_estado IN ( 'PC','PD','PE','PG','EE','EG', 'ED', 'ER') ), 'estilo3', '' )) AS estilo, 
				   origenusr.nom_origen as origen 
	        FROM   ot JOIN os ON os.id_os = ot.id_os 
	                  JOIN os_detalle ON os_detalle.id_os = os.id_os 
	                  JOIN estados e ON e.id_estado = ot.id_estado 
	                  JOIN tipos_despacho td ON td.id_tipodespacho = ot.id_tipodespacho 
	                  LEFT JOIN origenusr ON origenusr.id_origen = os.USR_ORIGEN 
	        WHERE  ot_tipo in ('PE','PS') 
		    AND	   os.origen in ('C','S','V', 'W') 
			$where0 $where1 $where2 $where3 $where4 $where5 $where6 $where7 $where_fecha $where_aux_local
	        GROUP  BY ot.ot_id
			$order1
			LIMIT ".($desde+0).",".($limite+0);
       // echo $query;
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_lista', 'LISTA_OT' ); 
	// echo $query."<br><br><br>";
        //Recuperamos las Fechas de Pago de la Cotizacion.
	$MiTemplate->set_block("main", "Fecha_Creacion", "BLO_cre");
	$queryfini = "select distinct DATE_FORMAT(os.os_fechaboleta, '%Y-%m-%d') as fechat ,
                	DATE_FORMAT(os.os_fechaboleta, '%d/%m/%Y') as fecha ,
       				if (DATE_FORMAT(os.os_fechaboleta, '%Y-%m-%d')='$select_fecha', 'selected', '')
       					selected FROM ot
                		JOIN os on os.id_os = ot.id_os
                		JOIN estados e on e.id_estado = ot.id_estado 
                		where os.os_fechaboleta is not null $where0 $where1 $where2 $where3 $where4 $where5 $where7 $where_fecha $where_aux_local order by 1 DESC";
	query_to_set_var( $queryfini, $MiTemplate, 1, 'BLO_cre', 'Fecha_Creacion' );

/*
   //Recuperamos las fechas
	$MiTemplate->set_block("main", "Fecha_Creacion", "BLO_cre");
	$queryf = "select distinct date_format(ot_fechacreacion, '%Y-%m-%d') as fechat , 
	DATE_FORMAT( ot_fechacreacion, '%d/%m/%Y') as fecha ,
	if (DATE_FORMAT( ot_fechacreacion, '%Y-%m-%d')='$select_fecha', 'selected', '') selected 
	FROM ot
	JOIN os	on os.id_os	= ot.id_os
	JOIN estados e	on e.id_estado	= ot.id_estado
	JOIN locales l	on l.id_local	= os.id_local
	where ot_fechacreacion is not null $where0 $where1 $where2 $where3  $where_aux_local order by 1 DESC";
	query_to_set_var( $queryf, $MiTemplate, 1, 'BLO_cre', 'Fecha_Creacion' );
*/    
	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

/**********************************************************************************************/
switch($req){
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
