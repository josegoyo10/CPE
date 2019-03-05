# ------------------------------------------------------------
# - PC1 : Carga de datos al modelo temporal de promociones
# - Por Gisela Estay
# - BBR-i
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
require "/var/www/html/centroproy2/procesos/general.pl";

###############################################################
# carga_arch_generico 
###############################################################
sub carga_arch_generico {
    local ($Path_load, $Path_load_proc, $Prefijo, $Extension, $Extension_vrf) = @_;
    local $i=1, $j=0, $z=0, $flag=0, $rc=0, $k, $m;
    local $linea, $ok, $oko, $okv, $ncomp;
    local @a, @b, @c;
	my $array_element ;

	#Recupero los archivos de precios que hayan del local
	if (opendir(DIRHANDLE,$Path_load)) { 
		$ok = 0; 
		foreach (readdir(DIRHANDLE)){ 
			if (substr($_, 0, 7) eq $Prefijo && substr($_, -3, 3) ne ".gz") {
				push @b, $_ ; 
				$ok = 1; 
			}
		}
		closedir DIRHANDLE; 
	}
	else {
		welog("ERROR: No se puede leer el directorio $Path_load", "carga_arch_generico", 0);
		die "ERROR: No se puede leer el directorio $Path_load\n"
	}

	if (!$ok) {
		wtlog("No hay precios nuevos para local $Prefijo", "carga_arch_generico", 0);
	}

	#Ordeno el arreglo por ASCII
	@b = sort(@b);
	#Dejo sólo los que tienen su respectivo archivo con extensión $Extension_vrf 
	$ncomp = '';  
	foreach $array_element(@b){
		if ((substr($array_element, -4, 4) eq $Extension) || $Extension eq "") {
			if ($ncomp) {
				#Algún archivo anterior no tenía archivo de comprobación
				welog("El archivo $array_element será descartado debido a que el archivo anterior $ncomp no tiene comprobación ", "carga_arch_generico", 1);
			}
			else {
				$oko = 0; 
				$okv = 0; 
				for ($k=0; $k<=$#b; ++$k) {
					#busco si existe archivo de verificacion
					if ( substr($array_element, 0, 21) . $Extension_vrf eq $b[$k] ) {
						#Se encontro el archivo de verificacion
						$okv = 1;
                                		for ($m=0; $m<=$#b; ++$m) {
							#busco si existe archivo original
                                        		if ( substr($array_element, 0, 21) . $Extension eq $b[$m] ) {
								#Se encontro archivo original
                                                		push @c, $array_element ;
                                                		$oko = 1;
                                        		}
                                		}
					}
				}
				if (!$okv) {
					welog("No se encuentra archivo de comprobación para: $array_element", "carga_arch_generico", 1);
					#$ncomp = $array_element;
					#Por ahora desactivo la restriccion de no cargar los archivos posteriores
				}
                                if (!$oko) {
                                        welog("No se encuentra archivo original para: $array_element", "carga_arch_geerico", 1);
                                        #$ncomp = $array_element;
                                        #Por ahora desactivo la restriccion de no cargar los archivos posteriores
                                }

			}
		}
	}
	undef (@b);

	foreach $array_element(@c){
		wtlog("Entidad de carga :PRECIOSCOSTO, archivo de carga :$array_element", "carga_arch_generico", 0);
		if (open (ARCH, "$Path_load/$array_element")) {
			$i = 0;
			$z = 0;
			$j = 0;
			while ($linea=<ARCH>) {
				$linea =~ s/\\/\\\\/g; 
				$linea =~ s/'/\\'/g; 
			
				if ( length($linea)>39 ){
					$z++; 
					&welog ("PRECIOS [$i]: El formato del registro es erróneo <$linea>.","carga_arch_generico",-4); 
				}
				else {
					push @a, (substr($linea, 0,18)+0);
					push @a,  substr($linea, 18,4);
					push @a, (substr($linea, 22,15)+0); 
					push @a, $j;
					&myprecios(@a);
					pop @a;
					pop @a;
					pop @a;
					pop @a;
				}
				$i++;
				$j++;
			}
		}
		&wtlog("Registros Procesados = ".($i-1).", registros erroneos = $z","carga_arch_generico",0);

		#Movemos el archivo procesado a la carpeta $Path_load_proc
		rename ("$Path_load/$array_element","$Path_load_proc/$array_element");
		unlink ("$Path_load/" . substr($array_element, 0, 21) . $Extension_vrf);
	}

	undef (@a);
	undef (@c);
}

###############################################################
# nueva_carga
###############################################################
sub nueva_carga() {
	local $query, $rc;

	if (!defined($dbh)) {
		&wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr,"nueva_carga",-1);
		return -1;
	}

	#genera un idcarga nuevo N: Nuevo A: Actual (al termino de carga) C: Caduco 
	$query = "INSERT INTO car_estadocarga (fechacarga, estado) VALUES (Now(), 'N')";
	$rc = $dbh->do($query);
	$idcarga = $dbh->{'mysql_insertid'};
}

###############################################################
# sub termina_carga
###############################################################
sub termina_carga() {
	local $qry, $rc;

	#El idcarga en estado N (nuevo) - > I (actual), se marca con I para diferenciar dela carga masiva A
    $qry = "UPDATE car_estadocarga SET estado = 'I', fechafincarga=now() WHERE estado ='N'";
    $rc = $dbh->do($qry);
    &wtlog("Termina carga QRY2:$qry, rc=$rc","termina_carga",0);
}
    
###############################################################
# myprecios
###############################################################
sub myprecios(@a) {
    my @a = @_;
    local $sth, $qsql;

	if (!defined($dbh)) {
		&wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "myprecios",-1); 
		return -1;
	}
 	
	$qsql = "INSERT INTO car_precios(cod_local, cod_prod1, prec_costo, id_carga, id_cadena,id_orden) 
			 VALUES					('@a[1]',	'@a[0]',   '@a[2]',	   $idcarga, 3,   '@a[3]');";
	if ($rc = exec_sql_non_select($qsql)<=0) {
   		welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "myprecios",-2);
   	}

	return $rc;
}

###############################################################
# ------- p r i n c i p al -----------------------------------
###############################################################

sub main {
    local $i;
    my $config			= read_config();
	my $Path_load		= $config->pc1_path_load_maes;
	my $Path_load_proc  = $config->pc1_path_load_proc;
	#my $Extension		= $config->pc1_extensionpr;
	my $Extension		= ""; #Archivos vienen sin extensión
	my $Extension_vrf   = $config->pc1_extension_vrf;
	my $Tipo;

    &wtlog("inicia programa $0","MAIN","COMIENZO");	
    nueva_carga();
    
    $qry = "SELECT prefijo FROM archivos where tipo ='PRC' order by 1";
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
    while(($Prefijo) = $sth->fetchrow_array) {
		carga_arch_generico($Path_load,$Path_load_proc,$Prefijo,$Extension,$Extension_vrf);
    }

    termina_carga();

	&wtlog("finaliza programa $0","MAIN","FIN");
	db_disc();
}

main();
