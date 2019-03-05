<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";

if($accion == export){
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=Reporte_compras_Listas_Regalos$_REQUEST[fectermino].xls");
	header("Pragma: no-cache");
	header("Expires: 0");
}

if($accion == printp){
	?>
	<script Language="Javascript">
	var NS = (navigator.appName == "Explorer");
	var VERSION = parseInt(navigator.appVersion);
	if (VERSION < 3) {
		alert ('verifique que este activo el control "Activex no Firmadas", si su version de explorer es inferior a la requerida consulte con el administrador');        
	}
	
		document.write("<body onload='imprimirsinasistente(); window.close();'>");
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
}

global $ses_usr_id;   
	   $tabla = "";	   
	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.		
	$queryENC = "SELECT L.idLista,Lc.nom_local 
	             FROM list_regalos_enc L
		         JOIN locales Lc ON (Lc.id_local=L.id_Local)
	 			 WHERE L.idLista IN (".$idLista.") AND L.id_Estado IN ('CC');";
	$result_ENC = tep_db_query($queryENC);			
			
	$tabla = "<table style='width: 650; height:auto; font-size: 10px;' align='center' cellpadding='1' cellspacing='1' border='2' bordercolor='#000'>
				<tr height='50' align='center' style='background-color: #efc'>
					<th colspan='7'>REPORTE DE COMPRAS LISTA DE REGALOS</th>
				</tr>
				<tr>
					<th style='width: 50; background-color: #efc;' align='center'>N° Lista</th>
					<th style='width: 101; background-color: #efc;' align='center'>Tienda</th>
					<th style='width: 107; background-color: #efc;' align='center'>Código</th>
					<th style='width: 260; background-color: #efc;' align='center'>Descripción</th>
					<th style='width: 47; background-color: #efc;' align='center'>Cnt.Sol</th>
					<th style='width: 47; background-color: #efc;' align='center'>Cnt.Cmp</th>
					<th style='width: 47; background-color: #efc;' align='center'>Cnt.x.Cmp</th>
				</tr>
				</table>
				<div style='width: 20cm; height: 22cm;' align='center'>
				<table style='width: 650; font-size: 10px; margin-left: -83;' align='center' cellpadding='1' cellspacing='1' border='1' bordercolor='#000'>";
	while( $res_ENC = tep_db_fetch_array( $result_ENC ) ) {		
		$tabla = $tabla.
				  "<tr style='background-color: #eee'>
						<td style='width: 53; height=1cm;' height='25' align='center'>".$res_ENC['idLista']."</td>
						<td style='width: 95; height=1cm;' height='25' align='center'>".$res_ENC['nom_local']."</td>		
				  ";

			$queryDET = "SELECT DISTINCT LD.idLista_enc, LD.cod_Ean, LD.descripcion, (LD.list_cantprod+LD.list_Cantcomp) AS list_cantprod, LD.list_Cantcomp, 
						LD.list_cantprod AS cant_Xcomp, (LD.list_cantprod+LD.list_Cantcomp) AS cant_org
						FROM list_regalos_det LD 
						LEFT JOIN list_os_det OD ON (OD.idLista_det=LD.idLista_det) 
						LEFT JOIN list_ot OT ON (OT.ot_idList=OD.OS_idOT) 
						WHERE LD.idLista_enc = ".($res_ENC['idLista']).";";
			$res_DET = tep_db_query($queryDET);											

			if(tep_db_num_rows( $res_DET ) < 1){
				$tabla = $tabla.
				         "<td style='width: 100; height=1cm;' align='center'>&nbsp;</td>
						  <td style='width: 250; height=1cm;' align='center'>&nbsp;</td>
						  <td style='width: 50; height=1cm;' align='center'>&nbsp;</td>
						  <td style='width: 50; height=1cm;' align='center'>&nbsp;</td>
						  <td style='width: 50; height=1cm;' align='center'>&nbsp;</td>
						 ";
			}
			else{
				while( $result_DET = tep_db_fetch_array( $res_DET ) ) {
					$codigo = ($result_DET['cod_Ean']."<br>").$codigo; 
					$descripcion = ($result_DET['descripcion']."<br>").$descripcion; 
					$cant_solici = ($result_DET['list_cantprod']."<br>").$cant_solici; 
					$cant_comp = ($result_DET['list_Cantcomp']."<br>").$cant_comp;
					$cant_Xcomp = (($result_DET['cant_Xcomp'])."<br>").$cant_Xcomp;
				}
				$tabla = $tabla.
				         "<td style='width: 100; height=1cm;' align='center'>".$codigo."</td>
						  <td style='width: 250; height=1cm;' align='left'>".$descripcion."</td>
						  <td style='width: 50; height=1cm;' align='center'>".$cant_solici."</td>
						  <td style='width: 50; height=1cm;' align='center'>".$cant_comp."</td>
						  <td style='width: 50; height=1cm;' align='center'>".$cant_Xcomp."</td>
						 ";
				
				$codigo = "";
				$descripcion = "";
				$cant_solici = "";
				$cant_comp = "";
				$cant_Xcomp = "";
			}	
	}
	$tabla = $tabla.
			 "</tr>
			  <tr><td style='page-break-after: always;'></td></tr>
			  </table>
			  </div>
			 ";
	echo $tabla;

?>