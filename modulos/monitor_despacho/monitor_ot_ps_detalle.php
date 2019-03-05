<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";
include "includes/funciones_despacho.php";
require_once('wsgdclient.php');
/**********************************************************************************************/
function digiVer($r){
    $s=1;for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
	return chr($s?$s+47:75);
}

/*verifica si la OT no es venta de patio*/


function os_patio($id_ot){
	$queryos_patio="SELECT distinct OS.id_os,OS.origen
        FROM os_detalle OD
        join os OS on (OD.id_os=OS.id_os)
        where OD.ot_id=$id_ot";
        $rq_1    = tep_db_query($queryos_patio);
        $res_1   = tep_db_fetch_array( $rq_1 );
        if($res_1['origen']=='P'){
                return $res_1['origen'];
        }
}
/****************************************/

/****************************************************************
 *
 * Despliega detalle PG por despachar, NO de por pickear
 *
 ****************************************************************/
function DisplayDetalle(){	
	
    global $ses_usr_id;
    global $id_ot,$tipoDes,$id_estado,$esta_tipo,$transicion,$destino,$ot_tipo,$accion;       
        $valororigen =os_patio($id_ot);
        if($valororigen =='P'){
        insertahistorial('Usuario '.get_nombre_usr( $ses_usr_id ).' Intenta OT ver '.$id_ot.' con origen en venta patio ');
        ?>
        <script language="JavaScript">
        window.alert("Nó se pueden revisar OT con origen en CPE");
        location.href='monitor_despacho.php';
        </script>
       <?php
        }

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
    $MiTemplate->set_file("main","monitor_despacho/monitor_ot_ps_detalle.htm");

	//variables globales
	$MiTemplate->set_var('esta_tipo',tohtml($esta_tipo));
	$MiTemplate->set_var('id_estado',tohtml($id_estado));
	$MiTemplate->set_var('tipoDes',tohtml($tipoDes));

// para cambiar los estados
	$MiTemplate->set_var('esta_tipo',tohtml($esta_tipo));
	$MiTemplate->set_var('tipoDes',tohtml($tipoDes));
	if ($transicion) {
		if ($transicion!='Dividir'){
		   cambia_estado($id_ot,$destino,$tipoDes,$id_estado,$esta_tipo,$accion,$ot_tipo);
		}
	}
    $query = "	SELECT ot_id, id_tipodespacho,id_os, ot_fechacreacion, e.esta_tipo,e.esta_nombre, ot_tipo,e.id_estado
				FROM ot JOIN estados e on e.id_estado = ot.id_estado
				WHERE ot.ot_id = $id_ot ";
				
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );
	$id_os = $res['id_os'];
	$esta_tipo=$res['esta_tipo'];
	$tipoDes=$res['id_tipodespacho'];
	$ot_tipo=$res['ot_tipo'];
	$id_estado=$res['id_estado'];
	$MiTemplate->set_var('esta_tipo',tohtml($esta_tipo));
	$MiTemplate->set_var('ot_tipo',tohtml($ot_tipo));
	$MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
	$MiTemplate->set_var('tipo_ot',tohtml( $res['ot_tipo']));
	$MiTemplate->set_var('tipoDes',tohtml( $res['id_tipodespacho']));
	$MiTemplate->set_var('ot_tipo',($res['ot_tipo']=='PE')?$res['ot_tipo'] . " - Producto Especial":$res['ot_tipo'] . " - Producto Stock");
	$MiTemplate->set_var('ot_fechacreacion',fecha_db2php(tohtml( $res['ot_fechacreacion'])));
	$MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));

	// Query de la OS
    $query = "SELECT os.id_direccion, cl.clie_rut as clie_rut, clie_nombre,clie_tipo, clie_razonsocial,clie_paterno, clie_materno, clie_telefonocasa, 
			 clie_telcontacto1, clie_telcontacto2 
			 FROM os 
			 JOIN clientes cl on cl.clie_rut= os.clie_rut
			 WHERE id_os = $id_os ";
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );
	$clie_rut = $res['clie_rut'];
	$MiTemplate->set_var('id_direccion',$res['id_direccion']);
	$MiTemplate->set_var('id_os',tohtml( $id_os));
	// Datos cliente
	 if ($res['clie_tipo']=='e'){
     $MiTemplate->set_var("empresa","Empresa");
     $MiTemplate->set_var("clie_razonsocial",$res['clie_razonsocial']);	 
	 }
	//$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']) . "-" . digiVer($res['clie_rut']));
	$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']));
	$MiTemplate->set_var('clie_nombre',tohtml( $res['clie_nombre']));
	$MiTemplate->set_var('clie_paterno',tohtml( $res['clie_paterno']));
	$MiTemplate->set_var('clie_materno',tohtml( $res['clie_materno']));

	// Query de la despaccho
    $queryDes = "SELECT distinct os.id_local,os.id_direccion ,dire_nombre,dire_direccion, dire_telefono, dire_observacion, NE.DESCRIPTION AS comu_nombre ,t.nombre
				FROM os  
				JOIN direcciones	D on D.id_direccion	= os.id_direccion				
				LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
				JOIN clientes		cl on cl.clie_rut	= os.clie_rut
				JOIN os_detalle		od on od.id_os	= os.id_os
				JOIN tipos_despacho t on t.id_tipodespacho = od.id_tipodespacho 
	WHERE os.id_os= ".$id_os." and od.id_tipodespacho=".$tipoDes;

	$rq = tep_db_query($queryDes);
	$resD = tep_db_fetch_array( $rq );

	// Datos Dirección despacho
	$MiTemplate->set_var('diredes_nombre',tohtml( $resD['dire_nombre']));
	$MiTemplate->set_var('diredes_direccion',tohtml( $resD['dire_direccion']));
	$MiTemplate->set_var('diredes_telefono',tohtml( $resD['dire_telefono']));
	$MiTemplate->set_var('diredes_observacion',tohtml( $resD['dire_observacion']));
	$MiTemplate->set_var('diredes_comuna',tohtml( $resD['comu_nombre']));
	$MiTemplate->set_var('tipodespacho',tohtml( $resD['nombre']));


	// Query de la direccion Principal del Cliente
    $query= "SELECT dire_nombre, dire_direccion, dire_telefono, dire_observacion, NE.DESCRIPTION AS comu_nombre
			FROM direcciones D
			LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
			WHERE clie_rut = $clie_rut AND dire_defecto = 'p' ";

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	// Direccion del cliente
	$MiTemplate->set_var('dirp_nombre',tohtml( $res['dire_nombre']));
	$MiTemplate->set_var('dirp_direccion',tohtml( $res['dire_direccion']));
	$MiTemplate->set_var('dirp_telefono', $res['dire_telefono']);
	$MiTemplate->set_var('dirp_observacion',tohtml( $res['dire_observacion']));
	$MiTemplate->set_var('dirp_comuna',tohtml( $res['comu_nombre']));

	// Query del detalle de la OT
    $MiTemplate->set_block("main", "LISTADO_OT", "BLO_detalle");
    $query = "SELECT cod_barra, id_os_detalle, cod_sap, osde_descripcion, osde_precio, osde_cantidad, osde_tipoprod, osde_especificacion 
	FROM os_detalle osd
		JOIN tipos_despacho t on t.id_tipodespacho = osd.id_tipodespacho WHERE ot_id = $id_ot ";
    //query_to_set_var( $query, $MiTemplate, 1, 'BLO_detalle', 'LISTADO_OT' );

	$resultado_Det = tep_db_query($query);
	
	if($id_estado=='PG'){	
		$CantiOTMsg = "<td width='105'> Cantidad OT Nueva </td>";
		$MiTemplate->set_var('CantiOTMsg', $CantiOTMsg);
	}
	$contar =0;
	
	$quer = "SELECT  COUNT( id_os_detalle) AS numero FROM os_detalle WHERE ot_id = $id_ot ";
	$resul = tep_db_query($quer);
	$numero = tep_db_fetch_array( $resul);
	
	while( $result = tep_db_fetch_array( $resultado_Det ) ) 
	{
		$MiTemplate->set_var('cod_barra',$result['cod_barra']);
		$MiTemplate->set_var('cod_sap',$result['cod_sap']);
		
		$MiTemplate->set_var('osde_descripcion',$result['osde_descripcion']);
		
		$MiTemplate->set_var('osde_precio',$result['osde_precio']);
		
		//echo "Ot_id: ",  $result['id_os_detalle'], "<br>";
		
		
		
		if($id_estado=='PG')
		{
		$cantidadACT_Old = "<input name='CActOld" . $result['id_os_detalle'] . "' id='CActOld" . $result['id_os_detalle'] . "' type='hidden' size='8' value='" . $result['osde_cantidad'] . "' ReadOnly >";
		$MiTemplate->set_var('osde_cantidadOld', $cantidadACT_Old);	
		
		$cantidadACT = "<input name='CAct" . $result['id_os_detalle'] . "' id='CAct" . $result['id_os_detalle'] . "' type='text' size='8' value='" . $result['osde_cantidad'] . "' ReadOnly >";
		$MiTemplate->set_var('osde_cantidad', $cantidadACT);
		}
		else{
			$MiTemplate->set_var('osde_cantidad',$result['osde_cantidad']);
		}
		if($id_estado=='PG')
		{			
		$cantidadOT = "<input onBlur='restar(this, " . $numero['numero'] . ");' name='Cnueva" . $result['id_os_detalle'] . "' id='Cnueva" . $result['id_os_detalle'] . "' type='text' size='8' value='0'>";
		$MiTemplate->set_var('cantidadOT',$cantidadOT);
		$idCantidad = 'Cnueva' . $result['id_os_detalle'];		
		$scriptCant = "window.document.detalle." . $idCantidad  . ".onkeypress = KeyIsNumber;"; 
		$MiTemplate->set_var('scriptCant', $scriptCant);
		}
		
		$MiTemplate->parse("BLO_detalle", "LISTADO_OT", true);
		$contar ++; 
	}
	
	$MiTemplate->set_var('minumero',$contar); 
    
	//para los botones del final
	$query=Botones($id_estado,$esta_tipo,$tipoDes,$ot_tipo);

	$MiTemplate->set_block("main", "botones", "PBbotones");
		if ( $rq1 = tep_db_query($query) ){
			while( $res2 = tep_db_fetch_array( $rq1 ) ) {
	if (($res2['id_estado_destino']!='EE')&&($res2['id_estado_origen']!='ED')){
			$MiTemplate->set_var('destino',tohtml( $res2['destino']));
			$MiTemplate->set_var('desc_transicion',tohtml( $res2['desc_transicion']));
			$MiTemplate->parse("PBbotones", "botones", true);
	}
			}
		}

	//Deja los Botones de Imprimir Guia según el estado
	if (($id_estado=='PG')||($id_estado=='EG') || ($id_estado=='ER')){
		$Imprimir_Guia="<input type='button' name='Button' value='Imprimir Guia' onClick='PrintGuia();' style='color: rgb(255, 0, 0); display:compact;'/>";
		$MiTemplate->set_var('Imprimir_Guia',$Imprimir_Guia);
		$MiTemplate->set_var('ot',$id_ot);
	}
	
	if($id_estado=='PG')
	{
		$dividir_OT = "<input name='dividir' id='dividir' type='button' value='Dividir OT' OnClick='enviar_form($id_ot);' disabled='true' >";
		$MiTemplate->set_var('dividir_OT',$dividir_OT);
	}
	
	if (($id_estado=='PN')){
		$Imprimir_Guia="<input type='button' name='Button' value='Imprimir OT' onClick='PrintOT();' style='color: rgb(255, 0, 0); display:compact;' />";
		$MiTemplate->set_var('Imprimir_Guia',$Imprimir_Guia);
		$MiTemplate->set_var('ot',$id_ot);
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
 * Despliega detalle "POR pickear"
 *
 ****************************************************************/
function DisplayPickear(){

    global $ses_usr_id;
    global $id_ot,$accion1,$accion2,$id,$key,$cp,$tipoDes,$id_estado,$esta_tipo,$accion,$cantpickeada,$indices,$cantidad,$destino,$transicion,$ot_tipo;
	
	
        $valororigen =os_patio($id_ot);
        if($valororigen =='P'){
        insertahistorial('Usuario '.get_nombre_usr( $ses_usr_id ).' Intenta OT ver '.$id_ot.' con origen en venta patio ');
        ?>
        <script language="JavaScript">
        window.alert("Solo se pueden revisar OT con origen en CPE");
        location.href='monitor_despacho.php' ;
        </script>
       <?
        }

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
    $MiTemplate->set_file("main","monitor_despacho/monitor_ot_ps_pickear.htm");

	///*********************************para aciones del WS******************************/
	if ($accion2=="WS"){
	$wml_desp=Datos_ws($id_ot);
	writelog('DATOS WS '.$wml_desp);
	//writelog('$wml_desp '.$wml_desp );
	$resultado_ws = envia_nueva_gd($wml_desp,$msgerror);
	writelog('RESULTADO '.$resultado_ws);
	if (!$resultado_ws){
		//error
		?><script language="JavaScript">
        window.alert("Existe un problema al enviar la Orden de Despacho por favor comuniquese con la mesa de ayuda : \n\t<?=$msgerror?>");
        </script>
		<?
		insertahistorial('No se ha generado la orden de Despacho en el sistema de Despacho a domicilio, para la Orden de Trabajo nº'.$id_ot.'-'.$msgerror);	
		}
		//ok 
		if ($resultado_ws){
			insertahistorial('Se ha generado la orden de Despacho nº'. $resultado_ws .' en el sistema de Despacho a domicilio');
			cambia_estado($id_ot,'ED',$tipoDes,$id_estado,$esta_tipo,$accion,$ot_tipo);
		}
	}	
	/* cuando es retira cliente*/
	if ($accion2=="cerrarPick"){
		cambia_estado($id_ot,'PG',$tipoDes,$id_estado,$esta_tipo,$accion,$ot_tipo);
	}


	///********************************Fin  aciones del WS******************************/
	//accion de updatear las cantidades pickeadas
	if (($accion=="upd")){
		foreach ($cantpickeada as $key=>$value) { 
			$cant=$cantpickeada[$key];
			$pick=$value;
			$indi=$indices[$key];
			$auxPick=$value;
			$auxCant=$cant;
			$sobra=($pick-$cant);
			$falta=($cant-$pick);
			$query_up="UPDATE os_detalle SET cant_pickeada =".($auxPick)."  WHERE id_os_detalle =".($indi);
			tep_db_query($query_up);
		}
	if($accion2=='Dividir'){
		/*revisa el caso que sea retira cliente ocupa la variable el la linea 269*/
		$queryDespacho="select id_tipodespacho from ot where ot_id=".($id_ot+0);
		$rq = tep_db_query($queryDespacho);
		$res = tep_db_fetch_array( $rq );
		$TipoidDespacho=$res['id_tipodespacho'];
		/********/
		$queryins="INSERT INTO ot (id_tipodespacho,id_estado,id_os,ot_tipo,ot_fechacreacion, ot_freactivacion) select id_tipodespacho,id_estado,id_os,ot_tipo,now(), now() as ot_fechacreacion  from ot where ot_id=".($id_ot+0); 
		tep_db_query($queryins);
		$ultimoID = tep_db_insert_id('');
		$query_sel="select id_os_detalle,ot_id,id_origen,id_tipodespacho,id_os,osde_tipoprod ,osde_subtipoprod,osde_precio,osde_cantidad,cant_pickeada,cod_sap,osde_descripcion,id_producto,cod_barra  from os_detalle where ot_id=".($id_ot+0);
		if ( $rq = tep_db_query($query_sel) ){
			while( $res = tep_db_fetch_array( $rq ) ) {
				/*si son ceros*/
				$id_os=$res['id_os'];
			if($res['cant_pickeada']==0.00){
				$qryIn="INSERT INTO os_detalle (ot_id,id_origen,id_tipodespacho,id_os,osde_tipoprod ,osde_subtipoprod,osde_precio,osde_cantidad,cant_pickeada,cod_sap,osde_descripcion,id_producto,cod_barra) values (".($ultimoID+0).",".($res['id_origen']+0).",".($res['id_tipodespacho']+0).",".($res['id_os']+0).",'".($res['osde_tipoprod'])."' ,'".($res['osde_subtipoprod'])."',".($res['osde_precio']+0).",".($res['osde_cantidad']).",0,'".($res['cod_sap'])."' ,'".($res['osde_descripcion'])."',".($res['id_producto'])." ,'".($res['cod_barra'])."')";
			
				tep_db_query($qryIn);
				$qrydel="DELETE FROM os_detalle WHERE id_os_detalle=".$res['id_os_detalle']. " and ot_id=".($id_ot+0);
			
				tep_db_query($qrydel);
			}
			if(($res['cant_pickeada']!=0.00)&&($res['cant_pickeada']!=$res['osde_cantidad'])){
				$resto=($res['osde_cantidad']-$res['cant_pickeada']);
				$qryIns="INSERT INTO os_detalle (ot_id,id_origen,id_tipodespacho,id_os,osde_tipoprod ,osde_subtipoprod,osde_precio,osde_cantidad,cant_pickeada,cod_sap,osde_descripcion,id_producto,cod_barra) values (".($ultimoID+0).",".($res['id_origen']+0).",".($res['id_tipodespacho']+0).",".($res['id_os']+0).",'".($res['osde_tipoprod'])."' ,'".($res['osde_subtipoprod'])."',".($res['osde_precio']+0).",".($resto).",0,'".($res['cod_sap'])."' ,'".($res['osde_descripcion'])."',".($res['id_producto'])." ,'".($res['cod_barra'])."')";
				tep_db_query($qryIns);
	
				$query_u="UPDATE os_detalle SET osde_cantidad =".($res['cant_pickeada'])." WHERE id_os_detalle =".(($res['id_os_detalle'])+0);
		
				tep_db_query($query_u);
			}
				$query_Estado="UPDATE ot SET id_estado ='".($esta_tipo)."' WHERE ot_id =".(($res['ot_id'])+0);
				tep_db_query($query_Estado);
			if($res['cant_pickeada']==$res['osde_cantidad']){
				$query_Estado="UPDATE ot SET id_estado ='".($esta_tipo)."' WHERE ot_id =".(($res['ot_id'])+0);
				tep_db_query($query_Estado);
			}
		}
		/*while*/
			insertahistorial("OT $id_ot se ha dividido y generó la OT $ultimoID, a partir de la OS $id_os",$id_os);
		if ($TipoidDespacho!=3){
			/***** se genera order de despacho para la ot dividida que quedo en estado por despachar *****/
				$wml_desp=Datos_ws($id_ot);
				$resultado_ws = envia_nueva_gd($wml_desp,$msgerror);
				if (!$resultado_ws){
				//error
				?><script language="JavaScript">
				window.alert("Existe un problema al enviar Desapacho: \n\t<?=$msgerror?>");
				</script>
				<?
				insertahistorial('No se ha generado la orden de Despacho en el sistema de Despacho a domicilio, para la Orden de Trabajo nº'.$id_ot.'-'.$msgerror);	
				}
				//ok 
				if ($resultado_ws){
					insertahistorial('Se ha generado la orden de Despacho nº'. $resultado_ws .' en el sistema de Despacho a domicilio');
					//cambia_estado($id_ot,'ED',$tipoDes,$id_estado,$esta_tipo,$accion,$ot_tipo);
				}
		}#tipodespacho 
		else{
			cambia_estado($id_ot,'PG',$tipoDes,$id_estado,$esta_tipo,$accion,$ot_tipo);
		}
			insertahistorial("OT $ultimoID se creo en estado Por Pickear",$id_os); 
		?>
        <script language="JavaScript">
            document.location = 'monitor_ot_ps_detalle.php?id_ot=<?=$ultimoID+0?>&tipoDes=<?=$tipoDes?>&id_estado=<?=PC?>';
        </script>
		<?
        tep_exit();
	  }/*if*/
	 }
	}//dividir


if ($transicion) {
	if ($transicion!='Dividir'){
	   cambia_estado($id_ot,$destino,$tipoDes,$id_estado,$esta_tipo,$accion,$ot_tipo);
	}

}
   // Query de la OT
    $query = "	SELECT ot_id, id_tipodespacho,id_os, ot_fechacreacion, e.esta_tipo,e.esta_nombre, ot_tipo,e.id_estado
				FROM ot JOIN estados e on e.id_estado = ot.id_estado
				WHERE ot.ot_id = $id_ot ";
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );
	$id_os = $res['id_os'];
	$esta_tipo=$res['esta_tipo'];
	$tipoDes=$res['id_tipodespacho'];
	$ot_tipo=$res['ot_tipo'];
	$id_estado=$res['id_estado'];
	$MiTemplate->set_var('esta_tipo',tohtml($esta_tipo));
	$MiTemplate->set_var('ot_tipo',tohtml($ot_tipo));
	$MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
	$MiTemplate->set_var('tipo_ot',tohtml( $res['ot_tipo']));
	$MiTemplate->set_var('tipoDes',tohtml( $res['id_tipodespacho']));
	$MiTemplate->set_var('ot_tipo',($res['ot_tipo']=='PE')?$res['ot_tipo'] . " - Producto Especial":$res['ot_tipo'] . " - Producto Stock");
	$MiTemplate->set_var('ot_fechacreacion',fecha_db2php(tohtml( $res['ot_fechacreacion'])));
	$MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));

	// Query de la OS
    $query = "SELECT os.id_local,os.id_direccion, cl.clie_rut as clie_rut, clie_nombre,clie_tipo, clie_razonsocial,clie_paterno, clie_materno, clie_telefonocasa, 
			 clie_telcontacto1, clie_telcontacto2 
			 FROM os 
			 JOIN clientes cl on cl.clie_rut= os.clie_rut
			 WHERE id_os = $id_os ";
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );
	$clie_rut = $res['clie_rut'];

	//nombre local//
	$qerylocal="SELECT nom_local FROM locales where id_local=". $res['id_local'];
	$rq = tep_db_query($qerylocal);
	$resL = tep_db_fetch_array( $rq );
	$MiTemplate->set_var('nom_local',$resL['nom_local']);


	$MiTemplate->set_var('id_direccion',$res['id_direccion']);
	$MiTemplate->set_var('id_os',tohtml( $id_os));
	// Datos cliente
	 if ($res['clie_tipo']=='e'){
     $MiTemplate->set_var("empresa","Empresa");
     $MiTemplate->set_var("clie_razonsocial",$res['clie_razonsocial']);	 
	 }
	//$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']) . "-" . digiVer($res['clie_rut']));
	$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']));
	$MiTemplate->set_var('clie_nombre',tohtml( $res['clie_nombre']));
	$MiTemplate->set_var('clie_paterno',tohtml( $res['clie_paterno']));
	$MiTemplate->set_var('clie_materno',tohtml( $res['clie_materno']));
	
	// Datos cliente
	if ($res['clie_tipo']=='e'){
		$MiTemplate->set_var("empresa","Empresa");
		$MiTemplate->set_var("clie_razonsocial",$res['clie_razonsocial']);	 
	}
	//$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']) . "-" . digiVer($res['clie_rut']));
	$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']));
	$MiTemplate->set_var('clie_nombre',tohtml( $res['clie_nombre']));
	$MiTemplate->set_var('clie_paterno',tohtml( $res['clie_paterno']));
	$MiTemplate->set_var('clie_materno',tohtml( $res['clie_materno']));

