# ------------------------------------------------------------
# - PC2 : Traspaso de datos y Validacion de precios 
# -
# - Por Gisela Estay
# - bbr ingenierìa
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
require "/var/www/html/centroproy2/procesos/general.pl";

use constant ERR_DB_CHILD_FK_REF => 1216;	#<1216>-<Cannot add or update a child row: a foreign key constraint fails>
use constant ERR_DB_COL_NOT_EXIST => 1054;	#No existe una columna 
use constant ERR_DB_DUP => 1062;			#<1062>-<Duplicate entry '01-1' for key 1>
use constant ERR_DB_SQL_SYNTAX => 1064;		#1064

# ***************************************************************************
#                            carga_to_trp 
# ---------------------------------------------------------------------------
# Levanta las cargas de archivos a las tablas detraspaso, usa el ultimo id
# ***************************************************************************
sub carga_to_trp {
local $qry, $rc, $sth, $qsql, $flag, $total_reg, $count_reg;
local $max_reg = 100000; 

	#lee id de carga nuevo
	$qry = "SELECT idcarga FROM car_estadocarga WHERE estado='I' order by 1 desc limit 0,1";
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
    	
    &wtlog("Insert PRECIOS","carga_to_trp",0);
	# copia PRECIOS

	$qry = "SELECT count(*) FROM car_precios WHERE id_carga = $idcarga";
	$sth = $dbh->prepare($qry);
	$rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("ERROR en cuenta de registros a traspaso Err:<$DBI::err>-<$DBI::errstr>","carga_to_trp",-1);
		return 0;
	}
	#extrae numero de registros
	@row = $sth->fetchrow_array;
	$total_reg = @row[0];
	
	$count_reg = 0; 
	if ($total_reg) {
		while ($count_reg <= $total_reg ) {

			$qry = "INSERT INTO trp_precios (cod_local,cod_prod1,prec_costo,car_estadocarga,id_cadena,id_orden) 
					SELECT cod_local,cod_prod1,prec_costo,0, id_cadena ,id_orden
					FROM car_precios 
					WHERE id_carga = $idcarga
					ORDER BY prec_costo ASC
					LIMIT $count_reg, $max_reg;";

			$rc = exec_sql_non_select($qry); 
			if ($rc <= 0) {$flag=1;}
			# Si estan todos ok, commit or else....rollback
			if ($flag) {
				&welog("Los registros no pueden ser cargados, rc = [$rc], qry = [$qry]","carga_to_trp",-2);
				#print  "MALA la carga";
				$dbh->rollback;
			}
			else {
				&wtlog("Los registros cargados con exito","carga_to_trp",0);
				$dbh->commit;
			}
			$count_reg += $max_reg; 
		}	
	}
	else {
		&wtlog("No hay registros para insertar","carga_to_trp",0);
	}

    # AutoCommit => 1
    $dbh->{AutoCommit} = 1;
}#carga_to_trp
	
