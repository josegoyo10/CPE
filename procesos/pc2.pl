# ------------------------------------------------------------
# - PC2 : Traspaso de datos y Validacion de carga 
# -
# - Por Gisela Estay Jeldes
# - bbr ingenierìa
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
require "/var/www/html/centroproy/procesos/general.pl";

use constant CAT_PADRE_ELIMINADA => '_CPAD_DEL';
use constant CAT_PRODUCTOS_HUERFANOS => '_CHIJ_DEL';
use constant PRODUCTOS_ELIMINADOS_PROMO_VIG => '_PRDEL_PV';
use constant CAT_ELIMINADA_PROMO_VIG => '_CATDEL_PV';
use constant ERR_DB_CHILD_FK_REF => 1216; #<1216>-<Cannot add or update a child row: a foreign key constraint fails>
use constant ERR_DB_COL_NOT_EXIST => 1054; #No existe una columna 
use constant ERR_DB_DUP => 1062;   #<1062>-<Duplicate entry '01-1' for key 1>
use constant ERR_DB_SQL_SYNTAX => 1064;    #1064

# ***************************************************************************
#                            carga_to_trp 
# ---------------------------------------------------------------------------
# Levanta las cargas de archivos a las tablas detraspaso, usa el ultimo id
# ***************************************************************************
sub carga_to_trp {
local $qry;
local $rc;
local $sth;
local $qsql;
$flag,$flag_CATPROD,$flag_PRODUCTOS,$flag_PROVEEDORES,$flag_PRODPROV,$flag_CODBARRA;

	#lee id de carga nuevo
	$qry = "SELECT idcarga FROM car_estadocarga WHERE estado='A' limit 1";
	$sth = $dbh->prepare($qry);
	$rc = $sth->execute();
	if (($sth->err)>0) {
	&welog("Listado de ID CARGA TRP Err:<$DBI::err>-<$DBI::errstr>","carga_to_trp",-1);
	return 0;
	}
	#extrae el idcarga
	@row = $sth->fetchrow_array;
	$idcarga = @row[0];
	
	&wtlog("IDCARGA =$idcarga","carga_to_trp",0);
	
	#vacia tablas TRP
	$qry = "TRUNCATE TABLE trp_productos;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_proveedores;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_prodxprov;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_catprod;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_codbarra;";
	exec_sql_non_select($qry);
	$qry = "DELETE FROM trp_osdetprod WHERE estado_disp = 'D'";
	exec_sql_non_select($qry);
	$qry = "DELETE FROM trp_promoscat WHERE estado_disp = 'D'";
	exec_sql_non_select($qry);
	
    $flag =0;
    
    # AutoCommit => 0
    $dbh->{AutoCommit} = 0;
    #revisa los id de carga de categorias */
    $query="select MAX(idcarga) from car_catprod";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_CATPROD = @row[0];

    if($id_CATPROD==$idcarga){
     	&wtlog("Insert CATPROD","carga_to_trp",0);
	    # copia catergorias
        $qry = "INSERT INTO trp_catprod (cod_cat,cod_cat_padre,descat,cat_nivel,cat_tipo,car_estadocarga,id_cod_error,id_cadena) 
        SELECT cod_cat,cod_cat_padre,descat,cat_nivel,cat_tipo,0,NULL,id_cadena FROM car_catprod WHERE idcarga=$idcarga";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_CATPROD=1;
    }
    #revisa los id de carga de productos */
    $query="select MAX(idcarga) from car_productos";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_PRODUCTOS = @row[0];

    if($id_PRODUCTOS==$idcarga){
        &wtlog("Insert PRODUCTOS","carga_to_trp",0);
        # copia productos
        $qry = "INSERT INTO trp_productos (cod_prod1,cod_prod2,des_corta,des_larga, cod_categ, cod_propal, car_estadocarga, id_cod_error, cod_categ_ant, id_cadena,prod_tipo,prod_subtipo,stock_proveedor) 
        SELECT cod_prod1, cod_prod2, des_corta, des_larga, cod_categ, cod_propal,0,NULL,NULL, id_cadena,prod_tipo,prod_subtipo,stock_proveedor FROM car_productos WHERE idcarga=$idcarga and estado=1 ;";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_PRODUCTOS=1;
    }
    
    #revisa los id de carga de proveedores */
    $query="select MAX(idcarga) from car_proveedores";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_PROVEEDORES = @row[0];

    if($id_PROVEEDORES==$idcarga){
        &wtlog("Insert PROVEEDORES","carga_to_trp",0);
        # copia proveedores
            $qry = "INSERT INTO trp_proveedores (codprov,rutprov,nomprov,razsoc,fonocto,nombcto,emailcto,car_estadocarga) 
            SELECT codprov,rutprov,nomprov,razsoc,fonocto,nombcto,emailcto,0 FROM car_proveedores WHERE idcarga=$idcarga;";
            if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_PROVEEDORES=1;
    }	

    #revisa los id de carga de prodprov */
    $query="select MAX(idcarga) from car_prodxprov";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_PRODPROV = @row[0];

    if($id_PRODPROV==$idcarga){
        &wtlog("Insert PROD x PROVEEDORES","carga_to_trp",0);
        # copia prodxprov
            $qry = "INSERT INTO trp_prodxprov (cod_prod1,cod_prov,car_estadocarga, id_cadena) 
            SELECT cod_prod1,cod_prov,0, id_cadena FROM car_prodxprov WHERE idcarga=$idcarga;";
            if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_PRODPROV=1;
    }

    #revisa los id de carga de codbarra */
    $query="select MAX(idcarga) from car_codbarra";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_CODBARRA = @row[0];

    if($id_CODBARRA==$idcarga){
        #copia codbarra  01-08-2005
        &wtlog("Insert COD BARRA PRODUCTOS","carga_to_trp",0);
        # copia prodxprov
        $qry = "INSERT INTO trp_codbarra(cod_prod1,cod_barra,tip_codbar,cod_ppal,unid_med,car_estadocarga, id_cod_error, id_cadena) 
        SELECT cod_prod1, cod_barra, tip_codbar, cod_ppal,unid_med,0, NULL, id_cadena FROM car_codbarra WHERE idcarga=$idcarga;";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}	
    }else{
        $flag_CODBARRA=1;
    }
    
    if ($flag) {
        &welog("Los registros no pueden ser cargados","carga_to_trp",-2);
        print  "MALA la carga";
        $dbh->rollback;
        }
    else {
        &wtlog("Los registros cargados con exito","carga_to_trp",0);
        $dbh->commit;
        }
	
    # AutoCommit => 1
    $dbh->{AutoCommit} = 1;
	}#carga_to_trp

	
