<?php
function writelog( $texto_in ) {
    global $ses_usr_id;
    if( defined( 'DIR_LOG' ) )
        error_log( date("dmY His", time() ) . " $ses_usr_id => " . $texto_in  . "\n" , 3, DIR_LOG.date("Ymd", time() ).".txt");
}

function writeevent( $texto_in ) {
    global $ses_usr_id;
    if( defined( 'DIR_LOG_EVENT' ) )
        error_log( date("dmY His", time() ) . " $ses_usr_id => " . $texto_in  . "\n" , 3, DIR_LOG_EVENT.date("Ymd", time() ).".txt");
}

function tep_exit() {
    return exit();
}

function include_idioma_mod( $idioma, $area ) {
    if( $area != '' )
        include_once("./idiomas/".$idioma."/".$area.".php" );
}

function random_password($length){
    $newpass = '';
    /** Init random num generator*/
    mt_srand ((double) microtime() * 1000000);
    while($length > 0){
        /*Create a new password using numbers and upper case letters ASCII 48-57
        * and 65 to 90 I have no idea how this will affect non ASCII users */
        $newchar = mt_rand(48, 90);
        if($newchar > 57 && $newchar < 65){ // Only use numbers and upper case letters
            continue;
        }
        $newpass .= sprintf("%c",$newchar);
        $length--;
    }
    return($newpass);
}

function get_nombre_usr( $id_usuario ) {

    $query_1 = "select usr_nombres, usr_apellidos from usuarios where usr_id = $id_usuario";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );

    return ucwords(strtolower($res_1['usr_nombres'] . " " . $res_1['usr_apellidos']));
}

function get_login_usr( $id_usuario ) {

    $query_1 = "select usr_login from usuarios where usr_id = $id_usuario";
    file_put_contents('showusers.txt', print_r($query_1));
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    return $res_1['usr_login'];

}

function get_login_origen( $id_usuario ) {
    $query_1 = "select usr_origen from usuarios where usr_id = $id_usuario";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
	if ($res_1['usr_origen']){
		return $res_1['usr_origen'];}
	else{
		return 0;
	}
}

function get_login_clave( $id_usuario ) {

    $query_1 = "select USR_CLAVE from usuarios where usr_id = $id_usuario";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    return $res_1['USR_CLAVE'];

}

function get_local_usr( $id_usuario ) {
    $query_1 = "select id_local from local_usr where USR_ID = $id_usuario";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    return $res_1['id_local'];

}

function get_locald_usr( $id_usuario ) {

    $query_1 = "select nom_local from local_usr lu join locales ls on ls.id_local=lu.id_local where USR_ID = $id_usuario";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    return $res_1['nom_local'];

}



function permisos_modulo( $id_mod, $pag_ini = '' ) {
    global $ses_usr_id;

    //writelog( getenv("PATH_INFO") . " $id_mod, $pag_ini" );

    if( $pag_ini != '' ) {
        $query_1 = "select mod_id from modulos where mod_url = '$pag_ini'";
        $rq_1 = tep_db_query($query_1);
        $res_1 = tep_db_fetch_array( $rq_1 );
        $id_mod = $res_1['mod_id'];
    }

    $lista_per = get_permisos_gen();
    $lista_per = substr( $lista_per, 0, strlen($lista_per)-1 );
    if( $lista_per == '' )
        $lista_per = -1;

    $insert_aux = 0;
    $delete_aux = 0;
    $update_aux = 0;
    $select_aux = 0;

    //recuperamos los permisos del módulo en la excepción, sólo si existe excepción
    $query_1 = "select pemo_insert, pemo_delete, pemo_update, pemo_select from permisosxmodulo where pemo_mod_id = $id_mod and pemo_per_id = $ses_usr_id and pemo_tipo = 2";
    $rq_1 = tep_db_query($query_1);
    if( tep_db_num_rows( $rq_1 ) > 0 ) {
        $res_1 = tep_db_fetch_array( $rq_1 );
        $insert_aux = $res_1['pemo_insert'];
        $delete_aux = $res_1['pemo_delete'];
        $update_aux = $res_1['pemo_update'];
        $select_aux = $res_1['pemo_select'];
    }
    else {
        //recuperamos los permisos del módulo con el perfil del usuario y sus perfiles de más bajo nivel
        $query_1 = "select pemo_insert, pemo_delete, pemo_update, pemo_select from permisosxmodulo where pemo_mod_id = $id_mod and pemo_per_id in ( $lista_per ) and pemo_tipo = 1";
        $rq_1 = tep_db_query($query_1);
        while( $res_1 = tep_db_fetch_array( $rq_1 ) ) {
            if( $res_1['pemo_insert'] == 1 )
                $insert_aux = 1;
            if( $res_1['pemo_delete'] == 1 )
                $delete_aux = 1;
            if( $res_1['pemo_update'] == 1 )
                $update_aux = 1;
            if( $res_1['pemo_select'] == 1 )
                $select_aux = 1;
        }
    }

    //idus
    $permisos = "";
    $permisos .= $insert_aux;
    $permisos .= $delete_aux;
    $permisos .= $update_aux;
    $permisos .= $select_aux;

    return $permisos;

}

function se_puede( $funcion, $permisos ) {
    if( $funcion == 'i' ) {
        $fun = substr( $permisos, 0, 1 );
        if( $fun == 1 ) {
            return 1;
        }
    }
    else if( $funcion == 'd' ) {
        $fun = substr( $permisos, 1, 1 );
        if( $fun == 1 ) {
            return 1;
        }
    }
    else if( $funcion == 'u' ) {
        $fun = substr( $permisos, 2, 1 );
        if( $fun == 1 ) {
            return 1;
        }
    }
    else if( $funcion == 's' ) {
        $fun = substr( $permisos, 3, 1 );
        if( $fun == 1 ) {
            return 1;
        }
    }
    return 0;
}

