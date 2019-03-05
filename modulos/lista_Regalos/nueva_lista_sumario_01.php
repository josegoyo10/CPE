<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';

include "../../includes/aplication_top.php";
include_once('../../includes/aplication_top.php');
require_once('../../wsClientUnique/ClientUnique.php');


include_idioma_mod( $ses_idioma, "nueva_lista_sumario_01" );


// ************************************************************************************  \\
// Consulta los datos del Cliente.
	$Qry_cliente = "SELECT CL.clie_rut, CONCAT(CL.clie_nombre,' ',CL.clie_paterno,' ',CL.clie_materno) AS clie_nombre 
	                FROM list_regalos_enc L JOIN clientes CL ON CL.clie_rut = L.clie_Rut 
	                WHERE L.idLista=".($idLista+0);	
	$res = tep_db_query($Qry_cliente);
	$resp = tep_db_fetch_array($res);
	
	$local_id = get_local_usr( $ses_usr_id );
	$USR_LOGIN = get_login_usr( $ses_usr_id );

 /* Obtiene los nombre de la persona que ingreso los datos en la Lista de Regalos*/
    $qnombre="SELECT U.usr_nombres,U.usr_apellidos FROM usuarios U INNER JOIN local_usr  as LU on (LU.usr_id=U.usr_id) WHERE id_local=".($local_id+0)." AND U.usr_login='$USR_LOGIN'";
    $ListAtendido = tep_db_query($qnombre);
    $ListAtendido = tep_db_fetch_array( $ListAtendido );
    $nombre=$ListAtendido['usr_nombres'];
    $apellido=$ListAtendido['usr_apellidos'];
    $nc=$nombre." ".$apellido;

// Consulta los datos de Localización de la Lista de Regalos
	$querysel="SELECT CL.clie_telefonocasa, CL.clie_telcontacto2, L.fec_Evento, L.clie_Rut, L.idLista, L.id_Estado, L.id_Local, L.descripcion, L.festejado, L.id_Evento, Ev.nombre, L.fec_creacion, E.esta_nombre ,Lc.nom_local, CL.clie_nombre, CL.clie_tipo, CL.clie_razonsocial, CL.clie_paterno, CL.clie_materno ,if('CP'=L.id_Estado, '', '') 'pagar', if (date(L.fec_creacion)>=date(now()), 1, 0) esfechavalida, if(L.id_Local=101, 1, 0) eslocalcorrecto
				FROM list_regalos_enc L
				LEFT JOIN estados E ON (E.id_estado=L.id_Estado)
				LEFT JOIN locales Lc ON (Lc.id_local=L.id_Local)
				LEFT JOIN direcciones D ON (L.id_Direccion=D.id_direccion)
				LEFT JOIN clientes CL ON (L.clie_Rut=CL.clie_rut)
				LEFT JOIN list_eventos Ev ON (L.id_Evento=Ev.idEvento)
				WHERE L.idLista=".($idLista+0);
	$osSel = tep_db_query($querysel);
	$osSel = tep_db_fetch_array( $osSel );

// Consulta los datos de direccion principal del Cliente 
	$queryPRI = "SELECT  D.clie_rut, D.id_direccion, D.dire_nombre AS dire_nombre_pri , D.dire_direccion AS dire_direccion_pri, D.id_direccion , D.dire_defecto , D.dire_telefono AS dire_telefono_pri , D.dire_observacion AS dire_observacion_pri 
				 FROM direcciones D
			 	WHERE D.clie_rut = ".$resp['clie_rut']." and D.dire_defecto='p' ";
	$direna = tep_db_query($queryPRI);
	$direna = tep_db_fetch_array( $direna );

// Consulta el Barrio-Localidad de los datos del cliente
	$dirCli = consulta_localizacion($direna['clie_rut'],1);
	$dirCliente = getlocalizacion($dirCli);

// Consulta la direccción de Servicio de la Lista de Regalos.
	$queryDir="SELECT L.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
				FROM list_regalos_enc L
				JOIN direcciones D ON D.id_direccion=L.id_direccion
				WHERE L.idLista =".($idLista+0);
	$osSelDir = tep_db_query($queryDir);
	$osSelDire = tep_db_fetch_array( $osSelDir );

	$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
	$dirServicio = getlocalizacion($dirServ);


// ************************************************************************************  \\
//** Paso de los datos a la Plantilla de Presentacion **\\
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