# ***************************************************************************
sub genera_categorias(){
    local ($nom_cat,$tipo ,$id_cadena) = @_;

	&wtlog("nombre=$nom_cat , tipo=$tipo, cadena=$id_cadena");
	$qry="UPDATE catprod SET id_catprod = '$nom_cat' , cat_tipo = '$tipo', estadoactivo='C' 
	WHERE id_catprod = '$nom_cat' AND id_cadena = $id_cadena";
	if (exec_sql_non_select($qry)>0){	return 1;}
	#&wtlog("update NO OK");

    $qry = "INSERT INTO catprod (id_catprod, id_cadena, id_cadpadre, id_catpadre,descat, cat_nivel, cat_tipo, estadoactivo) 
    VALUES ('$nom_cat', $id_cadena,  NULL, NULL, '$nom_cat', 1, '$tipo','C');";
	if (exec_sql_non_select($qry)>0){return 1;}
	#&wtlog("insert NO OK");
	
	&welog("ERROR DB: Genera categorias err:<$DBI::err>-<$DBI::errstr>","genera_categorias",-1);
	return 0;
}
	
# ***************************************************************************
# Genera BASE:
# Si las categorias base no existe las crea
# Marca todas las tablas y sus registros con estado en proceso. 
# estadoactivo="E"
# ***************************************************************************
sub genera_base {
    #local @arr_catbase = ('_CPAD_DEL','_CHIJ_DEL','_PRDEL_PV','_CATDEL_PV');
	local @arr_catbase = (CAT_PADRE_ELIMINADA,CAT_PRODUCTOS_HUERFANOS,PRODUCTOS_ELIMINADOS_PROMO_VIG,CAT_ELIMINADA_PROMO_VIG);
	local @arr_catbase_tipo = ('X','W','W','X');
	local @arr_catdescr = ('~Categorias Perdidas',
						'~Productos sin Categoria',
						'~Productos Eliminados en promociones vigentes',
						'~Categorias Eliminadas en promociones vigentes');
	local @row;
	local $id_cadena;
	local $qry;
	local $sth;
	local $rc;
	local $flag;
    $flag_CATPROD,$flag_PRODUCTOS,$flag_PROVEEDORES,$flag_PRODPROV,$flag_CODBARRA;	

	# marca todos los registros como erroneos
	$dbh->{AutoCommit} = 0;
	$flag=0;
	# actualiza con E todos los estados de tablas mencionados

    if(!$flag_CATPROD){
        $qry = "UPDATE catprod SET estadoactivo='E' WHERE cat_tipo<>'X'";
        if (exec_sql_non_select($qry)<0) {$flag=1;}
    }
    if(!$flag_PRODUCTOS){
        $qry = "UPDATE productos SET estadoactivo='E'";
        if (!exec_sql_non_select($qry)<0) {$flag=1;}
    }
    if(!$flag_PRODPROV){
        $qry = "UPDATE prodxprov SET estadoactivo='E'";
        if (!exec_sql_non_select($qry)<0) {$flag=1;}
    }
    if(!$flag_PROVEEDORES){
        $qry = "UPDATE proveedores SET estadoactivo='E'";
        if (!exec_sql_non_select($qry)<0) {$flag=1;}
    }
    if(!$flag_CODBARRA){
        $qry = "UPDATE codbarra SET estadoactivo='E'";
        if (!exec_sql_non_select($qry)<0) {$flag=1;}	
    }
    if ($flag==0) {	 $rc = $dbh->commit();	 }
    else         {  $rc = $dbh->rollback();	    return 0;}

	$dbh->{AutoCommit} = 1;
	
	
	# genera categorias clave si no existen	
	
	&wtlog("Busca cada cadena con sus padres","genera_base",0);
    $qry = "SELECT id_cadena FROM cadenas";
    $sth = $dbh->prepare($qry);

    if ( undef($rc = $sth->execute()) ) {
	    &welog("ERROR BD: Busqueda de cadenas: $DBI::errstr","genera_base",$DBI::err);
		return 0;
		}

	$i =1;
	while (@row = $sth->fetchrow_array) {
		$id_cadena =@row[0];
		for ($j=0;$j<=3;$j++) {
		#foreach (@arr_catbase, @arr_catbase_tipo ) {
			# CATEGORIAS BASE	
		    &wtlog("$i) GENERANDO CATEGORIA ($id_cadena) - @arr_catbase[$j] =>Tipo @arr_catbase_tipo[$j]","genera_base",0);
			if (!&genera_categorias(@arr_catbase[$j],@arr_catbase_tipo[$j],$id_cadena)){
				&welog("La categoria $_ no puede ser generada","genera_base",-1);
				return 0;
				}
			}#for
		$i++;			
		}#while
	&wtlog("Termina ciclo de categorias base","genera_base",0);
	
	return 1;
	}