$arr_perfiles_1 = array();
function make_permisos() {
    global $arr_perfiles_1;

    $query_1 = "select per_nombre, per_id, per_descripcion, per_padre from perfiles";

    file_put_contents("make_permisos.txt", $query_1);
    $db_1 = tep_db_query($query_1);
    $i = 0;
    while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
        $arr_perfiles_1[$i][0] = $res_1['per_id'];
        $arr_perfiles_1[$i][1] = $res_1['per_nombre'];
        $arr_perfiles_1[$i][2] = $res_1['per_descripcion'];
        $arr_perfiles_1[$i][3] = $res_1['per_padre'];
        $i++;
    }
}

// recupera los permisos para el usuario
function get_permisos_gen() {
    global $ses_usr_id, $valores_get_permisos;

    make_permisos();

    $query_1 = "select peus_per_id from perfilesxusuario where peus_usr_id = $ses_usr_id";
    $db_1 = tep_db_query($query_1);
    $lista_mod = "";
    while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
        $lista_mod .= $res_1['peus_per_id'] . ",";
        $valores_get_permisos = "";
        $aux = get_permisos( $res_1['peus_per_id'] );
        if( $aux != '' )
            $lista_mod .= $aux . ",";
    }
     
     file_put_contents('listamod.txt', $lista_mod);

    return $lista_mod;
}

// recupera los permisos hacia el interior
function get_permisos( $per_id ) {
    global $arr_perfiles_1, $valores_get_permisos;

    for( $i = 0; $i < sizeof( $arr_perfiles_1 ); $i++ ) {
        if( $arr_perfiles_1[$i][3] == $per_id ) {
            $valores_get_permisos .= $arr_perfiles_1[$i][0] . ",";
            get_permisos( $arr_perfiles_1[$i][0] );
        }
    }

    return substr( $valores_get_permisos, 0, strlen($valores_get_permisos)-1 );

}

// revisa si un usuario tiene el perfil asignado
function usuario_perfil( $perfil ) {
    global $ses_usr_id;
    
    $query_1 = "select peus_per_id from perfilesxusuario where peus_usr_id = $ses_usr_id and peus_per_id in ($perfil)";
    $db_1 = tep_db_query($query_1);
    return (tep_db_num_rows( $db_1 ));
}

// revisa si un usuario tiene otros perfiles asociados
function usuario_perfil_2( $perfil ) {
    global $ses_usr_id;
    
    $query_1 = "select peus_per_id from perfilesxusuario where peus_usr_id = $ses_usr_id and peus_per_id not in ($perfil)";
    $db_1 = tep_db_query($query_1);
    return (tep_db_num_rows( $db_1 ));
}


function get_modulos() {
    global $ses_usr_id;

    $lista_mod = get_permisos_gen();

    $query_1 = "select distinct pemo_per_id from permisosxmodulo where pemo_per_id = $ses_usr_id and pemo_tipo = 2";

    
    $db_1 = tep_db_query($query_1);
    if( tep_db_num_rows( $db_1 ) > 0 ) {
        while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
            $lista_mod .= $res_1['pemo_per_id'] . ",";
        }
    }

    $lista_mod = substr( $lista_mod, 0, strlen($lista_mod)-1 );

    if( $lista_mod == '' )
        $lista_mod = -1;

    $valores_ret = "";
    $query_1 = "select distinct pemo_mod_id from permisosxmodulo where pemo_per_id in ( $lista_mod )";
    $db_1 = tep_db_query($query_1);
    while( $res_1 = tep_db_fetch_array( $db_1 ) ) {
        $valores_ret .= $res_1['pemo_mod_id'] . ",";
    }

    return substr( $valores_ret, 0, strlen($valores_ret)-1 );

}

function desconectar_usuario( $ses_ult_carga ) {

    $dif = time() - $ses_ult_carga;
    if( $dif > MINUTOS_AUTOLOGOUT*10 ) {
        ?>
        <script>
            top.location.href = "../start/logout_01.php?action=logout";
        </script>
        <?
        //session_destroy();
        tep_exit();
    }
}

function kid_href( $pagina, $parametros, $texto, $titulo, $class, $target='' ) {
    $href = "";
    $href .= "'$pagina?tm=".time()."&$parametros'";
    if( $titulo )
        $href .= " title='$titulo'";
    if( $class )
        $href .= " class='$class'";
    if( $target )
        $href .= " target='$target'";

    return( "<A HREF=$href>$texto</a>" );

}

function tep_get_all_post_params_location() {
    global $HTTP_POST_VARS;
    $text = "";

    reset($HTTP_POST_VARS);
    while (list($key, $value) = each($HTTP_POST_VARS)) {
      if ( $key != session_name() && $key != 'action' && $key != 'x' && $key != 'y' )
        $text .= $key . '=' . $value . '&';
    }
    return substr( $text, 0, strlen($text)-1 );
}

function tep_get_all_post_params() {
    global $HTTP_POST_VARS;

    reset($HTTP_POST_VARS);
    while (list($key, $value) = each($HTTP_POST_VARS)) {
      if (($key != session_name()) && ($key != 'error') )
        ?>
        <input type="hidden" name="<?=$key?>" value="<?=$value?>">
        <?
    }

}

function tep_get_all_get_params() {
    global $HTTP_GET_VARS;

    $get_url = '';

    reset($HTTP_GET_VARS);
    while (list($key, $value) = each($HTTP_GET_VARS)) {
      if ( ($key != 'action') && ($key != 'tm') && ($key != session_name()) && ($key != 'error') )
        $get_url .= $key . '=' . $value . '&';
    }

    return $get_url;
}

function subir_archivo( $userfile, $userfile_name, $file_root ) {
    if( $userfile ) {
        writelog( "$userfile, $userfile_name, $file_root, " .  filesize ( $userfile ) );
        if( is_uploaded_file( $userfile ) ) {
            if( !is_dir( $file_root ) ) {
                mkdir($file_root, '0755');
            }
            $path = $file_root.$userfile_name;
            if( !move_uploaded_file( $userfile, $path ) ) {
                writelog( "Error el archivo $userfile_name no pudo ser movido a $path" );
            }
            else
                chmod( $path, 0644 );
        }
        else {
            writelog( "Error el archivo $userfile_name no pudo ser subido al servidor" );
        }
    }
}

