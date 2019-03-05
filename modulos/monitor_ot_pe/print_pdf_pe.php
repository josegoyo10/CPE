<?
/* dalos permisos , ver como */
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";
define('FPDF_FONTPATH','../../includes/PDF/font/');
include "../../includes/PDF/fpdf.php";

//Initialize datos OT
$column_notcp=$id_ot;
$column_tipoot="PE - Producto Especial";
$column_fecha="";
$column_oc="";
$column_recsap="";

//Initialize the 6 columns de datos cotizacion
$column_cot="";
$column_fechaco="";
$column_estado="";
$column_atendido="";

$column_loc="";
$column_fechaes="";
$column_osdes="";
$column_proy="";
$column_comentario="";



//Initialize the  detalle
$column_upc="";
$column_tipo="";
$column_descripcion="";
$column_despacho="";
$column_inst="";
$column_cant="";
$column_precio="";
$column_tot="";
$total = 0;

//Select the Products you want OT y cotizacion
$qry="SELECT DISTINCT L.nom_local,S.os_fechacotizacion,S.os_fechaestimacion,S.os_descripcion,S.os_comentarios,P.proy_nombre,t.ot_id, t.noc_sap,o.id_os,date_format(ot_fechacreacion, '%d/%m/%Y ')ot_fechacreacion ,e.esta_nombre,
		CONCAT(C.clie_nombre,' ',C.clie_paterno,' ',C.clie_materno) AS cliente, C.clie_rut AS cedula, D.dire_direccion, D.dire_telefono, t.desp_ddp, t.ot_comentario
		FROM os_detalle o
		JOIN ot t ON (o.id_os=t.id_os)
		JOIN os S ON (S.id_os=t.id_os)
		JOIN estados e ON (e.id_estado=S.id_estado)
		JOIN clientes C ON C.clie_rut = S.clie_rut
		JOIN direcciones D ON D.clie_rut =  C.clie_rut
		INNER JOIN locales L ON (L.id_local=S.id_local)
		INNER JOIN  proyectos P ON  (P.id_proyecto=S.id_proyecto)
	 WHERE t.ot_id=". ($id_ot + 0)." AND D.dire_defecto='p'";
	$rq = tep_db_query($qry);
	$res = tep_db_fetch_array( $rq );
	$column_fecha=$res['ot_fechacreacion']."\n";
	$column_oc=$res['noc_sap']."\n";
	$column_recsap="";
	$id_os=$res['id_os'];
	$estado=$res['esta_nombre'];
	$nom_local=trim($res['nom_local']);

	$column_cot=$id_os;
	$column_estado=$estado;
	$column_fechaco=fecha_db2php($res['os_fechacotizacion']);

	$column_loc=$nom_local;
	$column_fechaes=fecha_db2php($res['os_fechaestimacion']);
	$column_osdes=substr($res['os_descripcion'],0,30);
	$column_proy=substr($res['proy_nombre'],0,30);
	$column_comentario=substr($res['os_comentarios'],200);
	
	$column_cliente=$res['cliente'];
	$column_cedula=$res['cedula'];
	$column_direcion=$res['dire_direccion'];
	$column_telefono=$res['dire_telefono'];
	
	// Consulta el Barrio-Localidad de los datos del cliente
	$dirCli = consulta_localizacion($res['cedula'],1);
	$dirCliente = getlocalizacion($dirCli);
	$column_barrio=  $dirCliente['barrio']." - ".$dirCliente['localidad'];
	$column_ciudad= $dirCliente['ciudad'];
	$column_depto= $dirCliente['departamento'];
	
	if($res['desp_ddp'] == 0)
		$column_desp_por = 'Tienda';	 
		else
			$column_desp_por = 'Proveedor';
		
	$column_desp_obs=$res['ot_comentario'];
	

 /* para el nombre del que atendio*/
	$qnombre="select U.usr_nombres from usuarios U inner join os OS on (OS.	usr_id=U.usr_id) where id_os=".($id_os+0)."";
	$rq1 = tep_db_query($qnombre);
	$res1 = tep_db_fetch_array( $rq1 );
	$column_atendido=$res1['usr_nombres'];

	/*para que llene los ultima tabla*/			
	$instn="No";
	$insts="Sí";
	$precioVacio=" - ";
	$totalVacio=" 0 ";
	$especificacion="              Especificaciones =  ";

