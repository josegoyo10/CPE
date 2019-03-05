<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "nueva_cotizacion_0303" );
//include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

/************************************************************************************************/
function buscador() {
    global $ses_usr_id;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("LIMITE_REG",LIMITE_REG);

    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
    $MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
    $MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
    $MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
    
    $MiTemplate->set_var("TEXT_OBLIGATORIO_NUM",TEXT_OBLIGATORIO_NUM);
    $MiTemplate->set_var("TEXT_OBLIGATORIO_VAC",TEXT_OBLIGATORIO_VAC);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_0303.htm");
            
    // Agregamos el footer
    //$MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    //include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

function listado_proveedor() {
    global $ses_usr_id;
    global $rad_proveedor, $proveedor;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO3);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);
    $MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
    $MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
    
    $MiTemplate->set_var("rad_proveedor",$rad_proveedor);
    $MiTemplate->set_var("prov",$proveedor);
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03lista_prov.htm");
            
    if ( $rad_proveedor == 1 )
        $where_aux = "WHERE nom_prov like '%$proveedor%'"; 
    else if ( $rad_proveedor == 2 )
        $where_aux = "WHERE rut_prov = $proveedor"; 
/*cuantos son*/
	$qzyCant = "SELECT  count(*) as cuantos
				 FROM proveedores
                 $where_aux ";
                 $msg="Se encontraron ";
                 $msg1=" coincidencias";
                 $msg2=" coincidencias ,solo se mostrarán las primeras ".LIMITE_REG;
    if ( $rq = tep_db_query($qzyCant) ){
        while( $res = tep_db_fetch_array( $rq ) ) { 
            if ($res['cuantos']<LIMITE_REG){
            $MiTemplate->set_var('cuantos',$res['cuantos']);     
            $MiTemplate->set_var('msg',$msg);     
            $MiTemplate->set_var('msg1',$msg1);                 
            }else{
            
            $MiTemplate->set_var('cuantos',$res['cuantos']);     
            $MiTemplate->set_var('msg',$msg);     
            $MiTemplate->set_var('msg2',$msg2);             
            
            }
        }
    }




    //Recuperamos los estados
    $MiTemplate->set_block("main", "proveedor", "BLO_proveedor");
    
	$query_PE = "SELECT id_proveedor, nom_prov, rut_prov
				 FROM proveedores
				 $where_aux
				ORDER BY nom_prov";

    //query_to_set_var( $query_PE, $MiTemplate, 1, 'BLO_proveedor', 'proveedor' );   
    if ( $rq = tep_db_query($query_PE) ){
        while( $res = tep_db_fetch_array( $rq ) ) {

            $MiTemplate->set_var('id_proveedor',$res['id_proveedor']);
            $MiTemplate->set_var('nom_prov',$res['nom_prov']);
            $MiTemplate->set_var('rut_prov',$res['rut_prov']);
            $MiTemplate->set_var('dig_ver', dv($res['rut_prov']) );
            
            $MiTemplate->parse("BLO_proveedor", "proveedor", true);   
        }           
    }
    //echo $query_PE;
    // Agregamos el footer
    //$MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    //include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


