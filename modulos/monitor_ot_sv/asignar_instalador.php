<?php
//$pag_ini = '../monitor_ot_pe/monitor_ot_pe_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";

// *************************************************************************************
$qry=" select o.id_os,date_format(ot_fechacreacion, '%d/%m/%Y ')ot_fechacreacion ,e.esta_nombre,t.id_estado as origen
	from os_detalle o join ot t on (o.id_os=t.id_os) 
	join estados e on (e.id_estado=t.id_estado)
	where t.ot_id=" . ($id_ot + 0) ;
$rq = tep_db_query($qry);
$res = tep_db_fetch_array( $rq );
$id_os=$res['id_os'];
$estado=$res['esta_nombre'];
$fecha_creacion=$res['ot_fechacreacion'];
$origen=$res['origen'];
$origen_n=$res['esta_nombre'];

/***********************/
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("id_ot",$id_ot);
$MiTemplate->set_var("select_inst",$select_inst);
$MiTemplate->set_var("destino",$destino);
$MiTemplate->set_var("origen",$origen);
$MiTemplate->set_var("inst_rut",$inst_rut);
$MiTemplate->set_var("ot_freactivacion",$ot_freactivacion);
$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . "Asignar Instalador");
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
// Agregamos el main
$MiTemplate->set_file("main","monitor_ot_sv/asignar_instalador.htm");

$existeinst=false;
$ot_instalador=0;
//para la seleccion del select 
$qsel ="SELECT O.id_instalador, date_format(O.ot_freactivacion, '%d/%m/%Y') AS  ot_freactivacion, O.ot_comentario, I.id_empresa_instaladora
		FROM  ot O
		INNER JOIN  instaladores I ON  O.id_instalador = I.id_instalador
		WHERE  ot_id = ".($id_ot+0);

	$rq = tep_db_query($qsel);
	$res = tep_db_fetch_array( $rq );
	if ($res['id_instalador']){
			$ot_instalador_sel = $res['id_instalador'];
			$id_empresa_instaladora = $res['id_empresa_instaladora'];			
	}
	$MiTemplate->set_var("ot_freactivacion",$res['ot_freactivacion']);
	$MiTemplate->set_var("ot_comentario",$res['ot_comentario']);

// verificar si el instalador existe
// caso uno $inst_rut= rut $inst_selec== inr  fue ingresado el rut";
$inst_rut=intval($inst_rut/10);

//if ($select_inst=='inr')
//$where_aux1=" where inst_rut=" . $inst_rut;

if ($select_inst !='')
$where_aux1=" where id_instalador= ". ($select_inst);

$qbi="SELECT id_instalador, inst_rut FROM instaladores ".$where_aux1;	
	
	
	$rq = tep_db_query($qbi);
	$res = tep_db_fetch_array( $rq );
		if ($res['id_instalador']){
			$ot_instalador=$res['id_instalador'];
			$existeinst=true;
			$ins="id_instalador=".$ot_instalador;
		}
		
	if ($select_inst =='')
		$existeinst=false;

		
/* recuperamos los instaladores correspondientes*/
$qc="select d.id_comuna 
	from ot o
		join os_detalle osd on (o.ot_id  =osd.ot_id  )
		join os OS  on (OS.id_os=osd.id_os  )
		join direcciones d on (OS.id_direccion=d.id_direccion)
	where o.ot_id=".($id_ot+0);
	$rq = tep_db_query($qc);
	$res = tep_db_fetch_array( $rq );
	$comuna=$res['id_comuna'];
	
	/*LISTA DE EMPRESAS INSTALADORAS*/
	
 $MiTemplate->set_block("main", "Empresa", "BLO_empresa");		
	if ($ot_instalador_sel==''){
    	$query = "SELECT id_empresa_instaladora, descripcion FROM empresa_instaladora ORDER BY descripcion   ";		
		}
	else{
		//$query = "SELECT id_empresa_instaladora, descripcion FROM empresa_instaladora ORDER BY descripcion ";

		$query = "SELECT DISTINCT  E.id_empresa_instaladora, descripcion, case WHEN I.id_instalador = $ot_instalador_sel then 'selected' else '' end selected   FROM empresa_instaladora E
				  LEFT JOIN instaladores I ON  E.id_empresa_instaladora =  I.id_empresa_instaladora AND I.id_instalador  = $ot_instalador_sel
				  ORDER BY descripcion";
	}
    query_to_set_var($query, $MiTemplate, 1, 'BLO_empresa', 'Empresa');
    
	
	
	
	$query_Emp = "SELECT id_empresa_instaladora, descripcion FROM empresa_instaladora ORDER BY descripcion ";
	$result = tep_db_query($query_Emp);
	$result_Emp = tep_db_fetch_array($result);