/* precios y total parciado*/
    $query_OD="SELECT DISTINCT OD.osde_especificacion, (OD.cod_sap+0) as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio, O.os_descripcion, 
	TD.nombre as tipo_nombre ,OD.osde_descuento, D.dire_observacion, D.dire_telefono, D.dire_direccion,
	if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento', 
	if(osde_descuento<>0, ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total', 
	if(((osde_precio-osde_descuento)*osde_cantidad) is Null,' 0 ','')'totalVacio' ,
 	if(OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre', 
	if(OD.osde_tipoprod='SV', ' - ', if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion', 
	if(OD.osde_precio is null, ' - ', '') 'precioVacio' ,if(OD.osde_especificacion is not null, ' Especificaciones = ', '') 'especificacion' 
	FROM os_detalle OD 
	INNER JOIN tipos_despacho TD ON (TD.id_tipodespacho=OD.id_tipodespacho) 
	JOIN os O ON O.id_os= OD.id_os
	JOIN direcciones D ON D.id_direccion= O.id_direccion
	WHERE OD.id_os=".($id_os+0)."  AND OD.ot_id =".($id_ot+0)." ORDER BY OD.id_os_detalle DESC ";
	if ( $rq = tep_db_query($query_OD) ){
            while( $resOD = tep_db_fetch_array( $rq ) ) {
   				$column_upc = $column_upc.$resOD['cod_barra']." (".$resOD['cod_sap'].")"."\n";
				$column_tipo = $column_tipo.$resOD['osde_tipoprod']."\n";
				$column_descripcion = $column_descripcion.$resOD['osde_descripcion'].$resOD['especificacion'].$resOD['osde_especificacion']."\n";
				$column_despacho=$column_despacho.substr(trim($resOD['tipo_nombre']),0,13)."\n";
				$column_inst=$column_inst.$resOD['osde_instalacion']."\n";
				$column_cant=$column_cant.$resOD['osde_cantidad']."\n";
				$column_precio=$column_precio.formato_precio($resOD['osde_precio'])."\n";
				$total=$total+$resOD['osde_precio'];
				$column_tot=$column_tot.formato_precio($resOD['Total'])."\n";
			}
	}
	$rq = tep_db_query($query_OD);
	$resOD = tep_db_fetch_array( $rq );
	$column_direOD= $resOD['dire_direccion']."\n";
	$column_indicaOD= $resOD['dire_observacion']."\n";
	$column_observaOD= $resOD['os_descripcion']."\n";	
	
	// Consulta la direccción de Servicio de la cotización.
	$queryDir="SELECT O.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
				FROM os O
				JOIN direcciones D ON D.id_direccion=O.id_direccion
				WHERE id_os = $id_os";
	
	$osSelDir = tep_db_query($queryDir);
	$osSelDire = tep_db_fetch_array( $osSelDir );
	
	$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
	$dirServicio = getlocalizacion($dirServ);
	$column_barrioOD= $dirServicio['barrio']." - ".$dirServicio['localidad'];
	$column_telefonoOD= $osSelDire['dire_telefono']."\n";
	$column_ciudadOD= $dirServicio['ciudad']."\n";
	$column_deptoOD= $dirServicio['departamento']."\n";

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
$pdf->Cell(190,6,'Datos Orden  de Trabajo Proveedor(OT)  /  Orden de Compra(OC)',1,0,'C',1);
$pdf->Ln();


//Fields Name position
$Y_Fields_Name_position = 20;
//Table position, under Fields Name
$Y_Table_Position = 26;

//para los datos del OT
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos Orden de Trabajo',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 25;
//Table position, under Fields Name
$Y_Table_Position = 31;
$pdf->SetFillColor(232,232,232);

//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(25,6,'Nº OT CP',1,0,'L',1);
$pdf->SetX(30);
$pdf->Cell(60,6,'Tipo OT',1,0,'L',1);
$pdf->SetX(85);
$pdf->Cell(50,6,'Fecha Generación',1,0,'L',1);
$pdf->SetX(135);
$pdf->Cell(30,6,'Nº OC/SAP',1,0,'L',1);
$pdf->SetX(165);
$pdf->Cell(30,6,'Nº REC/SAP',1,0,'L',1);
$pdf->Ln();

//Now datos de la ot
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(25,6,$column_notcp,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(30);
$pdf->MultiCell(55,6,$column_tipoot,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(85);
$pdf->MultiCell(50,6,$column_fecha,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(135);
$pdf->MultiCell(30,6,$column_oc,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(165);
$pdf->MultiCell(30,6,$column_recsap,1);


/***********************************************para los datos de la cotización*/
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
$pdf->Cell(190,6,'Datos Cotización',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 46;
//Table position, under Fields Name
$Y_Table_Position = 52;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(45,6,'Nº Cotización',1,0,'C',1);
$pdf->SetX(50);
$pdf->Cell(40,6,'Fecha Cotización',1,0,'C',1);
$pdf->SetX(90);
$pdf->Cell(55,6,'Estado',1,0,'C',1);
$pdf->SetX(145);
$pdf->Cell(50,6,'Atendido Por',1,0,'C',1);
$pdf->Ln();
//Now show the 6 columns
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(45,6,$column_cot,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(50);
$pdf->MultiCell(40,6,$column_fechaco,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(90);
$pdf->MultiCell(55,6,$column_estado,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(145);
$pdf->MultiCell(50,6,$column_atendido,1);
/*segunda parte de la tabla*/
//Fields Name position
$Y_Fields_Name_position = 58;
//Table position, under Fields Name
$Y_Table_Position = 64;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(45,6,'Tienda',1,0,'L',1);
$pdf->SetX(50);
$pdf->Cell(40,6,'Validez',1,0,'L',1);
$pdf->SetX(90);
$pdf->Cell(55,6,'Descripción',1,0,'L',1);
$pdf->SetX(145);
$pdf->Cell(50,6,'Proyecto',1,0,'L',1);
$pdf->Ln();

//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(45,6,$column_loc,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(50);
$pdf->MultiCell(40,6,$column_fechaes,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(90);
$pdf->MultiCell(55,6,$column_osdes,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(145);
$pdf->MultiCell(50,6,$column_proy,1);
//Fields Name position
$Y_Fields_Name_position = 70;
//Table position, under Fields Name
$Y_Table_Position = 80;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(45,6,'Comentarios',1,0,'L',1);
$pdf->SetX(50);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(145,6,$column_comentario,1,'L');

//Fields Name position
$Y_Fields_Name_position = 80;
//Table position, under Fields Name
$Y_Table_Position = 86;
//Gray color filling each Field Name box
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos del Cliente',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 86;
//Table position, under Fields Name
$Y_Table_Position = 92;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(45,6,'Cédula',1,0,'C',1);
$pdf->SetX(50);
$pdf->Cell(95,6,'Nombre',1,0,'C',1);
$pdf->SetX(145);
$pdf->Cell(50,6,'Teléfono',1,0,'C',1);

//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(45,6,$column_cedula,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(50);
$pdf->MultiCell(95,6,$column_cliente,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(145);
$pdf->MultiCell(50,6,$column_telefono,1);
$pdf->Ln();
/*segunda parte de la tabla*/

//Fields Name position
$Y_Fields_Name_position = 98;
//Table position, under Fields Name
$Y_Table_Position = 104;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(45,6,'Barrio - Localidad',1,0,'C',1);
$pdf->SetX(50);
$pdf->Cell(85,6,'Dirección',1,0,'C',1);
$pdf->SetX(135);
$pdf->Cell(30,6,'Ciudad',1,0,'C',1);
$pdf->SetX(165);
$pdf->Cell(30,6,'Departamento',1,0,'C',1);

//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(45,6,$column_barrio,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(50);
$pdf->MultiCell(85,6,$column_direcion,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(135);
$pdf->MultiCell(30,6,$column_ciudad,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(165);
$pdf->MultiCell(30,6,$column_depto,1);
$pdf->SetY($Y_Table_Position);
/*segunda parte de la tabla*/


//Fields Name position
$Y_Fields_Name_position = 114;
//Table position, under Fields Name
$Y_Table_Position = 120;
//Gray color filling each Field Name box
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Dirección de Servicio (Despacho,Instalación) ',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 120;
//Table position, under Fields Name
$Y_Table_Position = 126;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(70,6,'Dirección',1,0,'C',1);
$pdf->SetX(75);
$pdf->Cell(70,6,'Barrio - Localidad',1,0,'C',1);
$pdf->SetX(145);
$pdf->Cell(50,6,'Teléfono',1,0,'C',1);
//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(70,6,$column_direOD,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(75);
$pdf->MultiCell(70,6,$column_barrioOD,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(145);
$pdf->MultiCell(50,6,$column_telefonoOD,1);
$pdf->SetY($Y_Table_Position);
/*segunda parte de la tabla*/

//Fields Name position
$Y_Fields_Name_position = 132;
//Table position, under Fields Name
$Y_Table_Position = 138;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(35,6,'Ciudad',1,0,'C',1);
$pdf->SetX(40);
$pdf->Cell(35,6,'Departamento',1,0,'C',1);
$pdf->SetX(75);
$pdf->Cell(120,6,'Indicación',1,0,'C',1);
//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(35,6,$column_ciudadOD,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(40);
$pdf->MultiCell(35,6,$column_deptoOD,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(75);
$pdf->MultiCell(120,6,$column_indicaOD,1);
$pdf->SetY($Y_Table_Position);
/*segunda parte de la tabla*/

//Fields Name position
$Y_Fields_Name_position = 144;
//Table position, under Fields Name
$Y_Table_Position = 150;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(70,6,'Despachar Por',1,0,'C',1);
$pdf->SetX(75);
$pdf->Cell(120,6,'Observación',1,0,'C',1);
//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(70,6,$column_desp_por,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(75);
$pdf->MultiCell(120,6,$column_observaOD,1);
$pdf->SetY($Y_Table_Position);
/*segunda parte de la tabla*/

//Fields Name position
$Y_Fields_Name_position = 160;
//Table position, under Fields Name
$Y_Table_Position = 166;

//Gray color filling each Field Name box
$pdf->SetFillColor(232,232,232);
//Bold Font for Field Name
$pdf->SetFont('Arial','B',10);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(40,6,'UPC/(SKU)',1,0,'L',1);
$pdf->SetX(45);
$pdf->Cell(10,6,'Tipo',1,0,'C',1);
$pdf->SetX(55);
$pdf->Cell(45,6,'Descripción',1,0,'C',1);
$pdf->SetX(100);
$pdf->Cell(20,6,'Despacho',1,0,'C',1);
$pdf->SetX(120);
$pdf->Cell(20,6,'Instalación',1,0,'C',1);
$pdf->SetX(140);
$pdf->Cell(15,6,'Cant',1,0,'C',1);
$pdf->SetX(155);
$pdf->Cell(20,6,'Precio',1,0,'C',1);
$pdf->SetX(175);
$pdf->Cell(20,6,'Total',1,0,'C',1);
$pdf->Ln();
//Now show the 6 columns
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(40,6,$column_upc,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(45);
$pdf->MultiCell(10,6,$column_tipo,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(55);
$pdf->MultiCell(45,6,$column_descripcion,1,'L');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(100);
$pdf->MultiCell(20,6,$column_despacho,1,'L');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(120);
$pdf->MultiCell(20,6,$column_inst,1,'C');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(140);
$pdf->MultiCell(15,6,$column_cant,1,'R');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(155);
$pdf->MultiCell(20,6,$column_precio,1,'R');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(175);
$pdf->MultiCell(20,6,$column_tot,1,'R');




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
