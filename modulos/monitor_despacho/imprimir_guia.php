<?
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";
include "ConvertCharset.class.php";

/****************************************************************
 *
 * IMPRIME Listado Monitor Despacho
 *
 ****************************************************************/
function ImprimirPopupGuia($ARR) {
	global $ses_usr_id;
	global $id_ot,$id_estado;
	
	$MiTemplate = new Template();
	// asignamos degug maximo
	$MiTemplate->debug = 0;
	// root directory de los templates
	$MiTemplate->set_root(DIRTEMPLATES);
	// variables perdidas
	$MiTemplate->set_unknowns("remove");
	$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

	// Agregamos el main
	$MiTemplate->set_file("main","monitor_despacho/imprimir_guia.htm");
	$id_ot=$ARR['id_ot'];
	
	//Partimos la cadena para enviar al id_ot de una en una a impresion

 	
	// Query de la despaccho
	$queryDes = "SELECT distinct os.id_os, od.ot_id, cl.clie_nombre,cl.clie_paterno,cl.clie_materno,cl.clie_rut,dire_direccion,comu_nombre,l.nom_local FROM os
	JOIN direcciones	d on d.id_direccion	= os.id_direccion
	JOIN comuna			c on c.id_comuna	= d.id_comuna
	JOIN clientes		cl on cl.clie_rut	= os.clie_rut
	JOIN os_detalle		od on od.id_os	= os.id_os
	JOIN locales		l  on os.id_local=l.id_local 
	WHERE od.ot_id in ($id_ot)";
	
	/*saca la cantidad de productos de la guia*/
	$cant_prod=CantidadProductos($id_ot);
	$MiTemplate->set_var('cant_prod',round($cant_prod));
	/*saca la cantidad lineas para imprimir de la guia*/
	$cant_detalle=CantidadDetalles($id_ot);
	$cant_lineas=CANT_LINEAS_GUIA;
	if ($cant_detalle>$cant_lineas){
		$total = ceil($cant_detalle/$cant_lineas);
		$MiTemplate->set_var("cant_doc",$total);
	}else{
		$MiTemplate->set_var("cant_doc",1);
		$total =1;
	}

	$impresora= Obtiene_Impresora($ses_usr_id);
	$MiTemplate->set_var("impresora",($impresora!='')?$impresora:IMPRESORA);
	
	$i=1;
	$MiTemplate->set_block("main", "Bloque_guias", "PBL_Modulos");
	/* se repite segun la cantidad de documentos que tenga*/
	if ( $rq = tep_db_query($queryDes) ){
		$res = tep_db_fetch_array( $rq );
		while( $i<=$total) {
			$MiTemplate->set_var('ot_id',tohtml( $res['ot_id']));
			$id_ot=$res['ot_id'];
			$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']));
			$MiTemplate->set_var('dv_rut',dv($res['clie_rut']));
			$MiTemplate->set_var('clie_nombre',tohtml( $res['clie_nombre'].' '.$res['clie_paterno'].' '.$res['clie_materno']));
			$MiTemplate->set_var('dire_direccion',tohtml( $res['dire_direccion']));
			$MiTemplate->set_var('comu_nombre',tohtml( $res['comu_nombre']));
			$MiTemplate->set_var('secuencia',tohtml( $i));
			$id_os=$res['id_os'];
			$local=$res['nom_local'];
			$i++;
			$MiTemplate->parse("PBL_Modulos", "Bloque_guias", true);   
		}
	}
	$MiTemplate->set_var('id_os',$id_os);
	$MiTemplate->set_var('nombre_local',$local);
	$MiTemplate->pparse("OUT_H", array("header"), true);
	$MiTemplate->parse("OUT_M", array("main"), true);
	$MiTemplate->p("OUT_M");
}

