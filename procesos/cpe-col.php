<?php
include_once("cfg_cpe_col.php");


$dir_log = dir($log_dir);
date_default_timezone_set('America/Argentina/Buenos_Aires');

$log = $dir_log->path."/".$file_log ;

//set_error_handler('error');

// Funcion que escribe el LOG de operaciones en un txt (Se configura
// del archivo conexion
function escribe_log($cadena2, $log) 
{
	$f2=fopen($log ,"a+");
	fwrite($f2,$cadena2."\r\n");
    fclose($f2);
	return ;
}

function comprimir ($nom_arxiu)
{
$fptr = fopen($nom_arxiu, "rb");
$dump = fread($fptr, filesize($nom_arxiu));
fclose($fptr);
 
//Comprime al máximo nivel, 9
$gzbackupData = gzencode($dump,9);
 
$fptr = fopen($nom_arxiu . ".gz", "wb");
fwrite($fptr, $gzbackupData);
fclose($fptr);
//Devuelve el nombre del archivo comprimido
return $nom_arxiu.".gz";
}

 /***********************************************************************************/
	// CompletaCerosI
	// Descripcion: Rellena una cadena con ceros a la izquierda
	/***********************************************************************************/
function CompletaCerosI($variable, $tamano){
	    return  sprintf("%0".($tamano+0).".".($tamano+0)."s", $variable);
	}

if (empty($dia_ayer) or empty($dia_ayer)) {
    $ahora = time();
    $ayer  = time()-(86400*$dias) ;
//echo date("Y-m-d", $ahora )."<br><br>";
//echo date("Y-m-d", $ayer)."<br>";
$dia_hoy  = date("Y-m-d", $ahora );
$dia_ayer = date("Y-m-d", $ayer);
$fecha = date("Ymd");

} else {
   $cadena2 = date("d-m-Y H:i:s")." "."Rutina corriendo en forma manual entre ".$dia_ayer." y ".$dia_hoy ;
    escribe_log ($cadena2, $log);
    $ano = substr($dia_hoy,0,4);
    $mes = substr($dia_hoy,5,2);
    $dia = substr($dia_hoy,8,2);
    $fecha = $ano.$mes.$dia;

}


$conexion = mysqli_connect($hostname, $username, $password ); 
if (!$conexion){
	$cadena2 = date("d-m-Y H:i:s")." "."Error en la conexion a la BASE DE DATOS NO CONECTADO";
    escribe_log ($cadena2, $log);
	exit;
} else {
	$cadena2 = date("d-m-Y H:i:s")." "."Se conecto en forma correcta al servidor";
	escribe_log ($cadena2, $log);

}
$base = mysqli_select_db($conexion, $database );

if (!$base) {
	
	$cadena2 = date("d-m-Y H:i:s")." "."No se abrio la BASE DE DATOS";
	escribe_log ($cadena2, $log);
	exit;
} else {
	
	$cadena2 = date("d-m-Y H:i:s")." "."La base de datos de abrio bien";
	escribe_log ($cadena2, $log);
}

// Parametros locales 
$pais     = "col" ; //  arg, chi, col, per;
$negocio  = "easy"; // ( easy, super, paris );
$programa = "cpe" ;
$grupo    = "cotiz" ;
$contenido = "ccotiz"; // ccotiz” (encabezado) | “dcotiz” (detalle)
$contenido_det = "dcotiz"; // ccotiz” (encabezado) | “dcotiz” (detalle)

$secuen = mysqli_query($conexion, "SELECT secuencia FROM teradata ORDER BY idteradata  DESC LIMIT 1 ");

$secuencia = null;

if ($secuen){
	$fila = mysqli_fetch_assoc($secuen);
	$secuencia = $fila['secuencia'];
	$cadena2 = date("d-m-Y H:i:s")." "."Lee la ultima secuencia guardada que es :".$secuencia;
}

escribe_log ($cadena2, $log);