################################## CATEGORIAS #############################################################
################################## CATEGORIAS #############################################################
################################## CATEGORIAS #############################################################
sub busca_promoscat {
	local ($codcat, $cadena) =@_;
	local $sth, $rc, $id_promo, $j;
	
$qry = "SELECT p.id_promocion
	FROM promociones p
	join grup_trabajo gt on p.id_promocion = gt.id_promocion
	join grupos_prod gp on (gt.id_grup_promo = gp.id_grup_promo AND gp.id_catprod = '$codcat' AND gp.id_cadena = $cadena)
	WHERE (p.estado='S' OR p.estado='P' OR p.estado='V' OR p.estado='W')";	
		
	$sth = $dbh->prepare($qry);
	$rc = $sth->execute();
	if (($sth->err)>0) {return 0;}
	
	$j=0;
	while($id_promo = $sth->fetchrow_array) {
		#debe almacenar en la tabla correspondiente el id de "promocionES" por cada registro de promocion
		&wtlog("categoria en promocion $idpromo","busca_promoscat",0);
		$qry = "INSERT INTO trp_promoscat (id_promocion, id_cadena, id_catprod, estadodisp) values ($id_promo, $cadena, '$codcat', 'N')";
		if (exec_sql_non_select($qry)<=0) {
			&welog("promocion <$idpromo> de la catgoria $catprod","busca_promoscat",-1);
			next;
			}
		&wtlog("promocion <$idpromo> de la categoria $catprod respaldada","busca_promoscat",0);
		$j++;
		}#while
		
	&wtlog("promociones encontradas: $j","busca_promoscat",0);
	if ($j==0) {return 0;}
	else {return 1;}
	}
# ***************************************************************************
sub estado_cat {
	local ($cod_cat, $id_cadena, $estado) = @_;
	local $qry;
	
	# IRSPROMO estadoactivo C: Cargado P: En proceso E: Error /Eliminado
	$qry ="UPDATE catprod SET estadoactivo = '$estado' WHERE id_catprod = '$cod_cat' AND id_cadena = $id_cadena";
	if (exec_sql_non_select($qry)<=0) {
		&welog("No puede actualizar el estado de la categoria <$cod_cat> a ->$estado","estado_cat",-1);
		return 0;
		}
	else {
		&wtlog("la categoria <$cod_cat> esta en estado ->$estado","estado_cat",0);
		return 1;
		}
	}
# ***************************************************************************
sub ins_catpadres_del {
local ($tipo, $cod_cat, $id_cadena, $i, $error_sql, $descat,$cat_nivel, $cat_tipo) = @_;
local $qry;
	&wtlog("CATEGORIAS PADRES ELIMINADAS ($i) : $error_sql== ".ERR_DB_CHILD_FK_REF);
	if ($error_sql==ERR_DB_CHILD_FK_REF) {
	
		if ($tipo eq "UPDATE") {
			$qry ="UPDATE catprod SET id_catpadre = '".CAT_PADRE_ELIMINADA."', id_cadpadre = $id_cadena ,  estadoactivo = 'C' 
			 WHERE id_catprod  ='$cod_cat' AND id_cadena = $id_cadena";
			}
		elsif ($tipo eq "INSERT") {
			$qry  = "INSERT INTO catprod (id_catprod, id_cadena, id_catpadre, id_cadpadre, descat, cat_nivel, cat_tipo, estadoactivo)
			 VALUES ('$cod_cat', $id_cadena, '".CAT_PADRE_ELIMINADA."', $id_cadena, '$descat', $cat_nivel, '$cat_tipo', 'C' )";
			}
			
		if (exec_sql_non_select($qry)<=0) { #fracaso
			&welog("CATEGORIAS PADRES ELIMINADAS ($i) : Error no puede ingresar la categoria a la rama base CAT_PADRE_ELIMINADA","ins_catpadres_del",-1);
			}
		else {&wtlog("CATEGORIAS PADRES ELIMINADAS ($i) : Padre => CAT_PADRE_ELIMINADA","ins_catpadres_del",0);}
		}
}

