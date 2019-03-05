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

	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	$query = "SELECT E.nombre, DATE_FORMAT(L.fec_Evento,'%d/%m/%Y') AS fec_Evento , DATE_FORMAT(now(),'%d/%m/%Y') AS Fecha, L.festejado, Lc.nom_local, L.fec_creacion, DATE_FORMAT(L.fec_creacion, '%d/%m/%Y') AS fec_creacion, DATE_FORMAT(L.fec_creacion, '%H:%i:%s') AS hor_creacion
       		  FROM list_regalos_enc L
		      JOIN locales Lc ON (Lc.id_local=L.id_Local)
              JOIN list_eventos E ON (E.idEvento=L.id_Evento)
	 		  WHERE L.idLista = ".$idLista.";";
	$result= tep_db_query($query);
	$row = tep_db_fetch_array( $result );	
		
	$queryENC = "SELECT L.idLista,Lc.nom_local 
	             FROM list_regalos_enc L
		         JOIN locales Lc ON (Lc.id_local=L.id_Local)
	 			 WHERE L.idLista = ".$idLista.";";
	$result_ENC = tep_db_query($queryENC);			
			
	$tabla = "<div style='width: 20cm; height: 22cm; margin-left: -50;' align='center'>  
	            <table style='width: 650; height:auto;' align='center' cellpadding='1' cellspacing='1' border='0' bordercolor='#000' >
	            <tr height='70' align='center'>
	            	<th style='width: 90;'><img src='../img/logo3.gif' width='90' height='50' border='0'></th>
					<th colspan='5' class='titulonormal'>LISTA DE REGALOS N°. $idLista</th>
					<th style='width: 90;'><img src='../img/LogoListaReg.jpg' width='95' height='95' border='0'></th>
				</tr>
				</table>			
						
	              <table style='width: 650; height:auto;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos del Evento</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Evento</td>
	                          <td width='230' class='textonormal'>".$row['nombre']."</td>
	                          <td width='100' class='fieldsetLegend'>Fecha</td>
	                          <td class='textonormal'>".$row['Fecha']."</td>
	                        </tr>
	                        <tr>
	                          <td class='fieldsetLegend'>Fecha de Entrega</td>
	                          <td class='textonormal'>".$row['fec_Evento']."</td>
	                          <td></td>
	                          <td></td>
	                        </tr>
	                        <tr>
	                          <td class='fieldsetLegend'>Festejado(s)</td>
	                          <td colspan='3' class='textonormal'>".$row['festejado']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
	              
	              <table style='width: 650; height:auto; margin-top: -5;' align='center' border='0' bordercolor='#000'>
	                <tr>
	                  <td><fieldset>
	                      <legend class='userinput'>Datos de Creación</legend>
	                      <table style='width: 600;' border='0' align='center' cellpadding='2' cellspacing='1'>
	                        <tr>
	                          <td width='100' class='fieldsetLegend'>Tienda</td>
	                          <td class='textonormal'>".$row['nom_local']."</td>
	                          <td class='fieldsetLegend'>Creada el</td>
	                          <td class='textonormal'>".$row['fec_creacion']."</td>
	                        </tr>
	                        <tr>
	                          <td></td>
	                          <td></td>
	                          <td width='100' class='fieldsetLegend'>Hora</td>
	                          <td class='textonormal'>".$row['hor_creacion']."</td>
	                        </tr>
	                  	  </table>
	                      </fieldset>
	                  </td>
	                </tr>
	              </table>
						
				<table style='width: 650; height:auto; font-size: 13px;' align='center' cellpadding='1' cellspacing='1' border='1' bordercolor='#000'>		
				<tr>
					<th style='width: 150; background-color: #efc;' align='center' class='fieldsetLegend'>Código</th>
					<th style='width: 305; background-color: #efc;' align='center' class='fieldsetLegend'>Descripción</th>
					<th style='width: 95; background-color: #efc;' align='center' class='fieldsetLegend'>Precio</th>
					<th style='width: 95; background-color: #efc;' align='center' class='fieldsetLegend'>Cantidad</th>
				</tr>
				</table>
				<table style='width: 650; font-size: 10px;' align='center' cellpadding='1' cellspacing='1' border='1' bordercolor='#000'>";
	while( $res_ENC = tep_db_fetch_array( $result_ENC ) ) {		

			$queryDET = "SELECT * FROM list_regalos_det  WHERE idLista_enc = ".($res_ENC['idLista']).";";
			$res_DET = tep_db_query($queryDET);											

			if(tep_db_num_rows( $res_DET ) < 1){
				$tabla = $tabla.
				         "<tr style='background-color: #eee'>
				            <td style='width: 100; height=1cm;' align='center'>&nbsp;</td>
						    <td style='width: 250; height=1cm;' align='center'>&nbsp;</td>
						    <td style='width: 50; height=1cm;' align='center'>&nbsp;</td>
						    <td style='width: 50; height=1cm;' align='center'>&nbsp;</td>
						    <td style='width: 50; height=1cm;' align='center'>&nbsp;</td>
						 ";
			}
			else{
				while( $result_DET = tep_db_fetch_array( $res_DET ) ) {
					$codigo = ($result_DET['cod_Ean']."<br><hr>").$codigo; 
					$descripcion = ($result_DET['descripcion']."<br><hr>").$descripcion; 
					$cantidad = ($result_DET['list_cantprod']."<br><hr>").$cantidad; 
					$precio = (formato_precio($result_DET['list_precio'])."<br><hr>").$precio;
				}
				$tabla = $tabla.
				         "<tr style='background-color: #eee'>
				            <td style='width: 147; height=1cm;' align='center' class='textonormal'>".$codigo."</td>
						    <td style='width: 296; height=1cm;' align='left' class='textonormal'>".$descripcion."</td>
						    <td style='width: 100; height=1cm;' align='right' class='textonormal'>".$precio."</td>
						    <td style='width: 100; height=1cm;' align='right' class='textonormal'>".$cantidad."</td>
						 ";
				
				$codigo = "";
				$descripcion = "";
				$cantidad = "";
				$precio = "";
			}	
	}
	$tabla = $tabla.
			 "</tr>
			  <tr><td style='page-break-after: always;'></td></tr>
			  </table>
			  			  
			  <table style='width: 660; height:auto; margin-top: 5;' align='center' border='0' bordercolor='#000' class='userinput'>
                <tr>
                  <td><fieldset style='font-size: 10px;  font-weight: bold;'>
                      <table style='width: 600; font-size: 10px;' border='0' align='center' cellpadding='2' cellspacing='1'>
                        <tr>
                          <td>               	
							<p align='left'>
								Los productos incluidos en este documento están sujetos a cambios
							    de precio de acuerdo a la tienda en la cual se adquieran y a eventos comerciales 
							    definidos por Easy Colombia. El precio de venta de cada producto corresponderá al 
							    precio vigente al momento de la compra.
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								La disponibilidad de los productos dependerá de su existencia en el inventario de la 
								tienda al momento de la compra. Easy no se compromete a separar los productos sin su 
								pago previo.
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								La entrega de los productos comprados se realizará en la fecha acordada al momento de la 
								creación de la Lista de Regalos y que consta en este documento.
							</p> 
                          </td>
                        </tr>
                        <tr>
                          <td>               	
							<p align='left'>
								Una vez comprado un producto de la lista de regalos, no se harán devoluciones.
							</p> 
                          </td>
                        </tr>
                  	  </table>
                      </fieldset>
                  </td>
                </tr>
              </table>
              

             
              
              <table style='width: 660; height:auto; margin-top: -5;' align='center' border='0' bordercolor='#000'>
                <tr>
                  <td><fieldset style='font-size: 10px;  font-weight: bold;'>
                      <table style='width: 600; font-size: 13px;' border='0' align='center' cellpadding='2' cellspacing='1'>
                        <tr>
                          <td>               	
							<p align='left'>
								<b>Firma Asesor Easy: <br><br><br>____________________________ 
							</p> 
                          </td>
                          <td >               	
							<p align='left'>
								<b>Firma Aceptado Cliente: <br><br><br> ____________________________ 
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

insertahistorial_ListaReg("Se imprime la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
	
?>