function ver_contenido_editable( $con_id ) {
    $query_1 = "select con_texto from con_editable where con_id = $con_id";
    $db_1 = tep_db_query($query_1);
    if( tep_db_num_rows($db_1) > 0 ) {
        $res_1 = tep_db_fetch_array( $db_1 );
        $msg = $res_1['con_texto'];
        if( se_puede( 'u', permisos_modulo( '', '../edi_text/edi_01.php' ) ) ) {
            $msg .= '['.kid_href( '../edi_text/edi_01.php', "action=updcon&con_id=$con_id", 'Editar', 'Editar programa del curso', '' ).']';
        }
    }
    else {
        $msg = "---";
    }
    return $msg;
}

function format_valor( $valor, $pre_valor = '$', $post_valor = '.-', $punto_decimal = ',', $punto_millar = '.', $decimales = 0 ) {
    return $pre_valor.number_format($valor, $decimales, $punto_decimal, $punto_millar).$post_valor;
}

define('CRLF', "\r\n", TRUE);

function mail_kid( $mail_to, $mail_to_c, $mail_subject, $mail_body, $mail_from, $mail_from_c, $header_adic, $mail_attach = '' ) {

    $mail_header = array(
        "From: $mail_from_c", 
        "Reply-To: $mail_from", 
        "To: $mail_to_c", 
        "X-Mailer:PHP/".phpversion(), 
        "Mime-Version: 1.0", 
        "Delivery-date: ".date( "D, d M Y H:i:s -0300" ), //Delivery-date: Fri, 14 Jan 2005 10:40:09 -0300
        "Date:".date( "D, d M Y H:i:s -0300" ),
        "Subject: $mail_subject"
        );

    // Existe un archivo a adjuntar
    if( $mail_attach != '' && file_exists($mail_attach) ) {
        $mime_boundary="--==Multipart_Boundary_x".md5(mt_rand())."x";
        $partes_archivo = split( "/", $mail_attach );
        $archivo['name'] = $partes_archivo[sizeof($partes_archivo)-1];
        $archivo['size'] = filesize($mail_attach);
        $archivo['type'] = filetype($mail_attach);
        // open the file for a binary read
        $file = fopen($mail_attach,'rb');
        // read the file content into a variable
        $data = fread($file,$archivo['size']);
        // close the file
        fclose($file);
        // now we encode it and split it into acceptable length lines
        $data = chunk_split(base64_encode($data));
        $mail_header_attach = array(
            "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\""
            );
        $tag = "\n";
        $mail_msg = "This is a multi-part message in MIME format.$tag$tag" .
                    "--$mime_boundary$tag" . 
                    "Content-Type: text/html; charset=iso-8859-1$tag" .
                    "Content-Transfer-Encoding: 7bit$tag" .
                    "$tag$tag" .
                    "$mail_body:$tag$tag".
                    "--$mime_boundary$tag".
                     "Content-Type: ".$archivo['type']."name=".$archivo['name']."$tag".
                     "Content-Transfer-Encoding: base64$tag".
                     "Content-Disposition: attachment; filename=\"".$archivo['name']."\"$tag$tag".
                     $data . "$tag$tag".
                     "--$mime_boundary--$tag";

    }
    else {
        $mail_msg = $mail_body;
        $mail_header_attach = array(
            "Content-Type: text/html; charset=iso-8859-1"
            );
    }

    $mail_header = array_merge ( $mail_header, $mail_header_attach );
    $mail_header = array_merge ( $mail_header, $header_adic );

    $headers = str_replace(CRLF.'.', CRLF.'..', trim(implode(CRLF, $mail_header )));

    if( KID_ENVIO_MAIL )
        mail( $mail_to , $mail_subject, $mail_msg, $headers);
    else
        writelog( "mail( $mail_to, $mail_subject, $mail_msg, $headers)" );    

}

function fecha_php2db ($fechaphp) {
	$arrfecha = split("/", $fechaphp);
	return $arrfecha[2] . "/" . $arrfecha[0] . "/" . $arrfecha[1] ;
}

function fecha_php2db_new ($fechaphp) {
	$arrfecha = split("/", $fechaphp);
	return $arrfecha[2] . "/" . $arrfecha[1] . "/" . $arrfecha[0] ;
}

function fecha_db2php ($fechadb) {
	$arr1 = split(" ", $fechadb);
	$arrfecha = split("-", $arr1[0]);
	if ($arrfecha[0] == 0)
		return "";
	else
		return $arrfecha[2] . "/" . $arrfecha[1] . "/" . $arrfecha[0] ;
}

function hora_db2php ($fechadb) {
	$arr1 = split(" ", $fechadb);
	$arrhora = split(":", $arr1[1]);
	return $arrhora[0];
}

function min_db2php ($fechadb) {
	$arr1 = split(" ", $fechadb);
	$arrmin = split(":", $arr1[1]);
	return $arrmin[1];
}
// Rescata el dígito verificador
function dv($r){
    $s=1;
    for($m=0;$r!=0;$r/=10)
        $s=($s+$r%10*(9-$m++%6))%11;
    return chr($s?$s+47:75);
}

function dvEAN13($r){
	$Factor = 3;
	$weightedTotal = 0;

	for($I = strlen($r)-1; $I>=0; --$I){
		$CurrentCharNum = substr($r, $I, 1);
		$weightedTotal = $weightedTotal + ($CurrentCharNum * $Factor);
		$Factor = 4 - $Factor;
	}

	$I = ($weightedTotal % 10);
	If ($I != 0)
		$CheckDigit = 10 - $I;
	else
		$CheckDigit = 0;

	return $CheckDigit;
}

