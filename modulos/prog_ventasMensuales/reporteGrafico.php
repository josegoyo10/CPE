<?php
$pag_ini = '../prog_instalacion/prog_Instalacion.php';
include ("../../includes/jpgraph-1.26/src/jpgraph.php");
include ("../../includes/jpgraph-1.26/src/jpgraph_bar.php");
include "../../includes/aplication_top.php";

$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$idLocal = $_GET['idLocal'];

$Query = "Select (sum((OD.osde_precio) * (OD.osde_cantidad))) AS Total, MONTH(OS.os_fechaboleta) as mes,
        CASE MONTH(OS.os_fechaboleta)
                  WHEN 1 THEN 'Enero'
                  WHEN 2 THEN 'Febrero'
                  WHEN 3 THEN 'Marzo'
                  WHEN 4 THEN 'Abril'
                  WHEN 5 THEN 'Mayo'
                  WHEN 6 THEN 'Junio'
                  WHEN 7 THEN 'Julio'
                  WHEN 8 THEN 'Agosto'
                  WHEN 9 THEN 'Septiembre'
                  WHEN 10 THEN 'Octubre'
                  WHEN 11 THEN 'Noviembre'
                  WHEN 12 THEN 'Diciembre'
                  END AS Mes, 
        YEAR(OS.os_fechaboleta) AS Ano 
		FROM os OS
		JOIN os_detalle OD on OD.id_os=OS.id_os
		WHERE OS.id_estado='SP' AND OS.os_numboleta is not null
		".(($fecha_desde)?" AND OS.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OS.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
		".(($fecha_hasta)?" AND OS.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OS.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
		".(($idLocal)?" AND OS.id_local ="."'".$idLocal."'":"")."
		GROUP BY Ano, Mes
		ORDER BY Ano, mes  ASC;";

$datay = array();
$Mes = array();
$res = tep_db_query($Query);
	while($row = tep_db_fetch_array( $res )){
		 // Datos en Y
		 array_push($datay, $row[Total]); 
		 array_push($Mes, $row[Mes]); 
	}
	
// Setup the graph.
$graph = new Graph(600,250,"auto");
$graph->SetScale("textlin");
$graph->img->SetMargin(90,50,30,25);
$graph->SetMarginColor('white');

// BACKGROUND 
$graph->ygrid->SetFill(true,'#DDDDDD@0.5','#BBBBBB@0.5'); 
$graph->ygrid->SetLineStyle('dashed'); 
$graph->ygrid->SetColor('gray'); 
$graph->xgrid->Show(); 
$graph->xgrid->SetLineStyle('dashed'); 
$graph->xgrid->SetColor('gray'); 

//TITLES
$graph->title->Set('REPORTE MENSUAL DE VENTAS');
$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);
$graph->title->SetColor('black');
$graph->SetBackgroundImage('../../img/prod_img/EASY.jpg',BGIMG_COPY);


// Setup font for axis
$graph->xaxis->SetFont(FF_FONT1);
$graph->yaxis->SetFont(FF_FONT1);

// Valores en Y Valores
$bplot = new BarPlot($datay);
$bplot->SetWidth(0.45); 

// Valores en X Meses
$graph->xaxis->SetTickLabels($Mes);
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

// Setup color for gradient fill style
$bplot->SetFillGradient("firebrick2","lightsteelblue",GRAD_DIAGONAL);

// Set color for the frame of each bar
$bplot->SetColor("gray");
$graph->Add($bplot);

// Finally send the graph to the browser
$graph->Stroke();
?>