function listado_producto() {
    global $ses_usr_id;
    global $rad_producto, $producto;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
    $MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
    $MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
    $MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    $MiTemplate->set_var("rad_producto",$rad_producto);
    $MiTemplate->set_var("prod",$producto);
    $MiTemplate->set_var("acc","PS");
	
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03lista_prod.htm");
            
    //Recuperamos los estados
    $MiTemplate->set_block("main", "producto", "BLO_producto");
    
    if ( $rad_producto == 1 ) // Se busca por codigo SAP
        $where_aux = " and p.cod_prod1 = '$producto'";
    else if ( $rad_producto == 2 ) // Se busca por UPC
        $where_aux = " and cb.cod_barra = '$producto'";
    else if ( $rad_producto == 3 ) // Se busca por descripcion
        $where_aux = " and p.des_corta like '%$producto%'";

    $query_PS ="
	SELECT distinct p.id_producto, 
		p.cod_prod1, 
		p.des_larga des_corta, 
		pre.prec_costo, 
		pre.prec_valor, 
		p.prod_tipo 
        FROM productos p 
		/*LEFT*/ JOIN precios pre 
			ON pre.id_producto = p.id_producto 
			AND id_local = ".get_local_usr($ses_usr_id)." 
		LEFT JOIN codbarra cb 
			ON cb.id_producto= p.id_producto
        WHERE prod_tipo IN ('PS','PE')
		$where_aux 
	LIMIT 0," . LIMITE_REG ; 
	//Para obtener la cantidad
    $qzyCantp = "SELECT distinct count(*) cuantosp
            FROM productos p
                /*LEFT*/ JOIN precios pre
                        ON pre.id_producto = p.id_producto
                        AND id_local = ".get_local_usr($ses_usr_id)."
                LEFT JOIN codbarra cb
                        ON cb.id_producto= p.id_producto
        WHERE prod_tipo IN ('PS','PE')
                $where_aux";

        $msg= "Se encontraron ";
        $msg1=" coincidencias";
        $msg2=" coincidencias, solo se mostrarán las primeras ".LIMITE_REG;

    if ( $rq = tep_db_query($qzyCantp) ){
        while( $res = tep_db_fetch_array( $rq ) ) { 
            if ($res['cuantosp']<LIMITE_REG){
            	$MiTemplate->set_var('cuantosp',$res['cuantosp']);     
            	$MiTemplate->set_var('msg',$msg);     
            	$MiTemplate->set_var('msg1',$msg1);                 
            }
	    else
	    {
            	$MiTemplate->set_var('cuantosp',$res['cuantosp']);     
            	$MiTemplate->set_var('msg',$msg);     
            	$MiTemplate->set_var('msg2',$msg2);
            }
        }
    }                

    if ( $rq = tep_db_query($query_PS) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('flag',"volver");
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_corta',$res['des_corta']);
            if ( strlen($res['prec_costo']) == 0 ){
                $MiTemplate->set_var('prec_costo','-');
                $MiTemplate->set_var('ALINEACION',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_costo',formato_precio($res['prec_costo']));
                $MiTemplate->set_var('ALINEACION',ALINEACION_DER);
            }
            if ( strlen($res['prec_valor']) == 0 ){
                $MiTemplate->set_var('prec_valor','-');
                $MiTemplate->set_var('ALINEACION2',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_valor',formato_precio($res['prec_valor']));
                $MiTemplate->set_var('ALINEACION2',ALINEACION_DER);
            }
            $MiTemplate->parse("BLO_producto", "producto", true);   
        }           
    }
    $MiTemplate->set_var('flag',"volver");    
    $MiTemplate->pparse("OUT_H", array("header"), false);
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


function listado_servicio() {
    global $ses_usr_id;
    global $rad_servicio, $servicio;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
    $MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
    $MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
    $MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    
	$MiTemplate->set_var("rad_servicio",$rad_servicio);
    $MiTemplate->set_var("servicio",$servicio);
    $MiTemplate->set_var("acc","SV");
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03lista_prod.htm");
            
    //Recuperamos los estados
    $MiTemplate->set_block("main", "producto", "BLO_producto");
    
    if ( $rad_servicio == 1 ) // Se busca por codigo SAP
        $where_aux = " and p.cod_prod1 = '$servicio'";
    else if ( $rad_servicio == 2 ) // Se busca por UPC
        $where_aux = " and cb.cod_barra = '$servicio'";
    else if ( $rad_servicio == 3 ) // Se busca por descripcion
        $where_aux = " and p.des_corta like '%$servicio%'";

    $query_SV = "
		select distinct p.id_producto, 
                    p.cod_prod1, 
                    p.des_larga, 
                    pre.prec_costo, 
                    pre.prec_valor, 
                    p.prod_tipo 
                from productos p 
            /*left*/ join precios pre 
			on pre.cod_prod1 = p.cod_prod1
/*            and id_local = ".get_local_usr($ses_usr_id)." */
            left join codbarra cb 
			on cb.cod_prod1 = p.cod_prod1
            where prod_tipo = 'SV' 
		    $where_aux  
		limit 0," . LIMITE_REG; 
                
	// Recupero la cantidad	
    $qzyCantp = "
                select distinct count(*) cuantosp
                from productos p
                    /*left*/ join precios pre
                        on pre.cod_prod1 = p.cod_prod1
                        and id_local = ".get_local_usr($ses_usr_id)."
                    left join codbarra cb
                        on cb.cod_prod1 = p.cod_prod1
                where prod_tipo = 'SV'
                    $where_aux";

                 $msg= "Se encontraron ";
                 $msg1=" coincidencias";
                 $msg2=" coincidencias, solo se mostrarán las primeras ".LIMITE_REG;

    if ( $rq = tep_db_query($qzyCantp) ){
        while( $res = tep_db_fetch_array( $rq ) ) { 
            if ($res['cuantosp']<LIMITE_REG){
            	$MiTemplate->set_var('cuantosp',$res['cuantosp']);     
            	$MiTemplate->set_var('msg',$msg);     
            	$MiTemplate->set_var('msg1',$msg1);                 
            }
	    else {
            	$MiTemplate->set_var('cuantosp',$res['cuantosp']);     
            	$MiTemplate->set_var('msg',$msg);     
            	$MiTemplate->set_var('msg2',$msg2);             
            }
        }
    } 

    if ( $rq = tep_db_query($query_SV) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('flag',"volver");
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_corta',$res['des_larga']);
            if ( strlen($res['prec_costo']) == 0 ){
                $MiTemplate->set_var('prec_costo','-');
                $MiTemplate->set_var('ALINEACION',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_costo',formato_precio($res['prec_costo']));
                $MiTemplate->set_var('ALINEACION',ALINEACION_DER);
            }
            if ( strlen($res['prec_valor']) == 0 ){
                $MiTemplate->set_var('prec_valor','-');
                $MiTemplate->set_var('ALINEACION2',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_valor',formato_precio($res['prec_valor']));
                $MiTemplate->set_var('ALINEACION2',ALINEACION_DER);
            }
            $MiTemplate->parse("BLO_producto", "producto", true);   
        }           
    }
    $MiTemplate->set_var('flag',"volver");
    $MiTemplate->pparse("OUT_H", array("header"), false);
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


function ver_proveedor(){
    global $ses_usr_id;
    global $id_prov, $rad_proveedor, $proveedor;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO2);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_100",TEXT_CAMPO_100);
    $MiTemplate->set_var("TEXT_CAMPO_101",TEXT_CAMPO_101);
    $MiTemplate->set_var("TEXT_CAMPO_102",TEXT_CAMPO_102);
    $MiTemplate->set_var("TEXT_CAMPO_103",TEXT_CAMPO_103);
    $MiTemplate->set_var("TEXT_CAMPO_104",TEXT_CAMPO_104);
    $MiTemplate->set_var("TEXT_CAMPO_105",TEXT_CAMPO_105);
    $MiTemplate->set_var("TEXT_CAMPO_106",TEXT_CAMPO_106);
    $MiTemplate->set_var("TEXT_CAMPO_107",TEXT_CAMPO_107);
    $MiTemplate->set_var("TEXT_CAMPO_108",TEXT_CAMPO_108);
    
    $MiTemplate->set_var("rad_proveedor",$rad_proveedor);
    $MiTemplate->set_var("prov",$proveedor);
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03ficha_prov.htm");
            
    //Recuperamos los estados
    $MiTemplate->set_block("main", "proveedor", "BLO_proveedor");
    $query = "SELECT nom_prov,
					p.id_proveedor, 
					p.nom_prov, 
					p.rut_prov,
					p.razsoc_prov,
					p.nombcto_prov,
					p.emailcto_prov,
					p.fonocto_prov,
					pe.nomcontacto,
					pe.mailcontacto
            FROM proveedores p
            LEFT JOIN proveedores_ext pe ON p.id_proveedor = pe.id_proveedor
            WHERE p.id_proveedor = ".( $id_prov + 0 )."  limit 0," . LIMITE_REG;

    //query_to_set_var( $query, $MiTemplate, 1, 'BLO_proveedor', 'proveedor' );   

    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            
            $MiTemplate->set_var('nom_prov',$res['nom_prov']);
            $MiTemplate->set_var('id_proveedor',$res['id_proveedor']);
            $MiTemplate->set_var('rut_prov',$res['rut_prov']);
            $MiTemplate->set_var('dig_ver', dv($res['rut_prov']) );
            $MiTemplate->set_var('razsoc_prov',$res['razsoc_prov']);
            $MiTemplate->set_var('fonocto_prov',$res['fonocto_prov']);
            $MiTemplate->set_var('nombcto_prov',$res['nombcto_prov']);
            $MiTemplate->set_var('emailcto_prov',$res['emailcto_prov']);
            /*$MiTemplate->set_var('comu_nombre',$res['comu_nombre']);*/            
            $MiTemplate->parse("BLO_proveedor", "proveedor", true);   
        }           
    }
    
    // Agregamos el footer
    //$MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    //include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}


function ver_productos(){
    global $ses_usr_id;
    global $id_prov, $rad_proveedor, $proveedor, $acc;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
    $MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
    $MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
    $MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    $MiTemplate->set_var("rad_proveedor",$rad_proveedor);
    $MiTemplate->set_var("prov",$proveedor);
    $MiTemplate->set_var("id_prov",$id_prov);
    $MiTemplate->set_var("acc",'PE');
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03lista_prod.htm");
            
    //Recuperamos los estados
    $MiTemplate->set_block("main", "producto", "BLO_producto");
    $query_ver ="SELECT p.id_producto,   
					p.cod_prod1,   
					p.des_corta,   
					pre.prec_costo,   
					pre.prec_valor  
                FROM prodxprov pp 
					/*LEFT*/ JOIN precios pre on pre.id_producto = p.id_producto AND id_local = ".get_local_usr($ses_usr_id)."
					JOIN productos p ON p.cod_prod1 = pp.cod_prod1
				WHERE pp.id_proveedor = ".($id_prov + 0)." limit 0," . LIMITE_REG;
    
//	echo $query_ver;
	
	//query_to_set_var( $query_PE, $MiTemplate, 1, 'BLO_proveedor', 'proveedor' );   
    
    if ( $rq = tep_db_query($query_ver) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_corta',$res['des_corta']);
            if ( strlen($res['prec_costo']) == 0 ){
                $MiTemplate->set_var('prec_costo','-');
                $MiTemplate->set_var('ALINEACION',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_costo',formato_precio($res['prec_costo']));
                $MiTemplate->set_var('ALINEACION',ALINEACION_DER);
            }
            if ( strlen($res['prec_valor']) == 0 ){
                $MiTemplate->set_var('prec_valor','-');
                $MiTemplate->set_var('ALINEACION2',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_valor',formato_precio($res['prec_valor']));
                $MiTemplate->set_var('ALINEACION2',ALINEACION_DER);
            }
            $MiTemplate->parse("BLO_producto", "producto", true);   
        }           
    }
        $MiTemplate->set_var('flag',"volver");  
    // Agregamos el footer
    //$MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    //include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


function ver_ficha() {
    global $ses_usr_id;
    global $id_pro, $accion, $rad_producto, $producto, $rad_servicio, $servicio, $acc;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_200",TEXT_CAMPO_200);
    $MiTemplate->set_var("TEXT_CAMPO_201",TEXT_CAMPO_201);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    $MiTemplate->set_var("rad_producto",$rad_producto);
    $MiTemplate->set_var("prod",$producto);
    $MiTemplate->set_var("rad_servicio",$rad_servicio);
    $MiTemplate->set_var("servicio",$servicio);
    $MiTemplate->set_var("acc",$acc);
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03ficha_produ.htm");
    
    $MiTemplate->set_block("main", "Producto", "BLO_Producto");
    $queryP = "select p.id_producto, 
                p.cod_prod1, 
                p.des_larga, 
                pr.nom_prov, 
                pe.espec_tecnicas, 
                pe.img
                from productos p
                left join productos_ext pe on  pe.id_producto = p.id_producto
                left join prodxprov pp on pp.id_producto = p.id_producto 
                left join proveedores pr on pr.id_proveedor = pp.id_proveedor
                where p.id_producto=".($id_pro+0)." limit 0," . LIMITE_REG;

    if ( $rq = tep_db_query($queryP) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_larga',$res['des_larga']);
            $MiTemplate->set_var('espec_tecnicas',$res['espec_tecnicas']);
            $MiTemplate->set_var('nom_prov',$res['nom_prov']);

            if( $res['img'] && file_exists(DIR_PROD_IMG.$res['img']) )
                $MiTemplate->set_var("img",DIR_PROD_IMG.$res['img']);
            else
                $MiTemplate->set_var("img",DIR_NO_IMG);
            
        }           
    }
    if ( $rq = tep_db_query($queryP) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $nom_prov.=$res['nom_prov'].'<br>';
        }           
        $MiTemplate->set_var('nom_prov',$nom_prov);
    }
        
    $MiTemplate->pparse("OUT_H", array("header"), false);
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


function ver_ficha_pro() {
    global $ses_usr_id;
    global $id_pro, $accion, $rad_proveedor, $proveedor;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_200",TEXT_CAMPO_200);
    $MiTemplate->set_var("TEXT_CAMPO_201",TEXT_CAMPO_201);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    $MiTemplate->set_var("rad_proveedor",$rad_proveedor);
    $MiTemplate->set_var("prov",$proveedor);
    $MiTemplate->set_var("accion",$accion);
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03ficha_produc.htm");
    
    $MiTemplate->set_block("main", "Producto", "BLO_Producto");
    $queryP = "select p.id_producto, 
                p.cod_prod1, 
                p.des_larga, 
                pr.nom_prov, 
                pe.espec_tecnicas, 
                pe.img
                from productos p
                left join productos_ext pe on  pe.id_producto = p.id_producto
                left join prodxprov pp on pp.id_producto = p.id_producto 
                left join proveedores pr on pr.id_proveedor = pp.id_proveedor
                where p.id_producto=".($id_pro+0)." limit 0," . LIMITE_REG;

    if ( $rq = tep_db_query($queryP) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_larga',$res['des_larga']);
            $MiTemplate->set_var('espec_tecnicas',$res['espec_tecnicas']);
  
    	    if( $res['img'] && file_exists(DIR_PROD_IMG.$res['img']) )
        	$MiTemplate->set_var("img",DIR_PROD_IMG.$res['img']);
    	    else
        	$MiTemplate->set_var("img",DIR_NO_IMG);
        }           
    }

    if ( $rq = tep_db_query($queryP) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $nom_prov.=$res['nom_prov'].'<br>';
        }           
        $MiTemplate->set_var('nom_prov',$nom_prov);
    }
    
    $MiTemplate->pparse("OUT_H", array("header"), false);
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}




// 
// JSE
//
function listado() {
    global $ses_usr_id;
    global $rad_servicio, $servicio, $accion, $flag;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
    $MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
    $MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
    $MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    
	$MiTemplate->set_var("rad_servicio",$rad_servicio);
    $MiTemplate->set_var("servicio",$servicio);
    $MiTemplate->set_var("acc","SV");
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03lista_prod.htm");
            
    //Recuperamos los estados
    $MiTemplate->set_block("main", "producto", "BLO_producto");
    
    if ( $accion == "despachos" )
		$subtipo = "'DE'";
	if ( $accion == "instalaciones" )
		$subtipo = "'IN','IR'";
	if ( $accion == "visitas" )
		$subtipo = "'VI'";


	$query_SV ="SELECT distinct p.id_producto, 
					p.cod_prod1, 
					p.des_larga, 
					pre.prec_costo, 
					pre.prec_valor, 
					p.prod_tipo 
				FROM productos p 
					/*LEFT*/ JOIN precios pre on pre.cod_prod1 = p.cod_prod1
/*				AND id_local = ".get_local_usr($ses_usr_id)." */
				JOIN codbarra cb on cb.cod_prod1 = p.cod_prod1
				WHERE prod_tipo = 'SV' 
					AND prod_subtipo in (".$subtipo.")"; 


	// para el boton de volver o cerrar
    if($flag=='Cerrar'){
        $MiTemplate->set_var('flag',$flag);
    }else{
        $MiTemplate->set_var('flag',"volver");
    }
	if ( $rq = tep_db_query($query_SV) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_corta',$res['des_larga']);
            if ( strlen($res['prec_costo']) == 0 ){
                $MiTemplate->set_var('prec_costo','-');
                $MiTemplate->set_var('ALINEACION',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_costo',formato_precio($res['prec_costo']));
                $MiTemplate->set_var('ALINEACION',ALINEACION_DER);
            }
            if ( strlen($res['prec_valor']) == 0 ){
                $MiTemplate->set_var('prec_valor','-');
                $MiTemplate->set_var('ALINEACION2',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_valor',formato_precio($res['prec_valor']));
                $MiTemplate->set_var('ALINEACION2',ALINEACION_DER);
            }
            $MiTemplate->parse("BLO_producto", "producto", true);   
        }           
    }
    // Agregamos el footer
    //$MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    //include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


###############3#
function listado_ape() {
    global $ses_usr_id;
    global $rad_servicio, $servicio, $accionv, $flag;
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO1);
    $MiTemplate->set_var("TEXT_SUB_TITULO",TEXT_SUB_TITULO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
    $MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
    $MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
    $MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
    $MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
    $MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);
    
    $MiTemplate->set_var("TEXT_BOTON_BUS",TEXT_BOTON_BUS);
    
	$MiTemplate->set_var("rad_servicio",$rad_servicio);
    $MiTemplate->set_var("servicio",$servicio);
    $MiTemplate->set_var("acc","PE");
    
    // Agregamos el main
    $MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03lista_prod.htm");
            
    //Recuperamos los estados
    $MiTemplate->set_block("main", "producto", "BLO_producto");
    $subtipo = "'CA'";
	$query_SV ="SELECT distinct p.id_producto, 
					p.cod_prod1, 
					p.des_larga, 
					pre.prec_costo, 
					pre.prec_valor, 
					p.prod_tipo 
				FROM productos p 
					/*LEFT*/ JOIN precios pre on pre.cod_prod1 = p.cod_prod1
				AND id_local = ".get_local_usr($ses_usr_id)." 
				JOIN codbarra cb on cb.cod_prod1 = p.cod_prod1
				WHERE prod_tipo = 'PE' 
					AND prod_subtipo in (".$subtipo.")"; 
	// para el boton de volver o cerrar
    if($flag=='Cerrar'){
        $MiTemplate->set_var('flag',$flag);
    }else{
        $MiTemplate->set_var('flag',"volver");
    }
	if ( $rq = tep_db_query($query_SV) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_producto',$res['id_producto']);
            $MiTemplate->set_var('cod_prod1',$res['cod_prod1']);
            $MiTemplate->set_var('des_corta',$res['des_larga']);
            if ( strlen($res['prec_costo']) == 0 ){
                $MiTemplate->set_var('prec_costo','-');
                $MiTemplate->set_var('ALINEACION',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_costo',formato_precio($res['prec_costo']));
                $MiTemplate->set_var('ALINEACION',ALINEACION_DER);
            }
            if ( strlen($res['prec_valor']) == 0 ){
                $MiTemplate->set_var('prec_valor','-');
                $MiTemplate->set_var('ALINEACION2',ALINEACION_CEN);
            }
            else{
                $MiTemplate->set_var('prec_valor',formato_precio($res['prec_valor']));
                $MiTemplate->set_var('ALINEACION2',ALINEACION_DER);
            }
            $MiTemplate->parse("BLO_producto", "producto", true);   
        }           
    }
    // Agregamos el footer
    //$MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    //include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}






/***********************************************************************************************/

if( $accion == 'PE' )
    listado_proveedor();
else if( $accion == 'PS' )
    listado_producto();
else if( $accion == 'SV' )
    listado_servicio();
else if( $accion == 'ver' )
    ver_ficha();
else if( $accion == 'verprov' )
    ver_proveedor();
else if( $accion == 'verprod' )
    ver_productos();
else if( $accion == 'verfp' )
    ver_ficha_pro();
else if( $accion == 'despachos' || $accion == 'instalaciones' || $accion == 'visitas')
    listado();
else if( $accion == 'apedido' )
    listado_ape();
else 
    buscador();

/**********************************************************************************************/

//include "../../includes/application_bottom.php";

?>