function gencode_EAN13($number, $ancho, $alto ) {
	//Genera imagen para código EAN13
	$srcimg = "img/barcode/";
	$altoimg = $alto*85/100;
	$altoimg2 = $alto*15/100;
	$anchoimg = $ancho/95*7;
	$anchoimg2 = $ancho/95*3;
	$anchoimg3 = $ancho/95*5;

	$arr_orientacion = Array (	'000000', 
								'001011', 
								'001101', 
								'001110', 
								'010011', 
								'011001', 
								'011100', 
								'010101', 
								'010110', 
								'011010');
	if (strlen($number)>13) {
		return "<br><b>Problemas en el código<br>Códgo NO es EAN13</b>";
		exit();
	}

	$dig_inic = substr(sprintf("%013s", $number), 0, 1);
	$tramoizq = substr(sprintf("%013s", $number), 1, 6);
	$tramoder = substr(sprintf("%013s", $number), 7, 6);

	$ret .= "<table border=0 cellspacing=0 cellpadding=0>";
	$ret .= "<tr>";

	$ret .= "<td>";
	$ret .= "&nbsp;";
	$ret .= "</td>";

	$ret .= "<td>";
	$ret .= "<img src=\"".$srcimg."c1-c3.png\" height=\"$altoimg\" width=\"$anchoimg2\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo izquierda
		$ret .= "<td>";
		$ret .= "<img src=\"".$srcimg.((!substr($arr_orientacion[$dig_inic], $i, 1))?substr($tramoizq, $i, 1)."-der-neg.png":substr($tramoizq, $i, 1)."-izq.png") . "\" height=\"$altoimg\" width=\"$anchoimg\">";
		$ret .= "</td>";
	}

	$ret .= "<td>";
	$ret .= "<img src=\"".$srcimg."c2.png\" height=\"$altoimg\" width=\"$anchoimg3\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo derecha
		$ret .= "<td>";
		$ret .= "<img src=\"".$srcimg.substr($tramoder, $i, 1)."-der.png\" height=\"$altoimg\" width=\"$anchoimg\">";
		$ret .= "</td>";
	}

	$ret .= "<td>";
	$ret .= "<img src=\"".$srcimg."c1-c3.png\" height=\"$altoimg\" width=\"$anchoimg2\">";
	$ret .= "</td>";

	$ret .= "</tr>";
	$ret .= "<tr>";

	$ret .= "<td>";
	$ret .= $dig_inic . "&nbsp;";;
	$ret .= "</td>";

	$ret .= "<td valign=top>";
	$ret .= "<img src=\"".$srcimg."c1-c3.png\" height=\"$altoimg2\" width=\"$anchoimg2\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo izquierda
		$ret .= "<td align=center>";
		$ret .= substr($tramoizq, $i, 1);
		$ret .= "</td>";
	}

	$ret .= "<td valign=top>";
	$ret .= "<img src=\"".$srcimg."c2.png\" height=\"$altoimg2\" width=\"$anchoimg3\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo derecha
		$ret .= "<td align=center>";
		$ret .= substr($tramoder, $i, 1);
		$ret .= "</td>";
	}

	$ret .= "<td valign=top>";
	$ret .= "<img src=\"".$srcimg."c1-c3.png\" height=\"$altoimg2\" width=\"$anchoimg2\">";
	$ret .= "</td>";

	$ret .= "</tr>";
	$ret .= "</table>";
	return $ret;
}
/* toma un precio y lo deja fromaiado con puntitos*/
function formato_precio($val) {
   $s = "";
   $valor = abs($val);
   $largo = strlen($valor);
   $mod = ($largo % 3 );
   for ($i=0; $i<$largo; $i++) {
      if (((($i - $mod) % 3) == 0) && ($i != 0)) {
         $s = $s . ".";
      }
      $s = $s . substr($valor, $i, 1);
   }
   return $s;  
}

function marca_OS_Finalizada ($id_ot) {
	//Obtener id_os padre
    $query_1 = "select id_os from ot where ot_id = " . ($id_ot + 0);
    $db_1 = tep_db_query($query_1);
    if( tep_db_num_rows($db_1) > 0 && $res_1 = tep_db_fetch_array( $db_1 )) 
		$id_os = $res_1['id_os'];
	else
		return 0;

	//Recuperamos los id de las OT y los estados terminales
    $query_1 = "SELECT o.ot_id, e.estadoterminal FROM ot o LEFT JOIN estados e ON  o.id_estado = e.id_estado WHERE id_os = " . ($id_os + 0);
    $db_1 = tep_db_query($query_1);
	$estado_terminal_os = 0; 
    if( tep_db_num_rows($db_1) > 0 && $res_1 = tep_db_fetch_array( $db_1 ))
		do {
			$estado_terminal_os = 1 && $res_1['estadoterminal'];
			$res_1 = tep_db_fetch_array( $db_1 );
		} while($res_1 && $estado_terminal_os);

	//Si $estado_terminal_os entonces marcamos la OS
	if ($estado_terminal_os) {
		$queryUP ="UPDATE os SET id_estado='SM' where id_os = " . ($id_os + 0) ;
		tep_db_query($queryUP);
	}

	return $estado_terminal_os;
}
/******para la administracion de pedidos especiales*/

