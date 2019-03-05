<?php
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
$SIN_PER = 1;
include ("../../includes/aplication_top.php");


// *************************************************************************************
/** ACCIONES*************/
/* elige una direcicon para editarla es la que se abre el en pop up*/
if($accion=="editar"){	
   	$queryDire="SELECT  D.dire_defecto, D.id_direccion , D.dire_nombre, D.dire_observacion,D.dire_direccion , D.dire_telefono , NE.DESCRIPTION AS comu_nombre ,CL.clie_nombre, CL.clie_paterno ,CL.clie_rut 
				FROM  direcciones D 
				LEFT JOIN cu_neighborhood  NE  ON NE.LOCATION = D.dire_localizacion 
				INNER JOIN  clientes CL ON  (CL.clie_rut=D.clie_rut) 
				WHERE D.id_direccion = $id_direccion ";
	$Qdire = tep_db_query($queryDire);
    $Qdire = tep_db_fetch_array( $Qdire );
}
if($accion=="Guardar"){
				
	
/* si la direccion ya existe*/
        $queryclie="select CL.clie_rut,CL.clie_nombre,CL.clie_paterno from clientes CL inner join os OS on (CL.clie_rut=OS.clie_rut) where OS.id_os =".($id_os+0)."";
        $clien = tep_db_query($queryclie);
        $clien = tep_db_fetch_array($clien);
        
        $num = strlen($barrios);
    	$localizacion = $barrios;
	    $barrios = substr($localizacion, $num-3, $num);
	    $id_localidad = substr($localizacion, $num-6, 3);
	    $ciudad = substr($localizacion, $num-9, 3);
	    $id_provincia = substr($localizacion, $num-12, 3);	    
	    
	    
    if ($id_direccion>0){
        $queryDireg="UPDATE direcciones SET dire_nombre='$dire_nombre', dire_direccion ='$dire_direccion', id_departamento=$departamento, id_ciudad=$ciudad, id_comuna=".($barrios+0).",dire_observacion ='$dire_observacion', dire_telefono='$dire_telefono', dire_activo=1, id_localidad = $id_localidad, id_provincia = $id_provincia, dire_localizacion = $localizacion  where id_direccion= ".($id_direccion+0)." ";
        $Qdireg = tep_db_query($queryDireg);
        /* cambia el id de la direccion en la OS*/
        $qupdi="UPDATE os SET id_direccion = " . $id_direccion . " where id_os = " . $id_os;
        tep_db_query($qupdi);
    }else{
       /* inserta la direccion y la deja como primaria*/
       	
    	$queryDireg="Insert Into direcciones(clie_rut,dire_nombre,dire_direccion,id_departamento, id_ciudad, id_localidad, id_comuna,dire_observacion,dire_telefono,dire_activo, id_provincia, dire_localizacion) VALUES(".( $clien['clie_rut']+0).",'$dire_nombre','$dire_direccion',$departamento, $ciudad, $id_localidad, ".($barrios).",'$dire_observacion','$dire_telefono',1, $id_provincia, $localizacion)";
       	writelog("Insert direcciones : ". $queryDireg);
       	$Qdireg  = tep_db_query($queryDireg);
       	$UidistD = tep_db_insert_id('');
        writelog("Ultimo ID : ". $UidistD);
        /* cambia el id de la direccion en la OS*/
        $qupdi="UPDATE os SET id_direccion=".($UidistD+0)." where id_os=".($id_os+0)." ";
        tep_db_query($qupdi);
     }
     
   	  	  
        ?>
        <script language="JavaScript">
            window.returnValue = 'refresh';
            window.close();
        </script>
        <?
       tep_exit();
}

if($accion=="Nueva"){
    $queryclie="select CL.clie_nombre,CL.clie_paterno from clientes CL inner join os OS on (CL.clie_rut=OS.clie_rut) where OS.id_os =".($id_os+0)."";
    $clien = tep_db_query($queryclie);
    $clien = tep_db_fetch_array($clien);
}

/*********************************/
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");


/* ultimo id insertado en direcciones*/
$MiTemplate->set_var("UidistD",$UidistD);
$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("id_direccion",$id_direccion);
$MiTemplate->set_var("id_os",$id_os);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

$valor="DISABLED";