function imprimir_guia($ARR){
	global $ses_usr_id;
	$MiTemplate = new Template();
	$secuencia		= $ARR['secuencia'];
	$arrSecuencia	= $ARR['secuencia'];
	$arrFecha		= $ARR['fecha'];
	$ot_id			= $ARR['ot_id'];
	$MiTemplate->set_var("impresora",$ARR['impresora']);
	$MiTemplate->set_var("ot_id",$ARR['ot_id']);
	$MiTemplate->set_var("archivo",$ARR['archivo']);
	$MiTemplate->set_var("URL_CPROY",URL_CPROY);
	tep_db_query("SET AUTOCOMMIT=0");
	$success = true;
	// update de impresora para usr
	$queryup="UPDATE usuarios  SET impresora='".addslashes($ARR['impresora'])."' where usr_id=".$ses_usr_id;
	$success = $success && tep_db_query($queryup);
	if ($success){
		tep_db_query("COMMIT");
	}else{
		tep_db_query("ROLLBACK");
		writelog('ERROR: No se pudo completar la operación en function imprimir_guia, de clase guia.class.php');
	}
	tep_db_query("SET AUTOCOMMIT=1");		

	$i=0;
	foreach ($secuencia as $key=>$value) { 
		$largoArregloSec=count($secuencia);
		$valor=$secuencia[$key];
		$i++;
		$tomorrow = ExisteEnArreglo($valor, $arrFecha);
		if($largoArregloSec==$i){
			$ultimaHoja=1;
			$archivo=ImprimeHojaGuia($ot_id,$valor, $tomorrow,$ultimaHoja);
		}
		else{
			$ultimaHoja=0;
			$archivo=ImprimeHojaGuia($ot_id,$valor, $tomorrow,$ultimaHoja);
		}

	}
	/* creo el string del header para enviar datos*/
	$strheader="Location:../monitor_despacho/imprimir_guia.php?ot_id=$ot_id&archivo=$archivo&accion=applet";
	
	foreach ($arrSecuencia as $key=>$value) { 
		$strheader.="&arrSecuencia[]=".$arrSecuencia[$key];
	}
	header($strheader);
}