if (is_null($secuencia) ){
	$secuencia = 0;
	$secu_file = "00";

} else { 
	
 
      if   ($secuencia<9 ) {
	      $secuencia=$secuencia+1;
	      $secu_file = "0".$secuencia;
 	
     } else { 
	     $secuencia = $secuencia+1;
	     $secu_file = $secuencia;
	
    }


    if ($secuencia>99) {
	    $secuencia = 0;
	    $secu_file = "00";
	}
} 



 $archivo = $pais."_".$negocio."_".$programa ."_".$grupo."_".$contenido."_".$fecha."_".$secu_file;
 $archivo_det = $pais."_".$negocio."_".$programa ."_".$grupo."_".$contenido_det."_".$fecha."_".$secu_file;
 
$encabezado=fopen($carpeta_info .$archivo.".dat","w+");
$detalle   =fopen($carpeta_info .$archivo_det.".dat","w+");
 $query_a = "select 	
	os.id_os				as id_transaccion,
	loc.cod_local                           as centro_cd,
	date(os.os_fechacreacion)		as fecha_inicio_trans,
	time(os.os_fechacreacion)		as hora_inicio_trans,
	space(1)				            as fecha_fin_trans,
	space(1)				            as hora_fin_trans,
	os.id_estado		        		as id_estado,
	space(1)				            as id_origen_venta,
	space(1)				            as id_origen_pedido,
	left(concat(replace(replace(IFNULL(os.os_comentarios,'') ,'|',' '),CHAR(13),' '),' ' ,replace(replace(IFNULL(os.os_descripcion,''),'|',' '), CHAR(13),' ')),255) as observaciones,
	space(1)				            as id_rango_entrega, 
	space(1)				            as id_transaccion_anterior,
	date(os.os_fechaboleta)			as fecha_facturacion,
	time(os.os_fechaboleta)			as hora_facturacion,
	space(1)				            as id_trn,
	space(1)				            as pos_numero_caja,
	os.os_numboleta	            as pos_numero_ticket,
	'cc'					              as tipo_id_operador,
	usr.USR_Login    	          as id_operador, 
	left(concat(replace(replace(IFNULL(usr.usr_apellidos,''),'|',' '), CHAR(13),' '), ' ',replace(replace(IFNULL(usr.usr_nombres,''),'|',' '), CHAR(13),' ')),255)	as nombre_operador,
	space(1)				            as pos_legajo_cajero,
	space(1)				            as flag_venta_empresa,
	space(1)				            as nro_sena,
	cli.clie_telcontacto1	      as telefono_contacto,
	space(1)				            as fecha_vencimiento_acopio,
	'cc'					              as tipo_documento,
	os.clie_rut				          as nro_documento,
	space(1)				            as nom_autorizo_mp,
	space(1)				            as fl_aprobado,
	SUM(os_det.osde_precio * os_det.osde_cantidad)	as importe_total ,
	space(1)				            as importe_flete,
	space(1)				            as importe_linea_caja,
	space(1)				            as vendedor_cd,
	space(1)				            as giro_cd,
	space(1)				            as giro_desc,
	'7 dias'				            as dias_validez_cnt,
	space(1)				            as condicion_pago_cd,
	space(1)				            as margen_total_pct,
	proy.id_proyecto			      as proyecto_cliente_cd,
	replace(replace(IFNULL(proy.proy_nombre,''),'|',' '), CHAR(13),' ')    as proyecto_cliente_desc,
	date(os.os_fechaestimada)	  as fecha_estimada_entrega,
	space(1)                    as persona_que_marco_pagado,
	space(1)				            as id_transaccion_visita
from	os
left join  usuarios  	      as usr 		    on os.usr_id = usr.usr_id
left join  proyectos  	    as proy 	    on os.id_proyecto = proy.id_proyecto
left join  os_detalle 	    as os_det    	on os.id_os = os_det.id_os
left join  clientes  	      as cli 		    on  os.clie_rut = cli.clie_rut
left join  locales 	        as loc 		    on os.id_local = loc.id_local    
	WHERE (os.os_fechacreacion>='$dia_ayer' AND os.os_fechacreacion<'$dia_hoy') group by  os.id_os";

	$cadena2 = date("d-m-Y H:i:s")." ".mysqli_errno($conexion)." ".mysqli_error($conexion)." en ".$query_a."\r\n";
     	escribe_log ($cadena2, $log);
	$cadena2 = date("d-m-Y H:i:s")." "."QUERY DEL ENCABEZADO:".$query_a."\r\n";
	escribe_log ($cadena2, $log);
	