function adm_pedesp($tipo,$subtipo,$codigos,$tipocatprod){
    $codNew = array();
    $codOld = array();
    $codNew2 = array();
    $codOld2 = array();

    /*lo que trae del html*/
    $codNew=split(",",$codigos); 
    $PECA_CAT="select codigos from tipo_subtipo_adm where tipo='$tipo' and subtipo='$subtipo' and tipocatprod='$tipocatprod'";
	//writelog($PECA_CAT);
	$peca_cat = tep_db_query($PECA_CAT);
    while ($row = mysql_fetch_row($peca_cat)) {
        $old=$row[0];
        $codOld=split(",",$row[0]); 
    }

	$h=0;
	$i=0;

	foreach ($codOld as $key => $value){
		$vari = array_search($value, $codNew);
		//if ($vari || $vari === 0)
			//$codNew[$vari] = 0; 
		if ($vari || $vari === 0)
			$codOld[$key] = 0;
	}

	foreach ($codOld as $key => $value){
		if($codOld[$key]!=0){
			$codOld2[$i]="'".$codOld[$key]."'";
			$i++;
		}
	}

	foreach ($codNew as $key => $value){
		if($codNew[$key]!=0){
			$codNew2[$h]="'".$codNew[$key]."'";
			$h++;
		}
	}
	$nuevos = implode (",", $codNew2);
	$ps = implode (",", $codOld2);

	/* hace update en la tabla productos*/
	if ($tipocatprod=='C') {
		/* ver que no sea vacio*/
		$qryupN="update productos  set
			prod_tipo='$tipo' , prod_subtipo='$subtipo'  where id_catprod in ($nuevos)";
		$qryuPS="update productos  set
			prod_tipo='PS' , prod_subtipo='PS'  where id_catprod in ($ps)";
		$qryI="update tipo_subtipo_adm set codigos='$codigos' where tipo='$tipo' and subtipo='$subtipo' and tipocatprod='C'";
	}
	else {
		$qryupN="update productos  set
		prod_tipo='$tipo', prod_subtipo='$subtipo' where  cod_prod1 in ($nuevos)";

		$qryuPS="update productos  set
		prod_tipo='PS' , prod_subtipo='PS' where  cod_prod1 in ($ps)";

		$qryI="update tipo_subtipo_adm set codigos='$codigos' where tipo='$tipo' and subtipo='$subtipo' and tipocatprod='P'";
	}

	if (strlen($nuevos)>0) {
		tep_db_query(trim($qryupN));
	}

	if (strlen($ps)>0) {
		tep_db_query(trim($qryuPS));
	}

	tep_db_query(trim($qryI));

/* inserta el codigo ingresado por el usuario al principio*/
}

function insertahistorial($comment, $os = 0, $ot = 0, $tipo = 'SYS') {
	global $ses_usr_id, $id_os, $ot_id, $id_ot;

	if (!$os)
		$os = $id_os;

	if (!$ot)
		$ot = $ot_id + $id_ot;

	$queryHist="Insert into historial (id_os,ot_id,hist_fecha,his_tipo,hist_usuario,hist_descripcion) 
				values (".($os+0).",".($ot+0).",now(),'$tipo','".get_nombre_usr( $ses_usr_id )."','$comment')";
	tep_db_query($queryHist);
}

function get_nombre_estado( $id_estado ) {

    $query_1 = "SELECT esta_nombre FROM estados WHERE id_estado = '$id_estado'";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );

    return ucwords($res_1['esta_nombre']);
}

/* retorna la cantidad de productos por idDespacho*/
function CantidadProductos($idot){
	$query_cant="select sum(osde_cantidad) as cantidad_productos from os_detalle where ot_id=".$idot;
	 $rq = tep_db_query($query_cant);
	 $res = tep_db_fetch_array( $rq );
	 $cant_prod=$res['cantidad_productos'];	
		if ($cant_prod<=0){
			writelog('function CantidadProductos no encuentra productos para esta Ot= '.$idot);
			return 0;
		}
	return $cant_prod;
}
/* retorna la cantidad  detalles de productos por idDespacho*/
	function CantidadDetalles($idot){
		$query_cant="select count(*) as cantidad_detalles from os_detalle where ot_id=".$idot;
         $rq = tep_db_query($query_cant);
         $res = tep_db_fetch_array( $rq );
		 $cant_det=$res['cantidad_detalles'];	
			if ($cant_det<=0){
				writelog('function CantidadDetalles no encuentra productos para este despacho= '.$idot);
				return 0;
			}
		return $cant_det;
	}
	/*funcion busca si el arreglo valor esta contenido en arreglo*/
	function ExisteEnArreglo($valor, $arreglo){
		foreach ($arreglo as $key=>$value) { 
			//writelog("Valor : [$valor] comparado con [".$arreglo[$key]."]");
			if ($valor == $arreglo[$key]){
				return 1;
				}
			}
		return 0;
	}
	/*funcioan que obtiene la impresora utilizada segun el usuario*/
	function Obtiene_Impresora($usuario){
		$query_print="SELECT impresora FROM usuarios where usr_id=".$usuario;
			if ($rq_p = tep_db_query($query_print)){
				$res_p = tep_db_fetch_array( $rq_p );
				$impresora=($res_p['impresora']!='')?$res_p['impresora']:$res_p['impresora'];
			}else{
				$nombre_usr=get_nombre_usr($usuario);
				writelog('Function Obtiene_Impresora , no puede obtener impresora de usuario'.$nombre);
			}
	return $impresora;
	}