// Query de la despaccho
    $queryDes = "SELECT distinct os.id_direccion ,dire_nombre,dire_direccion, dire_telefono, dire_observacion, NE.DESCRIPTION AS comu_nombre ,t.nombre,t.id_tipodespacho
	FROM os  
				JOIN direcciones	D on D.id_direccion	= os.id_direccion
				LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
				JOIN clientes		cl on cl.clie_rut	= os.clie_rut
				JOIN os_detalle		od on od.id_os	= os.id_os
				JOIN tipos_despacho t on t.id_tipodespacho = od.id_tipodespacho 
	WHERE os.id_os=".($id_os+0)." and od.id_tipodespacho=".$tipoDes;
	$rq = tep_db_query($queryDes);
	$resD = tep_db_fetch_array( $rq );

	// Datos Dirección despacho
	$MiTemplate->set_var('diredes_nombre',tohtml( $resD['dire_nombre']));
	$MiTemplate->set_var('diredes_direccion',tohtml( $resD['dire_direccion']));
	$MiTemplate->set_var('diredes_telefono', $resD['dire_telefono']);
	$MiTemplate->set_var('diredes_observacion',tohtml( $resD['dire_observacion']));
	$MiTemplate->set_var('diredes_comuna',tohtml( $resD['comu_nombre']));
	$MiTemplate->set_var('tipodespacho',tohtml( $resD['nombre']));
	$MiTemplate->set_var('id_tipodespacho',tohtml( $resD['id_tipodespacho']));
	// Query de la direccion Principal del Cliente
    $query= "SELECT dire_nombre, dire_direccion, dire_telefono, dire_observacion, NE.DESCRIPTION AS comu_nombre
            FROM direcciones D
			LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 	
			WHERE clie_rut = $clie_rut	AND dire_defecto = 'p' ";

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	// Direccion del cliente
	$MiTemplate->set_var('dirp_nombre',tohtml( $res['dire_nombre']));
	$MiTemplate->set_var('dirp_direccion',tohtml( $res['dire_direccion']));
	$MiTemplate->set_var('dirp_telefono',$res['dire_telefono']);
	$MiTemplate->set_var('dirp_observacion',tohtml( $res['dire_observacion']));
	$MiTemplate->set_var('dirp_comuna',tohtml( $res['comu_nombre']));

	// Query del detalle de la OT

    $MiTemplate->set_block("main", "OSDETALLE", "BLO_detalle");
	$query_OD = "SELECT id_os_detalle, cod_barra, cod_sap, osde_descripcion, osde_precio, osde_cantidad, osde_tipoprod,
	cant_pickeada, osde_especificacion
	,if (osde_cantidad < cant_pickeada,osde_cantidad,cant_pickeada)cant_pickeada
	,if(((osde_cantidad=cant_pickeada) or (cant_pickeada>osde_cantidad)), 'checked', '') 'checked' 
	,if(((osde_cantidad=cant_pickeada)or(cant_pickeada>osde_cantidad)), 'disabled', '') 'disabled'
	 FROM os_detalle osd
		JOIN tipos_despacho t on t.id_tipodespacho = osd.id_tipodespacho 
	WHERE ot_id = $id_ot ";
    query_to_set_var( $query_OD, $MiTemplate, 1, 'BLO_detalle', 'OSDETALLE' ); 

	//para los botones del final
	$query=Botones($id_estado,$esta_tipo,$tipoDes,$ot_tipo);
	$MiTemplate->set_block("main", "botones", "PBbotones");
		if ( $rq1 = tep_db_query($query) ){
			while( $res2 = tep_db_fetch_array( $rq1 ) ) {
				$MiTemplate->set_var('destino',tohtml( $res2['destino']));
				$MiTemplate->set_var('desc_transicion',tohtml( $res2['desc_transicion']));
				$MiTemplate->parse("PBbotones", "botones", true);
			}
		}
	
	$MiTemplate->set_var('id_estado',tohtml($id_estado));
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
function Update(){
	global $idosde, $precio, $costo, $obs;

	$query = "UPDATE os_detalle
			  SET    osde_preciocosto	= '$costo',
					 osde_precio		= '$precio'
			  WHERE  id_os_detalle		= $idosde
			  ";

	if (!tep_db_query($query))
		echo "error: ".$query;
	else
	 header( "Location: index.php" );
}



