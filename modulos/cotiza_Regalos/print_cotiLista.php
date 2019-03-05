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

	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	//Encabezado de la Cotización.
	$Qry_Enc = "SELECT DISTINCT R.idLista, V.nombre AS evento, R.festejado, E.invitado,L.cod_local, L.nom_local, E.OS_fecCrea, S.nombre AS tipoDespacho, A.esta_nombre AS OS_Estado
		         FROM list_os_det D
		         JOIN list_os_enc E ON (E.idLista_OS_enc = D.idLista_OS_enc)
		         JOIN list_regalos_det T ON (T.idLista_det = D.idLista_det)
		         JOIN list_regalos_enc R ON (R.idLista = E.idLista_enc)
		         JOIN list_eventos V ON (idEvento = R.id_Evento)
		         JOIN locales L ON (L.id_local = E.OS_Local)
		         JOIN tipos_despacho S ON (S.id_tipodespacho = T.list_idTipodespacho)
		         JOIN estados A ON (A.id_estado = E.OS_estado)
		 WHERE D.idLista_OS_enc = $idLista_OS_enc ";
	$res1 = tep_db_query($Qry_Enc);
	$result1 = tep_db_fetch_array( $res1 );

	//Detalle de la Cotización.
	$Qry_Det = "SELECT CONCAT(A.cod_Ean,'<br>','(',A.cod_Easy,')')AS codigo, A.descripcion,	REPLACE(FORMAT(A.list_precio,0),',','.') AS precio, 
        				REPLACE(FORMAT(((D.OS_cantidad)),0),',','.') AS cantidad 
						FROM list_os_det D
         				JOIN list_regalos_det A ON (A.idLista_det = D.idLista_det)
						WHERE D.idLista_OS_enc= $idLista_OS_enc ";
	$res2 = tep_db_query($Qry_Det);
						
	//Total de la cotización.				
	$Qry_Tot = "SELECT  SUM((A.list_precio * ( if( OS_cantPick<>0, REPLACE(FORMAT(((D.OS_cantidad+D.OS_cantPick)),0),',','.'),  REPLACE(FORMAT(((D.OS_cantidad)),0),',','.')) ))) AS Total
 				FROM list_os_det D
        		JOIN list_regalos_det A ON (A.idLista_det = D.idLista_det)
				WHERE D.idLista_OS_enc= $idLista_OS_enc ";
	$res3 = tep_db_query($Qry_Tot);
	$result3 = tep_db_fetch_array( $res3 );

	//Arma el código de Barras de la cotizacion
	$Codigo_Local = substr($result1['cod_local'], 1);	
	$cod_ean_os = calcula_num_os($idLista_OS_enc);		
	$codigo = PREFIJO_LISTA_REGALOS . $Codigo_Local . $cod_ean_os;
	$dig_verificacion = dvEAN13($codigo);
	$codigo_ean = $codigo . $dig_verificacion;		
	$codBarra = gencode_EAN13($codigo_ean, 150, 60);		
			
	$tabla = "<div style='width: 20cm; height: 22cm; margin-left: -50;' align='center'>  
	            <table style='width: 650; height:auto;' align='center' cellpadding='1' cellspacing='1' border='0' bordercolor='#000'>
	            <tr height='70' align='center'>
	            	<th style='width: 100;'><img src='../img/logo3.gif' width='90' height='50' border='0'></th>
					<th colspan='5' class='titulonormal'>COTIZACIÓN DE REGALOS No. $idLista_OS_enc</th>
					<th>$codBarra</th>
					<th style='width: 100;'><img src='../img/LogoListaReg.jpg' width='95' height='95' border='0'></th>
				</tr>
				</table>			
						
	              <table style='width: 650; height:auto;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos del Evento</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='120' class='fieldsetLegend'>Lista de Regalos No.</td>
	                          <td width='160' class='textonormal'>".$result1['idLista']."</td>
	                          <td width='100' class='fieldsetLegend'>Evento</td>
	                          <td class='textonormal'>".$result1['evento']."</td>
	                        </tr>
	                        <tr>
	                          <td class='fieldsetLegend'>Festejado(s)</td>
	                          <td class='textonormal'>".$result1['festejado']."</td>
	                          <td></td>
	                          <td></td>
	                        </tr>
	                        <tr>
	                          <td class='fieldsetLegend'>Invitado(s)</td>
	                          <td colspan='3' class='textonormal'>".$result1['invitado']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
	              
	              <table style='width: 650; height:auto; margin-top: -5;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos de Cotización</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='120' class='fieldsetLegend'>Tienda</td>
	                          <td width='160' class='textonormal'>".$result1['nom_local']."</td>
	                          <td width='100' class='fieldsetLegend'>Fecha</td>
	                          <td class='textonormal'>".fecha_db2php($result1['OS_fecCrea'])."</td>
	                        </tr>
	                        <tr>
	                          <td width='120' class='fieldsetLegend'>Tipo Despacho</td>
	                          <td width='160' class='textonormal'>".$result1['tipoDespacho']."</td>
	                          <td width='100' class='fieldsetLegend'>Estado</td>
	                          <td class='textonormal'>".$result1['OS_Estado']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
						
				<table style='width: 650; height:auto; font-size: 13px;' align='center' cellpadding='0' cellspacing='0' border='1'>		
				<tr>
					<th style='width: 150; height=1cm; background-color: #efc; font-size:12;' align='center' class='fieldsetLegend'>Código</th>
					<th style='width: 305; height=1cm; background-color: #efc; font-size:12' align='center' class='fieldsetLegend'>Descripción</th>
					<th style='width: 95; height=1cm; background-color: #efc; font-size:12' align='center' class='fieldsetLegend'>Precio</th>
					<th style='width: 95; height=1cm; background-color: #efc; font-size:12' align='center' class='fieldsetLegend'>Cantidad</th>
				</tr>
				</table>
				<table style='width: 650; font-size: 10px;' align='center' cellpadding='1' cellspacing='1' border='1'>";
	
	            while ($result2 = tep_db_fetch_array( $res2 )){
	            		$codEasy = $result2['codigo'];
	            		$descripcion = $result2['descripcion'];
	            		$precio = $result2['precio'];
	            		$cantidad = $result2['cantidad'];
	                    		
				$tabla = $tabla.
				         "<tr style='background-color: #eee'>
				            <td style='width: 148; height=1cm;' align='center' class='textonormal'>".$codEasy."</td>
						    <td style='width: 296; height=1cm;' align='left' class='textonormal'>".$descripcion."</td>
						    <td style='width: 98; height=1cm;' align='right' class='textonormal'>".$precio."</td>
						    <td style='width: 100; height=1cm;' align='right' class='textonormal'>".$cantidad."</td>
						 ";
	            }
				$codigo = "";
				$descripcion = "";
				$cantidad = "";
				$precio = "";

	$tabla = $tabla."</tr>
						<tr>
							<td colspan='2'></td>
							<td align='right' class='fieldsetLegend' style='height=1cm; background-color: #eee;'>Valor Total</td>
							<td align='right' class='fieldsetLegend' style='height=1cm; background-color: #eee;'>$ ".formato_precio($result3['Total'])."</td>
						</tr>
			  			<tr><td style='page-break-after: always;'></td></tr>
			 		 </table>";
				
	$tabla = $tabla.
			 "<table style='width: 660; height:auto; margin-top: 5;' align='center' border='0' bordercolor='#000' class='userinput'>
                <tr>
                  <td><fieldset style='font-size: 10px;  font-weight: bold;'>
                      <table style='width: 600; font-size: 10px;' border='0' align='center' cellpadding='2' cellspacing='1'>
                        <tr>
                          <td>               	
							<p align='left'>
								".CON_COM1."
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								".CON_COM2."
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								".CON_COM3."
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								".CON_COM4."
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								".CON_COM5."
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

?>