function ImprimeHojaGuia($ot_id,$sec,$tomorrow,$uHoja){
	$tplTmp = new Template();
	$tplTmp->set_file("main",DIR_TEMPLATE_GUIA."impresion_guia.tpl");
	//llena encabezado de la guia
	$query_enc="SELECT distinct os.id_os, od.ot_id,cl.clie_telcontacto1, cl.clie_nombre,cl.clie_paterno,cl.clie_materno,cl.clie_rut,dire_direccion,comu_nombre,l.nom_local FROM os
	JOIN direcciones	d on d.id_direccion	= os.id_direccion
	JOIN comuna			c on c.id_comuna	= d.id_comuna
	JOIN clientes		cl on cl.clie_rut	= os.clie_rut
	JOIN os_detalle		od on od.id_os	= os.id_os
	JOIN locales		l  on os.id_local=l.id_local 
	WHERE od.ot_id=".$ot_id;
	$rq = tep_db_query($query_enc);
	$res = tep_db_fetch_array($rq);
	$fechaactual = DATE('d/m/Y');
	$tplTmp->set_var("clie_nombre",$res['clie_nombre'].' '.$res['clie_paterno'].' '.$res['clie_materno']);
	$tplTmp->set_var("rut_cliente",$res['clie_rut'].'-'.dv($res['clie_rut']));			
	/*fecha de hoy o de mañana en la guia de despacho*/
	If ($tomorrow==1)
		$tplTmp->set_var("fechaactual",date("d/m/Y", mktime(0, 0, 0, date("m"),date("d")+1,date("Y"))));
	Else
		$tplTmp->set_var("fechaactual",DATE('d/m/Y'));

	$tplTmp->set_var("direccion_cliente",$res['dire_direccion']);
	$tplTmp->set_var("nombre_cumuna",$res['comu_nombre']);
	$tplTmp->set_var("Ctelefono_g",$res['clie_telcontacto1']);
	$tplTmp->set_var("id_os",$res['id_os']);
	$tplTmp->set_var("ten",'TE N');
	$tplTmp->set_var("trn",'TE R');
	$tplTmp->set_var("giro",'GIRO');
	$tplTmp->set_var("cajero",'Cajero');
	$tplTmp->set_var("folio",'Folio');
	$tplTmp->set_var("offsetn",'Offset');
	$tplTmp->set_var("preciounitario",'');
	/*si es la ultima hoja a imprimir hace un form feed*/
	$j=0;
	$tplTmp->set_block("main", "Bloque_form_feed", "PBL_Guiaff");
	if ($uHoja){
	/*imprime n lineas en blanco para poder sacar la hoja*/
		while( $j<5) {
			$tplTmp->set_var("form_feed",chr(10));
			$j++;
			$tplTmp->parse("PBL_Guiaff", "Bloque_form_feed", true); 
		}
	}else{
		$tplTmp->set_var("form_feed",'');
		$tplTmp->parse("PBL_Guiaff", "Bloque_form_feed", true); 
	}
	//saca el limite
	$cant_lineas = CANT_LINEAS_GUIA;
	if($sec==1){
		$lim=" LIMIT ".(0).",".($sec*$cant_lineas);
	}else{
		$lim=" LIMIT ".($sec*$cant_lineas-$cant_lineas).",".($cant_lineas);
	}

	//llena los datos detalle//		
	$query_det="SELECT cod_barra,osde_descripcion,osde_cantidad FROM os_detalle where ot_id=".$ot_id." order by osde_descripcion ".$lim;
	$tplTmp->set_block("main", "Bloque_det_guias", "PBL_Guia");
	if ( $rq = tep_db_query($query_det) ){
		$sub_i=0;
		while( $res = tep_db_fetch_array( $rq ) ) {
			$tplTmp->set_var("codigo",trim($res['cod_barra']));
			$tplTmp->set_var("cantidad",trim($res['osde_cantidad']));
			$tplTmp->set_var("descripcion",trim($res['osde_descripcion']));  
			$tplTmp->parse("PBL_Guia", "Bloque_det_guias", true); 
			$sub_i++;
			$cont=$sub_i;
		}	
		/*llena con lineas en blanco las lineas que no vienen con productos*/
		$cant_lineas=CANT_LINEAS_GUIA;		
		$tplTmp->set_block("main", "Bloque_Espacio", "PBL_GuiaE");
		$j=0;
		if($cont<$cant_lineas){
			$lineasblancas=($cant_lineas-$cont);
			while($j<($lineasblancas-1)){
				$tplTmp->set_var("lineablanca","\r");
				$tplTmp->parse("PBL_GuiaE", "Bloque_Espacio", true); 
				$j++;
			}
		}else{
			$tplTmp->parse("PBL_GuiaE", "Bloque_Espacio", true); 
		}
	}
	$tplTmp->parse("DETALLEGUIA", "main", true);
	/*para la codificacion de los caracteres extraños*/
	$FromCharset = "iso-8859-1";
	$ToCharset   = "cp437";
	$Entities	 = 0;
	$archivo_gen = $tplTmp->subst("DETALLEGUIA");
	$NewEncoding = new ConvertCharset;
	$NewArchivo_gen = $NewEncoding->Convert($archivo_gen, $FromCharset, $ToCharset, $Entities);
	$archivo=$ot_id.'_'.$sec.'_'.DATE('Ymdhis').'.txt';
	/* Genera el archivo con datos*/
	if ($file = fopen( DIR_IMPRESION_GUIA.$archivo,"a")) { 
		fputs ($file, $NewArchivo_gen);
		fclose($file); 
		$archivo_final='Final'._.$ot_id.'_'.DATE('Ymdhi').'.txt';
		if ($filef = fopen( DIR_IMPRESION_GUIA.$archivo_final,"a")) { 
			fputs ($filef, $NewArchivo_gen);
			fclose($filef); 
		}
	}
	return $archivo_final;
}

