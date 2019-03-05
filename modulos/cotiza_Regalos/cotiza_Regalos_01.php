<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include ("../../includes/aplication_top.php");
require_once('../../wsClientUnique/ClientUnique.php');

include_idioma_mod( $ses_idioma, "cotiza_Regalos_01" );

// *************************************************************************************
/************** Inicio Acciones*****************/

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );


if ($clie_rut){
	/* Realiza la busqueda de los datos del cliente*/
	    $queryClie =  "SELECT clie_rut, clie_nombre, clie_paterno, clie_materno, clie_email, clie_telefonocasa,clie_telcontacto1,clie_telcontacto2 FROM clientes where clie_rut=".($clie_rut+0)." and clie_tipo='p'";
	    $eclient = tep_db_query($queryClie);
	    $eclient = tep_db_fetch_array( $eclient );
	    $rutE=$eclient['clie_rut'];
	    
	    if ($rutE){$rutExiste=true;}
	/* Realiza la busqueda de la direccion del cliente*/
	    $queryDir =  "Select dire_direccion, dire_observacion,dire_telefono from direcciones where clie_rut=".($clie_rut+0)." and dire_activo=1 and dire_defecto='p' or dire_defecto=null";
	
	    $edirec = tep_db_query($queryDir);
	    $edirec = tep_db_fetch_array( $edirec );
}


/* Toma la cédula verifica si y EXISTE EL CLIENTE  en la tabla cliente y direccion*/
if ($clie_rut){
/* Realiza la busqueda de los datos del cliente*/
    $queryClie =  "SELECT clie_rut, clie_nombre, clie_paterno, clie_materno, clie_email, clie_telefonocasa,clie_telcontacto1,clie_telcontacto2 FROM clientes where clie_rut=".($clie_rut+0)." and clie_tipo='p'";

    $eclient = tep_db_query($queryClie);
    $eclient = tep_db_fetch_array( $eclient );
    $rutE=$eclient['clie_rut'];
    
    if( $rutE )
    {
    	$rutExiste=true;
    }
    else
    {
    	$Qry_InstCli = "INSERT INTO clientes(clie_rut) VALUES ('".$clie_rut."')";
    	tep_db_query($Qry_InstCli);

		$rutE = $clie_rut;
    }
/* Realiza la busqueda de la direccion del cliente*/
    $queryDir =  "Select dire_direccion, dire_observacion,dire_telefono from direcciones where clie_rut=".($clie_rut+0)." and dire_activo=1 and dire_defecto='p' or dire_defecto=null";

    $edirec = tep_db_query($queryDir);
    $edirec = tep_db_fetch_array( $edirec );
}


