<?
/* dalos permisos , ver como */
$pag_ini = '../facturacion/facturacion_00.php';
include "../../includes/aplication_top.php";
define('FPDF_FONTPATH','../../includes/PDF/font/');
include "../../includes/PDF/fpdf.php";




//Initialize the 6 columns de instalador
$column_rut="";
$column_nombre="";
$column_telefono="";
$column_direccion="";
$column_email="";

//Initialize the 6 columns de datos lote
$column_lote="";
$column_nfactura="";
$column_estado="";
$column_fecha="";
$column_usuario="";
$column_numero1="";
$column_numero2="";

//Initialize the 6 columns de la factura
$column_not = "";
$column_descripcion = "";
$column_tipo_ot = "";
$column_precio = "";
$column_margen = "";
$column_subtotal = "";
$total = 0;

//Select the Products you want to show in your PDF file
$query_re="select O.id_instalador,O.ot_id,O.ot_tipo,OD.osde_descripcion,OD.osde_precio,O.margenapagar,O.subtotalapagar, O.id_os ,if (OD.osde_descuento<>0,ROUND((OD.osde_precio-OD.osde_descuento)*OD.osde_cantidad),ROUND(OD.osde_precio*OD.osde_cantidad)) 'sub_total'
		from ot O 
		join os_detalle OD on OD.ot_id=O.ot_id
		join lote_instalador LI on O.id_lote=LI.id_lote
		where LI.id_lote=".$id_lote;
		if ( $rq = tep_db_query($query_re) ){
			while( $res = tep_db_fetch_array( $rq ) ) {
				$n_ot = $res["ot_id"];
				$descripcion = $res["osde_descripcion"];
				$tipo_ot = substr($res["ot_tipo"],0,20);
				$precio= $res["osde_precio"];
				$precio_to_show = number_format($res["osde_precio"],',','.','.');
				$margen= $res["margenapagar"];
				$subtotal=  number_format($res["subtotalapagar"],',','.','.');
				$id_instalador=$res['id_instalador'];
				$column_not = $column_not.$n_ot."\n";
				$column_descripcion = $column_descripcion.$descripcion."\n";
				$column_tipo_ot = $column_tipo_ot.$tipo_ot."\n";
				$column_precio = $column_precio.$precio_to_show."\n";
				$column_margen = $column_margen.$margen."%\n";
				$column_subtotal = $column_subtotal.$subtotal."\n";
				$total = $total+$precio;
			}
		}

/*para el termino de la factura*/
			$qry_fac="select id_lote,estado,fechageneracion,usuario,monto_factura,retencion_lote,num_factura,numero1,numero2 from lote_instalador where id_lote=".$id_lote;
					$rq = tep_db_query($qry_fac);
					$res1 = tep_db_fetch_array( $rq );
/*para que llene los ultimas filas*/			
				$column_not = $column_not." "."\n";
				$column_tipo_ot = $column_tipo_ot." "."\n";
				$column_descripcion = $column_descripcion."SUB TOTAL"."\n";
				$column_precio = $column_precio." "."\n";
				$column_margen = $column_margen." "."\n";
				//$column_subtotal = $column_subtotal.number_format($res1["monto_factura"],',','.','.')."\n";
				$column_subtotal = $column_subtotal.number_format(($res1["retencion_lote"]+$res1["monto_factura"]),',','.','.')."\n";
				$column_not = $column_not." "."\n";
				$column_tipo_ot = $column_tipo_ot." "."\n";
				$column_descripcion = $column_descripcion."RETENCIÓN FONDO GARANTÍA"."\n";
				$column_precio = $column_precio." "."\n";
				$column_margen = $column_margen." "."\n";
				$column_subtotal = $column_subtotal.number_format($res1["retencion_lote"],',','.','.')."\n";

				$column_not = $column_not." "."\n";
				$column_tipo_ot = $column_tipo_ot." "."\n";
				$column_descripcion = $column_descripcion."TOTAL A PAGAR"."\n";
				$column_precio = $column_precio." "."\n";
				$column_margen = $column_margen." "."\n";

				$column_subtotal = $column_subtotal.number_format($res1["monto_factura"],',','.','.')."\n";
				

/*para los datos del lote*/
			$column_lote= $column_lote.$res1["id_lote"]."\n";
			$column_nfactura=$column_nfactura.$res1["num_factura"]."\n";
			if ($res1["estado"]=='P'){
				$estado='Pre-Facturado';
			}
			if ($res1["estado"]=='F'){
				$estado='Facturado';
			}
			$column_estado=$estado."\n";
			$column_fecha=$res1["fechageneracion"]."\n";
			$column_usuario=$res1["usuario"]."\n";
			$column_numero1=$res1["numero1"]."\n";
			$column_numero2=$res1["numero2"]."\n";

/*para los datos del instalador*/
	$qry_ins="select distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno,i.inst_telefono,i.direccion,i.email 		from instaladores i
	where i.id_instalador=".($id_instalador+0);
		$rq = tep_db_query($qry_ins);
		$res = tep_db_fetch_array( $rq );
				$column_rut =($res["inst_rut"]."-".dv($res["inst_rut"]))."\n";
				$nombre=$res["inst_nombre"]." ".$res["inst_paterno"]." ".$res["inst_materno"];
				$nombre=substr(trim($nombre),0,30);
				$column_nombre= $nombre."\n";
				$column_telefono = $res["inst_telefono"]."\n";
				$column_direccion=$res["direccion"]."\n";
				$column_email= $res["email"]."\n";