################################## PROCESA CATEGORIAS #############################################################
sub procesa_cats {
local ($i, $z);
local ($qry, $qry2, $qryupd, $qryins, $CodDBIErr);
local ($idpromo, $stradic, $err_str, $err_code) ;
local ($cod_cat, $cod_cat_padre, $descat, $cat_nivel, $cat_tipo,$id_cadena, $str_codpadre);


    # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
    $qry = "SELECT cod_cat, cod_cat_padre, descat, cat_nivel, cat_tipo, id_cadena 
    FROM trp_catprod WHERE car_estadocarga='0' ORDER BY cat_nivel ASC;";
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("PCAT : Listado de TRP Categorias Err:<$DBI::err>-<$DBI::errstr>","procesa_cats",-1);
		return 0;
		}
    # lee trp_cat_prod
	$i = 1;
	$z = 0;
    while(($cod_cat, $cod_cat_padre, $descat, $cat_nivel, $cat_tipo,$id_cadena) = $sth->fetchrow_array) {
		&wtlog("--------------- PCAT ($i) -------------","procesa_cats",0);
		#saca ' comillas dobles o simples a la descripcion
		$descat=~ s/'/\\'/g;
		#saca ' 0 y los reemplaza por NULL para dejarlos como padres de la raiz
		if (($cod_cat_padre eq "0")||($cod_cat_padre == "")){$str_codpadre="null";}
		else{$str_codpadre = "'$cod_cat_padre'";}
		#$str_codpadre = "'$cod_cat_padre'";
        # IRSPROMO actualiza por codigo si actualiza el id existe 
		$err_str = "";
	    $qryupd ="UPDATE catprod SET id_catpadre = $str_codpadre, id_catprod = '$cod_cat', id_cadpadre = $id_cadena, descat = '$descat', cat_nivel = $cat_nivel, cat_tipo = '$cat_tipo', estadoactivo = 'P'  WHERE id_catprod  ='$cod_cat' AND id_cadena = $id_cadena";
				
		if (exec_sql_non_select($qryupd,0,\$CodDBIErr)<=0) {
			# la categoria IRSPROMO al no traer codigo de error no existe 
			if ($CodDBIErr == "") {
				&wtlog("PCAT ($i): Registro de categoria $cod_cat no existe!. Debe ser insertado.","procesa_cats",0);
				$qryins = "INSERT INTO catprod (id_catprod, id_cadena, id_catpadre, id_cadpadre, descat, cat_nivel, cat_tipo, estadoactivo) 
				VALUES ('$cod_cat', $id_cadena, $str_codpadre, $id_cadena, '$descat', $cat_nivel, '$cat_tipo', 'C' )";
				
				if (exec_sql_non_select($qryins,0,\$CodDBIErr)<=0) { #fracaso
					#anota el codigo de error si no pudo insertar o actualizar
					&welog("PCAT ($i) : Error $CodDBIErr al insertar categoria id=<$cod_cat> con $qryins","procesa_cats",$CodDBIErr);
					$err_str = " id_cod_error = $CodDBIErr ";
					$z++;					
					# debe anotarse el codigo de error si es el de clave foranea debe enviarlo a la cat de padres eliminados
					&ins_catpadres_del("INSERT", $cod_cat, $id_cadena, $i, $CodDBIErr, $descat,$cat_nivel, $cat_tipo);
					}
				else { #exito
					$err_str = " car_estadocarga = 1 ";
					# IRSPROMO estadoactivo C: Cargado P: En proceso E: Error /Eliminado
					estado_cat($cod_cat, $id_cadena, 'C');
					}#else insert
				}
			else  { #si existe error debe anotar el error y si es ERR_DB_CHILD_FK_REF lo deja en CAT_PADRE_ELIMINADA
				&welog("PCAT ($i) : Error $CodDBIErr al actualizar con $qryupd","procesa_cats",-3);
				$err_str = " id_cod_error = $CodDBIErr  ";
				$z++;
				&ins_catpadres_del("UPDATE", $cod_cat, $id_cadena, $i, $CodDBIErr, $descat,$cat_nivel, $cat_tipo);
				}#else update
			}
		else {
			$err_str = " car_estadocarga  = 1 ";
			estado_cat($cod_cat,$id_cadena, 'C');
			}
			
        # los campos id_cod_error, id_promocion se completan en el modelo TRP si la actualizacion falla o hay una promocion asociada
		# cat_tipo puede ser I: Intermedio = si es nodo / T: Terminal si es hoja <== da lo mismo
		if (!busca_promoscat($cod_cat, $id_cadena)) {
			&wtlog("PCAT ($i): No existe promociones para la categoria $cod_cat","procesa_cats",0);
			}
		#actualiza estado del registro de TRP --> 'P'
		$qryupd ="UPDATE trp_catprod SET $err_str WHERE cod_cat = '$cod_cat' AND id_cadena = $id_cadena";
		&wtlog("PCAT ($i): actualiza TRP categoria sql<$qryupd>","procesa_cats",0);
		if (exec_sql_non_select($qryupd)<=0) {
			&welog("PCAT ($i): No puede actualizar el estado del registro de categoria","procesa_cats",-4);
			return 0;
			}
        $i++;
        }#while
	wtlog("PCAT : Finzalizado procesados=$i erroneos=$z","procesa_cats",0);

	return 1;
	}#sub

################################## PRODUCTOS #############################################################
	
sub ins_prodpadres_del{
local ($tipo, $cod_prod1, $id_cadena, $i, $error_sql, $cod_prod2, $des_corta, $des_larga) = @_;
local ($qry, $lastid);

	&wtlog("PRODUCTOS SIN CATEGORIAS($i) : $error_sql== ".ERR_DB_CHILD_FK_REF,"ins_prodpadres_del",0);
	if ($error_sql==ERR_DB_CHILD_FK_REF) {
	
		if ($tipo eq "UPDATE") {
			$qry ="UPDATE productos SET id_catprod = '".CAT_PRODUCTOS_HUERFANOS."', estadoactivo = 'C'  
			WHERE cod_prod1='$cod_prod1' AND id_cadena = $id_cadena";
			}
		elsif ($tipo eq "INSERT") {
			$qry  = "INSERT INTO productos (cod_prod1, id_cadena, id_catprod, cod_prod2, des_corta, des_larga, estadoactivo) 
			VALUES ('$cod_prod1', $id_cadena, '".CAT_PRODUCTOS_HUERFANOS."', '$cod_prod2', '$des_corta', '$des_larga', 'C' )";
			}
			
		if (exec_sql_non_select($qry, \$lastid)<=0) { #fracaso
			&welog("PRODUCTOS SIN CATEGORIAS($i) : Error no puede ingresar el producto a la rama base PRODUCTOS CON CATEGORIAS INEXISTENTES ","ins_prodpadres_del",-1);
			return 0;
			}
		else {#exito
			&wtlog("PRODUCTOS SIN CATEGORIAS ($i) : Padre => PRODUCTOS CON CATEGORIAS INEXISTENTES ","ins_prodpadres_del",0);
			}
		}
	if ($tipo eq "INSERT") {
		return $lastid;
		}
}
	
# ***************************************************************************
sub estado_prod {
	local ($cod_prod1, $id_cadena, $estado) = @_;
	local $qry;
	
	# IRSPROMO estadoactivo C: Cargado P: En proceso E: Error /Eliminado
	$qry ="UPDATE productos SET estadoactivo = '$estado' WHERE cod_prod1 = '$cod_prod1' AND id_cadena = $id_cadena";
	if (exec_sql_non_select($qry)<=0) {
		&welog("No puede actualizar el estado del producto <$cod_prod1> a ->$estado","estado_prod",-1);
		return 0;
		}
	else {
		&wtlog("la categoria <$cod_prod1> esta en estado ->$estado","estado_prod",0);
		return 1;
		}
	}	   
	