################################## procesa_UpdPrecios ################################################################
sub procesa_UpdPrecios{ 
local $i, $z, $local_ant;
local $qry, $qry2, $qryupd, $qryins;
local $sth1, $rc1;
local $err_str, $err_code ;
local $cod_prod1, $cod_local, $Pprec_costo,$prec_costo,$prec_costo, $stock ,$TPcod,$PRcod;
local $id_producto,$id_local;
   
	wtlog("Inicia proceso","procesa_UPDprecios",0);

    # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
    $qry = "select distinct TP.prec_costo 'Pprec_costo', p.id_producto, L.id_local, TP.cod_prod1 'TPcod', TP.cod_local,TP.id_orden
			from trp_precios TP
			join productos p on (p.cod_prod1 = TP.cod_prod1)
			join locales L on (L.cod_local = TP.cod_local)
			where TP.car_estadocarga = 0
			order by id_local,TP.prec_costo ASC";
	$sth = $dbh->prepare($qry);
    $rc = $sth->execute();
     
    if (($sth->err)>0) {
		&welog("Listado de TRP Precios Err:<$DBI::err>-<$DBI::errstr>");
		return 0;
		}

	$i = 1;
	$z = 0;
	$local_ant = '';

    while(($Pprec_costo,$id_producto,$id_local,$TPcod,$cod_local) = $sth->fetchrow_array) {

        #saca ' comillas simples a la descripcion
		$prec_costo=~ s/'/\\'/g;
		$prec_costo=~ s/'/\\'/g;
		$stock=~ s/'/\\'/g;

		if ($local_ant ne $cod_local) {
			$qryupdf ="UPDATE locales SET fecha_carga_precios = now() WHERE id_local=$id_local";
			&wtlog("($i): actualiza fecha en local sql<$qryupdf>","procesa_precios",0);
			if (exec_sql_non_select($qryupdf)<=0) {
				&welog("($i): No se puede insertar fecha <$qryupdf>","procesa_UpdPrecios",0);
				return 0;
			}
		}
		$local_ant = $cod_local;	

		$err_str = "";
		$qryupd = " UPDATE precios 
					SET prec_costo= $Pprec_costo 
					WHERE id_producto = $id_producto and id_local = $id_local"; 
       
	        if (exec_sql_non_select($qryupd)<=0) {
			# El  producto no existe en la tabla precios no pudo ser actualizado debe ser instertado
			if ($DBI::err == "") {
				&wtlog("($i) : Registro de producto $cod_prod1 no existe!. Debe ser insertado.","procesa_precios",0);
				$qryins = "	INSERT INTO precios (id_producto, cod_prod1, id_local, cod_local, prec_costo)
							VALUES ($id_producto,'$TPcod',  $id_local, '$cod_local', '$Pprec_costo')";
				if (exec_sql_non_select($qryins)<=0) { #fracaso en INSERT
					#anota el codigo de error si no pudo insertar o actualizar
					&welog("($i) : Error $DBI::err al insertar precio id=<$cod_prod1>","procesa_precios",0);
					$err_str = " id_cod_error = $DBI::err ";
					$z++;
				}
				else { 
					$qryup = "	UPDATE trp_precios 
								SET car_estadocarga = 1 
								WHERE cod_prod1 = '$TPcod' and cod_local = '$cod_local'"; 

					if (exec_sql_non_select($qryup)<=0) { #fracaso en UPDATE
						&welog("($i) : Error $DBI::err al actualizar estado trp_precio id=<$cod_prod1>","procesa_precios",0);
					}
				} 
            }
			else  { #si existe error debe anotar el error
				$err_str = " id_cod_error = $DBI::err  ";
				&welog("($i) : Error $DBI::err al actualizar con $qryupd","procesa_precios",0);
				$z++;
			}
		}
		else {
			$qryupe = "	UPDATE trp_precios 
						SET car_estadocarga = 1 
						WHERE cod_prod1 = '$TPcod' and cod_local = '$cod_local'"; 
			if (exec_sql_non_select($qryupe)<=0) { #fracaso en UPDATE
				&welog("($i) : Error $DBI::err al actualizar estado trp_precio id=<$cod_prod1>","procesa_precios",0);
			}
		}


       $i++;
   }#while

   $qryupe = "
		INSERT INTO log_table (programa, tipo_log, fecha, subrutina, texto, cod_err)
		SELECT distinct 'precios_c2.pl', 'ERROR', now(), 'procesa_UpdPrecios', concat('El producto ', cod_prod1, ' no existe en el catalogo. No se puede actualizar el precio ', prec_costo, ' en el local ', cod_local), 0
		FROM trp_precios WHERE car_estadocarga = 0
   ";
   if (exec_sql_non_select($qryupe)<0) { #fracaso en UPDATE
       &welog("Error $DBI::err al insertar registros en log_table","procesa_UpdPrecios",0);
   }

	wtlog("Finalizado procesados=".($i-1).", erroneos=$z","procesa_UPDprecios",0);
	return 1;
}



# ***************************************************************************
sub trp_centro_proyectos {
	if (procesa_UpdPrecios()) {		
		&wtlog(" Precios de Productos procesados","trp_centro_proyectos",0);
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