mysql_close();

//Convert the Total Price to a number with (.) for thousands, and (,) for decimals.
$total = number_format($total,',','.','.');

//Create a new PDF file
$pdf=new FPDF();
$pdf->Open();
$pdf->AddPage();

/*titulo página*/
//Fields Name position
$Y_Fields_Name_position = 10;
//Table position, under Fields Name
$Y_Table_Position = 16;

//para los datos del instalador
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos Facturación',1,0,'C',1);
$pdf->Ln();


//Fields Name position
$Y_Fields_Name_position = 20;
//Table position, under Fields Name
$Y_Table_Position = 26;

//para los datos del instalador
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos Instalador',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 25;
//Table position, under Fields Name
$Y_Table_Position = 31;
$pdf->SetFillColor(232,232,232);
//encabezado de lote
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(25,6,'Rut',1,0,'L',1);
$pdf->SetX(30);
$pdf->Cell(45,6,'Nombre',1,0,'L',1);
$pdf->SetX(75);
$pdf->Cell(25,6,'Telefono',1,0,'L',1);
$pdf->SetX(100);
$pdf->Cell(40,6,'Direccion',1,0,'L',1);
$pdf->SetX(140);
$pdf->Cell(55,6,'Email',1,0,'L',1);
$pdf->Ln();
//Now show the 6 columns
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(25,6,$column_rut,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(30);
$pdf->MultiCell(45,6,$column_nombre,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(75);
$pdf->MultiCell(25,6,$column_telefono,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(100);
$pdf->MultiCell(40,6,$column_direccion,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(140);
$pdf->MultiCell(55,6,$column_email,1,'L');

/*para los datos del lote*/
//Fields Name position
$Y_Fields_Name_position = 40;
//Table position, under Fields Name
$Y_Table_Position = 46;
//Gray color filling each Field Name box
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos Lote',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 46;
//Table position, under Fields Name
$Y_Table_Position = 52;
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(25,6,'Nº Lote',1,0,'L',1);
$pdf->SetX(30);
$pdf->Cell(80,6,'Nº Factura',1,0,'L',1);
$pdf->SetX(90);
$pdf->Cell(25,6,'Estado',1,0,'L',1);
$pdf->SetX(115);
$pdf->Cell(50,6,'Fecha Generación',1,0,'L',1);
$pdf->SetX(165);
$pdf->Cell(30,6,'Usuario',1,0,'L',1);
$pdf->Ln();

//Now show the 6 columns
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(25,6,$column_lote,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(30);
$pdf->MultiCell(60,6,$column_nfactura,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(90);
$pdf->MultiCell(25,6,$column_estado,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(115);
$pdf->MultiCell(50,6,$column_fecha,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(165);
$pdf->MultiCell(30,6,$column_usuario,1);
//Fields Name position
$Y_Fields_Name_position = 58;
//Table position, under Fields Name
$Y_Table_Position = 64;
$pdf->SetFillColor(232,232,232);
//comentario
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(50,6,'Número 1 O/C',1,0,'L',1);
$pdf->SetX(55);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(45,6,$column_numero1,1,'R');
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(100);
$pdf->Cell(45,6,'Número 2 O/C',1,0,'L',1);
$pdf->SetX(145);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(50,6,$column_numero2,1,'R');


//Fields Name position
$Y_Fields_Name_position = 70;
//Table position, under Fields Name
$Y_Table_Position = 76;

//Gray color filling each Field Name box
$pdf->SetFillColor(232,232,232);
//Bold Font for Field Name
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(20,6,'Nº OT',1,0,'L',1);
$pdf->SetX(25);
$pdf->Cell(20,6,'Tipo OT',1,0,'L',1);
$pdf->SetX(45);
$pdf->Cell(55,6,'Descripción',1,0,'L',1);
$pdf->SetX(100);
$pdf->Cell(35,6,'Costo Servicio',1,0,'L',1);
$pdf->SetX(135);
$pdf->Cell(30,6,'Porcentaje',1,0,'L',1);
$pdf->SetX(165);
$pdf->Cell(30,6,'Sub Total',1,0,'L',1);
$pdf->Ln();

//Now show the 6 columns
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(20,6,$column_not,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(25);
$pdf->MultiCell(20,6,$column_tipo_ot,1,'L');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(45);
$pdf->MultiCell(55,6,$column_descripcion,1,'L');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(100);
$pdf->MultiCell(35,6,$column_precio,1,'R');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(135);
$pdf->MultiCell(30,6,$column_margen,1,'C');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(165);
$pdf->MultiCell(30,6,$column_subtotal,1,'R');



//Create lines (boxes) for each ROW (Product)
//If you don't use the following code, you don't create the lines separating each row
$i = 0;
$pdf->SetY($Y_Table_Position);
while ($i < $number_of_products)
{
    $pdf->SetX(45);
    $pdf->MultiCell(120,6,'',1);
    $i = $i +1;
}

$pdf->Output();



?>
