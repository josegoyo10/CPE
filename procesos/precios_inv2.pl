# ------------------------------------------------------------
# - PC2 : Traspaso de datos y Validacion de carga 
# -
# - Por Gisela Estay Jeldes
# - bbr ingenierìa
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
require "/var/www/html/centroproy2/procesos/general.pl";
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
$flag,$flag_CATINV;

	#lee id de carga nuevo
	$qry = "select MAX(id_carga) from car_precios";
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
	$qry = "TRUNCATE TABLE trp_precios;";
	exec_sql_non_select($qry);
	
    $flag =0;
    
    # AutoCommit => 0
    $dbh->{AutoCommit} = 0;
    
    if($idcarga){
     	&wtlog("Insert CATINV","carga_to_trp",0);
	    # copia catergorias
        $qry = "INSERT INTO trp_precios (cod_prod1, cod_local, stock, car_estadocarga, id_cod_error, id_cadena, id_orden)		
        SELECT cod_prod1, cod_local, stock, 0,NULL,id_cadena, id_orden FROM car_precios WHERE id_carga=$idcarga";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}

    }else{
        $flag_CATINV=1;
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

################################## procesa STOCK ##############################################################
sub procesa_stock { 
local $i, $z;
local $qry, $qry2, $qryupd, $qryins;
local $sth1, $rc1;
local $err_str, $err_code ;
local $cod_prod1, $cod_local,$Stock, $id_producto, $id_local, $TPcod;
local $id_proveedor;

     # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
    $qry = "select distinct TP.stock 'Stock', p.id_producto, L.id_local, TP.cod_prod1 'TPcod', TP.cod_local,TP.id_orden
			from trp_precios TP
			join productos p on (p.cod_prod1 = TP.cod_prod1)
			join locales L on (L.cod_local = TP.cod_local)
			where TP.car_estadocarga = 0
			order by id_local,TP.stock ASC;";

    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("Listado de TRP stock precios Err:<$DBI::err>-<$DBI::errstr>");
		return 0;
		}
    # lee trp_precios
	$i = 1;
	$z = 0;
    while(($Stock, $id_producto, $id_local,$TPcod, $cod_local) = $sth->fetchrow_array) {
		#saca ' comillas simples a la descripcion
		$cod_prod1=~ s/'/\\'/g;
		$cod_local=~ s/'/\\'/g;
		$Stock=~ s/'/\\'/g;

		$err_str = "";
		$qryupd = " UPDATE precios 
					SET stock= $Stock 
					WHERE id_producto = $id_producto and id_local = $id_local"; 
#welog("($i) :$qryupd","primero",0);
	if (exec_sql_non_select($qryupd)<=0) {
			# El  producto no existe en la tabla precios no pudo ser actualizado debe ser instertado
			if ($DBI::err == "") {
				&welog("($i) : Registro de producto $cod_prod1 no existe!. Debe ser insertado.","procesa_stock",0);
				$qryins = "	INSERT INTO precios (id_producto, cod_prod1, id_local, cod_local, stock)
							VALUES ($id_producto,'$TPcod',  $id_local, '$cod_local', '$Stock')";
								welog("($i) :$qryins","segundo",0);
				if (exec_sql_non_select($qryins)<=0) { #fracaso en INSERT
					#anota el codigo de error si no pudo insertar o actualizar
					&welog("($i) : Error $DBI::err al insertar precio id=<$cod_prod1>","procesa_stock",0);
					$err_str = " id_cod_error = $DBI::err ";
					$z++;
				}
			#	else { 
			#		$qryup = "	UPDATE trp_precios 
			#					SET car_estadocarga = 1 
			#					WHERE cod_prod1 = '$TPcod' and cod_local = '$cod_local'"; 
			#					welog("($i) :$qryup","tercero",0);
			#		if (exec_sql_non_select($qryup)<=0) { #fracaso en UPDATE
			#			&welog("($i) : Error $DBI::err al actualizar estado trp_precio id=<$cod_prod1>","procesa_stock",0);
			#		}
			#	} 
            }
			else  { #si existe error debe anotar el error
				$err_str = " id_cod_error = $DBI::err  ";
				&welog("($i) : Error $DBI::err al actualizar con $qryupd","cuarto",0);
				$z++;
			}
		}
	#	else {
	#	#	$qryupe = "	UPDATE trp_precios 
		#				SET car_estadocarga = 1 
		#				WHERE cod_prod1 = '$TPcod' and cod_local = '$cod_local'"; 
		#	welog("($i) :$qryupe","quinto",0);
		#	if (exec_sql_non_select($qryupe)<=0) { #fracaso en UPDATE
		#		&welog("($i) : Error $DBI::err al actualizar estado trp_precio id=<$cod_prod1>","procesa_stock",0);
		#	}
	#	}
        $i++;
        }#while

			$qrytrp = "	truncate car_precios"; 
			if (exec_sql_non_select($qrytrp)<=0) { #fracaso en Truncate
				&welog("($i) : Error $DBI::err al truncate tabla car_precio","procesa_stock",0);
			}

	wtlog("Finalizado procesados=$i erroneos=$z","procesa_stock",0);
	
			$qrytrp = "	truncate trp_precios"; 
			if (exec_sql_non_select($qrytrp)<=0) { #fracaso en Truncate
				&welog("($i) : Error $DBI::err al truncate tabla trp_precio","procesa_stock",0);
			}
	
	return 1;

}

	
# ***************************************************************************
sub trp_centro_proyectos {
    if(!$flag_CATINV){
        if (procesa_stock()) {	&wtlog("Stock procesados","trp_centro_proyectos",0);}
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