if ($accion=="editar" ){
    if ($Qdire['dire_defecto']=='p')
    	{
        $MiTemplate->set_var("valor","DISABLED");
        $MiTemplate->set_var("valor1","DISABLED");
        $MiTemplate->set_var("valor2","DISABLED");
        $MiTemplate->set_var("valor3","DISABLED");
        $MiTemplate->set_var("valor4","DISABLED");
        
    	}
    else{
         $MiTemplate->set_var("valor","ENABLED");
         $MiTemplate->set_var("valor1","ENABLED");
         $MiTemplate->set_var("valor2","ENABLED");
         $MiTemplate->set_var("valor3","ENABLED");
         $MiTemplate->set_var("valor4","ENABLED");
		 }
     
     $MiTemplate->set_var("clie_nombre",$Qdire['clie_nombre']);
     $MiTemplate->set_var("clie_paterno",$Qdire['clie_paterno']);
     $MiTemplate->set_var("id_direccion",$Qdire['id_direccion']);
     $MiTemplate->set_var("dire_nombre",$Qdire['dire_nombre']);
     $MiTemplate->set_var("dire_telefono",$Qdire['dire_telefono']);
     $MiTemplate->set_var("dire_direccion",$Qdire['dire_direccion']);
     $MiTemplate->set_var("comu_nombre",$Qdire['comu_nombre']);
     $MiTemplate->set_var("dire_observacion",$Qdire['dire_observacion']);
}
if ($accion=="Nueva" ){
     $MiTemplate->set_var("clie_nombre",$clien['clie_nombre']);
     $MiTemplate->set_var("clie_paterno",$clien['clie_paterno']);
}

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/libreta2.htm");

	$queryd = "SELECT clie_rut FROM direcciones WHERE id_direccion = " . $Qdire['id_direccion'];
    $resultado = tep_db_query($queryd);
    $eclient = tep_db_fetch_array($resultado);
    	
	$queryCiu =  "SELECT C.ID AS id_ciudad, C.DESCRIPTION AS nombre_ciudad  FROM cu_city C WHERE ID_DEPARTMENT = 91 ORDER BY C.DESCRIPTION ";
    $resultCiu = tep_db_query($queryCiu);
    $IdCiudad = tep_db_fetch_array($resultCiu);  

	$MiTemplate->set_block("main", "Departamento", "BLO_departamento");		
	if ($accion=='Nueva'){
    	$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento FROM cu_department D ORDER BY D.DESCRIPTION  ";
	}
	else{
		$query = "SELECT D.ID AS id_departamento, D.DESCRIPTION AS nombre_departamento, case WHEN  DI.id_direccion  is null then '' else 'selected' end selected   FROM cu_department D 
 				  LEFT JOIN  direcciones DI ON DI.id_departamento = D.ID AND DI.id_direccion = ". $Qdire['id_direccion'] . "
 				  ORDER BY D.DESCRIPTION ";		
	}
    query_to_set_var($query, $MiTemplate, 1, 'BLO_departamento', 'Departamento');
    
    
    $MiTemplate->set_block("main", "Ciudad", "BLO_ciudad");		
	if ($accion=='Nueva'){
    	$query = "SELECT C.ID AS id_ciudad, C.DESCRIPTION AS nombre_ciudad  FROM cu_city C WHERE ID_DEPARTMENT = 91 ORDER BY C.DESCRIPTION ";
	}
	else{
		
		$query="SELECT DISTINCT C.DESCRIPTION AS nombre_ciudad, CONCAT(C.ID, ',', C.ID_PROVINCE ) AS id_ciudad, case WHEN  DI.id_direccion is null then '' else 'selected' end selected 
				FROM cu_city C 
				LEFT JOIN cu_department D ON D.ID = C.ID_DEPARTMENT 
				LEFT JOIN cu_province PR ON PR.ID = C.ID_PROVINCE 
				LEFT JOIN direcciones DI ON  DI.id_departamento=D.ID AND DI.id_direccion =  ". $Qdire['id_direccion'] . "
				AND C.ID_PROVINCE = (SELECT id_provincia FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " )
				AND C.ID = (SELECT id_ciudad FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
				WHERE C.ID_DEPARTMENT = (SELECT id_departamento FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
				ORDER BY C.DESCRIPTION ";		
		
		
				
	}	 
	query_to_set_var($query, $MiTemplate, 1, 'BLO_ciudad', 'Ciudad');
		
	$MiTemplate->set_block("main", "Barrios", "BLO_com");
	if ($accion=='Nueva'){
        $query = "SELECT  DISTINCT N.LOCATION  AS id_comuna,  N.DESCRIPTION  AS comu_nombre  FROM cu_neighborhood N
				  INNER JOIN cu_locality L ON L.ID = N.ID_LOCALITY
				  WHERE N.ID_DEPARTMENT = 5
				  AND N.ID_CITY = 5 
				  ORDER BY  N.DESCRIPTION ";		
    }
    else{    	
		 $query =  "SELECT DISTINCT N.LOCATION AS id_comuna, CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre, case WHEN  DI.id_direccion  is null then '' else 'selected' end selected 
					FROM cu_neighborhood N 
					LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY
					AND L.ID_DEPARTMENT = (SELECT id_departamento FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND L.ID_PROVINCE = (SELECT id_provincia FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND L.ID_CITY = (SELECT id_ciudad FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 					
					LEFT JOIN direcciones DI ON  N.ID = DI.id_comuna AND DI.id_direccion = ". $Qdire['id_direccion'] . "
					AND N.ID_DEPARTMENT = (SELECT id_departamento FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND N.ID_PROVINCE = (SELECT id_provincia FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND N.ID_CITY = (SELECT id_ciudad FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND N.LOCATION = (SELECT dire_localizacion FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					WHERE N.ID_DEPARTMENT = (SELECT id_departamento FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND N.ID_PROVINCE = (SELECT id_provincia FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					AND N.ID_CITY = (SELECT id_ciudad FROM direcciones WHERE id_direccion = ". $Qdire['id_direccion'] . " ) 
					ORDER BY N.DESCRIPTION  ";
    }    
	query_to_set_var( $query, $MiTemplate, 1, 'BLO_com', 'Barrios');
		
	
$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>