# ***************************************************************************
sub busca_proyeprod {
	local ($cod_prod1, $cadena) =@_;
	local $sth, $rc, $id_promo, $j;
	
	#busca promociones para el producto 
	$qry = "SELECT OS.id_os,OS.id_osdetalle,pr.id_producto,OS.cod_sap
	FROM os_detalle OS
	join productos pr on (pr.id_producto = os.id_producto AND pr.cod_prod1 ='$cod_prod1')";
	$sth = $dbh->prepare($qry);
	$rc = $sth->execute();
	if (($sth->err)>0) {return 0;}
	
	$j=0;
	while($osdetalle = $sth->fetchrow_array) {
		#debe almacenar en la tabla correspondiente el id de "os_detalle" por cada registro de promocion
		&wtlog("Producto en Os_detalle $osdetalle","busca_proyeprod",0);
		$qry = "INSERT INTO trp_osdetprod (id_os,id_osdetalle,id_cadena, cod_prod1, estadodisp) 
		values ($id_os,$id_osdetalle, $cadena, '$cod_prod1', 'N')";
		if (exec_sql_non_select($qry)<=0) {
			&welog("promocion <$osdetalle> de la catgoria $cod_prod1","busca_proyeprod",-1);
			next;
			}
		&wtlog("promocion <$osdetalle> del producto $cod_prod1 respaldada","busca_proyeprod",0);
		$j++;
		}#while
	&wtlog("Nro promociones encontradas : $j","busca_proyeprod",0);
	if ($j==0) {return 0;}
	else {return 1;}
	}

###############################procesa_prodcutos#############################################################
sub procesa_productos {
local $i, $z;
local $qryupd, $qryins, $CodDBIErr;
local $qry, $sth, $rc, $qry2, $sth1, $rc1;
local $idpromo, $stradic, $err_str, $err_code ;
local $cod_prod1, $cod_prod2, $des_corta, $des_larga, $cod_categ, $cod_propal, $car_estadocarga, $id_cadena, $cod_barra,$prod_tipo,$prod_subtipo,$stock_proveedor;
local $codsap, $id_producto, $catprod ,$pcatprod, $prod2, $corta, $larga, $propal, $tipo, $PRprod_subtipo, $stock,$copro1,$cp1,$cadena;

	#Productos ya se marcaron como 'E'

	&wtlog("Comienzo con productos marcados como Eliminados","procesa_productos",0);
    # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo sap) sus campos
    $qry = "select p.cod_prod1 as cp1,PR.cod_prod1 as codsap,p.id_catprod,p.cod_prod2,p.des_corta,
    p.des_larga,p.id_cadena,p.stock_proveedor,
    PR.cod_prod2 as prod2, PR.des_corta as corta,PR.des_larga as larga, PR.cod_propal as propal, 
     PR.stock_proveedor as stock,
    PR.cod_categ,PR.id_cadena cadena
    from trp_productos PR 
    left join productos p on (PR.cod_prod1=p.cod_prod1)
    WHERE PR.car_estadocarga='0';";
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de productos :$qry Err:<$DBI::errstr>","procesa_productos",$DBI::err);
		return 0;
		}
    # lee trp_productosaaa
	$i = 1;
	$z = 0;
while(($cp1,$codsap,$id_catprod,$cod_prod2, $des_corta, $des_larga,$id_cadena,$stock_proveedor,$prod2, $corta, $larga, $propal, $stock,$cod_categ,$cadena) = $sth->fetchrow_array) {
    	if ($codsap=="") {
    		&welog("El producto no tiene codigo sap y no es procesable $qry , $cod_prod1, $cod_prod2, $des_corta, $des_larga, $cod_categ,  $id_cadena, $cod_barra,$prod_tipo,$prod_subtipo,$stock_proveedor","","0");
        	$i++;
    		next;
	    	}
		#saca ' comillas simples a la descripcion
		$des_corta=~ s/'/\\'/g;
		$des_larga=~ s/'/\\'/g;
    if ($codsap) {

		$err_str = "";
       $qryupd = "UPDATE productos SET 
		id_catprod = '$cod_categ',cod_prod2 = '$cod_prod2',
		des_corta ='$corta',des_larga = '$larga',estadoactivo='C',prod_tipo='PS',prod_subtipo='PS',stock_proveedor='$stock' 
		WHERE cod_prod1 = '$cp1'";

		if (exec_sql_non_select($qryupd)<=0) {
			# El productos no existe no pudo ser actualizado debe ser instertado
            if ($DBI::err == "") {
            $qryins = "INSERT INTO productos (id_cadena, id_catprod, cod_prod1, cod_prod2, des_corta, des_larga, estadoactivo,prod_tipo,prod_subtipo,stock_proveedor) 
                VALUES ($cadena, '$cod_categ', '$codsap', '$prod2', '$corta', '$larga', 'C','PS','PS','$stock')";
                if (exec_sql_non_select($qryins,\$id_proveedor)<=0) { #fracaso en INSERT
                    #anota el codigo de error si no pudo insertar o actualizar
                    $err_loc = $DBI::err;
					&welog("($i) : Error $err_loc al insertar producto id=<$codsap> <$DBI::errstr> ","procesa_productos",0);
					$err_str = " id_cod_error = " . $err_loc;
					$z++;
					}
				else { #exito en INSERT :RESCATAR EL LAST_ID INSERT PARA BUSCAR PROMOCIONES
					&wtlog("($i) : ID del producto insertado =<$codsap> $Up","procesa_productos",0);
					$err_str = " car_estadocarga = 1 ";
					}#else insert
		    }else  { #si existe error debe anotar el error
				$err_str = " id_cod_error = $DBI::err  ";
				&welog("($i) : Error $DBI::err al actualizar con $qryupd","procesa_productos",0);

				$z++;
				}#else update

		}else {
			$err_str = " car_estadocarga  = 1 ";
			}
 		#actualiza estado del registro de TRP --> 'P'
		$qryupdp ="UPDATE trp_productos SET $err_str WHERE cod_prod1= '$codsap'";
		&wtlog("($i): actualiza TRP productos sql<$qryupdp>","procesa_productos",0);
		if (exec_sql_non_select($qryupdp)<=0) {
			&welog("($i): <$qryupdp> No puede actualizar el estado del traspaso <$DBI::err>-<$DBI::errstr>","procesa_productos",0);
			return 0;
			}	
        $i++;
     }	#codsap
}#while
	wtlog("Finalizado procesados=$i erroneos=$z","procesa_productos",0);
	#BORRA Los estados 'E' de la tabla IRSPROMO proveedores
	$qrydel="DELETE FROM productos WHERE estadoactivo='E'";
	if (exec_sql_non_select($qrydel)<=0) {
		&wtlog("($i): No ha eliminado productos <$DBI::err>-<$DBI::errstr>","procesa_productos",0);
		}		
