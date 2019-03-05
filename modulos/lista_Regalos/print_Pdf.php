<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";
define('FPDF_FONTPATH','../../includes/PDF/font/');
include "../../includes/PDF/fpdf.php";
$USR_LOGIN = get_login_usr( $ses_usr_id );

	//Create a new PDF file
	$pdf=new FPDF();
	$pdf->Open();

	// Inicia la construccion del PDF por cada Lista
	//Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.	
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
			
	$pdf->AddPage();
			
	/*Titulo página*/
	$Y_Fields_Name_position = 10;
	$Y_Table_Position = 16;
	$pdf->SetFillColor(232,232,232);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(05);
	$pdf->Cell(190,6,'LISTA DE REGALOS N° .'.$idLista.' ',1,0,'C',1);
	$pdf->Ln();
	
			
	//Encabezado "Datos del Evento"
	$pdf->SetFillColor(232,232,232);
	$Y_Fields_Name_position = 20;
	$Y_Table_Position = 26;
	$pdf->SetFont('Arial','B',12);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(05);
	$pdf->Cell(190,6,'Datos del Evento',1,0,'L',1);
	$pdf->Ln();
	
	//Fila 1
	$pdf->SetX(05);
	$pdf->Cell(40,6,'Evento',1,0,'L',1);
	$pdf->SetX(45);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(70,6,$row['nombre'],1);
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(115);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(30,6,'Fecha',1,0,'L',1);
	$pdf->SetX(145);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(50,6,$row['Fecha'],1);
	$pdf->Ln();
	//Fila 2
	$Y_Fields_Name_position = 26;
	$Y_Table_Position = 32;
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(05);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(40,6,'Fecha de Entrega',1,0,'L',1);
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(45);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(70,6,$row['fec_Evento'],1);
	//Fila 3
	$Y_Fields_Name_position = 32;
	$Y_Table_Position = 38;
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(05);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(40,6,'Festejado(s)',1,0,'L',1);
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(45);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(70,6,$row['festejado'],1);
	
	//Encabezado "Datos del Evento"
	$pdf->SetFillColor(232,232,232);
	$Y_Fields_Name_position = 44;
	$Y_Table_Position = 50;
	$pdf->SetFont('Arial','B',12);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(05);
	$pdf->Cell(190,6,'Datos de Creacion',1,0,'L',1);
	$pdf->Ln();
	
	//Fila 1
	$pdf->SetX(05);
	$pdf->Cell(40,6,'Tienda',1,0,'L',1);
	$pdf->SetX(45);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(70,6,$row['nom_local'],1);
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(115);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(30,6,'Creada el',1,0,'L',1);
	$pdf->SetX(145);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(50,6,$row['fec_creacion'],1);
	$pdf->Ln();
	//Fila 2
	$Y_Fields_Name_position = 50;
	$Y_Table_Position = 56;
	$pdf->SetY($Y_Table_Position);
	$pdf->SetX(115);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(30,6,'Hora',1,0,'L',1);
	$pdf->SetX(145);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(50,6,$row['hor_creacion'],1);
	$pdf->Ln();
	
	
	//Fields Name position
	$Y_Fields_Name_position = 66;
	//Table position, under Fields Name
	$Y_Table_Position = 72;
	$pdf->SetFillColor(232,232,232);
			
	//Encabezado de OT
	$pdf->SetFont('Arial','B',12);
	$pdf->SetY($Y_Fields_Name_position);
			
	$pdf->SetX(05);
	$pdf->Cell(45,6,'Codigo',1,0,'C',1);
	$pdf->SetX(50);
	$pdf->Cell(90,6,'Descripción',1,0,'C',1);
	$pdf->SetX(140);
	$pdf->Cell(35,6,'Precio',1,0,'C',1);
	$pdf->SetX(175);
	$pdf->Cell(20,6,'Cantidad',1,0,'C',1);
	$pdf->Ln();
		
	while( $res_ENC = tep_db_fetch_array( $result_ENC ) ) {	
				
			$queryDET = "SELECT * FROM list_regalos_det  WHERE idLista_enc = ".$res_ENC['idLista'].";";
			$res_DET = tep_db_query($queryDET);											
	
			if(tep_db_num_rows( $res_DET ) < 1){
				$pdf->MultiCell(31,6," ",1);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(78);
				$pdf->MultiCell(70,6," ",1);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(148);
				$pdf->MultiCell(13,6," ",1);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(161);
				$pdf->MultiCell(15,6," ",1);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(176);
				$pdf->MultiCell(19,6," ",1);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(195);
			}
			else{
				while( $result_DET = tep_db_fetch_array( $res_DET ) ) {
					$codigo = ($result_DET['cod_Ean'])."\n".$codigo; 
					$descripcion = ($result_DET['descripcion'])."\n".$descripcion; 
					$cantidad = ($result_DET['list_cantprod'])."\n".$cantidad; 
					$precio = (formato_precio($result_DET['list_precio']))."\n".$precio;
				}	
				//Filas Resultado
				$pdf->SetFont('Arial','',9);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetY($Y_Table_Position);	
					
				$pdf->SetX(5);
				$pdf->MultiCell(45,6,$codigo,1,'C');
					
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(50);
				$pdf->MultiCell(90,6,$descripcion,1,'L');
				
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(140);
				$pdf->MultiCell(35,6,$precio,1,'R');
					
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(175);
				$pdf->MultiCell(20,6,$cantidad,1,'R');
					
				$codigo = "";
				$descripcion = "";
				$cant_solici = "";
				$cant_comp = "";
				$cant_Xcomp = "";
			}
		}
		
		$pdf->AddPage();
			
	/*Titulo página*/
	$Y_Fields_Name_position = 10;
	$Y_Table_Position = 16;
	$pdf->SetFillColor(232,232,232);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(05);
	$pdf->Cell(190,6,'CONDICIONES COMERCIALES',1,0,'C',1);
	$pdf->Ln();
	
	//Encabezado "Condiciones Comerciales"
	//Condicion1
	$Y_Fields_Name_position = 16;
	$Y_Table_Position = 22;
	$pdf->SetFont('Arial','',10);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(05);
	$pdf->MultiCell(190,6,'Los productos incluidos en este documento están sujetos a cambios de precio de acuerdo a la tienda en la cual se adquieran y a eventos comerciales definidos por Easy Colombia. El precio de venta de cada producto corresponderá al precio vigente al momento de la compra.',1);
	//Condicion2
	$pdf->SetX(05);
	$pdf->MultiCell(190,6,'La disponibilidad de los productos dependerá de su existencia en el inventario de la tienda al momento de la compra. Easy no se compromete a separar los productos sin su pago previo.',1);
	//Condicion3
	$pdf->SetX(05);
	$pdf->MultiCell(190,6,'La entrega de los productos comprados se realizará en la fecha acordada al momento de la creación de la Lista de Regalos y que consta en este documento.',1);
	//Condicion4
	$pdf->SetX(05);
	$pdf->MultiCell(190,6,'Una vez comprado un producto de la lista de regalos, no se harán devoluciones.',1);
	$pdf->Ln();
	
	//Firmas de recibido
	$Y_Fields_Name_position = 70;
	$Y_Table_Position = 76;
	$pdf->SetFont('Arial','B',12);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(05);
	$pdf->MultiCell(85,25,'Firma Asesor Easy: ________________ ',1);
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetX(90);
	$pdf->MultiCell(100,25,'Firma Aceptado Cliente: ________________ ',1);
	

	$pdf->Output();
	
	insertahistorial_ListaReg("Se imprime el archivo PDF para la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
?>
