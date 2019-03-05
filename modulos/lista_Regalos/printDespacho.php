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
$idLista = $_GET['idLista'];

	$tabla = "";
	$USR_LOGIN = get_login_usr( $ses_usr_id );

	$Updt = "UPDATE list_regalos_enc SET GD_numImpresion = (GD_numImpresion+1), GD_fecImpresion = now() WHERE idLista = $idLista";
	tep_db_query($Updt);
	
	$QryEnc ="SELECT L.idLista, L.GD_id, L.clie_Rut, T.cod_local, T.nom_local, L.fec_Evento AS fec_entrega, V.nombre AS evento,  CONCAT(C.clie_nombre,' ',C.clie_materno,' ',C.clie_paterno)AS clie_nombre, C.clie_telefonocasa, D.dire_direccion, D.dire_localizacion, L.GD_fecImpresion, L.GD_numImpresion
				FROM list_regalos_enc L 
				LEFT JOIN clientes C ON (C.clie_rut=L.clie_Rut) 
				LEFT JOIN list_eventos V ON (V.idEvento=L.id_Evento) 
				LEFT JOIN direcciones D ON (D.id_direccion=L.id_Direccion) 
				LEFT JOIN locales T ON (T.id_local=L.id_Local)
				WHERE idLista = $idLista";
	$res = tep_db_query($QryEnc);
	$result = tep_db_fetch_array( $res );	

	$datosDireccion= getlocalizacion($result['dire_localizacion']);

	for($i=0; $i<GD_IMP; $i++){
	
	$tabla = "<div style='width: 20cm; height: 22cm; margin-left: -50;' align='center'>  
	            <table style='width: 650; height:auto;' align='center' cellpadding='1' cellspacing='1' border='0' bordercolor='#000' >
	            <tr height='100%' align='center'>
	            	<td>
	            		<table border='0' align='center'>
				  			<tr>
								<td align='left' style='width: 150'><img src='../img/logo3.gif' border='0' style='width:95; height: 55;'></td>
								<td>
									<table width='550'>
									  <tr>
									    <td class='titulonormal' height='30'>ACTA DE ENTREGA No.".$result['idLista']."</td>
									  </tr>
									  <tr>
									    <td class='titulonormal' height='30'>LISTA DE REGALOS No.".$result['GD_id']."</td>
									  </tr>
									</table>
								</td>
							</tr>
						</table>
	            	</td>
				</tr>
				</table>			
						
	              <table style='width: 650; height:auto;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos del Evento</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Evento: </td>
	                          <td width='230' class='textonormal'>".$result['evento']."</td>
	                          <td width='100' class='fieldsetLegend'>Fecha Entrega: </td>
	                          <td class='textonormal'>".fecha_db2php($result['fec_entrega'])."</td>
	                        </tr>
	                        <tr>
	                          <td class='fieldsetLegend'>Dirección: </td>
	                          <td class='textonormal'>".$result['dire_direccion']."</td>
	                          <td class='fieldsetLegend'>Barrio:</td>
	                          <td class='textonormal'>".$datosDireccion[barrio]."</td>
	                        </tr>
	                        <tr>
	                          <td class='fieldsetLegend'>Nombre Cliente</td>
	                          <td class='textonormal'>".$result['clie_nombre']."</td>
	                         <td class='fieldsetLegend'>Teléfono:</td>
	                          <td class='textonormal'>".$result['clie_telefonocasa']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
	              
	              <table style='width: 650; height:auto; margin-top: -5;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos Acta</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Fecha impresión: </td>
	                          <td colspan='2' class='textonormal'>".$result['GD_fecImpresion']."</td>
	                        </tr>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Tienda</td>
	                          <td colspan='2' class='textonormal'>".$result['nom_local']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
						
				<table style='width: 650; height:auto; font-size: 13px;' align='center' cellpadding='1' cellspacing='1' border='1' bordercolor='#000'>		
				<tr>
					<th style='width: 150; background-color: #efc;' align='center' class='fieldsetLegend'>Código</th>
					<th style='width: 407; background-color: #efc;' align='center' class='fieldsetLegend'>Descripción</th>
					<th style='width: 139; background-color: #efc;' align='center' class='fieldsetLegend'>Cantidad</th>
				</tr>
				</table>
				<table style='width: 650; font-size: 10px;' align='center' cellpadding='1' cellspacing='1' border='1' bordercolor='#000'>";

			$queryDET = "SELECT DISTINCT LT.cod_Ean, LT.cod_Easy, LT.descripcion, SUM(LD.OS_cantPick) AS cantPick
							FROM list_os_det LD
							LEFT JOIN list_regalos_det LT ON (LT.idLista_det=LD.idLista_det)
							LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LD.idLista_OS_enc
							LEFT JOIN list_ot LO ON LO.ot_idList =LD.OS_idOT
							WHERE LE.idLista_enc=".$idLista." AND LE.OS_estado='SP'
							GROUP BY LT.cod_Ean";
			$res_DET = tep_db_query($queryDET);											

			if(tep_db_num_rows( $res_DET ) < 1){
				$tabla = $tabla.
				         "<tr style='background-color: #eee'>
				            <td style='width: 150; height=1cm;' align='center'>&nbsp;</td>
						    <td style='width: 407; height=1cm;' align='center'>&nbsp;</td>
						    <td style='width: 139; height=1cm;' align='center'>&nbsp;</td>
						 ";
			}
			else{
				while( $result_DET = tep_db_fetch_array( $res_DET ) ) {
					$codigo = ($result_DET['cod_Ean']." / (".$result_DET['cod_Easy'].")<br><hr>").$codigo; 
					$descripcion = ($result_DET['descripcion']."<br><hr>").$descripcion; 
					$cantidad = ($result_DET['cantPick']."<br><hr>").$cantidad; 
				}
				$tabla = $tabla.
				         "<tr style='background-color: #eee'>
				            <td style='width: 150; height=1cm;' align='center' class='textonormal'>".$codigo."</td>
						    <td style='width: 400; height=1cm;' align='left' class='textonormal'>".$descripcion."</td>
						    <td style='width: 150; height=1cm;' align='center' class='textonormal'>".$cantidad."</td>
						 ";
				
				$codigo = "";
				$descripcion = "";
				$cantidad = "";
			}	

	$tabla = $tabla.
			 "</tr>
			  <tr><td style='page-break-after: always;'></td></tr>
			  </table>			  			

			  <table style='width: 650; height:auto; margin-top: 0;' align='center' border='0' bordercolor='#000'>
                <tr>
                  <td><fieldset style='font-size: 10px;  font-weight: bold;'>
                      <table style='width: 600; font-size: 13px;' border='0' align='center' cellpadding='2' cellspacing='1'>
                        <tr>
                          <td style='width: 100px; height:' class='fieldsetLegend' >
                          	ENTREGA
                          </td>
                          <td style='width: 450; height: 100;'>               	
							<p align='left'>
								<b>Firma Asesor Easy:   ____________________________ 
							</p> 
                          </td>
                         </tr>
                         <tr>
                          <td style='width: 100;' class='fieldsetLegend'>
                          	RECIBE
                          </td>
                          <td style='width: 450;'>               	
							<p align='left'>
								<b>Firma Despachos:    ____________________________ 
							</p> 
                          </td>
                        </tr>
                        <tr>
                         <td style='width: 50;' class='fieldsetLegend'>&nbsp;</td>
                        </tr>
                  	  </table>
                      </fieldset>
                  </td>
                </tr>
              </table>
              
			  </div>
			  <H1 class='SaltoDePagina'> </h1>
			 ";
	echo $tabla;
}

insertahistorial_ListaReg("Se imprime la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
	
?>