/***************** retorna si una os puedo o no generar archivo********************/
function generaocos($numos){
	$valor=0;
	/*verifica estado tipo y subtipo de os y si esta marcada*/
	$query="SELECT OS.id_os, OD.ot_id,OS.id_estado, OS.os_marcasap,OD.osde_tipoprod, OD.osde_subtipoprod
	FROM os OS
	join os_detalle OD on (OS.id_os=OD.id_os)
	join ot OT on (OT.ot_id=OD.ot_id)
	where 1 and OT.id_estado='EC' and OS.id_os=".$numos;
	if ( tep_db_query($query)){	
		$rq_1 = tep_db_query($query);
		while( $res = tep_db_fetch_array( $rq_1 ) ) {
			if ( ($res['id_estado']=='SP') && ($res['os_marcasap']!='E') && ($res['osde_tipoprod']=='PE') && ($res['osde_subtipoprod']=='CA') ){
				writeevent('(generaocos) -> OS '.$numos.' OT->'.$res['ot_id'].' Cumple parámetros para ser filtrada');
				$valor=1;		
			}
		}
	}else{
		writeevent('Error (generaocos) -> OS '.$numos.' Error en query');
		return 0;
	}
	return $valor;
}
/******************************************************************************/
function generatexto($numos){
/*genera texto para insertar en un archivo*/
	$texto=0;
	$query="SELECT distinct OD.id_os,OD.ot_id,OD.osde_tipoprod, OD.osde_subtipoprod,L.cod_local,
			DATE_FORMAT(OS.os_fechaboleta,'%Y%m%d%H%i%s') as fechapagoos, DATE_FORMAT(now(),'%Y%m%d%H%i%s') fechaer,O.id_tipodespacho,OS.clie_rut, CL.clie_nombre , CL.clie_paterno, CL.clie_materno ,
			CL.clie_telefonocasa,CL.clie_telcontacto1,D.dire_direccion,C.comu_nombre,D.dire_observacion,
			OD.cod_sap,OD.cod_barra,CB.unid_med,OD.osde_cantidad
	FROM ot O
		join os_detalle OD on (OD.id_os=O.id_os) and (OD.osde_tipoprod=O.ot_tipo)
		join os OS         on (OS.id_os=O.id_os)
		join locales L     on (OS.id_local=L.id_local)
		join clientes CL   on (OS.clie_rut=CL.clie_rut)
		join direcciones D on (OS.id_direccion=D.id_direccion)
		join comuna C      on (D.id_comuna=C.id_comuna)
		left join codbarra CB   on (OD.cod_sap=CB.cod_prod1) and (OD.cod_barra=CB.cod_barra)
	where 1 and OD.osde_tipoprod='PE' and  OD.osde_subtipoprod='CA' and OS.id_os=".$numos;
	if (tep_db_query($query)){
		$rq_1 = tep_db_query($query);
		while( $res = tep_db_fetch_array( $rq_1 ) ) {
			$detalle.= CompletaCerosI($res['id_os'], 10); #10
			$detalle.= CompletaCerosI($res['ot_id'], 10); #10	20
			$detalle.= $res['osde_tipoprod'];			  #2	22	
			$detalle.= $res['osde_subtipoprod'];		  #2	24
			$detalle.= $res['cod_local'];				  #4	28
			$detalle.= CompletaEspaciosD(trim($res['fechapagoos']),14); #14	42
			$detalle.= CompletaEspaciosD(trim($res['fechaer']),14);		#14	56
			if ($res['id_tipodespacho']==1)
				$despacho	= 'Z05';
			else
				$despacho	= 'Z02';
			$detalle.=$despacho;							#3	59
			$detalle.= CompletaCerosI($res['clie_rut'],8);  #8	67
			$nombre	 =$res['clie_nombre'].' '.$res['clie_paterno'].' '.$res['clie_materno'];
			$detalle.= CompletaEspaciosD($nombre,30); #30	97
			if ($res['clie_telefonocasa'])
				 $telefono=CompletaEspaciosD($res['clie_telefonocasa'],15);
			else
				 $telefono=CompletaEspaciosD($res['clie_telcontacto1'],15);
			$detalle.=$telefono;					 #15	112
			$detalle.= CompletaEspaciosD($res['dire_direccion'],50); #50	162
			$detalle.= CompletaEspaciosD($res['comu_nombre'],30);	 #30	192
			$detalle.= CompletaEspaciosD($res['dire_observacion'],100); #100 292
			$detalle.= CompletaCerosI($res['cod_sap'],18);			#18	310
			$dvean	 = dvEAN13($res['cod_barra']);	
			$cod_barra=$res['cod_barra'].$dvean;
			$detalle.= CompletaCerosI($cod_barra,13);				#13	323
			if (!$res['unid_med']){
				$unimed='ST';
			}else{
				$unimed=$res['unid_med'];
			}
			$detalle.= CompletaEspaciosD($unimed,3);			#3	326
			$cantidad=($res['osde_cantidad']*BASE);
			$detalle.= CompletaCerosI($cantidad,12);			#12	338
			$detalle.= CompletaCerosI(BASE,4);					#4	342
			$detalle.= CompletaEspaciosD(' ',35);				#35	377
			$detalle.= CompletaEspaciosD(' ',35);				#35	412
			$detalle.= CompletaEspaciosD(' ',35);				#35	447
			//$detalle.= "\n"; si no va dejatodo en una sola linea si es mas de una ot  por OS
			$detalle.= "\n";
			$texto=$detalle;
		}
	}else{
		writeevent('Error (generatexto)-> No se pudo generar texto para OS '.$numos);
		return 0;
	}
return $texto;
}

/*genera un nombre alaeatorio CPIOSPPPPPPPPPAAAAMMDDHHMMSS*/
function generanombrearchivo(){
	$nombre= DATE('Ymdhis');
	//$nombre=md5(DATE('Ymdhis'));
	return	$nombre;
}


/*function para escribir en un archovi un msg dado*/
function escritura($msg,$archivo){
#	$prefijo = PREFIJO;		#ARCH
#	$ext	 = EXTENSION;	#txt
#	$ruta	 = DIR_SAP;		#../../sap/ 
#	$reqcomp = REQCOMP;		#1
#	$gzip	 = GZIP;			#1
	
if (defined('PREFIJO')){$prefijo=PREFIJO;}else{$prefijo='';}
if (defined('EXTENSION')){$ext=EXTENSION;}else{$ext='';}
if (defined('DIR_SAP')){$ruta=DIR_SAP;}else{$ruta='';}
if (defined('REQCOMP')){$reqcomp=REQCOMP;}else{$reqcomp=0;}
if (defined('GZIP')){$gzip=PREFIJO;}else{$gzip=0;}

	if (!$archivo){
		writeevent("Error (escritura) -> No viene nombre archivo");
		$archivo= generanombrearchivo();
	}
	if ($archivo){
	 	/* escribe en el archivo el msg*/
		if ($file = fopen($ruta.$prefijo.$archivo.$ext,"a")) { 
			fputs ($file, $msg);
			fclose($file); 
		}else{
			writeevent("Error (escritura)-> No pudo escribir el msg en el archivo ".$archivo);
		}
	}else{
		writeevent("Error (escritura)-> No se genero nombre archivo");
	}
	/* requiere archivo gz*/
	if (gzip ($ruta.$prefijo.$archivo.$ext, 9)) 
		writeevent("(escritura) -> Fichero ".$prefijo.$archivo.$ext." comprimido satisfactoriamente!"); 
	else{ 
		writeevent("Error (escritura) -> Se ha producido un error en la compresión del fichero"); 
		writeevent("Error (escritura) -> Asegúrese de que la ruta del fichero a comprimir es valida y tiene permisos de escritura"); 
	} 
	/* crea archivo de comprobación, si lo requiere */
	if ($reqcomp){
		if ($file = fopen($ruta.$prefijo.$archivo.'.trg',"a")) { 
			fputs ($file, ' ');
			fclose($file); 
		}else
			writeevent("Error (escritura)-> No pudo crear el archivo de comprobacion para el archivo ".$archivo);
	}else
		writeevent("(escritura) -> archivo ".$archivo." no require archivo de comprobación"); 	
	return $archivo;
}

