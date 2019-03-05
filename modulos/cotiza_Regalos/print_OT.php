<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";
?>
<link rel="stylesheet" href="../estilos.css">
<script Language="Javascript">
	var NS = (navigator.appName == "Explorer");
	var VERSION = parseInt(navigator.appVersion);
	if (VERSION < 3) {
		alert ('verifique que este activo el control "Activex no Firmadas", si su version de explorer es inferior a la requerida consulte con el administrador');        
	}
	document.write("<body style='margin-left: 0;' onload='imprimirsinasistente(); window.close();'>");
	
	function imprimirsinasistente(){  
		if (NS) {
    	window.print();  
		} else {
    	var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
		document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
    	WebBrowser1.ExecWB(6, 2);
  		WebBrowser1.outerHTML = "";  
		}
	}
</script>
<style type="text/css">
	H1.SaltoDePagina
	{
		PAGE-BREAK-AFTER: always;
	}
</style>
<?

global $ses_usr_id;  

// Cuenta las id_od que vienen en la cadena.
$longitud = count(split(",",$id_OT));
// Obtiene las id_ot de la cadena separandolas por ,.
$cadena = split(",",$id_OT);
	
	
	// Imprime todas las id_ot.

for ($i=0; $i<$longitud; $i++){ 	
	   $tabla = "";
	// Se consulta el encabezado de la OT.
	$Qry_Enc = "SELECT DISTINCT LR.idLista, OT.ot_idList, LO.idLista_OS_enc, OT.ot_listFeccrea, LR.fec_Evento, OT.ot_listTipo, ES.esta_nombre, OT.ot_listTiendaPago, LR.festejado, LR.descripcion,TD.nombre, DR.dire_direccion, DR.dire_telefono, DR.dire_localizacion, LE.idLista_enc, LR.clie_Rut, OT.ot_listNumImp, OT.ot_usuAutoriza, CONCAT(US.USR_NOMBRES,\" \",US.USR_APELLIDOS) AS user, LC.nom_local
				FROM list_ot OT 
				LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList
				LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LO.idLista_OS_enc
				LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
				LEFT JOIN list_regalos_enc LR ON LR.idLista = LD.idLista_enc 
				LEFT JOIN direcciones DR ON DR.id_direccion = LR.id_Direccion
				LEFT JOIN tipos_despacho TD ON TD.id_tipodespacho = LD.list_idTipodespacho
				LEFT JOIN estados ES ON ES.id_estado = OT.ot_idEstado
                LEFT JOIN usuarios US ON USR_LOGIN = OT.ot_usuAutoriza
                LEFT JOIN locales LC ON LC.id_local = LR.id_Local
				WHERE OT.ot_idList= ($cadena[$i]+0)";
	$res1 = tep_db_query($Qry_Enc);
	$result1 = tep_db_fetch_array( $res1 );	
	
	//Se consulta el detalle (Productos) de la OT
	$Qry_Det = "SELECT LO.idLista_OS_det, LD.cod_ean, LD.cod_Easy, LD.descripcion, LD.list_precio, LO.OS_cantPick,
	            REPLACE(FORMAT(((LO.OS_cantidad)),0),',','.') AS cantidad
	            FROM list_ot OT 
				LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList 
				LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
				WHERE OT.ot_idList=".($cadena[$i]+0)."  GROUP By LD.cod_Easy";
	$res2 = tep_db_query($Qry_Det);
	while ($result2 = tep_db_fetch_array( $res2 )){
			$detalles= "<tr>
				<td style='width: 120;'>".$result2['cod_ean']."<br>".$result2['cod_Easy']."</td>
				<td style='width: 350;'>".$result2['descripcion']."</td>
				<td style='width: 120;' align='right'>".$result2['list_precio']."</td>
				<td style='width: 70;' align='right'>".$result2['cantidad']."</td>
				<td style='width: 70;' align='center'>
				<div style='width: 35px; margin-top: 12;'align='center'>_________</div>
				</td>
			</tr>".$detalles;
	}
	
	//Totaliza los Productos de la OT
	$Qry_Tot = "SELECT SUM( REPLACE(FORMAT(((LO.OS_cantidad)),0),',','.') ) AS Total, 
            	SUM(LO.OS_cantPick) AS TotalPick
             	FROM list_ot OT 
				LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList 
				LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
				WHERE OT.ot_idList= ($cadena[$i]+0) ";
	$res3 = tep_db_query($Qry_Tot);
	$result3 = tep_db_fetch_array( $res3 );
	
	// Crea la cabesera de Re-impresion
	if ($result1['ot_listNumImp'] != 0){
		$addTable = "<tr>
						<div><td class='subtitulonormal'>Autorizado Por: ".$result1['user']."</td></div>
						<div><td class='subtitulonormal'>Reimpresión N°. ".$result1['ot_listNumImp']."</td></div>
					</tr>";
	} 
	
	//Establece el Tipo de Orden de Trabajo
	if($result1['ot_listTipo'] == 'PS')
		$tipoOT = 'Producto Stock';
	else if ($result1['ot_listTipo'] == 'SV')
		$tipoOT = 'Servicio';
	else if ($result1['ot_listTipo'] == 'PE')
		$tipoOT = 'Pedido Especial';
		
	// Datos de la Ubicacion
	$datosDireccion= getlocalizacion($result1['dire_localizacion']);
			
	$tabla = "<div style='width: 20cm; height: 22cm; margin-left: -50;' align='center'>  			
	            <table border='0' align='center' style='width: 650;'>
				<tr>
				  	<td colspan='3'>
				  		<table border='0' align='center' style='width: 650; margin-top: 0;'>
				  			<tr>
								<td align='left' style='width: 150'><img src='../img/logo3.gif' border='0' style='width:95; height: 55;'></td>
								<td>
									<table width='550'>
										<tr>
											<td class='subtitulonormal'>Picking Orden de Trabajo N°. ".$result1['ot_idList']."</td>
										</tr>
									".$addTable."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<fieldset>
						<legend class='fieldsetLegend'>Datos de la Orden de Trabajo</legend>
						<table>
							<tr>
								<td style='width: 130;' class='textonormal'>N° Orden de Trabajo.</td>
								<td style='width: 250;' class='userinput'>".$result1['ot_idList']."</td>
								<td style='width: 120;' class='textonormal'>N° Orden de Servicio.</td>
								<td style='width: 150;' class='userinput'>".$result1['idLista_OS_enc']."</td>
							</tr>
							<tr>
								<td style='width: 130;' class='textonormal'>Fecha de Generación: </td>
								<td style='width: 250;' class='userinput'>".fecha_db2php($result1['ot_listFeccrea'])."</td>
								<td style='width: 100;' class='textonormal'>Fecha de entrega: </td>
								<td style='width: 150;' class='userinput'>".fecha_db2php($result1['fec_Evento'])."</td>
							</tr>
							<tr>
								<td style='width: 130;' class='textonormal'>Tipo de Orden de Trabajo: </td>
								<td style='width: 250;' class='userinput'>".$tipoOT."</td>
								<td style='width: 100;' class='textonormal'>Estado: </td>
								<td style='width: 150;' class='userinput'>".$result1['esta_nombre']."</td>
							</tr>
							<tr>
								<td style='width: 100;' class='textonormal'>Tienda: </td>
								<td style='width: 150;' class='userinput'>".$result1['nom_local']."</td>
								<td style='width: 100;' class='textonormal'>Lista de Regalos N°. </td>
								<td style='width: 150;' class='userinput'>".$result1['idLista']."</td>
							</tr>
						</table>
						</fieldset>
					</td>			
				</tr>
				<tr>
					<td colspan='3'>
						<fieldset>
						<legend class='fieldsetLegend'>Datos del Servicio</legend>
						<table>
							<tr>
								<td style='width: 130;' class='textonormal'>Nombre: </td>
								<td style='width: 250;' class='userinput'>".$result1['festejado']."</td>
								<td style='width: 100;' class='textonormal'>Teléfono: </td>
								<td style='width: 150;' class='userinput'>".$result1['dire_telefono']."</td>
							</tr>
							<tr>
								<td style='width: 130;' class='textonormal'>Dirección: </td>
								<td style='width: 250;' class='userinput'>".$result1['dire_direccion']."</td>
								<td style='width: 100;' class='textonormal'>Barrio: </td>
								<td style='width: 150;' class='userinput'>".$datosDireccion['barrio']."</td>
							</tr>
							<tr>
								<td style='width: 130;' class='textonormal'>Tipo Despacho: </td>
								<td style='width: 250;' class='userinput'>".$result1['nombre']."</td>
								<td style='width: 100;' class='textonormal'>Ciudad: </td>
								<td style='width: 150;' class='userinput'>".$datosDireccion['ciudad']."</td>
							</tr>
							<tr>
								<td style='width: 130;' class='textonormal'>Observación: </td>
								<td style='width: 250;' class='userinput'>".$result1['descripcion']."</td>
								<td style='width: 100;' class='textonormal'>Departamento: </td>
								<td style='width: 150;' class='userinput'>".$datosDireccion['departamento']."<br>".$datosDireccion['localidad']."</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<table class='tabla1'>
							<tr>
								<th style='width: 140;'>Código</th>
								<th style='width: 320;'>Descripción</th>
								<th style='width: 110;'>Precio Unitario</th>
								<th style='width: 90;'>Cantidad<br>Solicitada</th>
								<th style='width: 90;'>Cantidad<br>Pickeada</th>
							</tr>
							".$detalles."
							<tr>
								<td colspan='2'></td>
								<th style='width: 120;' align='right'>Total</th>
								<th style='width: 70;' align='right'>".$result3['Total']."</th>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<table width='80%' style='margin-top: 25' align='center'>
							<tr>
								<td align='right' class='subtitulonormal'>
									Firma Operador Logistico:  
								</td>
								<td>
									 _________________________________  
								</td>
							</tr>
							<tr><td style='height:15'></td></tr>
							<tr>
								<td align='right' class='subtitulonormal' style='width: 600; height:30'>
									Revisada por Auxiliar de seguridad:  
								</td>
								<td>
									 _________________________________  
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</div>
			 ";
	echo $tabla;
	$UPD =  "UPDATE list_ot SET ot_listNumImp = '".($result1['ot_listNumImp']+1)."' WHERE ot_idList='".($cadena[$i]+0)."'";
	tep_db_query($UPD);
	
	if ($i < $longitud - 1)
		{		
			echo'<H1 class="SaltoDePagina"> </h1>';
  		}
}
?>