if ($result = mysqli_query($conexion, $query_a)){ 
        
        $cantidad = mysqli_num_rows($result);
    	$cant_reg_enc= $cantidad;
	
        if ($cantidad==0) {
       
        	$cadena = "";
  	     fwrite($encabezado,$cadena );
  	     $ahora = date("Y-m-d H:i:s");
  	     $insert_0 = "INSERT teradata ( detalle , encabezado , fecha_proceso , fecha_paso_interfase , secuencia , archivo ) 
		              VALUES ( '0'  ,'1'  ,'$dia_ayer' , '$ahora' , '$secu_file' , '$archivo.dat'  )";
  	     mysqli_query($conexion , $insert_0  );
  	     $cadena2 = date("d-m-Y H:i:s")." "."Carga Teradata Encabezado para O archivos ". $insert_0." de archivo ".$archivo.".dat";
	     escribe_log ($cadena2, $log);
       } else {
    /* fetch associative array */
  while ($row = mysqli_fetch_assoc($result)) {
    	
    	
    	$id_transaccion                   =$row['id_transaccion']; 
    
        $query_b = "SELECT id_cotizacion FROM teradata WHERE (id_cotizacion='$id_transaccion' AND encabezado='1')";
    	$result1 = mysqli_query($conexion , $query_b  );
        $cant = mysqli_num_rows($result1);
        $cadena2 = date("d-m-Y H:i:s")." "."SI EL VALOR ES 1 el proceso ya paso borrar datos de tabla TERADATA:".$cant."\r\n";
         escribe_log ($cadena2, $log);
    	if ($cant==0){
    		
    		
    		 $id_transaccion                   = trim($row['id_transaccion']);                  // campo 1
    		 $centro_cd                        = trim($row['centro_cd']);                       // campo 2  
    		 $fecha_inicio_trans               = $row['fecha_inicio_trans'];              // campo 3   
    		 $hora_inicio_trans                = $row['hora_inicio_trans'];               // campo 4 
    		 $fecha_fin_trans                  = trim($row['fecha_fin_trans']);                 // campo 5
    		 $hora_fin_trans                   = trim($row['hora_fin_trans']);                  // campo 6
    		 $id_estado                        = trim($row['id_estado']);                       // campo 7
    		 $id_origen_venta                  = trim($row['id_origen_venta']);                 // campo 8
    		 $id_origen_pedido                 = trim($row['id_origen_pedido']);                 // campo 9
    	     $id_subtipo_operacion             = ''                            ;                // campo 10
     	     $observaciones                    = trim($row['observaciones']);                   // campo 11
     	     $id_rango_entrega                 = trim($row['id_rango_entrega']);                // campo 12
     	     $id_transaccion_anterior          = trim($row['id_transaccion_anterior']);         // campo 13
     	     $fecha_facturacion                = trim($row['fecha_facturacion']);               // campo 14
     	     $hora_facturacion                 = trim($row['hora_facturacion']);                // campo 15
     	     $id_trn                           = trim($row['id_trn']);                         // campo 16
     	     $pos_numero_caja                  = trim($row['pos_numero_caja']);                 // campo 17
     	     $pos_numero_ticket                = trim($row['pos_numero_ticket']);               // campo 18
     	     $tipo_id_operador                 = trim($row['tipo_id_operador']);                // campo 19
             $id_operador			           = trim($row['id_operador']);                     // campo 20
             $nombre_operador           	   = trim($row['nombre_operador']);                 // campo 21
             $pos_legajo_cajero                = trim($row['pos_legajo_cajero']);             // campo 22
             $flag_venta_empresa               = trim($row['flag_venta_empresa']);              // campo 23
             $nro_sena                         = trim($row['nro_sena']);                       // campo 24
     	     $telefono_contacto                = trim($row['telefono_contacto']);               // campo 25
             $fecha_vencimiento_acopio         = trim($row['fecha_vencimiento_acopio']);        // campo 26
             $tipo_documento                   = trim($row['tipo_documento']);                  // campo 27
             $nro_documento                    = trim($row['nro_documento']);                   // campo 28
             $nom_autorizo_mp                  = trim($row['nom_autorizo_mp']);                 // campo 29     
     	     $fl_aprobado                      = trim($row['fl_aprobado']);                     // campo 30
             $importe_total                    = trim($row['importe_total']);                   // campo 31 
             $importe_flete                    = trim($row['importe_flete']);                   // campo 32
             $importe_linea_caja			   = trim($row['importe_linea_caja']);              // campo 33
             $vendedor_cd                      = trim($row['vendedor_cd']);                     // campo 34
             $giro_cd                          = trim($row['giro_cd']);                         // campo 35
             $giro_desc                        = trim($row['giro_desc']);                       // campo 36
             $dias_validez_cnt                 = trim($row['dias_validez_cnt']);                // campo 37
     	     $condicion_pago_cd                = trim($row['condicion_pago_cd']);               // campo 38
             $margen_total_pct                 = trim($row['margen_total_pct']);                // campo 39
             $proyecto_cliente_cd              = trim($row['proyecto_cliente_cd']);             // campo 40
             $proyecto_cliente_desc            = trim($row['proyecto_cliente_desc']);           // campo 41
             $fecha_estimada_entrega           = trim($row['fecha_estimada_entrega']);          // campo 42     
     	     $persona_que_marco_pagado         = trim($row['persona_que_marco_pagado']);        // campo 43
             $id_transaccion_visita            = trim($row['id_transaccion_visita']);           // campo 44    		
             
            
    	    
     	     $cadena =$id_transaccion.'|'.$centro_cd.'|'.$fecha_inicio_trans.'|'.$hora_inicio_trans.'|'.$fecha_fin_trans.'|'.$hora_fin_trans.'|'.$id_estado.'|'.$id_origen_venta.'|';
             $cadena = $cadena.$id_origen_pedido.'|'.$id_subtipo_operacion.'|'.$observaciones.'|'.$id_rango_entrega.'|'.$id_transaccion_anterior.'|'.$fecha_facturacion.'|'.$hora_facturacion.'|'.$id_trn.'|'.$pos_numero_caja.'|'; 
     	     $cadena = $cadena.$pos_numero_ticket.'|'.$tipo_id_operador.'|'.$id_operador.'|'.$nombre_operador.'|'.$pos_legajo_cajero.'|'.$flag_venta_empresa.'|'.$nro_sena.'|'.$telefono_contacto.'|'.$fecha_vencimiento_acopio.'|'.$tipo_documento.'|'.$nro_documento.'|'.$nom_autorizo_mp.'|'.$fl_aprobado.'|'.$importe_total.'|';
     	     $cadena = $cadena.$importe_flete.'|'.$importe_linea_caja.'|'.$vendedor_cd.'|'.$giro_cd.'|'.$giro_desc.'|'.$dias_validez_cnt.'|'.$condicion_pago_cd .'|'.$margen_total_pct.'|'.$proyecto_cliente_cd.'|'.$proyecto_cliente_desc.'|'.$fecha_estimada_entrega.'|'.$persona_que_marco_pagado.'|'.$id_transaccion_visita."\r\n";
     	 
    	  
            fwrite($encabezado,$cadena );
            $cadena2 = date("d-m-Y H:i:s")." "."Carga Datos ENCABEZADO ". $cadena." de archivo ".$archivo.".dat"."\r\n";;
	        escribe_log ($cadena2, $log);
            $ahora = date("Y-m-d H:i:s");
            $insert = "INSERT teradata (id_cotizacion , detalle , encabezado , fecha_proceso , fecha_paso_interfase , secuencia , archivo ) VALUES (' $id_transaccion', '0'  ,'1'  ,'$dia_ayer' , '$ahora' , '$secu_file' , '$archivo.dat'  )";
            mysqli_query($conexion , $insert  ); 
           $cadena2 = date("d-m-Y H:i:s")." "."Carga Teradata Encabezado para  archivos". $insert." de archivo ".$archivo.".dat"."\r\n";
	       escribe_log ($cadena2, $log);
			
    	}
    	
    	
    }
    
  }   
        $cadena2 = date("d-m-Y H:i:s")." "."Termino de grabar el archivo de datos del ENCABEZADO:". $archivo.".dat"."\r\n";
    	escribe_log ($cadena2, $log);

       fclose($encabezado);  
  $tamano_enc= filesize($carpeta_info .$archivo.".dat");

  $ctrl_enc = $pais.'|'.$negocio.'|'.$programa.'|'.$grupo.'|'.$contenido.'|'.$dia_hoy .'|'.$secu_file.'|'.$cant_reg_enc.'|'.$tamano_enc ;
 
     $ctrl=fopen($carpeta_info .$archivo.".ctr","w+");
     fwrite($ctrl,$ctrl_enc );
     fclose($ctrl);
	 
   $cadena2 = date("d-m-Y H:i:s")." "."Termino de grabar el archivo de control del ENCABEZADO:". $archivo.".ctr" ;
   escribe_log ($cadena2, $log);
   
   

    
 // Empieza DETALLES 


    $query_b = "select 	
	os_det.id_os			        as id_transaccion,
	os_det.id_os_detalle            as nro_linea,
	os_det.cod_barra		        as codigo_barras,
	os_det.cod_sap			        as articulo_cd,
	os_det.osde_precio		        as importe_unitario,
	os_det.osde_precio		        as importe_unitario_sin_descuentos,
	os_det.osde_cantidad		    as cantidad,
	os_det.osde_precio		        as precio_uni_cobrado,
	os_det.osde_cantidad		    as cantidad_cobrada,
	replace(os_det.observacion,'|',' ')		        as observaciones,
	((os_det.osde_descuento *100) / osde_precio)	as porc_descuento,
	space(1)			            as porc_iva,
	space(1)			            as nrolista,
	space(1)			            as tipolista,
	loc.cod_local			        as local_despacho_cd,
	os_det.osde_preciocosto         as costo_unitario,
	os_det.osde_descuento           as importe_descuento,
	space(1)                        as id_autoriza,
	'usuario'                       as tipo_id_autorizador,
	os_det.usrpassaut               as id_autorizo_operador,
	os_det.usrpassaut               as nombre_autorizante_dto,
	space(1)                        as fecha_autorizacion,
	os_det.osde_descuento           as bonificacion,
	space(1)                        as unidad_de_medida,
	os_det.id_origen                as origen_descuento,
	os_det.id_tipodespacho          as tipo_despacho,
	os_det.osde_tipoprod            as tipo_articulo_pres,
	os_det.osde_subtipoprod         as subtipo_articulo_pres,
	date(os.os_fechaestimada)       as fecha_entrega
	

from os_detalle as os_det
left join os on os_det.id_os = os.id_os
left join locales as loc on os.id_local = loc.id_local 
WHERE (os.os_fechacreacion>='$dia_ayer' AND os.os_fechacreacion<'$dia_hoy') " ;  

   $cadena2 = date("d-m-Y H:i:s")." "."QUERY DEL DETALLE :".$query_b."\r\n";
    escribe_log ($cadena2, $log);
      
 if ($result1 = mysqli_query($conexion, $query_b)){ 
    
    $cantidad = mysqli_num_rows($result1);
	$cant_reg_det=0;
       if ($cantidad==0) {
          
        	$cadena = "";
  	        fwrite($detalle,$cadena );
  	        $ahora = date("Y-m-d H:i:s");
  	          $insert1_0 = "INSERT teradata ( detalle , encabezado , fecha_proceso , fecha_paso_interfase , secuencia , archivo ) VALUES ( '1'  ,'0'  ,'$dia_ayer' , '$ahora' , '$secu_file' , '$archivo_det.dat'  )";
            mysqli_query($conexion , $insert1_0  );    
        $cadena2 = date("d-m-Y H:i:s")." "."Carga Teradata DETALLE para O archivos". $insert1_0." de archivo ".$archivo_det.".dat" ;
	     escribe_log ($cadena2, $log);
	   }
 
  
  while ($row = mysqli_fetch_assoc($result1)) {
    	
        $id_cotizacion = $row['id_transaccion'];
        $cod_prod = $row['articulo_cd'];
     
    	
        $query_c = "SELECT id_cotizacion FROM teradata WHERE (id_cotizacion='$id_cotizacion' AND detalle='1' AND codprod='$cod_prod')";
    	$resultado = mysqli_query($conexion , $query_c  );
        $cant = mysqli_num_rows($resultado);
        $cadena2 = date("d-m-Y H:i:s")." "."SI EL VALOR ES 1 el proceso ya paso borrar datos de tabla TERADATA DETALLE:".$cant."\r\n";; ;
         escribe_log ($cadena2, $log);
    	if ($cant==0){
    	    $cant_reg_det = $cant_reg_det+1;
    	
    	     $id_transaccion                   = trim($row['id_transaccion']);                  // campo 1
    		 $nro_linea                        = trim($row['nro_linea']);                       // campo 2  
    		 $codigo_barras                    = trim($row['codigo_barras']);                   // campo 3   
    		 $articulo_cd                      = CompletaCerosI($row['articulo_cd'],18);   // campo 4 
    		 $importe_unitario                 = trim($row['importe_unitario']);                // campo 5
    		 $importe_unitario_sin_descuentos  = trim($row['importe_unitario_sin_descuentos']); // campo 6
    		 $cantidad                         = trim($row['cantidad']);                        // campo 7
    		 $precio_uni_cobrado               = trim($row['precio_uni_cobrado']);              // campo 8
    		 $cantidad_cobrada                 = trim($row['cantidad_cobrada']);               // campo 9
    	     $observaciones                    = trim($row['observaciones']);                   // campo 10
     	     $porc_descuento                   = trim($row['porc_descuento']);                  // campo 11
     	     $porc_iva                         = trim($row['porc_iva']);                        // campo 12
     	     $nrolista                         = trim($row['nrolista']);                        // campo 13
     	     $tipolista                        = trim($row['tipolista']);                       // campo 14
     	     $local_despacho_cd                = trim($row['local_despacho_cd']);              // campo 15
     	     $costo_unitario                   = trim($row['costo_unitario']);                  // campo 16
     	     $importe_descuento                = trim($row['importe_descuento']);               // campo 17
     	     $id_autoriza                      = trim($row['id_autoriza']);                     // campo 18
     	     $tipo_id_autorizador              = trim($row['tipo_id_autorizador']);             // campo 19
             $id_autorizo_operador             = trim($row['id_autorizo_operador']);            // campo 20
             $nombre_autorizante_dto           = trim($row['nombre_autorizante_dto']);          // campo 21
             $fecha_autorizacion               = trim($row['fecha_autorizacion']);              // campo 22
             $bonificacion                     = trim($row['bonificacion']);                    // campo 23
             $unidad_de_medida                 = trim($row['unidad_de_medida']);                // campo 24
     	     $origen_descuento                 = trim($row['origen_descuento']);                // campo 25
             $tipo_despacho                    = trim($row['tipo_despacho']);                   // campo 26
             $tipo_articulo_pres               = trim($row['tipo_articulo_pres']);              // campo 27
             $subtipo_articulo_pres            = trim($row['subtipo_articulo_pres']);           // campo 28
             $fecha_entrega                    = $row['fecha_entrega'];                   // campo 29     
     	     $tipo_retiro                      = '';                     // campo 30
             $orde_compra_cd                   = '';                 // campo 31
     	     
     	     
    	     $cadena1 = $id_transaccion.'|'.$nro_linea.'|'.$codigo_barras.'|'.$articulo_cd.'|'.$importe_unitario.'|';
			 $cadena1 = $cadena1.$importe_unitario_sin_descuentos.'|'.$cantidad.'|'.$precio_uni_cobrado.'|'.$cantidad_cobrada.'|';
			 $cadena1 = $cadena1.$observaciones.'|'.$porc_descuento.'|'.$porc_iva.'|'.$nrolista.'|'.$tipolista.'|';
			 $cadena1 = $cadena1.$local_despacho_cd.'|'.$costo_unitario.'|'.$importe_descuento.'|'.$id_autoriza.'|';   
			 $cadena1 = $cadena1.$tipo_id_autorizador.'|'.$id_autorizo_operador.'|'.$nombre_autorizante_dto.'|';
			 $cadena1 = $cadena1.$fecha_autorizacion.'|'.$bonificacion.'|'.$unidad_de_medida.'|';
			 $cadena1 = $cadena1.$origen_descuento.'|'.$tipo_despacho.'|'.$tipo_articulo_pres.'|'.$subtipo_articulo_pres.'|';
    	     $cadena1 = $cadena1.$fecha_entrega.'|'.$tipo_retiro.'|'.$orde_compra_cd."\r\n";
    	     $cadena2 = date("d-m-Y H:i:s")." "."Exporta DETALLE ". $cadena1." de archivo ".$archivo_det.".dat" ;
	         escribe_log ($cadena2, $log);	
             fwrite($detalle,$cadena1 );
     
            $ahora = date("Y-m-d H:i:s");
            $insert = "INSERT teradata (id_cotizacion , detalle , encabezado , fecha_proceso , fecha_paso_interfase , secuencia , archivo ,codprod ) VALUES ('$id_cotizacion', '1'  ,'0'  ,'$dia_ayer' , '$ahora' , '$secu_file' , '$archivo_det.dat', '$articulo_cd'  )";
            mysqli_query($conexion , $insert  );   
            $cadena2 = date("d-m-Y H:i:s")." "."Carga Teradata DETALLE para archivos". $insert." de archivo ".$archivo_det.".dat" ;
	        escribe_log ($cadena2, $log);			
    	}
    	
    	
    }
    }
    $cadena2 = date("d-m-Y H:i:s")." "."Termino de grabar el archivo de datos del DETALLE:". $archivo_det.".dat" ;
    	escribe_log ($cadena2, $log);

  fclose($detalle);
  $tamano_det = filesize($carpeta_info .$archivo_det.".dat");
   $ctrl_d=fopen($carpeta_info .$archivo_det.".ctr","w+");
   
 $ctrl_det = $pais.'|'.$negocio.'|'.$programa.'|'.$grupo.'|'.$contenido_det.'|'.$dia_hoy .'|'.$secu_file.'|'.$cant_reg_det.'|'.$tamano_det ;
        fwrite($ctrl_d,$ctrl_det );
  fclose($ctrl_d);
   $cadena2 = date("d-m-Y H:i:s")." "."Termino de grabar el archivo de control del DETALLE:". $archivo_det.".ctr" ;
    	escribe_log ($cadena2, $log);
   

 /// Borra LOG 
   $dia_limpieza = substr($dia_ayer,8,2);
   $mes_limpieza = substr($dia_ayer,5,2);
   $ano_limpieza =  substr($dia_ayer,0,4);
 
   $fecha_limp = mktime(0, 0, 0, $mes_limpieza, $dia_limpieza, $ano_limpieza);
   $fecha_limp = $fecha_limp-(86400*$dias_limpieza);
   $fecha_limp = date("Y-m-d",$fecha_limp);
   
   $borra_log="DELETE FROM teradata WHERE fecha_proceso<='$fecha_limp'";
   $borrado_log =  mysqli_query($conexion, $borra_log);
       $cadena2 = date("d-m-Y H:i:s")." "."Borra tabla Teradata anterior a ".$fecha_limp ;
    	escribe_log ($cadena2, $log);
    
} else {
	
	
	
}
$gzipcab=comprimir ($carpeta_info .$archivo.".dat");
if ($gzipcab)

$gzipdet=comprimir ($carpeta_info .$archivo_det.".dat");
if ($gzipdet)
// Fin zipeo

unlink($carpeta_info.$archivo_det.".dat");
unlink($carpeta_info.$archivo.".dat");

//echo "RESULT VALE ".$result ;
 //fclose($encabezado); // Cierro encabezado 
//echo "RESULT VALE ".$result ;
$cadena2 = date("d-m-Y H:i:s")." "."Fin de la rutina" ;
    	escribe_log ($cadena2, $log);


?>
