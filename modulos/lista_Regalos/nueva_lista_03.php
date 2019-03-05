<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";
include "../nueva_cotizacion/activewidgets.php";

include_idioma_mod( $ses_idioma, "nueva_lista_03" );

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );

function insert_Lista($clie_rut, $local_id, $ses_usr_id){
	$qry_List =  "INSERT INTO list_regalos_enc ( clie_Rut, id_Evento, fec_creacion, id_Estado, festejado, fec_Evento, fec_entrega, id_Direccion, id_Local, id_Usuario, descripcion) 
				  VALUES ( '".$clie_rut."', '1', now(), 'NP', '', now(), now(), '0', '".$local_id."', '".$ses_usr_id."', '')";	
	tep_db_query($qry_List);		

    $ultimoID = tep_db_insert_id('');
	//insertahistorial("Se creó OS $ultimoID en estado En Trabajo", $ultimoID);
	
    return($ultimoID);
}

function insert_detalle($Lineas,$idLista, $local_id){
	$Lineas = substr ($Lineas, 0, strlen($string) - 1);
	$Lineas = split(",", $Lineas);

	foreach ($Lineas AS $key=>$Value) {
			$qry_Det =  "INSERT INTO tmp_listdet ( codProd ) VALUES ('".$Value."')";
			tep_db_query($qry_Det);
	}
	
	$Qry_Sel = "SELECT codProd, count(*)AS cantidad FROM tmp_listdet GROUP BY codProd;";
	$Sel = tep_db_query($Qry_Sel);
	while($res_Sel = tep_db_fetch_array($Sel)) {
		
		$Qry_Prod = "SELECT C.cod_barra, C.cod_prod1, P.des_larga, P.prod_tipo, S.prec_valor
			         FROM codbarra C
			         JOIN productos P ON (P.cod_prod1 = C.cod_prod1)
			         JOIN precios S ON (S.cod_prod1 = C.cod_prod1)
					WHERE C.cod_barra='".$res_Sel['codProd']."' AND S.id_local='".$local_id."' AND P.estadoactivo = 'C' AND C.estadoactivo = 'C' AND S.estadoactivo = 'C' ;";
		
		$res_Prod = tep_db_query($Qry_Prod);
		$res_Prod = tep_db_fetch_array($res_Prod);
				
		if($res_Prod['cod_barra'] != "")
		{
			$Ins_Det = "INSERT INTO `list_regalos_det` (`idLista_enc`,`cod_Ean`,`cod_Easy`,`descripcion`,`list_tipoprod`,`list_cantprod`,`list_precio`,`list_idTipodespacho`,`list_instalacion`) VALUES 
	 					('".$idLista."', '".$res_Prod['cod_barra']."', '".$res_Prod['cod_prod1']."','".$res_Prod['des_larga']."','".$res_Prod['prod_tipo']."','".$res_Sel['cantidad']."','".$res_Prod['prec_valor']."',1,0);";			
			tep_db_query($Ins_Det);
		}
	}
	$Qry_trnk = "TRUNCATE TABLE tmp_listdet;";
	tep_db_query($Qry_trnk);
}
function uploadFile($archivo, $nombre, $idLista, $local_id, $clie_rut){
	if (copy($archivo, UPD_FILE.$nombre)){
		//echo"Archivo cargado Correctamente",$nombre;exit();
		LoadProducts($nombre, $idLista, $local_id, $clie_rut);
		
		return;
	}
	else{
		//echo "Error en carga";exit();
	}
}

function LoadProducts($nombre, $idLista, $local_id, $clie_rut){
		if ($fp = fopen(UPD_FILE.$nombre,"r")){
			while (!feof($fp)) {
		        while($bufer = fgets($fp, 4096)){
				$Lineas .= $bufer.",";
		        }
		    }
		}

		fclose (UPD_FILE.$nombre);
		unlink(UPD_FILE.$nombre);
		
		if($Lineas){
			insert_detalle($Lineas,$idLista, $local_id);
		}
		else{
		/* cambia el estado a Con Productos*/
    		$qupdi="UPDATE list_regalos_enc SET id_Estado='NP' where idLista=".($idLista+0)." ";
    		tep_db_query($qupdi);
			?>
			<script language="JavaScript" >
				alert("El archivo descargado de la PDA, no contiene Productos");
				location.href="nueva_lista_03.php?idLista=" +<?php echo $idLista; ?>+ "&clie_rut=" +<?php echo $clie_rut; ?>+ "";
			</script>
			<?
		}
}