/*Actualiza el dato exista o no*/
if ($accion=="update"){
    /* Se hace este select para ver si el cliente es nuevo o ya existe*/
    $qexiste="select clie_nombre from clientes where clie_rut=".($clie_rut+0);
    $exis = tep_db_query($qexiste);
    $exis = tep_db_fetch_array( $exis );
	
    $num = strlen($barrios);
    $localizacion = $barrios;
    $barrios = substr($localizacion, $num-3, $num);
    $clie_localidad = substr($localizacion, $num-6, 3);
    $ciudad = substr($localizacion, $num-9, 3);
    $clie_provincia = substr($localizacion, $num-12, 3);  
    
    if($tipo_contribuyente == 'RS')
	$id_contribuyente = 2;
    
    if($sexo == 1)
    $valor_sexo = 'F';

	if($sexo == 2)
    $valor_sexo = 'M';
    
        
    $queryUpC ="UPDATE clientes SET clie_nombre='$clie_nombre', clie_paterno='$clie_paterno', clie_materno='$clie_materno',clie_telefonocasa='$clie_telefonocasa',clie_telcontacto1='$clie_telcontacto1',clie_telcontacto2='$clie_telcontacto2',clie_activo=1,clie_tipo='p', clie_email='$clie_email', clie_ciudad = $ciudad, clie_barrio = $barrios, clie_sexo = '$sexo', clie_tipo_cliente = $tipo_cliente, clie_categoria_cliente = $categoria_cliente, clie_tipo_contribuyente = '$tipo_contribuyente', clie_departamento = $departamento, clie_localidad = $clie_localidad,  clie_provincia = $clie_provincia, clie_localizacion = $localizacion, id_tipo_doc = $id_tipo_doc WHERE clie_rut=".($clie_rut+0)."";       	    
    
    tep_db_query($queryUpC);

             if ($edirec['dire_direccion']!='' ){
                $queryUpD ="UPDATE direcciones SET dire_direccion='$dire_direccion',dire_telefono='$clie_telefonocasa',dire_observacion='$dire_observacion', id_departamento = $departamento, id_ciudad = $ciudad, id_localidad = $clie_localidad, id_comuna= $barrios,  id_provincia = $clie_provincia, dire_localizacion = $localizacion  WHERE clie_rut=" . $clie_rut . " and dire_defecto='p'";
            } else {
                $queryUpD =  "INSERT INTO direcciones (dire_direccion,clie_rut,dire_telefono,dire_observacion, id_departamento, id_ciudad, id_localidad, id_comuna, dire_activo,dire_defecto,dire_nombre, id_provincia, dire_localizacion ) VALUES('$dire_direccion',".($clie_rut+0).",'$clie_telefonocasa','$dire_observacion', $departamento, $ciudad, $clie_localidad, $barrios, 1,'p','Principal', $clie_provincia, $localizacion)";
            }
    tep_db_query($queryUpD);

/* MODO BUSQUEDA PARA CLIENTE UNICO*/        
  
   $response = ClientUnique::searchById($clie_rut);
		if($response){
			//echo "Este es el Nombre: ", $response['FirstName'], "<br>";	
			//print_r($response);
			//echo "<br>";			
			}
		else {
			print"<script language=\"JavaScript\">alert(\"Se presento un error en el WS. \");</script>";
		$TypeCustomer = 6;
		}    	
        
    
/* MODO INSERCIÓN PARA CLIENTE UNICO*/    

if($response['IdCustomer'] == 0 )
{ 
	
$xml = "<request>
  <customer>
  <Source>" . PREFIJO_CENTRO_PROYECTOS . "</Source>
    <IdCustomer>$clie_rut</IdCustomer>
    <IdCategory>$categoria_cliente</IdCategory>
  	<IdTypeContribuyente>$id_contribuyente</IdTypeContribuyente>
  	<TypeCustomer>
    <IdTypeCustomer>$tipo_cliente</IdTypeCustomer>
    </TypeCustomer>
    <Location>$localizacion</Location>
	<IdDoc>$id_tipo_doc</IdDoc>
	<FirstName>$clie_nombre</FirstName>
	<Surname1>$clie_paterno</Surname1>
    <Surname2>$clie_materno</Surname2>
    <Address>$dire_direccion</Address>
	<Phone>$clie_telefonocasa</Phone>
    <Phone2>$clie_telcontacto2</Phone2>
    <Fax>$clie_telcontacto1</Fax>
    <Email>$clie_email</Email>
    <AgeRange>0</AgeRange>
    <Contact></Contact>
    <Gender>$valor_sexo</Gender>
    <Occupation></Occupation>
    <Profession></Profession>
    <ReteIca>0</ReteIca>
    <ReteFuente>0</ReteFuente>
    <ReteIva>0</ReteIva>
    <OtrIva>0</OtrIva>
    <ExenIva>0</ExenIva>
    <Status>A</Status>
    <CustomerLoyality></CustomerLoyality>
  </customer>
</request>";

$respuestaCreacion = ClientUnique::createClient($xml);	

	if($respuestaCreacion['state'] == 0)
	{		
		$nombreArc = date('Hidm');
		$nombreArc = $nombreArc . 'ClienteCreacion';
		$archivo = fopen("../../Cliente_Unico/" . $nombreArc . ".xml", "w+");
		fwrite($archivo, $xml);
		fclose($archivo);
	}

}

/* CUANDO ES UN CLIENTE INGRESADO DESDE POS */
if($response['TypeCustomer'] == '')
{
	 $response[0]['IdTypeCustomer'] = 6;	 
}

/* MODO ACTUALIZACIÓN PARA CLIENTE UNICO*/

if($response['IdCustomer'] != 0 )
{ 

$xml = "<request>
  <customer>
  <Source>" . PREFIJO_CENTRO_PROYECTOS . "</Source>
    <IdCustomer>$clie_rut</IdCustomer>
    <IdCategory>$categoria_cliente</IdCategory>
  	<IdTypeContribuyente>$id_contribuyente</IdTypeContribuyente>
  	<TypeCustomer>
    <IdTypeCustomer>" . $response[0]['IdTypeCustomer'] . "</IdTypeCustomer>
    </TypeCustomer>
    <Location>$localizacion</Location>
	<IdDoc>$id_tipo_doc</IdDoc>
	<FirstName>$clie_nombre</FirstName>
	<Surname1>$clie_paterno</Surname1>
    <Surname2>$clie_materno</Surname2>
    <Address>$dire_direccion</Address>
	<Phone>$clie_telefonocasa</Phone>
    <Phone2>$clie_telcontacto2</Phone2>
    <Fax>$clie_telcontacto1</Fax>
    <Email>$clie_email</Email>
    <AgeRange>0</AgeRange>
    <Contact></Contact>
    <Gender>$valor_sexo</Gender>
    <Occupation>" . $response['Occupation'] . "</Occupation>
    <Profession>" . $response['Profession'] . "</Profession>
    <ReteIca>0</ReteIca>
    <ReteFuente>0</ReteFuente>
    <ReteIva>0</ReteIva>
    <OtrIva>0</OtrIva>
    <ExenIva>0</ExenIva>
    <Status>A</Status>
    <CustomerLoyality></CustomerLoyality>
  </customer>
</request>";

	
	$respuestaActualizar = ClientUnique::updateClient($xml);
	
	
	if($respuestaActualizar['state'] == 0)
	{		
		$nombreArc = date('Hidm');
		$nombreArc = $nombreArc . 'ClienteModificacion';
		$archivo = fopen("../../Cliente_Unico/" . $nombreArc . ".xml", "w+");
		fwrite($archivo, $xml);
		fclose($archivo);
	}
	
	
}

if($response['IdTypeCustomer'])
	$TypeCustomer = $response['IdTypeCustomer'];

//if( $response[0]['IdTypeCustomer']==0 || $response[0]['IdTypeCustomer']==2 || $response[0]['IdTypeCustomer']==4 || $response[0]['IdTypeCustomer']==6 || $response[0]['IdTypeCustomer']==9 || $response[0]['IdTypeCustomer']==10 || $response[0]['IdTypeCustomer']==11 || $response[0]['IdTypeCustomer']==12 )
if($response['IdGroup'] == 1 || $response['IdGroup'] == "")
{
$TipoClienteUnico = 1;
}
else{
	$TipoClienteUnico = 2;
}

if($TipoClienteUnico == 2)
{
	$delCLie =  "DELETE FROM clientes WHERE clie_rut =".$clie_rut;
	tep_db_query($delCLie);
	?>
	<script language="JavaScript">
		alert('Este cliente no puede realizar cotizaciones en CP\n\tPor Favor Remitalo a CVE');
		llocation.href="/cotiza_Regalos_00.php?idLista=<?echo $idLista;?>";
	</script>
	<?php	
}

if (!$donde){	
	echo "<script language='JavaScript'>";		
		echo "location.href='cotiza_Regalos_02.php?clie_rut=" .($clie_rut+0). "&idLista=".($idLista+0)." '";
	echo "</script>";
		    
}else{
	?>
	<script language="JavaScript">
		window.returnValue = 'refresh';
		window.close();
	</script>
	<?php
}
tep_exit();
}

