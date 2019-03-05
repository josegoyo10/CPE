<?

function writeeventws( $texto_in ) {
    global $ses_usr_id;
    if( defined( 'DIR_LOG_WS' ) )
        error_log( date("dmY His", time() ) . " $ses_usr_id => " . $texto_in  . "\n" , 3, DIR_LOG_WS.date("Ymd", time() ).".txt");
}

function verifica_local($cod_local){
    $query_l = "select id_local, cod_local, nom_local,ip_local, cod_local_pos from locales where cod_local = '$cod_local'";
	$rq_1	 = tep_db_query($query_l);
    $res_1   = tep_db_fetch_array( $rq_1 );
	if($res_1['id_local']){
		return $res_1['id_local'];
	}else{
		writeeventws('verifica_local -> No existe local para  '.$cod_local); 	
		return 0;
	}
}
// Rescata el dígito verificador
function dvws($r){
    $s=1;
    for($m=0;$r!=0;$r/=10)
        $s=($s+$r%10*(9-$m++%6))%11;
    return chr($s?$s+47:75);
}
function valida_rut($r){
	$r=strtoupper(ereg_replace('\.|,|-','',$r));
	$sub_rut=substr($r,0,strlen($r)-1);
	$sub_dv=substr($r,-1);
	$x=2;
	$s=0;
	for ( $i=strlen($sub_rut)-1;$i>=0;$i-- ){
		if ( $x >7 ){
			$x=2;
		}
		$s += $sub_rut[$i]*$x;
		$x++;
	}
	$dv=11-($s%11);
	if ( $dv==10 ){
		$dv='K';
	}
	if ( $dv==11 ){
		$dv='0';
	}
	if ( $dv==$sub_dv ){
		return true;
	}else{
		return false;
	}
}

function verifica_rut($rut){
/*si es positivo*/
	if(is_numeric($rut)&&($rut>0)){
		$digv	  = dvws($rut);
		if (strlen($rut)<=8){
			$nuevorut = $rut.$digv;
			$valorrut =valida_rut($nuevorut);
			writeeventws('Verifica_rut -> Largo de rut correcto '.strlen($rut)); 
			return $rut;
		}else{
			writeeventws('Verifica_rut -> Largo de rut incorrecto '.strlen($rut)); 
			return 0;
		}
	}else{
		writeeventws('Verifica_rut -> el rut es negativo '.$rut); 
		return 0;
	}
}

function busca_direccion($clie_rut){
/*busca una direcion segun un cliente dado*/
	$query_l = "SELECT id_direccion, clie_rut, dire_nombre,dire_defecto FROM direcciones where clie_rut=$clie_rut and dire_defecto='p'";
	$rq_1	 = tep_db_query($query_l);
    $res_1   = tep_db_fetch_array( $rq_1 );
	if($res_1['id_direccion']){
		return $res_1['id_direccion'];
	}else{
		writeeventws('busca_direccion -> No existe direccion para el rut  '.$clie_rut); 	
		return 0;
	}
}

function inserta_nueva_direccion($clie_rut){
/*Inserta nueva dirección en caso que el cliente no tenga una*/
	$queryin="INSERT INTO direcciones 
			 (id_comuna, clie_rut, dire_nombre, dire_direccion, dire_telefono, dire_observacion, dire_activo, dire_defecto)
			 VALUES
			 (291,$clie_rut,'Principal','calle,avenida','','Direccion por defecto debe cambiarse',1,'p')";
	if (tep_db_query($queryin)){
		$last_id = tep_db_insert_id( '' );
		writeeventws('inserta_nueva_direccion -> Inserta direccion para el rut  '.$clie_rut); 
		return $last_id;
	}else{
		writeevent("Error (inserta_nueva_direccion) No se pudo insertar la direecion principal para rut ".$rutp );	
		return 0;
	}
}

function verifica_cliente($rut){
/*verifica si existe el cliente en CPE*/
    $query_l = "select clie_rut,clie_tipo, clie_nombre from clientes where clie_rut = $rut";
	$rq_1	 = tep_db_query($query_l);
    $res_1   = tep_db_fetch_array( $rq_1 );
	if($res_1['clie_rut']){
		return $res_1['clie_rut'];
	}else{
	/*si no existe lo agrega*/
		writeeventws('verifica_cliente -> No existe cliente rut '.$rut.' Se debe ingresar al sistema'); 
		$queryINCL="INSERT INTO clientes (clie_rut, clie_tipo, clie_nombre, clie_activo) VALUES ($rut,'p','Cliente Ingresado por Os en POS',1)";
		if(!tep_db_query($queryINCL)){
			writeeventws('Error (verifica_cliente) no se pudo insertar rut '.$rut.' en tabla clientes CPE'.$queryINCL); 
			return 0;
		}else{
			writeeventws('verifica_cliente ->Se agrego satisfactoriamente el rut '.$rut.' en tabla clientes CPE'); 
			return $rut;
		}
	}
}

