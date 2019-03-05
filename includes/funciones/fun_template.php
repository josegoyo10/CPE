<?
/*################################################################
  Funciones y definiciones

  Autor      : KID Chile Ltda.
  Fecha      : 22 septiembre 2004
  Plataforma : BaseKid
  Cliente    : KID Chile
                                            2004 (c) Kid Chile LTDA.
  ################################################################*/


function query_to_set_var( $query, &$MiTemplate, $tipo, $var1, $var2 ) {

	$contador = 0;
    $rq = tep_db_query($query);
    if( tep_db_num_rows($rq) > 0 ) {
        $res = mysql_fetch_assoc( $rq );
        $arr_k = array_keys ($res);

        $rq = tep_db_query($query);
        while( $res = tep_db_fetch_array( $rq ) ) {
            for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
            }
            if( $tipo ) {
				++$contador;
                $MiTemplate->parse($var1, $var2, true);
			}
        }
    }
	return $contador;
}


//-------------------------------------------
// Realiza consulta de inventario Real
//-------------------------------------------
function query_to_set_var_inventory( $query, &$MiTemplate, $tipo, $var1, $var2, $arreglo, $nosap = false) {

	$contador = 0;
	$verdad  = false;
    $rq = tep_db_query($query);
    if( tep_db_num_rows($rq) > 0 ) {
        $res = mysql_fetch_assoc( $rq );
        $arr_k = array_keys ($res);

        $rq = tep_db_query($query);
        
        while( $res = tep_db_fetch_array( $rq ) ) {
            for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                if($nosap){
                    $longitud = strlen($res[ean]);
                    $EAN = substr($res[ean], 0, $longitud - 1);
                    $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));		
                    $verdad  = true;
                }else{
          	
                    for( $j = 0; $j < $arreglo['maxInventory']; $j++ ) {
                                            $longitud = strlen($res[ean]);
                            $EAN = substr($res[ean], 0, $longitud - 1);

                            if( $arreglo[$j]['SAP'] == $res[cod_sap] && $arreglo[$j]['store'] == $res[cod_local]){
                                    $MiTemplate->set_var('inventario', tohtml( $arreglo[$j]['amount'] ));
                                    $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));		
                                    $verdad  = true;	            		
                            }
                    }
                    
                }
            }
        if( $tipo && $verdad) {
			++$contador;
            $MiTemplate->parse($var1, $var2, true);
			}
        }
      
    }
	return $contador;
}



//-------------------------------
// Convert non-standard characters to HTML
//-------------------------------
function tohtml($strValue)
{
  //return htmlspecialchars($strValue);
  return $strValue;
}

//-------------------------------
// Obtain specific URL Parameter from URL string
//-------------------------------
function get_param($param_name)
{
  global $HTTP_POST_VARS;
  global $HTTP_GET_VARS;

  $param_value = "";
  if(isset($HTTP_POST_VARS[$param_name]))
    $param_value = $HTTP_POST_VARS[$param_name];
  else if(isset($HTTP_GET_VARS[$param_name]))
    $param_value = $HTTP_GET_VARS[$param_name];

  return $param_value;
}


function get_server_param($param_name)
{
  global $HTTP_SERVER_VARS;

  $param_value = "";
  if(isset($HTTP_SERVER_VARS[$param_name]))
    $param_value = $HTTP_SERVER_VARS[$param_name];

  return $param_value;
}

function set_session($param_name, $param_value)
{
  global ${$param_name};
  if(session_is_registered($param_name))
    session_unregister($param_name);
  ${$param_name} = $param_value;
  session_register($param_name);
}

//-------------------------------
// Convert value for use with SQL statament
//-------------------------------
function tosql($value, $type)
{
  if(!strlen($value))
    return "NULL";
  else
    if($type == "Number")
      return str_replace (",", ".", doubleval($value));
    else
    {
      if(get_magic_quotes_gpc() == 0)
      {
        $value = str_replace("'","''",$value);
        $value = str_replace("\\","\\\\",$value);
      }
      else
      {
        $value = str_replace("\\'","''",$value);
        $value = str_replace("\\\"","\"",$value);
      }

      return "'" . $value . "'";
    }
}

?>