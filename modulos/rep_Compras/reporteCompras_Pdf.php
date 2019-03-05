<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";
define('FPDF_FONTPATH','../../includes/PDF/font/');
include "../../includes/PDF/fpdf.php";

	//Create a new PDF file
	$pdf=new FPDF();
	$pdf->Open();
			
	$queryENC = "SELECT L.idLista,Lc.nom_local 
	             FROM list_regalos_enc L
		         JOIN locales Lc ON (Lc.id_local=L.id_Local)
	 			 WHERE L.idLista IN (".$idLista.") AND L.id_Estado IN ('CC');";
	$result_ENC = tep_db_query($queryENC);
	while( $res_ENC = tep_db_fetch_array( $result_ENC ) ) {	
		$id_lista = $res_ENC['idLista'].",".$id_lista;
	}

	$cont = count(split(',',$id_lista));
	$id_Lista = split(',',$id_lista);

	for ($i=0; $i<$cont-1; ++$i) 
		{
			// Inicia la construccion del PDF por cada Lista
			//Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.		
			$queryENC = "SELECT L.idLista,Lc.nom_local 
			             FROM list_regalos_enc L
				         JOIN locales Lc ON (Lc.id_local=L.id_Local)
			 			 WHERE L.idLista = ".$id_Lista[$i].";";
			$result_ENC = tep_db_query($queryENC);
			
			$pdf->AddPage();
			
			/*Titulo página*/
			//Fields Name position
			$Y_Fields_Name_position = 10;
			//Table position, under Fields Name
			$Y_Table_Position = 16;
			
			//Encabezado de sumario
			$pdf->SetFillColor(232,232,232);
			$pdf->SetFont('Arial','B',12);
			$pdf->SetY($Y_Fields_Name_position);
			$pdf->SetX(05);
			$pdf->Cell(190,6,'REPORTE DE COMPRAS LISTA DE REGALOS',1,0,'C',1);
			$pdf->Ln();
			
			
			//Fields Name position
			$Y_Fields_Name_position = 20;
			//Table position, under Fields Name
			$Y_Table_Position = 26;
			
			//Fields Name position
			$Y_Fields_Name_position = 26;
			//Table position, under Fields Name
			$Y_Table_Position = 32;
			$pdf->SetFillColor(232,232,232);
			
			//Encabezado de OT
			$pdf->SetFont('Arial','B',12);
			$pdf->SetY($Y_Fields_Name_position);
			
			$pdf->SetX(05);
			$pdf->Cell(17,6,'N° Lista',1,0,'C',1);
			$pdf->SetX(22);
			$pdf->Cell(25,6,'Tienda',1,0,'C',1);
			$pdf->SetX(47);
			$pdf->Cell(31,6,'Codigo',1,0,'C',1);
			$pdf->SetX(78);
			$pdf->Cell(70,6,'Descripción',1,0,'C',1);
			$pdf->SetX(148);
			$pdf->Cell(13,6,'C.Sol',1,0,'C',1);
			$pdf->SetX(161);
			$pdf->Cell(15,6,'C.Com',1,0,'C',1);
			$pdf->SetX(176);
			$pdf->Cell(19,6,'C.xComp',1,0,'C',1);
			$pdf->Ln();
		
			while( $res_ENC = tep_db_fetch_array( $result_ENC ) ) {	
				//Filas Resultado
				$pdf->SetFont('Arial','',9);
				$pdf->SetY($Y_Table_Position);
				
				$pdf->SetX(05);
				$pdf->MultiCell(17,6,$res_ENC['idLista'],1);
				//$pdf->SetY($Y_Table_Position);
				$pdf->SetX(22);
				$pdf->MultiCell(25,6,$res_ENC['nom_local'],1);
				//$pdf->SetY($Y_Table_Position);
				
				$queryDET = "SELECT DISTINCT LD.idLista_enc, LD.cod_Ean, LD.descripcion, (LD.list_cantprod+LD.list_Cantcomp) AS list_cantprod, LD.list_Cantcomp, 
								LD.list_cantprod AS cant_Xcomp, (LD.list_cantprod+LD.list_Cantcomp) AS cant_org
								FROM list_regalos_det LD 
								LEFT JOIN list_os_det OD ON (OD.idLista_det=LD.idLista_det) 
								LEFT JOIN list_ot OT ON (OT.ot_idList=OD.OS_idOT) 
								WHERE LD.idLista_enc = ".$res_ENC['idLista'].";";
				$res_DET = tep_db_query($queryDET);											
		
				if(tep_db_num_rows( $res_DET ) < 1){
					$pdf->MultiCell(31,6,"&nbsp;",1);
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(78);
					$pdf->MultiCell(70,6,"&nbsp;",1);
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(148);
					$pdf->MultiCell(13,6,"&nbsp;",1);
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(161);
					$pdf->MultiCell(15,6,"&nbsp;",1);
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(176);
					$pdf->MultiCell(19,6,"&nbsp;",1);
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(195);
				}
				else{
					while( $result_DET = tep_db_fetch_array( $res_DET ) ) {
						$codigo = ($result_DET['cod_Ean'])."\n".$codigo; 
						$descripcion = ($result_DET['descripcion'])."\n".$descripcion; 
						$cant_solici = ($result_DET['list_cantprod'])."\n".$cant_solici; 
						$cant_comp = ($result_DET['list_Cantcomp'])."\n".$cant_comp;
						$cant_Xcomp = ($result_DET['cant_Xcomp'])."\n".$cant_Xcomp;
					}	
					$pdf->SetY($Y_Table_Position);		
					$pdf->SetX(47);
					$pdf->MultiCell(31,6,$codigo,1,'L');
					
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(78);
					$pdf->MultiCell(70,6,$descripcion,1,'L');
					
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(148);
					$pdf->MultiCell(13,6,$cant_solici,1,'L');
					
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(161);
					$pdf->MultiCell(15,6,$cant_comp,1,'L');
					
					$pdf->SetY($Y_Table_Position);
					$pdf->SetX(176);
					$pdf->MultiCell(19,6,$cant_Xcomp,1,'L');
					
					$codigo = "";
					$descripcion = "";
					$cant_solici = "";
					$cant_comp = "";
					$cant_Xcomp = "";
				}
			}
			
		}
	
	$pdf->Output();

?>