function marcaos($numos){
	$queryUP ="UPDATE os SET os_marcasap='E' where id_os = " . ($numos + 0) ;
	if (tep_db_query($queryUP)){
		return $numos;
	}else{ 
		return 0;
	}
}

/*funcion que zipea un archivo dado*/
function gzip($sFichOrigen, $iNivelComp){ 
	$sFichDetino = $sFichOrigen.".gz"; 
	if ( ! $fOrigen = @fopen($sFichOrigen, "rb")) 
		return false; 
	$sOriBin = fread($fOrigen, filesize($sFichOrigen)); 
	fclose($fOrigen); 
	$sDesGZ = gzencode($sOriBin, $iNivelComp); 
	if ( ! $fDestino = @fopen ($sFichDetino, "wb")) 
		return false; 
	fwrite($fDestino, $sDesGZ); 
	fclose($fDestino); 
	return true; 
} 
/***********************************************************************************/
/*********** Funciones para tratar archivos recuperados del sap ********************/
function lectura(){
if (defined('REQCOMPR')){$reqcomp=REQCOMPR;}else{$reqcomp=0;}
if (defined('DIR_SAP_READ')){$dire=DIR_SAP_READ;}else{$dire=0;}
if (defined('EXTREAD')){$extension=EXTREAD;}else{$extension='';}

	#$reqcomp= REQCOMPR;		# si requiere comprobacion o no
	#$dire   = DIR_SAP_READ; # direcorio donde saca los archivos para leer
	if ($archivosordenados=leedirectorio($dire)){
		$cont =count($archivosordenados);
		foreach ($archivosordenados as $key=>$value) { 
			$texto = substr($archivosordenados[$key],0,28);
			$ext  =  substr($archivosordenados[$key],28,4);
			//si no viene con extension el archivo del sap y la variable EXTREAD no está definida
			if(!$ext){
				$ext='';
			}
			if ($reqcomp){
				if($ext==$extension){
					$valor=buscatrg($archivosordenados, $texto);
					if ($existetrg=buscatrg($archivosordenados, $texto)){
						$textosap =recuperamsg($dire,$archivosordenados[$key]);
						if ($textosap =recuperamsg($dire,$archivosordenados[$key])){
						/*copia archivos procesados a otro dire*/
							if (!$enviado = enviaproc($dire,$archivosordenados[$key])){
								writeevent("(lectura) -> No envió los archivos leidos con exito "); 				
							}else{ 
								/*Borra Archivo y trg*/
								if (borrarchivo($dire,$archivosordenados[$key]))
									writeevent("(lectura) -> Borro los archivos leidos con exito "); 	
								else
									writeevent("Error (lectura) -> No borro los archivos leidos "); 				
							}
						}else
							writeevent("(lectura) -> No se puede recuperar el texto "); 		
					}else{
						writeevent("(lectura) -> No se encuentra archivo de comprobacion para archivo ".$archivosordenados[$key]); 	
					}
				}
			}else{
				if ($textosap =recuperamsg($dire,$archivosordenados[$key])){
					if (!$enviado = enviaproc($dire,$archivosordenados[$key])){
						writeevent("(lectura) -> No envio los archivos leidos con exito "); 				
					}else{ 
						if (borrarchivo($dire,$archivosordenados[$key]))
							writeevent("(lectura) -> Borro los archivos leidos con exito "); 	
						else
							writeevent("Error (lectura) -> No borro los archivos leidos "); 						
					}
				}else
					writeevent("Error (lectura) -> No se puede recuperar el texto "); 		
			}
		}
	return $textosap;
	}else{
		writeevent("(lectura) -> No existen archivos que leer en el directorio indicado ".$dire); 	
		return	0;
	}
}

function leedirectorio($directorio){
	if ($directorio){
		$directorio = opendir($directorio);
		$cont=0;
		while ($archivo = readdir($directorio)){
			$ordenados[$cont]= $archivo;
			$cont++;
		}
		/*Borro elementos no deseados*/
		foreach ($ordenados as $key=>$value) { 
			$texto = substr($ordenados[$key],0,28);
			 if(strlen($texto)<3){
				unset($ordenados[$key]);
			 }
		}
		/*Ordeno el directorio ascendentemente*/ 
		natcasesort($ordenados);
		return $ordenados;
	}else{
		writeevent("(leedirectorio) -> No existe directorio definido para sacar archivos con OC"); 	
		return 0;
	}
}
 
function buscatrg($arreglodir,$nombrearchivo){
	$dire=DIR_SAP_READ;
	chdir($dire);
	/*busca en este directorio el nombre de archivo*/
	$ext=TRGEXT;
		/*se recorre el arreglo con los archivos*/
		foreach ($arreglodir as $key=>$value) { 
			$linea=$arreglodir[$key];
			if (strlen($linea)>28){
				$textotrg = substr($arreglodir[$key],0,28);
				$exttrg   = substr($arreglodir[$key],28,4);
				/* y se compara */
				if (defined('TRGEXT')){$ext=TRGEXT;}else{$ext='';}
				if ($ext==$exttrg){
					if ($nombrearchivo==$textotrg){
						#$existetrg=1;
						return $existetrg=1;
					}else{
						#writeevent("Error function(buscatrg) -> No existe archivo de comprobacion para archivo -> ".$nombrearchivo); 
						return $existetrg=0;
					}
				}
			}
		}
}

