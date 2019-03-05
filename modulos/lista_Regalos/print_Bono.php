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
<?

global $ses_usr_id;   
	   $tabla = "";
	  
	$USR_LOGIN = get_login_usr( $ses_usr_id );

	//Actualiza la cantidad de impresiones y fecha de impresión
	$Updt = "UPDATE list_bono SET fec_impresion=now(), num_impre=(num_impre+1) WHERE id_Lista=".$idLista."";
	tep_db_query($Updt);
	
	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	$query = "SELECT B.*,B.fec_impresion, date_format(E.fec_creacion, '%Y-%m-%d') AS fec_creacion, L.nom_local, E.clie_Rut, CONCAT(C.clie_nombre,' ',C.clie_paterno,' ',C.clie_materno) AS nom_cliente, C.clie_telefonocasa, CONCAT(U.USR_NOMBRES,' ', USR_APELLIDOS) AS asesor, L.cod_local
				FROM list_bono B
				LEFT JOIN list_regalos_enc E ON E.idLista=B.id_Lista  
				LEFT JOIN locales L ON L.id_local=E.id_Local
				LEFT JOIN clientes C ON C.clie_rut=E.clie_Rut
               	LEFT JOIN usuarios U ON U.USR_ID=E.id_Usuario
				WHERE E.idLista=".$idLista."";
	$result= tep_db_query($query);
	$row = tep_db_fetch_array( $result );	

	// Genero el Codigo de Barras
	$Codigo_Local = substr($row['cod_local'], 1);	
	$cod_ean_os = calcula_num_os($idLista);		
	$codigo = PREFIJO_LISTA_REGALOS . $Codigo_Local . $cod_ean_os;
	$dig_verificacion = dvEAN13($codigo);
	$codigo_ean = $codigo . $dig_verificacion;	
	$cod_Barras = gencode_EAN13($codigo_ean, 200, 100);
	
	$tabla = "<div style='width: 20cm; height: 22cm; margin-left: -50;' align='center'>  
	            <table style='width: 650; height:auto;' align='center' cellpadding='1' cellspacing='1' border='0' bordercolor='#000' >
	            <tr height='70' align='center'>
	            	<table border='0' align='center' style='width: 650; margin-top: 0;'>
				  			<tr>
								<td align='left' style='width: 150'><img src='../img/LogoListaReg.jpg' border='0' style='width:95; height: 95;'></td>
								<td>
									<table width='400' style='margin-left: 50px;'>
									  <tr>
									    <td class='subtitulonormal' align='left'>LISTA DE REGALOS N°.".$row['id_Lista']."</td>
									  </tr>
									  <tr>
									    <td class='subtitulonormal' align='left'>ACTA PARA LIQUIDACIÓN DE BONO</td>
									  </tr>
									  <tr>
									    <td class='subtitulonormal' align='left'>BONO N°.".$row['id_Bono']."</td>
									  </tr>
									</table>
								</td>
								<td style='width: 150;'></td>
							</tr>
						</table>
				</tr>
				</table>			
						
	              <table style='width: 650; height:auto;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos del Bono</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Bono N°. </td>
	                          <td width='230' class='textonormal'>".$row['id_Bono']."</td>
	                          <td width='80' class='fieldsetLegend'>Fecha de Impresión: </td>
	                          <td class='textonormal'>".($row['fec_impresion'])."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
	              
	              <table style='width: 650; height:auto; margin-top: -5;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos de la Lista</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='110' class='fieldsetLegend'>Lista de regalos N°. </td>
	                          <td class='textonormal'>".$row['id_Lista']."</td>
	                          <td class='fieldsetLegend'></td>
	                          <td class='textonormal'></td>
	                        </tr>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Tienda: </td>
	                          <td width='220' class='textonormal'>".$row['nom_local']."</td>
	                          <td width='80' class='fieldsetLegend'>Creada el: </td>
	                          <td class='textonormal'>".$row['fec_creacion']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
	              
	              <table width='650'>
				  	<tr>
						<td style='100%;' colspan='3' class='fieldsetLegend'>MEDIANTE ESTE BONO RECLAMA UNA GIFT CARD POR EL SIGUIENTE VALOR:</td>
					</tr>
					<tr>
						<td style='width: 100%; font-size:30px; font-weight:700; height: 80;' align='center'>
							$cod_Barras
							<div style='position:absolute;visibility:visible; left:270px; top:255px; width:120px; height:40px; overflow:no;'>$ ".formato_precio($row['valor'])."</div>
							<div style='position:absolute;visibility:visible; left:180px; top:297px' width:210px; overflow:no;'>
							  <img src='img/Blanc.png' border='0' style='width:300; height: 14;'>
							</div>
						</td>	
					</tr>
				</table>
                    
              <table style='width: 650; height:auto;' align='center' border='0' bordercolor='#000'>
              	<tr>
                  <td><fieldset style='font-size: 10px;  font-weight: bold;'>
                      <table style='width: 600; font-size: 13px;' border='0' align='center' cellpadding='2' cellspacing='1'>
                        <tr>
                			<td style='width: 650; height:50;' class='textonormal'>Autorizado Por:   ".$row['asesor']."<td>
                		</tr>
                      	<tr>
                          <td class='textonormal'>               	
							<p align='left'>
								<b>Firma Asesor Easy: <br><br><br> _______________________________ 
							</p> 
                          </td>
                          <td class='textonormal'>                	
							<p align='left'>
								<b>Firma Aceptado Cliente: <br><br><br> _______________________________ 
							</p> 
                          </td>
                        </tr>
                  	  </table>
                      </fieldset>
                  </td>
                </tr>
              </table>
              
			  </div>
			 ";
	echo $tabla;

insertahistorial_ListaReg("Se imprime el Bono de la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
	
?>