return 1;
}

############################################################################################################
sub procesa_subtipos {
local $i, $z;
local $qryupd, $qryins, $CodDBIErr;
local $qry, $sth, $rc, $qry2, $sth1, $rc1;
local $idpromo, $stradic, $err_str, $err_code ;
local $cod_prod1, $cod_prod2, $des_corta, $des_larga, $cod_categ, $cod_propal, $car_estadocarga, $id_cadena, $cod_barra,$prod_tipo,$prod_subtipo,$stock_proveedor;
local $codsap, $id_producto, $catprod ,$pcatprod, $prod2, $corta, $larga, $propal, $tipo, $PRprod_subtipo, $stock,$copro1;
#query para updatiar tipos y subtipos productos especiales
    $qry = "select distinct PE.id_catprod,p.cod_prod1,p.id_catprod,c.id_catprod from productos p
    join catprod c on (p.id_catprod=c.id_catprod)
    join pedido_especial PE on (PE.id_catprod=p.id_catprod)";
	$sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de TRP Proveedores Err:<$DBI::err>-<$DBI::errstr>");
		return 0;
		}
    # lee trp_productosaaa
	$i = 1;
	$z = 0;
    while(($id_catprod, $cod_prod1) = $sth->fetchrow_array) {
      $qryupdtip = "UPDATE productos SET estadoactivo = 'C',prod_tipo='PE',prod_subtipo='CA' WHERE cod_prod1 = '$cod_prod1'";
        if (exec_sql_non_select($qryupdtip)<=0) {
			# no se puede updatiar
     		&welog("($i): No puede actualizar PROdcuto <$qryupdtip>","procesa_subtipos",0);
			return 0;
            }
     }

#query para updatiar tipos y subtipos servicios de instalacion
    $qryi = "select distinct I.id_catprod,p.cod_prod1,p.id_catprod,c.id_catprod from productos p
    join catprod c on (p.id_catprod=c.id_catprod)
    join instalaciones I on (I.id_catprod=p.id_catprod)";
	$sth = $dbh->prepare($qryi);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de TRP Proveedores Err:<$DBI::err>-<$DBI::errstr>");
		return 0;
		}
    # lee trp_productosaaa
	$i = 1;
	$z = 0;
    while(($id_catprod, $cod_prod1) = $sth->fetchrow_array) {
      $qryupdtipi = "UPDATE productos SET estadoactivo = 'C',prod_tipo='SV',prod_subtipo='IR' WHERE cod_prod1 = '$cod_prod1'";
        if (exec_sql_non_select($qryupdtipi)<=0) {
			# no se puede updatiar
     		&welog("($i): No puede actualizar PROdcuto <$qryupdtipi>","procesa_subtipos",0);
			return 0;
            }
     }

}