//** Acciones disponibles para la Lista de Regalos **\\
$Crg_Prod= "<td width='70' align='center'><a href='nueva_lista_03.php?idLista=".$idLista."&clie_rut=".$resp['clie_rut']."'>Cargar<br>Productos</a></td>";
$Edt_List="<td width='70' align='center'><a href='edita_lista.php?idLista=".$idLista."&clie_rut=".$resp['clie_rut']."'>Editar<br>Lista</a></td>";
$Ctz_Reg="<td width='70' align='center'><a href='../cotiza_Regalos/cotiza_Regalos_00.php?idLista=".$idLista."'>Cotizar<br>Regalos</a></td>";
$Impr_List="<td width='70' align='center'><a href='#' onclick=\"javascript: nuevaVentana = null; nuevaVentana=window.open('print_lista.php?idLista=".$idLista."', 1,1,50,50,1,0,0,0,0,1,1); nuevaVentana.location.href='print_lista.php?idLista=".$idLista."';\">Imprimir<br>Lista</a></td>";
$List_Pdf="<td width='70' align='center'><a href='#' onclick=\"javascript: nuevaVentana = null; nuevaVentana=window.open('print_Pdf.php?idLista=".$idLista."', 1,0,0,0,0,1,1); nuevaVentana.location.href='print_Pdf.php?idLista=".$idLista."';\">Exportar<br>a PDF</a></td>";

$Ver_Os="<td width='70' align='center'><a href='../cotiza_Regalos/monitor_OSRegalos.php?idLista=".$idLista."&clie_rut=".$resp['clie_rut']."'>Ver<br>OS's</a></td>";
$Ver_Ot="<td width='70' align='center'><a href='../cotiza_Regalos/monitor_OTRegalos.php?idLista=".$idLista."&clie_rut=".$resp['clie_rut']."'>Ver<br>OT's</a></td>";
$Ver_OD="<td width='70' align='center'><a href='print_despachoLista.php?idLista=".$idLista."'>Ver<br>Acta de Entrega</a></td>";
$Cer_Lista="<td width='70' align='center'><a href='cerrarLista.php?idLista=".$idLista."'>Cerrar<br>Lista</a></td>";
$Track="<td width='70' align='center'><a href='tracking.php?idLista=".$idLista."&clie_rut=".$resp['clie_rut']."'>Tracking<br>Lista</a></td>";

// Habilita las opciones según el Estado.
if($osSel['id_Estado'] == 'NP'){  
	$MiTemplate->set_var("Edt_List", $Edt_List);
	$MiTemplate->set_var("Crg_Prod", $Crg_Prod);
}

if($osSel['id_Estado'] == 'CP'){ 
	$MiTemplate->set_var("Edt_List", $Edt_List);
	$MiTemplate->set_var("Ver_Ot", $Ver_Ot);
	$MiTemplate->set_var("Ver_Os", $Ver_Os);
	$MiTemplate->set_var("Ctz_Reg", $Ctz_Reg);
	$MiTemplate->set_var("Cer_Lista", $Cer_Lista);
}

if($osSel['id_Estado'] == 'CC'){ 
	$MiTemplate->set_var("Edt_List", $Edt_List);
	$MiTemplate->set_var("Ver_Ot", $Ver_Ot);
	$MiTemplate->set_var("Ver_Os", $Ver_Os);
	$MiTemplate->set_var("Ctz_Reg", $Ctz_Reg);
	$MiTemplate->set_var("Cer_Lista", $Cer_Lista);
}

if($osSel['id_Estado'] == 'LP'){ 
	$MiTemplate->set_var("Ver_Ot", $Ver_Ot);
	$MiTemplate->set_var("Ver_Os", $Ver_Os);
	$MiTemplate->set_var("Ctz_Reg", $Ctz_Reg);
	$MiTemplate->set_var("Cer_Lista", $Cer_Lista);
}

if($osSel['id_Estado'] == 'LC'){
	 $MiTemplate->set_var("Ver_Ot", $Ver_Ot);
	 $MiTemplate->set_var("Ver_Os", $Ver_Os);
	 $MiTemplate->set_var("Ver_OD", $Ver_OD);
}

if($osSel['id_Estado'] == 'LA'){
	 $MiTemplate->set_var("Ver_Ot", $Ver_Ot);
	 $MiTemplate->set_var("Ver_Os", $Ver_Os);
}

