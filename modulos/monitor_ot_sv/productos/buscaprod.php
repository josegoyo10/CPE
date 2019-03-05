<?
// Esta página sólo se ejecuta como CGI
$SIN_PER = 1;
include "../../includes/aplication_top.php";

if (isset($codigo) && is_numeric($codigo) && $codigo>0 && isset($tipocod) && ($tipocod=="SAP" || $tipocod=="EAN")) {

    if ($tipocod=="SAP") 
        $Mquery = "SELECT des_corta FROM productos where cod_prod1 = '$codigo'";

    if ($tipocod=="EAN") 
        $Mquery = "SELECT des_corta FROM codbarra c JOIN productos p on p.cod_prod1 = c.cod_prod1 WHERE c.cod_barra = '$codigo'";

    $Mrq = tep_db_query($Mquery);

    if($Mres = tep_db_fetch_array( $Mrq ))
        echo "" . $Mres["des_corta"];
    else
        echo "";
}
else {
    //No se cumplió alguna condición
    echo "";
}

?>