################################## PROVEEDORES##############################################################
sub procesa_proveedores { 
local $i, $z;
local $qry, $qry2, $qryupd, $qryins;
local $sth1, $rc1;
local $err_str, $err_code ;
local $codprov, $rutprov, $nomprov, $razsoc, $fonocto, $nombcto, $emailcto;
local $id_proveedor;

     # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
    $qry = "SELECT codprov, rutprov, nomprov, razsoc, fonocto, nombcto, emailcto
     FROM trp_proveedores t
     left join proveedores p  on (p.cod_prov = t.codprov)      
    WHERE car_estadocarga = '0';";
	
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de TRP Proveedores Err:<$DBI::err>-<$DBI::errstr>");
		return 0;
		}
    # lee trp_productosaaa
	$i = 1;
	$z = 0;
    while(($codprov, $rutprov, $nomprov, $razsoc, $fonocto, $nombcto, $emailcto) = $sth->fetchrow_array) {
		#saca ' comillas simples a la descripcion
		$nomprov=~ s/'/\\'/g;
		$razsoc=~ s/'/\\'/g;
		$nombcto=~ s/'/\\'/g;
        # IRSPROMO actualiza por codigo si actualiza el id existe 
		
		$err_str = "";
		$qryupd = "UPDATE proveedores SET rut_prov= '$rutprov', nom_prov= '$nomprov',
        razsoc_prov='$razsoc',fonocto_prov='$fonocto',nombcto_prov='$nombcto'
        ,emailcto_prov='$emailcto',estadoactivo = 'C' WHERE cod_prov = '$codprov'";
		if (exec_sql_non_select($qryupd)<=0) {
			# El proveedor no existe no pudo ser actualizado debe ser instertado
			if ($DBI::err == "") {
				&wtlog("($i) : Registro de proveedor $cod_prov no existe!. Debe ser insertado.","procesa_proveedores",0);
                $qryins = "INSERT INTO proveedores (cod_prov, rut_prov,nom_prov,razsoc_prov, fonocto_prov, nombcto_prov, emailcto_prov, estadoactivo)
				VALUES ('$codprov', '$rutprov', '$nomprov', '$razsoc','$fonocto','$nombcto','$emailcto','C')";

				if (exec_sql_non_select($qryins,\$id_proveedor)<=0) { #fracaso en INSERT
					#anota el codigo de error si no pudo insertar o actualizar
					$err_loc = $DBI::err;
					&welog("($i) : Error $err_loc al insertar proveedor id=<$codprov> <$DBI::errstr> ","procesa_proveedores",0);
					$err_str = " id_cod_error = " . $err_loc ;
					$z++;
					}
				else { #exito en INSERT :RESCATAR EL LAST_ID INSERT PARA BUSCAR PROMOCIONES
					&wtlog("($i) : ID del producto insertado =<$codprov> $Up","procesa_proveedores",0);
					$err_str = " car_estadocarga = 1 ";
					}#else insert
				}
			else  { #si existe error debe anotar el error
				$err_str = " id_cod_error = $DBI::err  ";
				&welog("($i) : Error $DBI::err al actualizar con $qryupd","procesa_proveedores",0);

				$z++;
				}#else update
			}
		else {
			$err_str = " car_estadocarga  = 1 ";
			}
			
		#actualiza estado del registro de TRP --> 'P'
		$qryupd ="UPDATE trp_proveedores SET $err_str WHERE codprov= '$codprov'";
		&wtlog("($i): actualiza TRP proveedor sql<$qryupd>","procesa_proveedores",0);
		if (exec_sql_non_select($qryupd)<=0) {
			&welog("($i): No puede actualizar el estado del traspaso <$DBI::err>-<$DBI::errstr>","procesa_proveedores",0);
			return 0;
			}
        $i++;
        }#while
	wtlog("Finalizado procesados=$i erroneos=$z","procesa_proveedores",0);
	
	#BORRA Los estados 'E' de la tabla IRSPROMO proveedores
	$qrydel="DELETE FROM proveedores WHERE estadoactivo='E'";
	if (exec_sql_non_select($qrydel)<=0) {
		&wtlog("($i): No ha eliminado proveedores <$DBI::err>-<$DBI::errstr>","procesa_proveedores",0);
		}	
	
	return 1;

}