function DividirOT()
{
	
	//echo "<br><br><br>";
	
	$id_ot = $_REQUEST['id_ot'];
	$consulta = "SELECT id_os FROM ot WHERE ot_id = $id_ot";
	$resultado = tep_db_query($consulta);
	$result = tep_db_fetch_array($resultado);
	
	$Query_Div = "INSERT INTO ot(id_tipodespacho, id_estado,       id_os,          ot_tipo,  ot_fechacreacion, ot_freactivacion, desp_ddp,  statusprint, numero_impresiones,  numero_impresionesGuia)
	                     VALUES (       3,           'PG', " . $result['id_os'] . ", 'PS',             now(),       now(),          0,           1,                0,                0);";
	
	$result_dividir = tep_db_query($Query_Div);
	$Id = mysql_insert_id();
	
	
	$consulta = "SELECT id_os_detalle FROM os_detalle WHERE id_os = (SELECT id_os  FROM ot WHERE ot_id = $id_ot) AND id_tipodespacho = 3 AND osde_tipoprod = 'PS' ";
	$resultado = tep_db_query($consulta);
	while ($res_ID = tep_db_fetch_array($resultado))
	{
	$Nombre = 'Cnueva' . $res_ID['id_os_detalle']; 	
	$Actual = 'CAct' . $res_ID['id_os_detalle'];
	
	
		if($_POST[$Nombre] != 0)
		{		
		$consulta_Det = "SELECT * FROM os_detalle WHERE id_os_detalle = " . $res_ID['id_os_detalle'];
		$result_Det = tep_db_query($consulta_Det);
		$detalle = tep_db_fetch_array($result_Det);
		
		$Query_Det = "INSERT INTO os_detalle (ot_id,             id_origen,                        id_tipodespacho,                      id_os,                    osde_tipoprod,                 osde_subtipoprod,                        osde_precio,                 osde_cantidad,                 cod_sap,                    osde_descripcion,                    id_producto,                       cod_barra,                       ind_dec)
		                            VALUES (" . $Id . ", " . $detalle['id_origen'] . ", " . $detalle['id_tipodespacho'] . ", " . $detalle['id_os'] . ", '" . $detalle['osde_tipoprod'] . "', '" . $detalle['osde_subtipoprod'] . "', " . $detalle['osde_precio'] . ", " . $_POST[$Nombre] . ", " . $detalle['cod_sap'] . ", '" . $detalle['osde_descripcion'] . "', " . $detalle['id_producto'] . ", " . $detalle['cod_barra'] . ", " . $detalle['ind_dec'] . ")";
		
		//echo $Query_Det, "<br><br>";
		$result_Detalle = tep_db_query($Query_Det);		
		
		$actualizar = "UPDATE  os_detalle SET osde_cantidad = " . $_POST[$Actual] . " WHERE id_os_detalle = " . $res_ID['id_os_detalle'];
		$result_act = tep_db_query($actualizar);
		
		$actualizar_OT = "UPDATE  ot SET id_estado = 'EM' WHERE ot_id = " . $id_ot;
		$result_OT = tep_db_query($actualizar_OT);
		}
	}
	
	
}

/**********************************************************************************************/
switch($id_estado){
/*Por Pickear */
	case "PC":
	     DisplayPickear();
		break;
	case "upd":
		Update();
		break;	
	case "Dividir":
		DividirOT();
	default:
		DisplayDetalle();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