//lista de instaladores, viene seleccionado si es Cambio de instalador
	if ($origen=='VC'){
		$no_existe='';
	}else{
		$no_existe='RUT';
	}
	$MiTemplate->set_block("main", "instalador", "BLO_ins");
	
	if($ot_instalador_sel != '')
	{
	$qry_ins = "SELECT distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno, i.inst_telefono, i.direccion, i.email, 
				if (i.id_instalador='$ot_instalador_sel','selected','$no_existe')selected
				from instaladores i				
				WHERE i.id_empresa_instaladora = $id_empresa_instaladora ";
	}
	else{
		$qry_ins = "SELECT distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno, i.inst_telefono, i.direccion, i.email				
					FROM instaladores i				
					WHERE i.id_empresa_instaladora = " . $result_Emp['id_empresa_instaladora'];	
	}
				
	
	//echo $qry_ins."<br>";
	
    if ( $rq = tep_db_query($qry_ins) ){
		while( $res = tep_db_fetch_array( $rq ) ) {
			$i++;
			if ($res['selected']=='RUT')
				$j++;
			else	
				$s++;
		$MiTemplate->set_var("id_instalador",$res['id_instalador']);
		$MiTemplate->set_var("inst_rut",$res['inst_rut']);
		$MiTemplate->set_var("inst_nombre",$res['inst_nombre']);
		$MiTemplate->set_var("inst_paterno",$res['inst_paterno']);
		$MiTemplate->set_var("inst_materno",$res['inst_materno']);
		$MiTemplate->set_var("inst_telefono",$res['inst_telefono']);
		$MiTemplate->set_var("direccion",$res['direccion']);
		$MiTemplate->set_var("email",$res['email']);
			if($res['id_instalador']==$ot_instalador_sel){
				$MiTemplate->set_var("selected",'selected');
			}else{
				$MiTemplate->set_var("selected",'');
			}
		$MiTemplate->parse("BLO_ins", "instalador", true);
		}
	}

	$qbi="SELECT id_instalador,inst_rut FROM instaladores where id_instalador=".($ot_instalador_sel+0);
	$rq = tep_db_query($qbi);
	$res = tep_db_fetch_array( $rq );
	if($i==$j){
		$MiTemplate->set_var("disabled",'disabled');
		$MiTemplate->set_var("disabledr",'enabled');
		$MiTemplate->set_var("inst_selec",'inr');
		$MiTemplate->set_var("checked",'checked');
		//$MiTemplate->set_var("selected",'');
		$MiTemplate->set_var("inst_rut",$res['inst_rut']);
		//$MiTemplate->set_var('dvr',dv($res["inst_rut"]));
		if($res['inst_rut']){
				$MiTemplate->set_var('rut',$res['inst_rut']);
		}

	}
	else{
		$MiTemplate->set_var("checkedno",'checked');
		$MiTemplate->set_var("disabled",'enabled');
		$MiTemplate->set_var("disabledr",'disabled');
		$MiTemplate->set_var("inst_selec",'ins');
	}