$MiTemplate->set_var("Track", $Track);
$MiTemplate->set_var("Impr_List", $Impr_List);
$MiTemplate->set_var("List_Pdf", $List_Pdf);

     // Datos del la Lista de Reglaos     
     $MiTemplate->set_var("idLista", ($idLista?$idLista:"Datos no disponibles"));   
     $MiTemplate->set_var("list_fec_cracion", fecha_db2php($osSel['fec_creacion']));
     $MiTemplate->set_var("esta_nombre", ($osSel['esta_nombre']?$osSel['esta_nombre']:"Datos no disponibles") );
     $MiTemplate->set_var('atendido', ($nc?$nc:"Datos no disponibles") );
     $MiTemplate->set_var("nom_local", ($osSel['nom_local']?$osSel['nom_local']:"Datos no disponibles") );
     $MiTemplate->set_var("list_fecEvento", fecha_db2php($osSel['fec_Evento']));
     $MiTemplate->set_var("evento",($osSel['nombre']?$osSel['nombre']:"Datos no disponibles") );
     $MiTemplate->set_var("festejado", ($osSel['festejado']?$osSel['festejado']:"Datos no disponibles") );
     
     // Datos de Cliente
     $MiTemplate->set_var("rut_cliente", ($resp['clie_rut']?$resp['clie_rut']:"Datos no disponibles") );
     $MiTemplate->set_var("clie_nombre", ($resp['clie_nombre']?$resp['clie_nombre']:"Datos no disponibles") );
     $MiTemplate->set_var("clie_paterno", $osSel['clie_paterno'] );
     $MiTemplate->set_var("clie_materno", $osSel['clie_materno'] );
     $MiTemplate->set_var("dire_direccion_pri", ($direna['dire_direccion_pri']?$direna['dire_direccion_pri']:$osSel['clie_materno']) );
     $MiTemplate->set_var("comu_nombre_pri",$dirCliente['barrio']." - ".$dirCliente['localidad']);
     $MiTemplate->set_var("telefono_celular", ($osSel['clie_telcontacto2']?$osSel['clie_telcontacto2']:"Datos no disponibles") );
     $MiTemplate->set_var("dire_telefono_pri", ($osSel['clie_telefonocasa']?$osSel['clie_telefonocasa']:"Datos no disponibles") );

     // Dirección de Servicio
     $MiTemplate->set_var("dire_direccion", ($osSelDire['dire_direccion']?$osSelDire['dire_direccion']:"Datos no disponibles") );     
     $MiTemplate->set_var("dire_telefono", ($osSelDire['dire_telefono']?$osSelDire['dire_telefono']:"Datos no disponibles") );
     $MiTemplate->set_var("dire_observacion", ($osSelDire['dire_observacion']?$osSelDire['dire_observacion']:"Datos no disponibles") );
     $MiTemplate->set_var("departamento", ($dirServicio['departamento']?$dirServicio['departamento']:"Datos no disponibles") );
     $MiTemplate->set_var("ciudad", ($dirServicio['ciudad']?$dirServicio['ciudad']:"Datos no disponibles") );
     $MiTemplate->set_var("comu_nombre",$dirServicio['barrio']." - ".$dirServicio['localidad']);
     
     // Variables de Control
     $MiTemplate->set_var("clie_rut",$osSel['clie_rut']);
     $MiTemplate->set_var("id_estado",$osSel['id_estado']);
     $MiTemplate->set_var("esfechavalida",$osSel['esfechavalida']);
     $MiTemplate->set_var("eslocalcorrecto",$osSel['eslocalcorrecto']);
     $eslocalcorrecto= $osSel['eslocalcorrecto'];
     $MiTemplate->set_var("os_cotizaciones_cruzadas",$osSel['os_cotizaciones_cruzadas']);
     $MiTemplate->set_var("os_comentarios",$osSel['os_comentarios']);
     
	// Para el tipo de cliente
	if ($osSel['clie_tipo']=='e'){
     $MiTemplate->set_var("empresa","Empresa");
     $MiTemplate->set_var("clie_razonsocial",$osSel['clie_razonsocial']);	 
	}
	// para ver el tipo de estado
     $MiTemplate->set_var("nombre_estado",$osSel['esta_nombre']);
     $MiTemplate->set_var("id_estado_s",$osSel['id_estado']);

// definición de Etiquetas
$MiTemplate->set_var("NOMBRE_PAGINA",NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
$MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
$MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
$MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
$MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
$MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
$MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);

$MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
$MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
$MiTemplate->set_var("TEXT_CAMPO_13",TEXT_CAMPO_13);
$MiTemplate->set_var("TEXT_CAMPO_14",TEXT_CAMPO_14);
$MiTemplate->set_var("TEXT_CAMPO_15",TEXT_CAMPO_15);
$MiTemplate->set_var("TEXT_CAMPO_16",TEXT_CAMPO_16);
$MiTemplate->set_var("TEXT_CAMPO_17",TEXT_CAMPO_17);
$MiTemplate->set_var("TEXT_CAMPO_18",TEXT_CAMPO_18);
$MiTemplate->set_var("TEXT_CAMPO_19",TEXT_CAMPO_19);
$MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
$MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
$MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
$MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
$MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);


// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/nueva_lista_sumario_01.htm");

/* para los productos*/
$instn="No";
$insts="Sí";
$precioVacio=" - ";
$totalVacio=" 0 ";
$total0='0';
$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
$notienedespacho=0;

/* Validar Precios Vigentes */
	// Valida el estado de las Listas de Regalos patra iniciar el proceso de validacion de Precios Vigentes.
	$MiTemplate->set_block("main", "Precio_Total", "PBLModulos");
	if($osSel['id_Estado'] == 'CP' || $osSel['id_Estado'] == 'CC'){
		// Precios y total parciado
	    $query_OD="SELECT DISTINCT L.idLista_det, (L.cod_Easy+0)AS cod_Easy, (L.cod_Ean+0)AS cod_Ean, L.descripcion, (P.peso/1000) AS peso, L.list_tipoprod, L.list_cantprod, L.list_idTipodespacho, Le.fec_entrega, L.list_instalacion, TD.nombre as tipo_nombre, PR.prec_valor, L.list_precio, ROUND((L.list_precio * L.list_cantprod))'Total', if(L.list_tipoprod='SV', ' - ', if (L.list_instalacion, 'SI', 'NO')) 'list_instalacion', if(L.list_precio is null, ' - ', '') 'precioVacio'
					FROM list_regalos_det L
					INNER JOIN list_regalos_enc Le ON Le.idlista=L.idLista_enc
					INNER JOIN tipos_despacho TD ON (TD.id_tipodespacho=L.list_idTipodespacho)
					INNER JOIN productos P ON L.cod_Easy = P.cod_prod1
					INNER JOIN precios PR ON PR.cod_prod1 = L.cod_Easy AND PR.id_local = '".$local_id."'
					WHERE idLista_enc=".($idLista+0)."
					ORDER BY L.idLista_det DESC;";	    
	    if ( $rq = tep_db_query($query_OD) ){
	            while( $res = tep_db_fetch_array( $rq ) ) {
           
				/* Cargamos el precio correspondiente al precio actual del producto */
	            	$new_Precio= $res['prec_valor'];
	            	$new_Total= ($res['prec_valor'] * $res['list_cantprod']);
	                $idList_det= $res['idLista_det'];
	              
	                if($res['list_tipoprod'] == 'PS')
	                {
	                $queryOD ="UPDATE list_regalos_det L INNER JOIN productos P ON L.cod_Easy = P.cod_prod1
	       						SET list_precio='".$new_Precio."' where L.idLista_det=".($idList_det+0)."";
	                tep_db_query($queryOD);    
	                }
	                	                	   		   		
		            $MiTemplate->set_var('precioVacio', '');	       		            
		            $MiTemplate->set_var('totalVacio', '');   
         	
			        if ($res['prec_valor']==''){
			        	
			        	if($res['list_tipoprod'] == 'PS')
		                {
		   		   			$MiTemplate->set_var('list_precio', formato_precio($res['prec_valor']));		                
		                }
		                else{
		                	$MiTemplate->set_var('list_precio', formato_precio($res['list_precio']));		                	
		                }
		            }
		            else{
			            if($res['list_tipoprod'] == 'PS')
		                {
		   		   		$MiTemplate->set_var('list_precio', formato_precio($res['prec_valor']));		                
		                }
		                else{
		                	$MiTemplate->set_var('list_precio', formato_precio($res['list_precio']));		                	
		                }
	                		                
		                $MiTemplate->set_var('precioVacio', '');
		            }
		            if ($res['Total']==''){
		            	if($res['list_tipoprod'] == 'PS')
		                {		   		   		
		                $MiTemplate->set_var('Total', formato_precio($totalVacio));
		                }
		                else{		                	
		                	$MiTemplate->set_var('Total', formato_precio($res['Total']));
		                }
		            	
		                $MiTemplate->set_var('Total', '');
		            }else{
		            	
			            if($res['list_tipoprod'] == 'PS')
		                {		   		   		
		                	$MiTemplate->set_var('Total', formato_precio($res['Total']));
		                }
		                else{		                	
		                	$MiTemplate->set_var('Total', formato_precio($res['Total']));
		                }
		            	
		                $MiTemplate->set_var('totalVacio', '');
		                
		            }   
	      
	                $MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
	                $MiTemplate->set_var('list_instalacion', $res['list_instalacion']);
	                $MiTemplate->set_var('descripcion', $res['descripcion']);                
	                $MiTemplate->set_var('cod_Ean', $res['cod_Ean']);
	                $MiTemplate->set_var('cod_Easy', $res['cod_Easy']);
	                $MiTemplate->set_var('list_tipoprod', $res['list_tipoprod']);
	                $MiTemplate->set_var('fec_entrega', fecha_db2php( $res['fec_entrega']) );
	                $MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
	                $MiTemplate->set_var('list_cantprod', $res['list_cantprod']);
	                $MiTemplate->parse("PBLModulos", "Precio_Total", true);            
	        }  
	    }      
	} 
	// Si el estado NO es "Lista con Productos" o "Listas con Compras", no aplica cambios al precio de los productos.
	else{ 
		/* Precios y total parciado aplicando precios almacenados en la Lista de Regalos*/
	    $query_OD="SELECT DISTINCT L.idLista_det, (L.cod_Easy+0)AS cod_Easy, (L.cod_Ean+0)AS cod_Ean, L.descripcion, (P.peso/1000) AS peso, L.list_tipoprod, L.list_cantprod, L.list_idTipodespacho, Le.fec_entrega, L.list_instalacion, TD.nombre as tipo_nombre, PR.prec_valor, L.list_precio, ROUND((L.list_precio * L.list_cantprod))'Total', if(L.list_tipoprod='SV', ' - ', if (L.list_instalacion, 'SI', 'NO')) 'list_instalacion', if(L.list_precio is null, ' - ', '') 'precioVacio'
					FROM list_regalos_det L
					INNER JOIN list_regalos_enc Le ON Le.idlista=L.idLista_enc
					INNER JOIN tipos_despacho TD ON (TD.id_tipodespacho=L.list_idTipodespacho)
					INNER JOIN productos P ON L.cod_Easy = P.cod_prod1
					INNER JOIN precios PR ON PR.cod_prod1 = L.cod_Easy AND PR.id_local = '".$local_id."'
					WHERE idLista_enc=".($idLista+0)."
					ORDER BY L.idLista_det DESC;";

	    if ( $rq = tep_db_query($query_OD) ){
	            while( $res = tep_db_fetch_array( $rq ) ) {
	            if ($res['list_precio']==''){
	                $MiTemplate->set_var('precioVacio', $precioVacio);
	                $MiTemplate->set_var('list_precio', '');
	            }
	            else{
	                $MiTemplate->set_var('list_precio', formato_precio($res['list_precio']));
	                $MiTemplate->set_var('precioVacio', '');
	            }
	            if ($res['Total']==''){
	                $MiTemplate->set_var('totalVacio', $totalVacio);
	                $MiTemplate->set_var('Total', '');
	            }
	            else{
	                $MiTemplate->set_var('totalVacio', '');
	                $MiTemplate->set_var('Total', formato_precio($res['Total']));
	            }
	            
	            	$MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
	                $MiTemplate->set_var('list_instalacion', $res['list_instalacion']);
	                $MiTemplate->set_var('descripcion', $res['descripcion']);                
	                $MiTemplate->set_var('cod_Ean', $res['cod_Ean']);
	                $MiTemplate->set_var('cod_Easy', $res['cod_Easy']);
	                $MiTemplate->set_var('list_tipoprod', $res['list_tipoprod']);
	                $MiTemplate->set_var('fec_entrega', fecha_db2php( $res['fec_entrega']) );
	                $MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
	                $MiTemplate->set_var('list_cantprod', $res['list_cantprod']);
	                $MiTemplate->parse("PBLModulos", "Precio_Total", true);   	               
	        }           
	    }
	}

    /*para obtener el total*/
      $query_TG = "SELECT L.list_precio, L.list_cantprod, ROUND((L.list_precio * L.list_cantprod))'Total' 
      				FROM list_regalos_det L 
      			   WHERE idLista_enc=".($idLista+0)."";      
      $suma=0;
      $vari='si';
        if ( $rq = tep_db_query($query_TG) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
            $suma=$suma+$res['Total'];
            }
        }
       $MiTemplate->set_var('TG',formato_precio($suma));

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";


$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>