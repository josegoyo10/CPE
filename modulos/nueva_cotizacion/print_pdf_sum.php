<?
/* dalos permisos , ver como */
$SIN_PER = 1;
include "../../includes/aplication_top.php";
define('FPDF_FONTPATH','../../includes/PDF/font/');
include "../../includes/PDF/fpdf.php";

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

/*columanas para los datos del cliente*/
$column_rut="";
$column_nombre="";
$column_direccion_pri="";
$column_comuna_pri="";
$column_telefono_pri="";

/*columanas para direccion de despacho*/
$column_telefono_des="";
$column_direccion_des="";
$column_comuna_des="";
$column_indicaciones="";

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

//selecciona los datos del encabezado de la cotización
$qry="SELECT S.os_fechaestimacion, S.clie_rut, S.id_os, S.id_estado ,S.id_local, S.os_descripcion, S.os_comentarios, S.id_proyecto,
		S.os_fechacotizacion, E.esta_nombre, L.nom_local, P.proy_nombre, CL.clie_rut, CL.clie_nombre, CL.clie_tipo, CL.clie_razonsocial,
		CL.clie_paterno, CL.clie_materno,
		if('SM'=S.id_estado, '$pagar', '') 'pagar', 
		if (date(os_fechaestimacion)>=date(now()), 1, 0) esfechavalida, 
		if(S.id_local=".(get_local_usr( $ses_usr_id )+0).", 1, 0) eslocalcorrecto
		FROM os S
		JOIN estados E ON E.id_estado = S.id_estado
		JOIN locales L ON L.id_local = S.id_local
		JOIN proyectos P ON P.id_proyecto = S.id_proyecto
		JOIN clientes CL ON CL.clie_rut = S.clie_rut
		WHERE S.id_os=".($id_os+0);
	
	$rq = tep_db_query($qry);
	$res = tep_db_fetch_array( $rq );
	
	$id_os=$res['id_os'];
	$estado=$res['esta_nombre'];
	$nom_local=trim($res['nom_local']);

	$column_cot=$id_os;
	$column_estado=$estado;
	$column_fechaco=fecha_db2php($res['os_fechacotizacion']);

	$column_loc=$nom_local;
	$column_fechaes=fecha_db2php($res['os_fechaestimacion']);
	$column_osdes=substr($res['os_descripcion'],0,30);
	$column_proy=substr($res['proy_nombre'],0,40);
	$column_comentario=substr($res['os_comentarios'],0,120);
	
	
	// Consulta la direccción de Servicio de la cotización.
	$queryDir="SELECT O.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
				FROM os O
				JOIN direcciones D ON D.id_direccion=O.id_direccion
				WHERE id_os = ".($id_os+0);
	
	$osSelDir = tep_db_query($queryDir);
	$osSelDire = tep_db_fetch_array( $osSelDir );
	
	$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
	$dirServicio = getlocalizacion($dirServ);
	
	//Para la direccion de despacho
	$column_telefono_des=$osSelDire['dire_telefono']."\n";
	$column_direccion_des=substr($osSelDire['dire_direccion'],0,100)."\n";
	$column_comuna_des=$dirServicio['barrio'];
	$column_localidad_des=$dirServicio['localidad']."\n";
	$column_departamento_des=$dirServicio['departamento']."\n";
	$column_ciudad_des=$dirServicio['ciudad']."\n";
	$column_indicaciones=$osSelDire['dire_observacion']."\n";
	
    //Para el nombre del que atendio
	$qnombre="select U.usr_nombres from usuarios U inner join os OS on (OS.	usr_id=U.usr_id) where id_os=".($id_os+0)."";
	$rq1 = tep_db_query($qnombre);
	$res1 = tep_db_fetch_array( $rq1 );
	$column_atendido=$res1['usr_nombres'];

/* datos del cliente*/
$queryPRI="SELECT CL.clie_nombre,CL.clie_tipo,CL.clie_razonsocial,CL.clie_paterno,CL.clie_materno,OS.id_direccion,OS.clie_rut,D.id_direccion,D.dire_nombre as dire_nombre_pri ,D.dire_direccion as dire_direccion_pri,
				D.id_direccion ,D.dire_defecto ,D.dire_telefono as dire_telefono_pri ,D.dire_observacion as dire_observacion_pri
				FROM os OS
				LEFT JOIN direcciones D ON (OS.clie_rut= D.clie_rut)
				LEFT JOIN clientes CL ON (OS.clie_rut=CL.clie_rut)
			where OS.id_os=".($id_os+0)." and D.dire_defecto='p'";
$rq_pri = tep_db_query($queryPRI);
$res_pri = tep_db_fetch_array( $rq_pri );

// Consulta el Barrio-Localidad de los datos del cliente
$dirCli = consulta_localizacion($res_pri['clie_rut'],1);
$dirCliente = getlocalizacion($dirCli);