if ($accion){
/*updatiar la ot*/
// Verifica  que se hayan asociado las OD requeridas.
// Verifica si la Ot corresponde a una Visita o INstalacion.
    $tipoOT='';
  	$queryInstala="SELECT COUNT(*) as cont
				  FROM ot OT
				  JOIN os_detalle OD ON OD.id_os= OT.id_os
				  WHERE OT.ot_id=".$id_ot." AND OD.osde_subtipoprod='IN'";
   			$rqInstala = tep_db_query($queryInstala);
			$resInstala = tep_db_fetch_array($rqInstala);
			if($resInstala[cont] != 0)
				$tipoOT='IN';
	$queryVisita="SELECT COUNT(*) as cont
				  FROM ot OT
				  JOIN os_detalle OD ON OD.id_os= OT.id_os
				  WHERE OT.ot_id=".$id_ot." AND OD.osde_subtipoprod='VI'";
			$rqVisita = tep_db_query($queryVisita);
			$resVisita = tep_db_fetch_array($rqVisita);
			if($resVisita[cont] != 0)
				$tipoOT='VI';;

	if($tipoOT == 'IN'){
		$queryVerOSAso="SELECT os_asoVisita, os_asoMaterial FROM ot WHERE ot_id=".$id_ot.";";
		$rqVerOSAso = tep_db_query($queryVerOSAso);
		$resVerOSAso = tep_db_fetch_array($rqVerOSAso);
		if($resVerOSAso['os_asoVisita'] ==0  && $resVerOSAso['os_asoMaterial']==0){
			echo '<SCRIPT language="JavaScript">
				  	alert("Debe Asociar la OS de Instalación y Materiales .");
					window.close();
			      </SCRIPT>';
			exit();
		}
	}
			
// para el nombre del estado 
	$sqn="SELECT distinct esta_nombre,desc_transicion, ce.orden,flujo,id_estado_destino,id_estado_origen, estadoterminal 
			FROM ot join cambiosestado ce on ce.id_estado_origen = ot.id_estado 
					join estados e on  e.id_estado = id_estado_destino 
			WHERE  id_estado_destino not in ('VN')  and id_estado_destino in ('".$destino."') 
				and id_estado_origen in ('".$origen."')  	  
			ORDER BY ce.orden";

    if ( $rq = tep_db_query($sqn) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
				$noes=$res['esta_nombre'];
				$ori=$res['id_estado_origen'];
			}
	}
	//si existe instalador
	if ($existeinst){
		$komentario="";
		$qU="update ot SET ".$ins.",ot_comentario='".$comentario."', id_estado='".$destino."', ot_freactivacion='" . fecha_php2db_new($ot_freactivacion)."' where ot_id=".($id_ot+0);
		tep_db_query($qU);

		/*datos del instalador*/	
		$qery_in="SELECT inst_nombre,inst_paterno,inst_materno, inst_rut FROM instaladores where id_instalador=".($ot_instalador+0); 
		$rq = tep_db_query($qery_in);
		$res1 = tep_db_fetch_array( $rq );
		$nombre_inst=$res1['inst_nombre']." ".$res1['inst_paterno']." ".$res1['inst_materno'];
		$rut_inst=$res1['inst_rut'];
		if ($comentario){
			$komentario=" y agrega el siguiente comentario:".$comentario;
		}
		//Se registra en el tracking
		if ($origen=='VC'){
			insertahistorial("OT $id_ot asigna Instalador (Nombre:$nombre_inst, Rut:$rut_inst), cambia de estado $origen_n a estado $noes, deja fecha de reactivación en $ot_freactivacion $komentario");	
		}
		if ($origen==$destino){
			insertahistorial("OT $id_ot Reasigna Instalador  (Nombre:$nombre_inst, Rut:$rut_inst) para la ot, deja fecha de reactivación en $ot_freactivacion $komentario");	
		}
		if($origen=='VR'){
			insertahistorial("OT $id_ot en Estado POR REALIZAR, Reasigna Instalador  (Nombre:$nombre_inst, Rut:$rut_inst) para la ot, deja fecha de reactivación en $ot_freactivacion $komentario");	
		}
		if($origen=='VZ'){
			insertahistorial("OT $id_ot en Estado POR CONFIRMAR REALIZACIÓN, Reasigna Instalador  (Nombre:$nombre_inst, Rut:$rut_inst) para la ot, deja fecha de reactivación en $ot_freactivacion $komentario");	
		}
		?>
		<script language="JavaScript">
			window.returnValue = 'refresh';
			window.close();
		</script>
		<?
	}else{
	?>
	<script language="JavaScript">
		alert("No existe el Cédula del instalador");
		window.close();
	</script>
	<?
	tep_exit();
	}
	
}

$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>