function recuperalineasfile($dire,$Archivo){
	$archivo = fopen ($dire.$Archivo, "r"); 
	//inicializo una variable para llevar la cuenta de las líneas y los caracteres 
	$num_lineas = 0; 
	$caracteres = 0; 

	//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo 
	while (!feof ($archivo)) { 
		//si extraigo una línea del archivo y no es false 
		if ($linea = fgets($archivo)){ 
		   //acumulo una en la variable número de líneas 
		   $num_lineas++; 
		} 
	} 
	fclose ($archivo); 
	return $num_lineas; 
}

function recuperamsg($dire,$Archivo){
	$i=0;
	if ($dire){
		chdir($dire);
		if (file_exists($Archivo)){
		/* contar las lineas del archivo */
			$lineas = file($Archivo); 
			$numlineasfile=recuperalineasfile($dire,$Archivo);
			if ($numlineasfile){
				while ($i<=($numlineasfile-1)) { 
					$msg[$i] =$lineas[$i];
					$i++;
				}
			}
		}else{
			writeevent("Error (buscatrg) El archivo ".$Archivo." no existe ");
		}
		return $msg;
	}else{
		writeevent("Error (buscatrg) faltan argumentos para encontrar el archivo");	
		return 0;
	}
}

function enviaproc($dire,$archivo){
	if (rename($dire.$archivo, DIR_SAP_PROC.$archivo)){
		writeevent("(enviaproc) Archivo ".$archivo." enviado a carpeta de procesados");
		return 1;
	}else{
		writeevent("Error (enviaproc) Archivo ".$archivo." no pudo ser enviado a carpeta de procesados");
		return 0;
	}
}

function borrarchivo($dire,$archivo){
	chdir($dire);
	/*Borra Archivo y trg*/
	$arre    = split(EXTREAD, $archivo);
	unlink($archivo);
	unlink($arre[0].".trg");
	return 1;
}

function recuperadatos($texto){
	$ot  = substr ($texto, 10, 10);
	$oc  = substr ($texto, 461, 10);
	$rut = substr ($texto, 475, 8);
	$nom = substr ($texto, 483, 35);
	$datos=$ot.'-'.$oc.'-'.$rut.'-'.$nom;
	if ($oc)
		return $datos;
	else{
		writeevent("Error (recuperadatos) No existe el dato de la OC");	
		return 0;
	}
}

function cambiaestado($id_ot,$noc_sap,$rut,$nom,$id_proveedor){
	$query=" UPDATE ot SET id_estado ='ER',noc_sap=".$noc_sap.", id_proveedor=".$id_proveedor." WHERE ot_id =".$id_ot;
	if(tep_db_query($query))
		$resp=1;
	else{
		writeevent("Error (cambiaestado) No se puede cambiar el estado de la ot n ".$id_ot);	
		$resp=0;
	}
	/*nombre del estado*/
	$qn="SELECT esta_nombre FROM estados where id_estado='ER'";
	if(tep_db_query($qn)){
		$rq = tep_db_query($qn);
		$res = tep_db_fetch_array($rq);
		$nombre=$res['esta_nombre'];
		writeevent("OT $id_ot ha cambiado a estado $nombre");	
		insertahistorial("OT $id_ot ha cambiado a estado $nombre",0,$id_ot,'Sys');
		insertahistorial("OT $id_ot asigna Proveedor (Rut: $rut, Nombre: $nom),cambia a estado $nombre, asigna número Nº $noc_sap de orden de compra ",0,$id_ot,'Sys');
		writeevent("OT $id_ot asigna Proveedor (Rut: $rut, Nombre: $nom),cambia a estado $nombre, asigna número Nº $noc_sap de orden de compra ");
		$resp=1;
	}else{
		writeevent("Error (cambiaestado) No se puede obtener el nombre del estado para la ot n ".$id_ot );	
		$resp=0;
	}
	return $resp;
}


/******* verifica la existencia de un proveedor*******/
function verificaproveedor($rutp){
	$query="SELECT id_proveedor,cod_prov, rut_prov, nom_prov, razsoc_prov,estadoactivo FROM proveedores where rut_prov=".$rutp;
	if (tep_db_query($query)){
		$rq = tep_db_query($query);
		$res = tep_db_fetch_array( $rq );
		$id_proveedor	=$res['id_proveedor'];
		$rut_prov		=$res['rut_prov'];
		$nom_prov		=$res['nom_prov'];
		$razsoc_prov	=$res['razsoc_prov'];
		return $id_proveedor;
	}else{
		writeevent("Error (verificaproveedor) No se vereficar el proveerdor para rut ".$rutp );	
		return 0;
	}
}

/****** inserta proveedor*************/
function insertaproveedor($rutp,$nomp){
	$query="INSERT proveedores (rut_prov,nom_prov,razsoc_prov,estadoactivo)
	values (".($rutp+0).",'$nomp','".$nomp."','C')";
	if (tep_db_query($query)){
		$last_id = tep_db_insert_id( '' );
		return $last_id;
	}else{
		writeevent("Error (insertaproveedor) No se pudo insertar el proveerdor para rut ".$rutp );	
		return 0;
	}
}

/***********************************************************************************/

/***********************************************************************************/
// CompletaEspaciosD
// Descripcion: Rellena una cadena con espacios a la derecha
/***********************************************************************************/
function CompletaEspaciosD($variable, $tamano){
	return  sprintf("%-".($tamano+0).".".($tamano+0)."s", $variable);
}

/***********************************************************************************/
// CompletaCerosD
// Descripcion: Rellena una cadena con ceros a la derecha
/***********************************************************************************/
function CompletaCerosD($variable, $tamano){
	return  sprintf("%-0".($tamano+0).".".($tamano+0)."s", $variable);
}

/***********************************************************************************/
// CompletaCerosI
// Descripcion: Rellena una cadena con ceros a la izquierda
/***********************************************************************************/
function CompletaCerosI($variable, $tamano){
	return  sprintf("%0".($tamano+0).".".($tamano+0)."s", $variable);
}
	

?>