// Este codigo Pinta los Datos en la Plantilla
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' .NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

if ( isset($rutE) ){
	
	
	
	$datosCliente = ClientUnique::searchById($clie_rut);		
		
		if($datosCliente['IdCustomer'] != 0)
		{
		$MiTemplate->set_var("clie_rut",$datosCliente['IdCustomer']);
	    	$MiTemplate->set_var("clie_nombre",$datosCliente['FirstName']);
	    	$MiTemplate->set_var("clie_paterno",$datosCliente['Surname1']);
	    	$MiTemplate->set_var("clie_materno",$datosCliente['Surname2']);	    
	    	$MiTemplate->set_var("clie_email",$datosCliente['Email']);     
	    	$MiTemplate->set_var("clie_telefonocasa",$datosCliente['Phone']);
	    	$MiTemplate->set_var("clie_telcontacto1",$datosCliente['Fax']);
	    	$MiTemplate->set_var("clie_telcontacto2",$datosCliente['Phone2']);	    
	    	$MiTemplate->set_var("dire_direccion",$datosCliente['Address']);
	    	$MiTemplate->set_var("dire_observacion",$edirec['dire_observacion']);
		}
		else{
			$MiTemplate->set_var("clie_rut", $clie_rut);
		}	
	
		if($datosCliente['IdCustomer'] == 0 && $eclient['clie_nombre'] != '')
		{
		     $MiTemplate->set_var("clie_rut",$eclient['clie_rut']);
		     $MiTemplate->set_var("clie_nombre",$eclient['clie_nombre']);
		     $MiTemplate->set_var("clie_paterno",$eclient['clie_paterno']);
		     $MiTemplate->set_var("clie_materno",$eclient['clie_materno']);
		     
		     $MiTemplate->set_var("clie_email",$eclient['clie_email']);     
		     $MiTemplate->set_var("clie_telefonocasa",$eclient['clie_telefonocasa']);
		     $MiTemplate->set_var("clie_telcontacto1",$eclient['clie_telcontacto1']);
		     $MiTemplate->set_var("clie_telcontacto2",$eclient['clie_telcontacto2']);	     
		     $MiTemplate->set_var("dire_direccion",$edirec['dire_direccion']);
		     $MiTemplate->set_var("dire_observacion",$edirec['dire_observacion']);	     
		}
	
     	
     if ($donde)
     $MiTemplate->set_var("leyenda","Ingrese Datos del Cliente");
 }

	$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
	$MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
	$MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
	$MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
	$MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
	$MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
	$MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
	$MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
	$MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);
	
	$MiTemplate->set_var("TEXT_FLECHA_ANT",TEXT_FLECHA_ANT);
	$MiTemplate->set_var("TEXT_FLECHA_SIG",TEXT_FLECHA_SIG);
	
	// Agregamos el header
	$MiTemplate->set_file("header","header_ident.html");
	$MiTemplate->set_var("idLista",$idLista);
	
	// Agregamos el main
	$MiTemplate->set_file("main1","cotiza_Regalos/template/nueva_lista_temTitulo.html");
	$MiTemplate->set_file("main2","cotiza_Regalos/template/nueva_lista_temCuerpo.html");
	$MiTemplate->set_file("main3","cotiza_Regalos/template/nueva_lista_temBotones.html");
	$MiTemplate->set_file("main4","cotiza_Regalos/template/nueva_lista_botones.html");

	
	$queryCiu =  "SELECT C.ID AS id_ciudad, C.DESCRIPTION AS nombre_ciudad  FROM cu_city C WHERE ID_DEPARTMENT = 91 ORDER BY C.DESCRIPTION ";
    $resultCiu = tep_db_query($queryCiu);
    $IdCiudad = tep_db_fetch_array($resultCiu);    
    
	
    $MiTemplate->set_block("main2", "Departamento", "BLO_departamento");		
	if ($eclient['clie_nombre']==''){
    	$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento FROM cu_department D ORDER BY D.DESCRIPTION  ";
		
    	if($datosCliente['IdCustomer'] != 0){ 
    		$num = strlen($datosCliente['Location']);
			if($num == 13)
			$datosCliente['Location'] = "0" . $datosCliente['Location'];
			   		
			$num = strlen($datosCliente['Location']);    		
    		$id_departamen = substr($datosCliente['Location'], $num-14, 2);
			$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento, case WHEN D.ID = $id_departamen then 'selected' else '' end selected  FROM cu_department D ORDER BY D.DESCRIPTION ";			
		}
	}
	else{
        if($datosCliente['IdCustomer'] != 0)
		{
		$num = strlen($datosCliente['Location']);

		if($num == 13)
		$datosCliente['Location'] = "0" . $datosCliente['Location'];

		if($num == 0)
		$datosCliente['Location'] = "000000000000000";

		$num = strlen($datosCliente['Location']);
		$id_departamen = substr($datosCliente['Location'], $num-14, 2);
		$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento, case WHEN D.ID = $id_departamen then 'selected' else '' end selected  FROM cu_department D ORDER BY D.DESCRIPTION ";
		}
		else{
		$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento, case WHEN clie_rut is null then '' else 'selected' end selected   FROM cu_department D
			  LEFT JOIN clientes CLI ON D.ID = CLI.clie_departamento AND CLI.clie_rut = " . $eclient['clie_rut'] . "
			  ORDER BY D.DESCRIPTION ";
		}
		/*
		$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento, case WHEN clie_rut is null then '' else 'selected' end selected   FROM cu_department D 
				  LEFT JOIN clientes CLI ON D.ID = CLI.clie_departamento AND CLI.clie_rut = " . $eclient['clie_rut'] . "    
				  ORDER BY D.DESCRIPTION ";
		*/
	}
    query_to_set_var($query, $MiTemplate, 1, 'BLO_departamento', 'Departamento');
    
    
	
    $MiTemplate->set_block("main2", "Ciudad", "BLO_ciudad");		
	if ($eclient['clie_nombre']==''){
    	$query = "SELECT CONCAT(C.ID, ',', C.ID_PROVINCE ) AS id_ciudad, C.DESCRIPTION AS nombre_ciudad  
    	FROM cu_city C WHERE ID_DEPARTMENT = 91 ORDER BY C.DESCRIPTION ";
    	
		if($datosCliente['IdCustomer'] != 0){			
			$num = strlen($datosCliente['Location']);
			if($num == 13)
			$datosCliente['Location'] = "0" . $datosCliente['Location'];
			
			$num = strlen($datosCliente['Location']);
			
    		$id_departamen = substr($datosCliente['Location'], $num-14, 2);
    		$id_provinc = substr($datosCliente['Location'], $num-12, 3);
    		$id_ciud = substr($datosCliente['Location'], $num-9, 3);
			
    		$query = "SELECT DISTINCT C.DESCRIPTION AS nombre_ciudad, CONCAT(C.ID, ',', C.ID_PROVINCE ) AS id_ciudad, case WHEN C.ID = $id_ciud  AND C.ID_PROVINCE  = $id_provinc  then  'selected' else '' end selected 
					  FROM cu_city C 
					  WHERE C.ID_DEPARTMENT = $id_departamen 
					  ORDER BY C.DESCRIPTION ";			
		}
	}
	else{
        if($datosCliente['IdCustomer'] != 0)
		{
			$num = strlen($datosCliente['Location']);
			if($num == 13)
			$datosCliente['Location'] = "0" . $datosCliente['Location'];

			$num = strlen($datosCliente['Location']);

    		$id_departamen = substr($datosCliente['Location'], $num-14, 2);
    		$id_provinc = substr($datosCliente['Location'], $num-12, 3);
    		$id_ciud = substr($datosCliente['Location'], $num-9, 3);

    		$query = "SELECT DISTINCT C.DESCRIPTION AS nombre_ciudad, CONCAT(C.ID, ',', C.ID_PROVINCE ) AS id_ciudad, case WHEN C.ID = $id_ciud  AND C.ID_PROVINCE  = $id_provinc  then  'selected' else '' end selected
					  FROM cu_city C
					  WHERE C.ID_DEPARTMENT = $id_departamen
					  ORDER BY C.DESCRIPTION ";
		}
		else{
		$query="SELECT DISTINCT C.DESCRIPTION AS nombre_ciudad, CONCAT(C.ID, ',', C.ID_PROVINCE ) AS id_ciudad, case WHEN CLI.clie_rut is null then '' else 'selected' end selected
				FROM cu_city C
				LEFT JOIN cu_department D ON D.ID = C.ID_DEPARTMENT
				LEFT JOIN cu_province PR ON PR.ID = C.ID_PROVINCE
				LEFT JOIN clientes CLI ON C.ID = CLI.clie_ciudad
				AND CLI.clie_rut = " . $eclient['clie_rut'] . "
				AND C.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . ")
				AND C.ID = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . ")
				WHERE C.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
				ORDER BY C.DESCRIPTION ";
		}

		/*
		$query="SELECT DISTINCT C.DESCRIPTION AS nombre_ciudad, CONCAT(C.ID, ',', C.ID_PROVINCE ) AS id_ciudad, case WHEN CLI.clie_rut is null then '' else 'selected' end selected 
				FROM cu_city C 
				LEFT JOIN cu_department D ON D.ID = C.ID_DEPARTMENT 
				LEFT JOIN cu_province PR ON PR.ID = C.ID_PROVINCE 
				LEFT JOIN clientes CLI ON C.ID = CLI.clie_ciudad 
				AND CLI.clie_rut = " . $eclient['clie_rut'] . " 
				AND C.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . ")
				AND C.ID = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . ") 
				WHERE C.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
				ORDER BY C.DESCRIPTION ";
		*/
	}	 
	query_to_set_var($query, $MiTemplate, 1, 'BLO_ciudad', 'Ciudad');
	
	$MiTemplate->set_block("main2", "Barrios", "BLO_com");
	if ( trim($eclient['clie_nombre'])==''){
        $query = "SELECT  DISTINCT N.LOCATION  AS id_comuna,  CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre FROM cu_neighborhood N
				  LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY
				  AND L.ID_DEPARTMENT = 91 AND L.ID_CITY = 5
				  AND L.ID_PROVINCE = 1
				  LEFT JOIN cu_province PR ON PR.ID = N.ID_PROVINCE
				  WHERE N.ID_DEPARTMENT = 91
				  AND N.ID_CITY = 5
				  AND N.ID_PROVINCE = 1
		 		  ORDER BY  N.DESCRIPTION ";
		        
		if($datosCliente['IdCustomer'] != 0){			
			$num = strlen($datosCliente['Location']);
			if($num == 13)
			$datosCliente['Location'] = "0" . $datosCliente['Location'];
			
			$num = strlen($datosCliente['Location']);
			
    		$id_departamen = substr($datosCliente['Location'], $num-14, 2);
    		$id_provinc = substr($datosCliente['Location'], $num-12, 3);
    		$id_ciud = substr($datosCliente['Location'], $num-9, 3);
			$id_localit = substr($datosCliente['Location'], $num-6, 3);
    		
    		$query = "SELECT DISTINCT N.LOCATION AS id_comuna, CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre, 
    				case WHEN  N.LOCATION = " . $datosCliente['Location'] . " then 'selected' else '' end selected 
					FROM cu_neighborhood N 
					LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY AND  L.ID_DEPARTMENT = $id_departamen AND L.ID_PROVINCE = $id_provinc AND L.ID_CITY = $id_ciud 
					WHERE N.ID_DEPARTMENT = $id_departamen AND N.ID_PROVINCE = $id_provinc AND N.ID_CITY = $id_ciud 					
			 		ORDER BY N.DESCRIPTION ";

		}
				
    }
    else{
    
        if($datosCliente['IdCustomer'] != 0)
    	{
			$num = strlen($datosCliente['Location']);
			if($num == 13)
			$datosCliente['Location'] = "0" . $datosCliente['Location'];

			$num = strlen($datosCliente['Location']);

    		$id_departamen = substr($datosCliente['Location'], $num-14, 2);
    		$id_provinc = substr($datosCliente['Location'], $num-12, 3);
    		$id_ciud = substr($datosCliente['Location'], $num-9, 3);
			$id_localit = substr($datosCliente['Location'], $num-6, 3);

    		$query = "SELECT DISTINCT N.LOCATION AS id_comuna, CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre,
    				case WHEN  N.LOCATION = " . $datosCliente['Location'] . " then 'selected' else '' end selected
					FROM cu_neighborhood N
					LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY AND  L.ID_DEPARTMENT = $id_departamen AND L.ID_PROVINCE = $id_provinc AND L.ID_CITY = $id_ciud
					WHERE N.ID_DEPARTMENT = $id_departamen AND N.ID_PROVINCE = $id_provinc AND N.ID_CITY = $id_ciud
			 		ORDER BY N.DESCRIPTION ";

		}
		else{
    	 $query =  "SELECT DISTINCT N.LOCATION AS id_comuna, CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre, case WHEN CLI.clie_rut is null then '' else 'selected' end selected
					FROM cu_neighborhood N
					LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY
					AND L.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND L.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND L.ID_CITY = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					LEFT JOIN clientes CLI ON  N.ID = CLI.clie_barrio AND CLI.clie_rut = " . $eclient['clie_rut'] . "
					AND N.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND N.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND N.ID_CITY = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND N.LOCATION = (SELECT clie_localizacion FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					WHERE N.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND N.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					AND N.ID_CITY = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " )
					ORDER BY N.DESCRIPTION  ";
		}

		/*
    	 $query =  "SELECT DISTINCT N.LOCATION AS id_comuna, CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre, case WHEN CLI.clie_rut is null then '' else 'selected' end selected 
					FROM cu_neighborhood N 
					LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY 
					AND L.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND L.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND L.ID_CITY = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					LEFT JOIN clientes CLI ON  N.ID = CLI.clie_barrio AND CLI.clie_rut = " . $eclient['clie_rut'] . "
					AND N.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND N.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND N.ID_CITY = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND N.LOCATION = (SELECT clie_localizacion FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					WHERE N.ID_DEPARTMENT = (SELECT clie_departamento FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND N.ID_PROVINCE = (SELECT clie_provincia FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					AND N.ID_CITY = (SELECT clie_ciudad FROM clientes WHERE clie_rut = " . $eclient['clie_rut'] . " ) 
					ORDER BY N.DESCRIPTION  ";
		*/
    	
    }    
	query_to_set_var($query, $MiTemplate, 1, 'BLO_com', 'Barrios');		
	

	$MiTemplate->set_block("main2", "Categoria_cliente", "BLO_categoria");
	if ($eclient['clie_nombre']==''){
		$query = "SELECT id_categoria, nombre_categoria  FROM  tipo_categoria_cliente ORDER BY id_categoria ";
		if($datosCliente['IdCustomer'] != 0){

			$query = "SELECT id_categoria, nombre_categoria,  case WHEN  id_categoria = " . $datosCliente['IdCategory'] . "  then 'selected' else ''  end selected
					  FROM  tipo_categoria_cliente T ORDER BY id_categoria ";
		}
	}
	else{
	$query = "SELECT id_categoria, nombre_categoria,  case WHEN clie_rut is null then '' else 'selected' end selected   FROM  tipo_categoria_cliente T LEFT JOIN clientes C ON T.id_categoria = C.clie_categoria_cliente AND C.clie_rut = " . $eclient['clie_rut'] . " ORDER BY id_categoria ";
	}
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_categoria', 'Categoria_cliente' );
	
	
	$MiTemplate->set_block("main2", "Tipo_cliente", "BLO_tipo");
	if ($eclient['clie_nombre']==''){
		$query = "SELECT id_tipo_cliente, nombre_tipo_cliente  FROM  tipo_cliente WHERE id_tipo_cliente <> 4 ORDER BY nombre_tipo_cliente"; 
	}
	else{
    	$query = "SELECT id_tipo_cliente, nombre_tipo_cliente,  case WHEN clie_rut is null then '' else 'selected' end selected  FROM  tipo_cliente T LEFT JOIN clientes C ON T.id_tipo_cliente = C.clie_tipo_cliente AND C.clie_rut = " . $eclient['clie_rut'] . " WHERE id_tipo_cliente <> 4 ORDER BY nombre_tipo_cliente " ;

	if($datosCliente['IdCustomer'] != 0){	
		$query = "SELECT id_tipo_cliente, nombre_tipo_cliente,  case WHEN T.id_tipo_cliente = " . $datosCliente[0]['IdTypeCustomer'] . "  then 'selected' else '' end selected  
				  FROM  tipo_cliente T 
				  WHERE id_tipo_cliente <> 4 ORDER BY nombre_tipo_cliente ";
	}

	}    
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_tipo', 'Tipo_cliente' );
	
	
	$MiTemplate->set_block("main2", "Sexo_cliente", "BLO_sexo");
	if ($eclient['clie_nombre']==''){
		
		$query = "SELECT id_sexo_cliente, sexo_cliente FROM  sexo_cliente ";
		
		if($datosCliente['IdCustomer'] != 0){
			if($datosCliente['Gender'] == 'F')
			$sexoClieUni = 1;
			else
			$sexoClieUni = 2;
			
			$query = "SELECT id_sexo_cliente, sexo_cliente, case WHEN id_sexo_cliente = $sexoClieUni  then 'selected' else '' end selected  FROM  sexo_cliente ";			
		}
	    
	}
	else{    
	$query = "SELECT id_sexo_cliente, sexo_cliente, case WHEN clie_rut is null then '' else 'selected' end selected  FROM  sexo_cliente S LEFT JOIN clientes C ON S.id_sexo_cliente = C.clie_sexo AND C.clie_rut = " . $eclient['clie_rut'];
	}
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_sexo', 'Sexo_cliente' );
	
	
	
	$MiTemplate->set_block("main2", "tipo_doc", "BLO_tipo_doc");
	if ($eclient['clie_nombre']==''){
		
		$query = "SELECT id_nif, descripcion FROM  tipo_nif ";
		
		if($datosCliente['IdCustomer'] != 0){
			$query = "SELECT id_nif, descripcion, case WHEN id_nif = " . $datosCliente['IdDoc'] .  " then  'selected' else '' end selected   
					  FROM  tipo_nif ";			
		}
	    
	}
	else{    
	$query = "SELECT T.id_nif, T.descripcion, case WHEN C.clie_rut is null then '' else 'selected' end selected  
			FROM  tipo_nif T 
			LEFT JOIN clientes C ON T.id_nif = C.id_tipo_doc AND C.clie_rut = " . $eclient['clie_rut'];
	}
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_tipo_doc', 'tipo_doc');

// Agregamos el footer
if ($donde!="fin"){
	include "../../includes/footer_cproy.php";
	$MiTemplate->pparse("OUT_H", array("header"), true);
	include "../../menu/menu.php";
	$MiTemplate->parse("OUT_M", array("main1","main2","main3","footer"), true);
	$MiTemplate->p("OUT_M");
}
else{
	$MiTemplate->set_var("espacio","<br><br>");
    $MiTemplate->set_var("donde",$donde);
	$MiTemplate->parse("OUT_M", array("main","main2","main4"), true);
	$MiTemplate->p("OUT_M");
}
	include "../../includes/application_bottom.php";
?>