// *************************************************************************************
//Verifico que exista el id de local antes de continuar
if (!get_local_usr( $ses_usr_id )) {
	?>
	<SCRIPT language="JavaScript" >
		alert('Para ingresar a esta interfaz, es necesario que ud. tenga asignado un local.\nContáctese con el administrador');
		history.back();
	</SCRIPT>
	<?php
	tep_exit();
}

/***************************/
/** Inicio Acciones  **/
if ($accion == "crear" && !$idLista){
	$accion="";
// Se inserta el encabezado para una nueva Lista de Regalos
	$newList = insert_Lista($clie_rut, $local_id, $ses_usr_id);

	insertahistorial_ListaReg("Se ha creado la Lista de regalos N°. ".$newList.".", $USR_LOGIN, null, $newList, null, $tipo = 'SYS');
	
	if($newList){
		header ('Location: nueva_lista_03.php?idLista='.$newList.'&clie_rut='.$clie_rut);
	}
}

if($act_Dir == "$act_Dir"){
	$act_Dir="";
	/* cambia el id de la direccion en la Lista de Regalos*/
    $qupdi="UPDATE list_regalos_enc SET id_Direccion=".($idDireccion+0)." where idLista=".($idLista+0)." ";
    tep_db_query($qupdi);
}

if($carga_List){
	/* cambia el estado a Con Productos*/
	insertahistorial_ListaReg("Se han cargado correctamente productos para la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
	
    $qupdi="UPDATE list_regalos_enc SET id_Estado='CP' where idLista=".($idLista+0)." ";
    tep_db_query($qupdi);
}

if ($accion == 'guardar' ){
	$accion="";
	$Qry_ListDet="UPDATE list_regalos_enc SET id_Evento=".($select_evento).", id_Direccion=".($idDireccion).", festejado='".($festejado)."', fec_Evento='".invierte_fechaGuion($fecha_event)."' WHERE idLista=".$idLista."";
	tep_db_query($Qry_ListDet);

	if ($_FILES['add_List']['tmp_name']){
		$archivo = $_FILES['add_List']['tmp_name'];
		$nombre = $_FILES['add_List']['name'];
		uploadFile($archivo, $nombre, $idLista, $local_id, $clie_rut);
	}
	?>
	<script language="JavaScript" >
		alert("¡¡ Su Lista de Regalos N°." +<?php echo $idLista; ?>+ " ha sido creada Exitosamente !!");
		location.href="nueva_lista_sumario_01.php?idLista=" +<?php echo $idLista; ?>+ "";
	</script>
	<?php
}

/** Fin Acciones **/

/*Consulta la Direccion de Despacho seleccionada*/
$queryDir = "SELECT D.id_direccion, D.dire_nombre AS nom_direccion , D.dire_direccion, D.dire_observacion, D.dire_localizacion, D.clie_rut 
    		 FROM list_regalos_enc L
			 INNER JOIN direcciones D ON (D.id_direccion=L.id_Direccion)
    	  	 WHERE idLista = $idLista";    
$edirec = tep_db_query($queryDir);
$edirec = tep_db_fetch_array( $edirec );
    
$direcc = getlocalizacion($edirec['dire_localizacion']);

/*Verifica si se cargaron productos a la Lista de Regalos*/
$Qry_Listdet="SELECT COUNT(*) AS CONT FROM list_regalos_det WHERE idLista_enc = $idLista";
$Listdet = tep_db_query($Qry_Listdet);
$Listdet = tep_db_fetch_array($Listdet);

if($Listdet['CONT'] > 0){
	$carga_List = 1;
}

/* Paso de variables a la plantilla de presentacio */
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

//Agregamos la fecha actual
$MiTemplate->set_var("fecha_hoy",date("Ymd", time()));

$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("nom_direccion",$edirec['nom_direccion']);
$MiTemplate->set_var("dire_direccion_des",$edirec['dire_direccion']);
$MiTemplate->set_var("dire_observacion_des",$edirec['dire_observacion']);
$MiTemplate->set_var("departamento",$direcc['departamento']);
$MiTemplate->set_var("ciudad",$direcc['ciudad']);
$MiTemplate->set_var("comu_nombre_des",$direcc['barrio']);
$MiTemplate->set_var("idLista",$idLista);
$MiTemplate->set_var("idDireccion",$edirec['id_direccion']);
$MiTemplate->set_var("carga_List",$carga_List);
$MiTemplate->set_var("accion",$accion);
$MiTemplate->set_var("ruta_Lista",RUTA_LISTA);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
$MiTemplate->set_var("TEXT_CAMPO_1", TEXT_CAMPO_1 );
$MiTemplate->set_var("TEXT_CAMPO_2", TEXT_CAMPO_2 );
$MiTemplate->set_var("TEXT_CAMPO_3", TEXT_CAMPO_3 );
$MiTemplate->set_var("TEXT_CAMPO_4", TEXT_CAMPO_4 );
$MiTemplate->set_var("TEXT_CAMPO_5", TEXT_CAMPO_5 );
$MiTemplate->set_var("TEXT_CAMPO_6", TEXT_CAMPO_6 );
$MiTemplate->set_var("TEXT_CAMPO_7", TEXT_CAMPO_7 );
$MiTemplate->set_var("TEXT_CAMPO_8", TEXT_CAMPO_8 );
$MiTemplate->set_var("TEXT_CAMPO_9", TEXT_CAMPO_9 );
$MiTemplate->set_var("TEXT_CAMPO_10", TEXT_CAMPO_10 );
$MiTemplate->set_var("TEXT_CAMPO_11", TEXT_CAMPO_11 );
$MiTemplate->set_var("TEXT_CAMPO_12", TEXT_CAMPO_12 );
$MiTemplate->set_var("TEXT_CAMPO_13", TEXT_CAMPO_13 );
$MiTemplate->set_var("TEXT_CAMPO_14", TEXT_CAMPO_14 );
$MiTemplate->set_var("TEXT_CAMPO_15", TEXT_CAMPO_15 );
$MiTemplate->set_var("TEXT_CAMPO_16", TEXT_CAMPO_16 );
$MiTemplate->set_var("TEXT_CAMPO_17", TEXT_CAMPO_17 );
$MiTemplate->set_var("TEXT_CAMPO_18", TEXT_CAMPO_18 );
// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/nueva_lista_03.htm");
        
$qnombre="select U.usr_nombres,U.usr_apellidos ,LO.cod_local from usuarios U inner join local_usr  as LU on (LU.usr_id=U.usr_id) join locales LO on (LO.id_local=LU.id_local)
where LU.id_local=".($local_id+0)." and U.usr_login='$USR_LOGIN'";
$rq = tep_db_query($qnombre);
$res = tep_db_fetch_array( $rq );
$nc=$res['usr_nombres']." ".$res['usr_apellidos'];
$codlocal=$res['cod_local'];
$MiTemplate->set_var("codlocal",$codlocal );

// Para la seleccion de los eventos
$MiTemplate->set_block("main", "Eventos", "BLO_event");
$queryP = "SELECT idEvento, nombre, if('$select_evento'=idEvento, 'selected', '')'selected' FROM list_eventos ORDER BY nombre";
query_to_set_var( $queryP, $MiTemplate, 1, 'BLO_event', 'Eventos' );

// Para la seleccion de la libreta de direcciones.
$MiTemplate->set_block("main", "Direcciones", "BLO_Dir");

$queryR = "SELECT D.id_direccion, D.dire_nombre, D.dire_direccion as dire_direcciond, D.id_direccion ,D.dire_defecto ,NE.DESCRIPTION as comu_nombred ,D.dire_telefono,D.dire_observacion  
			FROM direcciones D  
			LEFT JOIN cu_neighborhood  NE  ON NE.LOCATION = D.dire_localizacion 
		   WHERE D.clie_rut = $clie_rut ";
query_to_set_var( $queryR, $MiTemplate, 1, 'BLO_Dir', 'Direcciones');


// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>