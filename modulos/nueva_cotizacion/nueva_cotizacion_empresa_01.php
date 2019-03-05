<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************


/* toma el rut y verifica si y EXISTE EL CLIENTE  en la tabla cliente y direccion*/


function digiVer($r){
    $s=1;for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
return chr($s?$s+47:75);}

$digito=digiVer($clie_rut);

/************** Inicio Acciones*****************/
if ($clie_rut){
    $queryClie =  "SELECT clie_tipo,clie_rut, clie_nombre, clie_paterno, clie_materno,clie_telefonocasa,clie_telcontacto1,clie_telcontacto2,clie_rutcontacto,clie_razonsocial,clie_giro FROM clientes where clie_rut=".($clie_rut+0)." and clie_tipo='e' and clie_activo=1";
    $eclient = tep_db_query($queryClie);
    $eclient = tep_db_fetch_array( $eclient );
    $rutE=$eclient['clie_rut'];
    if ($rutE){$rutExiste=true;}
    $queryDir =  "Select dire_direccion, dire_observacion,dire_telefono from direcciones where clie_rut=".($clie_rut+0)." and dire_activo=1 and dire_defecto='p'";
    $edirec = tep_db_query($queryDir);
    $edirec = tep_db_fetch_array( $edirec );
    $clicont=$eclient['clie_rutcontacto'];
        if ($eclient['clie_nombre']){
            $digicontac=digiVer($clicont);
            $digicontac="-". $digicontac;
        }
}

/*updatea el dato exista o no*/
if ($accion=="update"){
    /* se hace este select para ver si el cliente es nuevo o ya existe*/
    $qexiste="select clie_nombre from clientes where clie_rut=".($clie_rut+0);
    $exis = tep_db_query($qexiste);
    $exis = tep_db_fetch_array( $exis );

   $queryUpC ="UPDATE clientes SET  clie_razonsocial='$clie_razonsocial',  clie_nombre='$clie_nombre',clie_paterno='$clie_paterno',clie_materno='$clie_materno',clie_telefonocasa='$clie_telefonocasa',clie_telcontacto1='$clie_telcontacto1',clie_telcontacto2='$clie_telcontacto2',clie_activo=1,clie_tipo='e' ,clie_rutcontacto=".($clie_rutcontacto+0).", clie_razonsocial='$clie_razonsocial', clie_giro='$clie_giro' where clie_rut=".($clie_rut+0)." and clie_tipo='e'";
    tep_db_query($queryUpC);

       if ($exis['clie_nombre']!='' ){
             $queryUpD ="UPDATE direcciones SET dire_direccion='$dire_direccion',dire_telefono='$clie_telefonocasa',dire_observacion='$dire_observacion', id_comuna=".($select2+0)." WHERE clie_rut=".($clie_rut+0)." and dire_defecto='p'";
       } else {
            $queryUpD =  "INSERT INTO direcciones (dire_direccion,clie_rut,dire_telefono,dire_observacion,id_comuna,dire_activo,dire_defecto,dire_nombre) VALUES('$dire_direccion',".($clie_rut+0).",'$clie_telefonocasa','$dire_observacion',".($select2+0).",1,'p','Primaria')";
       }
    tep_db_query($queryUpD);
    $digicontac=dv($clie_rutcontacto);
	if (!$donde){
		header ('Location: nueva_cotizacion_02.php?clie_rut='.($clie_rut+0).'&clie_tipo=e');
	}else{
	?>
	<script language="JavaScript">
		window.returnValue = 'refresh';
		window.close();
	</script>
	<?
	}
tep_exit();
}

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' .NOMBRE_PAGINA_E);
$MiTemplate->set_var("TEXT_TITULO_E",TEXT_TITULO_E);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));