$column_rut = $res_pri['clie_rut'];
$nombre_clie= $res_pri['clie_nombre']." ".$res_pri['clie_paterno']." ".$res_pri['clie_materno']."".$res_pri['clie_razonsocial']."\n";
$column_nombre=substr($nombre_clie,0,50)."\n";
$column_direccion_pri=$res_pri['dire_direccion_pri']."\n";
$column_comuna_pri=$dirCliente['barrio'];
$column_localidad=substr($dirCliente['localidad'],0,50)."\n";
$column_departamento=substr($dirCliente['departamento'],0,80)."\n";
$column_ciudad= substr($dirCliente['ciudad'],0,80)."\n";
$column_telefono_pri=$res_pri['dire_telefono_pri']."\n";


	/*para que llene los datos ultima tabla*/			
	$instn="No";
	$insts="Sí";
	$precioVacio=" - ";
	$totalVacio=" 0 ";
	$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
	$suma=0;
/* precios y total parciado*/
    $query_OD="select OD.osde_especificacion, (OD.cod_sap+0) as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,TD.nombre as tipo_nombre ,OD.osde_descuento,
		if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
		if(osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
		if(((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','')'totalVacio' ,
		if(OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre',if(OD.osde_tipoprod='SV', ' - ', 
		if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio',
		if(OD.osde_especificacion is not null, '$especificacion', '') 'especificacion' 
	from os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
	where id_os=".($id_os+0)." order by OD.id_os_detalle desc ";
	if ( $rq = tep_db_query($query_OD) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
   				$column_upc = $column_upc.$res['cod_barra']." (".$res['cod_sap'].")"."\n";
				$column_tipo = $column_tipo.$res['osde_tipoprod']."\n";
/*				$column_descripcion = $column_descripcion.$res['osde_descripcion'].$res['especificacion'].$res['osde_especificacion']."\n";*/
				$column_descripcion = $column_descripcion.$res['osde_descripcion']."\n";
				$column_despacho=$column_despacho.$res['tipo_nombre']."\n";
				$column_inst=$column_inst.$res['osde_instalacion']."\n";
				$column_cant=$column_cant.$res['osde_cantidad']."\n";
				$column_precio=$column_precio.formato_precio($res['osde_precio'])."\n";
				$total=$total+$res['osde_precio'];
	            $suma=$suma+$res['Total'];
				$column_tot=$column_tot.formato_precio($res['Total'])."\n";
			}
	}
/*para el final total de la tabla*/
	$column_upc = $column_upc." "."\n";
	$column_tipo = $column_tipo." "."\n";
	$column_descripcion = $column_descripcion." Total"."\n";
	$column_despacho = $column_despacho." "."\n";
	$column_inst = $column_inst." "."\n";
	$column_cant = $column_cant." "."\n";
	$column_precio = $column_precio." "."\n";
	$column_tot = $column_tot.formato_precio($suma)."\n";
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

//para los datossumario
$pdf->SetFillColor(232,232,232);
//encabezado de sumario
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos OT Sumario',1,0,'C',1);
$pdf->Ln();


//Fields Name position
$Y_Fields_Name_position = 20;
//Table position, under Fields Name
$Y_Table_Position = 26;

//Gray color filling each Field Name box
$pdf->SetFillColor(232,232,232);
//encabezado de instalador
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos Cotización',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 26;
//Table position, under Fields Name
$Y_Table_Position = 32;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(45,6,'Nº Cotización',1,0,'L',1);
$pdf->SetX(50);
$pdf->Cell(40,6,'Fecha Cotización',1,0,'L',1);
$pdf->SetX(90);
$pdf->Cell(55,6,'Estado',1,0,'L',1);
$pdf->SetX(145);
$pdf->Cell(50,6,'Atendido Por',1,0,'L',1);
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
$Y_Fields_Name_position = 38;
//Table position, under Fields Name
$Y_Table_Position = 44;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(45,6,'Local',1,0,'L',1);
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
//*******comentario******/
$Y_Fields_Name_position = 50;
//Table position, under Fields Name
$Y_Table_Position = 56;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(45,6,'Comentarios',1,0,'L',1);
$pdf->SetX(50);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(145,6,$column_comentario,1,'L');

/***********tabla con los datos del cliente*/
//Fields Name position
$Y_Fields_Name_position = 60;
//Table position, under Fields Name
$Y_Table_Position = 66;
//para los datos sumario
$pdf->SetFillColor(232,232,232);

$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Datos Cliente',1,0,'L',1);
$pdf->Ln();

//Fields Name position
$Y_Fields_Name_position = 66;
//Table position, under Fields Name
$Y_Table_Position = 72;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(20,6,'Cédula',1,0,'L',1);
$pdf->SetX(25);
$pdf->Cell(60,6,'Nombre',1,0,'L',1);
$pdf->SetX(85);
$pdf->Cell(75,6,'Dirección',1,0,'L',1);
$pdf->SetX(160);
$pdf->Cell(35,6,'Teléfono',1,0,'L',1);
$pdf->Ln();

//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(20,6,$column_rut,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(25);
$pdf->MultiCell(60,6,$column_nombre,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(85);
$pdf->MultiCell(75,6,$column_direccion_pri,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(160);
$pdf->MultiCell(35,6,$column_telefono_pri,1);

//Fields Name position
$Y_Fields_Name_position = 78;
//Table position, under Fields Name
$Y_Table_Position = 84;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(50,6,'Barrio - Localidad',1,0,'L',1);
$pdf->SetX(55);
$pdf->Cell(65,6,'Departamento',1,0,'L',1);
$pdf->SetX(120);
$pdf->Cell(75,6,'Ciudad',1,0,'L',1);
$pdf->Ln();

//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(50,6,$column_comuna_pri.' - '.$column_localidad,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(55);
$pdf->MultiCell(65,6,$column_departamento,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(120);
$pdf->MultiCell(75,6,$column_ciudad,1);
$pdf->SetY($Y_Table_Position);


/***********************direccion de despacho*/
/***********tabla con los datos del cliente*/
//Fields Name position
$Y_Fields_Name_position = 94;
//Table position, under Fields Name
$Y_Table_Position = 100;
//para los datos sumario
$pdf->SetFillColor(232,232,232);

$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(05);
$pdf->Cell(190,6,'Direccion de Servicio (Despacho,Instaladores)',1,0,'L',1);
$pdf->Ln();
//Fields Name position
$Y_Fields_Name_position = 100;
//Table position, under Fields Name
$Y_Table_Position = 106;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(50,6,'Teléfono',1,0,'L',1);
$pdf->SetX(55);
$pdf->Cell(65,6,'Dirección',1,0,'L',1);
$pdf->SetX(120);
$pdf->Cell(75,6,'Barrio - Localidad',1,0,'L',1);
$pdf->Ln();
//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(50,6,$column_telefono_des,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(55);
$pdf->MultiCell(65,6,$column_direccion_des,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(120);
$pdf->MultiCell(75,6,$column_comuna_des.' - '.$column_localidad_des,1);
$pdf->SetY($Y_Table_Position);

//Fields Name position
$Y_Fields_Name_position = 112;
//Table position, under Fields Name
$Y_Table_Position = 118;
$pdf->SetFillColor(232,232,232);
//encabezado de OT
$pdf->SetFont('Arial','B',12);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(05);
$pdf->Cell(80,6,'Indicación',1,0,'L',1);
$pdf->SetX(85);
$pdf->Cell(55,6,'Departamento',1,0,'L',1);
$pdf->SetX(140);
$pdf->Cell(55,6,'Ciudad',1,0,'L',1);
$pdf->Ln();
//Now show the 6 columns
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(80,6,$column_indicaciones,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(85);
$pdf->MultiCell(55,6,$column_departamento_des,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(140);
$pdf->MultiCell(55,6,$column_ciudad_des,1);
$pdf->SetY($Y_Table_Position);




/********************************************/
$Y_Fields_Name_position = 130;
$Y_Table_Position =136;

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
$pdf->Cell(40,6,'Descripción',1,0,'C',1);
$pdf->SetX(95);
$pdf->Cell(20,6,'Despacho',1,0,'C',1);
$pdf->SetX(115);
$pdf->Cell(20,6,'Instalación',1,0,'C',1);
$pdf->SetX(135);
$pdf->Cell(15,6,'Cant',1,0,'C',1);
$pdf->SetX(150);
$pdf->Cell(20,6,'Precio',1,0,'C',1);
$pdf->SetX(170);
$pdf->Cell(30,6,'Total',1,0,'C',1);
$pdf->Ln();
//Now show the 6 columns
$pdf->SetFont('Arial','',9);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(05);
$pdf->MultiCell(40,6,$column_upc,1);
$pdf->SetY($Y_Table_Position);
$pdf->SetX(45);
$pdf->MultiCell(10,6,$column_tipo,1,'C');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(55);
$pdf->MultiCell(40,6,$column_descripcion,1,'L');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(95);
$pdf->MultiCell(20,6,$column_despacho,1,'C');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(115);
$pdf->MultiCell(20,6,$column_inst,1,'C');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(135);
$pdf->MultiCell(15,6,$column_cant,1,'R');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(150);
$pdf->MultiCell(20,6,$column_precio,1,'R');
$pdf->SetY($Y_Table_Position);
$pdf->SetX(170);
$pdf->MultiCell(30,6,$column_tot,1,'R');




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
