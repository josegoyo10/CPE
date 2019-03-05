<?
$pag_ini = '../upload/upload.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "upload" );

/**********************************************************************************************/

function form_datos( $error ) {
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
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("BOTON",BOTON);
    if( $error == 1 )
        $MiTemplate->set_var("TEXT_ERROR",TEXT_ERROR_1);
    else if( $error == 2 )
        $MiTemplate->set_var("TEXT_ERROR",TEXT_ERROR_2);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","upload/form_1.html");

    $query = "show tables";
    $MiTemplate->set_block("main", "Tablas", "PBLTablas");
    //query_to_set_var( $query, &$MiTemplate, 1, 'PBLTablas', 'Tablas' );
    $rq = tep_db_query($query);
    if( tep_db_num_rows($rq) > 0 ) {
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('tabla',tohtml( $res[0] ));
            $MiTemplate->parse('PBLTablas', 'Tablas', true);
        }
    }

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), true);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

    include "../../includes/application_bottom.php";

}

function cargar_datos( $tabla, $userfile, $userfile_name ) {

    if( $userfile != '' && $userfile != 'none' ) {
        $nombre_archivo = date("YmdHis").'.csv';
        subir_archivo( $userfile, $nombre_archivo, DIR_UPLOAD );
    }

    tep_db_query("set autocommit = 0");
    $trx_ok = 1;

    // Revisamos el archivo para incluirlo en la tabla
    $fp = fopen(DIR_UPLOAD.$nombre_archivo, "r");

    // Leemos los nombres de las columnas del archivo
    $buffer = fgets($fp, 10000);
    $query = "insert into $tabla ( $buffer ) values ";

    while (!feof($fp)) {
        $buffer = fgets($fp, 10000);
        $linea = '';
        if( $buffer != '' ) {
            $linea=split( ",", $buffer );
            
            for( $i = 0; $i < sizeof($columnas); $i++ ) {
                if( $linea[$i] == '' )
                    $linea[$i] = 'NULL';
            }
            $valores = implode(",", $linea);
            $query_1 = "$query ($valores)";
            //writelog( $query_1 );
            $db_1 = tep_db_query($query_1);
            if( $db_1 == '' ) {
                $trx_ok = 0;
                break;
            }
        }
    }
    fclose($fp);

    if( $trx_ok ) {
        $error = 1;
        tep_db_query("commit"); 
    }
    else {
        $error = 2;
        tep_db_query("rollback");
    }

    tep_db_query("set autocommit = 1");

    unlink( DIR_UPLOAD.$nombre_archivo );

    header('Location: upload.php?error='.$error );
    tep_exit();

}

/**********************************************************************************************/

if( $action == 'up' )
    cargar_datos( $tabla, $userfile, $userfile_name );
else
    form_datos( $error );

/**********************************************************************************************/

include "../../includes/application_bottom.php";


?>