if ($eclient['clie_rut'] AND $tpcliente="persona"){
     $MiTemplate->set_var("clie_rut",$eclient['clie_rut']);
     $MiTemplate->set_var("clie_tipo",$eclient['clie_tipo']);
     $MiTemplate->set_var("digito",$digito);
     $MiTemplate->set_var("digicontac",$digicontac);
     $MiTemplate->set_var("clie_razonsocial",$eclient['clie_razonsocial']);
     $MiTemplate->set_var("clie_giro",$eclient['clie_giro']);
     $MiTemplate->set_var("clie_rutcontacto",$eclient['clie_rutcontacto']);
	 $digitoC=digiVer($eclient['clie_rutcontacto']);
     $MiTemplate->set_var("dire_telefono",$edirec['dire_telefono']);
     $MiTemplate->set_var("clie_nombre",$eclient['clie_nombre']);
     $MiTemplate->set_var("clie_paterno",$eclient['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$eclient['clie_materno']);
     $MiTemplate->set_var("clie_telefonocasa",$eclient['clie_telefonocasa']);
     $MiTemplate->set_var("clie_telcontacto1",$eclient['clie_telcontacto1']);
     $MiTemplate->set_var("clie_telcontacto2",$eclient['clie_telcontacto2']);
     $MiTemplate->set_var("clie_materno",$eclient['clie_materno']);
     $MiTemplate->set_var("dire_direccion",$edirec['dire_direccion']);
     $MiTemplate->set_var("dire_observacion",$edirec['dire_observacion']);
     $MiTemplate->set_var("dire_telefono",$edirec['dire_telefono']);
if ($donde)
     $MiTemplate->set_var("leyenda","Ingrese Datos del Cliente Empresa");
   }

$MiTemplate->set_var("digicontac",$digicontac);
$MiTemplate->set_var("TEXT_CAMPOE_1",TEXT_CAMPOE_1);
$MiTemplate->set_var("TEXT_CAMPOE_2",TEXT_CAMPOE_2);
$MiTemplate->set_var("TEXT_CAMPOE_3",TEXT_CAMPOE_3);
$MiTemplate->set_var("TEXT_CAMPOE_4",TEXT_CAMPOE_4);
$MiTemplate->set_var("TEXT_CAMPOE_5",TEXT_CAMPOE_5);
$MiTemplate->set_var("TEXT_CAMPOE_6",TEXT_CAMPOE_6);
$MiTemplate->set_var("TEXT_CAMPOE_7",TEXT_CAMPOE_7);
$MiTemplate->set_var("TEXT_CAMPOE_8",TEXT_CAMPOE_8);
$MiTemplate->set_var("TEXT_CAMPOE_9",TEXT_CAMPOE_9);
$MiTemplate->set_var("TEXT_CAMPOE_10",TEXT_CAMPOE_10);
$MiTemplate->set_var("TEXT_CAMPOE_11",TEXT_CAMPOE_11);
$MiTemplate->set_var("TEXT_CAMPOE_12",TEXT_CAMPOE_12);
$MiTemplate->set_var("TEXT_CAMPOE_13",TEXT_CAMPOE_13);
$MiTemplate->set_var("TEXT_CAMPOE_14",TEXT_CAMPOE_14);
$MiTemplate->set_var("TEXT_CAMPOE_15",TEXT_CAMPOE_15);
$MiTemplate->set_var("TEXT_FLECHA_SIG",TEXT_FLECHA_SIG);


// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main1","nueva_cotizacion_tituloE.html");
$MiTemplate->set_file("main2","nueva_cotizacion_TemEmp.html");
$MiTemplate->set_file("main3","nueva_cotizacion_temBotones.html");
$MiTemplate->set_file("main4","nueva_cotizacion_botones.html");;

    $MiTemplate->set_block("main2", "Comunas", "BLO_com");
    /* si el cliente es nuevo*/
        if ( trim($eclient['clie_nombre'])==''){
            $query = "SELECT distinct c.id_comuna, c.comu_nombre FROM comuna c  order by c.comu_nombre ";
        }else{
            /*si el cliente ya existe*/
            $query = "SELECT distinct c.id_comuna, c.comu_nombre, case WHEN clie_rut is null then '' else  'selected' end selected FROM comuna c LEFT JOIN direcciones d ON c.id_comuna = d.id_comuna AND d.clie_rut =".($eclient['clie_rut']+0)."  and d.dire_defecto='p'  order by c.comu_nombre " ;
        }

    query_to_set_var( $query, $MiTemplate, 1, 'BLO_com', 'Comunas' );



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