################################## PROD X PROV ##############################################################
sub procesa_prod_x_proveedor { 
local $i, $z;
local $qry, $qry2, $qryupd, $qryins;
local $sth1, $rc1;
local $err_str, $err_code;
local $cod_prod1, $cod_prov, $id_cadena,$id_producto,$id_proveedor;

    # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
	$qry = "SELECT t.cod_prod1, t.cod_prov, t.id_cadena,p.id_producto,pv.id_proveedor
    FROM trp_prodxprov t
		join productos p on (p.cod_prod1 = t.cod_prod1 and p.id_cadena = t.id_cadena)
		join proveedores pv on pv.cod_prov = t.cod_prov 
        WHERE car_estadocarga = '0'";
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de TRP Err:<$DBI::err>-<$DBI::errstr>","procesa_prod_x_proveedor",0);
		return 0;
		}
    # lee trp
	$i = 1;
	$z = 0;
    while(($cod_prod1, $cod_prov, $id_cadena,$id_producto,$id_proveedor) = $sth->fetchrow_array) {

		$err_str = "";
		$qryupd = "UPDATE prodxprov SET id_producto= '$id_producto', id_proveedor= '$id_proveedor',
        cod_prod1='$cod_prod1',cod_prov='$cod_prov',estadoactivo='C' WHERE cod_prov = '$cod_prov' and cod_prod1='$cod_prod1'";
if (exec_sql_non_select($qryupd)<=0) {
        &wtlog("----- ($i) -");
		$qryins = "INSERT INTO prodxprov (id_producto,id_proveedor,cod_prod1, cod_prov, estadoactivo) VALUES ($id_producto,$id_proveedor,'$cod_prod1', '$cod_prov', 'C')";
        if (exec_sql_non_select($qryins)<=0) { #fracaso en INSERT
			#anota el codigo de error si no pudo insertar o actualizar
			&welog("($i) : Error $DBI::err al insertar prodxprov id=<$cod_prov> <$DBI::errstr> ","procesa_prod_x_proveedor",0);
			$err_str = " id_cod_error = $DBI::err ";
			$z++;
			}
		else { #exito en INSERT :RESCATAR EL LAST_ID 
			&wtlog("($i) : ID del producto insertado proveedor <$cod_prov> y producto <$cod_prod1> ","procesa_prod_x_proveedor",0);
			$err_str = " car_estadocarga = 1 ";
			}#else insert
}
else {
				$err_str = " car_estadocarga  = 1 ";
				}
		#actualiza estado del registro de TRP --> 'P'
		$qryupd ="UPDATE trp_prodxprov SET $err_str WHERE cod_prod1='$cod_prod1' AND cod_prov= '$cod_prov'";
		&wtlog("($i): actualiza TRP proveedor sql<$qryupd>","procesa_prod_x_proveedor",0);
		if (exec_sql_non_select($qryupd)<=0) {
			&welog("($i): No puede actualizar el estado del traspaso <$DBI::err>-<$DBI::errstr>");
			return 0;
			}
        $i++;
        }#while
	wtlog("Finalizado procesados=$i erroneos=$z","procesa_prod_x_proveedor",0);
	
	#BORRA Los estados 'E' de la tabla IRSPROMO prodxprov
	$qrydel="DELETE FROM prodxprov WHERE estadoactivo='E'";
	if (exec_sql_non_select($qrydel)<=0) {
		&wtlog("($i): No ha eliminado prodxprov <$DBI::err>-<$DBI::errstr>","procesa_prod_x_proveedor",0);
		}	
	
		
	return 1;
}
################################## COD BARRA ##############################################################
sub procesa_cod_barra{ 
local $i, $z;
local $qry, $qry2, $qryupd, $qryins, $CodDBIErr;
local $sth1, $rc1;
local $err_str, $err_code;
local $cod_prod1, $cod_barra, $tip_codbar, $cod_ppal, $unid_med, $id_producto,$pcod1,$codbarra;

    # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
# antiguo query
#SELECT t.cod_prod1, t.cod_barra, t.tip_codbar, t.cod_ppal,p_id_producto FROM trp_codbarra t left join productos p on (p.cod_prod1 = t.cod_prod1 and p.id_cadena = t.id_cadena)	WHERE car_estadocarga = '0' ORDER BY t.id_cadena ASC, t.cod_prod1 ASC
$qry = "SELECT t.cod_prod1, t.cod_barra, t.tip_codbar, t.cod_ppal, t.unid_med, p.id_producto
		FROM trp_codbarra t
		join productos p on (p.cod_prod1 = t.cod_prod1 and p.id_cadena = t.id_cadena)
		WHERE car_estadocarga = '0' ";
        # ORDER BY t.id_cadena ASC, t.cod_prod1 ASC
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de TRP COD BARRA Err:<$DBI::err>-<$DBI::errstr>","procesa_cod_barra",0);
		return 0;
		}
    # lee trp
	$i = 1;
	$z = 0;
    while(($cod_prod1, $cod_barra, $tip_codbar, $cod_ppal, $unid_med, $id_producto) = $sth->fetchrow_array) {
		&wtlog("----- ($i) ---","procesa_cod_barra",0);
		
		if ($cod_prod1 == "") {
			$err_str = " id_cod_error = 1216 "; #sin producto
		}
		else {

         $qryupd = "UPDATE codbarra SET id_producto= $id_producto, cod_ppal='$cod_ppal', unid_med='$unid_med', estadoactivo= 'C' 
					WHERE cod_prod1 ='$cod_prod1' AND cod_barra= '$cod_barra' AND tip_codbar= '$tip_codbar' ";
			if (exec_sql_non_select($qryupd)<=0) {
				#El código de barra no existe previamente, debe ser insertado
                $qryins = "INSERT INTO codbarra (id_producto,cod_barra, tip_codbar, cod_ppal, unid_med, cod_prod1,estadoactivo) 
				           VALUES ($id_producto,'$cod_barra', '$tip_codbar', '$cod_ppal', '$unid_med', '$cod_prod1', 'C')";
				if (exec_sql_non_select($qryins,0,\$CodDBIErr)<=0) { #fracaso en INSERT
					#anota el codigo de error si no pudo insertar o actualizar
					&welog("($i) : insertar cod_barra id=<$cod_prov> <$DBI::errstr> ","procesa_cod_barra",$CodDBIErr);
					$err_str = " id_cod_error = $CodDBIErr ";
					$z++;
				}
				else { #exito en INSERT :RESCATAR EL LAST_ID 
					&wtlog("($i) : ID del producto insertado proveedor <$id_proveedor> y producto <$id_producto> ","procesa_cod_barra",0);
					$err_str = " car_estadocarga = 1 ";
				}#else insert
			}
			else {
				&wtlog("($i) : ID del producto actualizado proveedor <$id_proveedor> y producto <$id_producto> ","procesa_cod_barra",0);
				$err_str = " car_estadocarga = 1 ";
			}
		}
		
		#actualiza estado del registro de TRP --> 'P'
		$qryupd ="UPDATE trp_codbarra SET $err_str WHERE cod_prod1='$cod_prod1' AND cod_barra= '$cod_barra' AND tip_codbar= '$tip_codbar'";
		&wtlog("($i): actualiza TRP codigos_barra sql<$qryupd>","procesa_cod_barra",0);
		if (exec_sql_non_select($qryupd,0,\$CodDBIErr)<=0) {
			&welog("($i): No puede actualizar el estado del traspaso <$CodDBIErr>-<$DBI::errstr>","procesa_cod_barra",$CodDBIErr);
			return 0;
			}
				
        $i++;
        }#while
	wtlog("Finalizado procesados=$i erroneos=$z","procesa_cod_barra",0);
	
	#BORRA Los estados 'E' de la tabla IRSPROMO COD_BARRA
	$qrydel="DELETE FROM codigos_barra WHERE estadoactivo='E'";
	if (exec_sql_non_select($qrydel)<=0) {
		&wtlog("($i): No ha eliminado codigos_barra <$DBI::err>-<$DBI::errstr>","procesa_cod_barra",0);
		}	
	
		
	return 1;
}

# ***************************************************************************
sub trp_centro_proyectos {
    genera_base();
    if(!$flag_CATPROD){
        if (procesa_cats()) {		&wtlog("Categorias procesadas","trp_centro_proyectos",0);} 
    }
    if(!$flag_PRODUCTOS){
        if (procesa_productos()) {		&wtlog("Productos procesados","trp_centro_proyectos",0);}
    }
    if(!$flag_PROVEEDORES){
        if (procesa_proveedores()) {	&wtlog("Proveedores procesados","trp_centro_proyectos",0);}
    }
    if(!$flag_PRODPROV){
        if (procesa_prod_x_proveedor()) {&wtlog("Producto x Proveedor procesados","trp_centro_proyectos",0);}
    }
    if(!$flag_CODBARRA){
        if (procesa_cod_barra()) {		&wtlog("Codigos de Barra de Productos procesados","trp_centro_proyectos",0);}
    }
 
}

# ***************************************************************************
sub main {

    my $config   = read_config();

    #tipo de logs
    &wtlog("inicia programa $0...","MAIN","COMIENZO");	

    #proceso principal
    
    carga_to_trp();

    trp_centro_proyectos();
	
    &wtlog("finaliza programa $0...","MAIN","FIN");
	
    #proceso principal	
    db_disc();
}# main
	
main();