function ImprimirApplet($ARR){
	global $ses_usr_id;
	$this1 = new Template();
	$this1->debug = 0;
	$this1->set_root(DIRTEMPLATES);
	$this1->set_unknowns("remove");

	$this1->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
	$this1->set_var("TEXT_TITULO",TEXT_TITULO);
	$this1->set_var("SUBTITULO1",TEXT_2);
	$this1->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
	// Agregamos el main
	$this1->set_file("main","monitor_despacho/print_applet.htm");
	$ot_id=$ARR['ot_id'];
	$archivo =$ARR['archivo'];
	$secuencia=$ARR['arrSecuencia'];
	$Fecha=$ARR['arrFecha'];
	$impresora= Obtiene_Impresora($ses_usr_id);
	$this1->set_var("impresora",($impresora!='')?$impresora:IMPRESORA);


	$this1->set_var("URL_CPROY",URL_CPROY);
	$cant_prod=CantidadProductos($ot_id);
	$this1->set_var('cant_prod',$cant_prod);

	$cant_detalle=CantidadDetalles($ot_id);
	$cant_lineas=CANT_LINEAS_GUIA;
	if ($cant_detalle>$cant_lineas){
		$total = ceil($cant_detalle/$cant_lineas);
		$this1->set_var("cant_doc",$total);
	}else {
		$this1->set_var("cant_doc",1);
		$total=1;
	}
	$this1->set_var("URL_DESPD",URL_DESPD);
	$this1->set_var("ot_id",$ARR['ot_id']);
	$this1->set_var("archivo",$ARR['archivo']);

	// Recuperamos informacion de Productos 
	$query = "SELECT distinct os.id_os, od.ot_id,cl.clie_telcontacto1, cl.clie_nombre,cl.clie_paterno,cl.clie_materno,cl.clie_rut,dire_direccion,comu_nombre,l.nom_local FROM os
	JOIN direcciones	d on d.id_direccion	= os.id_direccion
	JOIN comuna			c on c.id_comuna	= d.id_comuna
	JOIN clientes		cl on cl.clie_rut	= os.clie_rut
	JOIN os_detalle		od on od.id_os	= os.id_os
	JOIN locales		l  on os.id_local=l.id_local 
	WHERE od.ot_id=".$ot_id;
	$this1->set_block("main", "Bloque_guias_applet", "PBL_Modulos_a");
	if ( $rq = tep_db_query($query) ){
	$res = tep_db_fetch_array( $rq );
		foreach ($secuencia as $key=>$value) {
			$this1->set_var("Crut_a",($res['clie_rut'])?$res['clie_rut']."-".dv($res['clie_rut']):'');
			$this1->set_var("ot_id",($ot_id>0)?$ot_id:'');
			$this1->set_var("Ctelefono_a",$res['Ctelefono_a']);
			$this1->set_var("Cnombre_a",$res['clie_nombre'].' '.$res['clie_paterno'].' '.$res['clie_materno']);
			$this1->set_var("Cdireccion_a",$res['dire_direccion']);
			$this1->set_var("nombre_comuna_a",$res['comu_nombre']);
			$local=$res['nom_local'];
			$this1->set_var("secuencia_a",$secuencia[$key]);
			$this1->set_var("ot_id",$ot_id);
			$this1->parse("PBL_Modulos_a", "Bloque_guias_applet", true);   
		}
	}
	$this1->set_var('id_os',$id_os);
	$this1->set_var('nombre_local',$local);

	$this1->pparse("OUT_H", array("header"), true);
	$this1->parse("OUT_M", array("main"), true);
	$this1->p("OUT_M");
	return $archivo;
}

/**********************************************************************************************/

switch($accion){
	case "ImprimirPopupGuia":
		ImprimirPopupGuia( array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS));
	break;
	case "print_guia":
		imprimir_guia( array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS));
	break;
	case "applet":
		ImprimirApplet( array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS) );
	break;
	default:
		writelog('NO VIENE ACCION');
	break;


}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>