function verifica_precio($precio){
/**si es +**/
	if (is_numeric($precio)&&($precio>=0)){
	/*  si es de largo tope 8*/
		if ((strlen($precio))<=8){
			return $precio;
		}else{
			writeeventws('verifica_precio -> el precio es largo mayor '.strlen($precio)); 
			return 0;
		}
	}else{
		writeeventws('verifica_precio -> el precio es negativo '.$precio); 
		return 0;
	}
}

function verifica_ean($ean){
/*verifica si el largo corrsponde o no a un EAN*/
	if ( is_numeric($ean) && ($ean>=0) ){
		if ((strlen($ean))<=13){
			return $ean;
		}else{
			writeeventws('verifica_ean -> el EAN es largo mayor '.strlen($ean)); 
			return 0;
		}
	}else{
		writeeventws('verifica_ean -> el EAN es negativo '.$ean); 
		return 0;
	}
}

function verifica_eancp($ean){
/*Verifica si existe el ean en CPE*/
	$query_cb = "SELECT p.cod_prod1
				FROM codbarra cb
				join productos p on (cb.cod_prod1=p.cod_prod1)
				where cod_barra =".$ean;

	$rq_cb	 = tep_db_query($query_cb);
    $res_cb   = tep_db_fetch_array( $rq_cb );
    return $res_cb;
}

function recuperadatos_eancp($ean){
/*debe estar en codbarra y en productos y trae el detalle*/
    $query_cb = "SELECT cb.cod_barra, cb.cod_prod1, cb.cod_prod1, cb.unid_med,
				p.id_producto, p.cod_prod1, p.des_corta, p.des_larga, p.prod_tipo, p.prod_subtipo
				FROM codbarra cb
				join productos p on (cb.cod_prod1=p.cod_prod1)
				where cod_barra ="."'$ean'";
    $rq_cb	 = tep_db_query($query_cb);
    $res_cb   = tep_db_fetch_array( $rq_cb );
    return $res_cb;
}


function verifica_cantidad($cantidad){
/**si es + y de largo correspondiente**/
	if (is_numeric($cantidad)&&($cantidad>=0)){
	/*parte entera y decimal por separado*/
	$num_cant = split("\.",$cantidad);
	/*  si es de largo tope 8 enteros*/
		if ((strlen($num_cant[0])<=8)&&(strlen($num_cant[1])<=2)){
			return $cantidad;
		}else{
			writeeventws('verifica_cantidad -> la cantidad es largo mayor '.strlen($cantidad)); 
			return 0;
		}
	}else{
		writeeventws('verifica_cantidad -> la cantidad es negativa '.$cantidad); 
		return 0;
	}
}
/*para webservice de busqueda por sap y retorna ean*/

function verifica_numerosap($sap){
/**si es +**/
	if (is_numeric($sap)&&($sap>=0)){
	/*  si es de largo tope 18*/
		if (strlen($sap)<=18){
			return $sap;
		}else{
			writeeventws('verifica_numerosap -> El numero sap supera el largo esperado '.strlen($sap)); 
			return 0;
		}
	}else{
		writeeventws('verifica_numerosap ->  El numero sap es negativo o no es un numero '.$sap); 
		return 0;
	}

}

function verifica_sapcp($sap){
$codigoean = array() ;
/*debe estar en productos y en codbarra*/
    $query_sap = "SELECT cb.cod_barra 
				 FROM productos p
				 JOIN codbarra cb on (cb.cod_prod1=p.cod_prod1)
				 where p.cod_prod1 =".$sap;
	#writeeventws('QUERY '. $query_sap);
	$i=0;
    if ( $rq = tep_db_query($query_sap) ){
		while( $res = tep_db_fetch_array( $rq ) ) {
			$codigoean[$i]=$res['cod_barra'];
			$i++;
		}
	}
	if($codigoean){
		return $codigoean;
	}else{
		writeevent("Error (verifica_sap) No se encontro el producto en CPE  ".$sap );	
		return 0;
	}
}

function verifica_codprec( $condicion, $local){
if (defined('LIMWS3')){$lim=LIMWS3;}else{$lim=100;}
/*debe estar en productos y en codbarra y precio*/
    $query_sap = "SELECT cb.cod_barra, pr.prec_valor, p.des_larga
				 FROM productos p
				 JOIN codbarra cb on (cb.cod_prod1=p.cod_prod1)
				 JOIN precios pr on (pr.cod_prod1=p.cod_prod1)
				 where pr.cod_local='$local' $condicion
				order by p.des_corta 
				limit 0,10";
	$rq_env = tep_db_query($query_sap);
	$rq_cb  = tep_db_query($query_sap);
    $res    = tep_db_fetch_array( $rq_cb );
	if ($res['cod_barra']){
			return $rq_env;
	}else{
			writeeventws("Error (verifica_codprec) No hay respuesta para este texto ".$texto);	
			return 0;
	}
}

function verifica_texto($texto){
	if ((strlen($texto)<=50)&&(strlen($texto)>0)){
		return $texto;
	}else{
		writeeventws('verifica_texto -> el EAN es largo que no corresponde '.strlen($texto)); 
		return 0;
	}
}





?>
