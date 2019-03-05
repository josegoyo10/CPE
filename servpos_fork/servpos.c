
/* Inclusion de ambiente de operacion */
#include "servpos.h"

void bbr_load_config(void);
void bbr_config_get2tokens( char *, char *, char *);
void bbr_end_program( char *, int );

void bbr_open_server_local( int );
int bbr_read_tcp( int, char *, int );
void bbr_send_tcp( int, char *, int );
void bbr_signal_func( int );
//void bbr_send_mail( char *, char * );


void kill_process( int );
void Cerrar_Socket( int );
void bbr_my_clean_up( int);

int procesa_datos( int );
int verifica_local(int ,long );
int verifica_fecha_venc( long );
int generarOt(long );
void procesaC2( char *, int , long ,char *);

int bbr_mysql_connect( void );
void bbr_mysql_disconnect( void );
void bbr_mysql_query( char * );
/**************************************************************************/
/*           Declaracion de variables globales                            */
/**************************************************************************/
//REG_FEEDBACK Reg_feedback;               /* Estructura feedback           */
//BLOQ_SKU Bloq_sku;                       /* Estructura bloque de sku      */
//BLOQ_SEC Bloq_sec;                       /* Estructura bloque de seccion  */
//FCB Fcb;                                 /* Estructura de FCB del feedback*/
//char Buf_fb[MAX_BUFFER_TCP];             /* Buffer de feedback p-total    */
int Tcp_port;                            /* Puerto de listen del servicio */
int Sock_listen = -1;                    /* Socket de listen              */
int Sock_work = -1;                      /* Socket de trabajo             */
char Mail_subject[256+1];                /* Subject del e-mail            */
char Mail_msg[512+1];                    /* Cuerpo del e-mail             */
char Mail_from[64+1];                    /* Remitente del e-mail          */
char Mail_to[30][64+1];                  /* Matriz de destinatarios de un */
                                         /* e-mail                        */
MYSQL       Mysql;                       /* Registro de mysql             */
MYSQL_ROW   Row, Row2;                   /* Registro de bd leido          */
MYSQL_RES   *Result, *Result2;           /* Resultado de la consulta      */


int  Cant_hijos;                         /* Cantidad de procesos hijos    */

char Mysql_ip[20];                       /* IP base datos mysql           */
char Mysql_db[MAX_MYSQL_DB];             /* Nombre base datos mysql       */
char Mysql_user[MAX_MYSQL_USER];         /* usuario de conexion mysql     */
char Mysql_pwd[MAX_MYSQL_USER];          /* Password de conexion mysql    */

/* Programa principal */
int main( int argc, char *argv[] ) {
    char msg_time[32], *p_msg_time;
    int rc;

    /* Se setea funcion de interrupcion ante eventos externos CTRL/C */
    /* para finalizar el programa                                    */
    signal( SIGINT, bbr_my_clean_up );
    /* Para cerrar las conexiones de forma correcta ante un KILL del */
    /* proceso se debe limpiar las conexiones y cierre de archivos   */
    signal( SIGTERM, bbr_my_clean_up );
    /* No se deberia caer por estas segnales (ALARM PIPE) pero segun */
    /* (JKL) en el SCO-UNIX se caia por esta segnal !?               */
    signal( SIGALRM, bbr_signal_func );
    signal( SIGPIPE, bbr_signal_func );

    /* Se carga la configuracion de parametros requerida */
    bbr_load_config();
    /* Se habilita logging */
    open_logs(1);

    /* Se levanta el servicio para los clientes TCP/IP */
    bbr_open_server_local( WITH_LISTEN );
    wtlog("(main) Socket modo passivo");

    /* Dejar socket en modo pasivo */
    /*if ( ( Sock_listen = so_passive( Tcp_port, MAX_LISTEN_TCP ) ) < 0 ) {
        bbr_end_program( "(main) Error apertura socket listen local", 1 );
        }*/


    wtlog( "(mysql) DB   = %s", &Mysql_db[0] );
    wtlog( "(mysql) IP   = %s", &Mysql_ip[0] );
    wtlog( "(mysql) USER = %s", &Mysql_user[0] );
    wtlog( "(mysql) PWD  = %s", &Mysql_pwd[0] );

    /* Inicializo el manejador de conexion */
    mysql_init( &Mysql );
    wtlog( "(mysql) Inicializando.... ok!" );


    wtlog( "(mysql) Conectando a mysql..." );
    /* Entro a ciclo para conectarme al servidor de mysql */
    while ( bbr_mysql_connect() == 0 ) {
        sleep( 10 );
        welog( "(mysql) Re-intentando conexion a mysql..." );
    }
    /* Se establece la conexion con la base de datos*/
    wtlog( "(mysql) Conexion.... OK!" );


    /* Se setea la cantidad de hijos activos en cero */
    Cant_hijos = 0;
    /* Asignar la fn. kill_process a la segnal SIGCHLD */
    (void)signal( SIGCHLD, kill_process );


    /* Se entra a un ciclo a la espera de requerimientos de servicio */
    FOREVER {
        wtlog( "(fork) inicio Ciclo, Cant_hijos = %d", Cant_hijos );

        /*if ( Cant_hijos == MAX_NUM_HIJOS ) {
            wtlog( "(fork) Numero maximo de hijos %d alcanzado = %d hijo(s)", MAX_NUM_HIJOS, Cant_hijos );
            sleep(1);
            continue;
            }*/
            /*if num hijos*/

        /* Se envia mensaje de atencion */
        memset( msg_time, 0, 32 );
        /* Se obtiene fecha y hora actual y se traspasa a mensaje */
        /* de impresion  DD/MM/AAAA HH:MM:SS                      */
        p_msg_time = bbr_get_time( msg_time, 1 );
        wtlog( "(main) Servicio activo %s", p_msg_time );

        wtlog("(main) acepta nueva conexion");
        /* Aceptar conexion */
        Sock_work = so_accept( Sock_listen );
        if ( Sock_work < 0 ) {
            wtlog( "(fork) Error al aceptar conexion, continuar" );
            continue;
            }   /* del if (Sock_work... */

        wtlog("(forK) acepto conexion... crea un hijo");
        /* Crear proceso hijo para atender al cliente */
        switch( fork() ) {
            case 0 : 
                /* proceso hijo */
                wtlog( "(fork) PROCESO HIJO" );
                so_close( Sock_listen );
                (void) signal( SIGTERM, Cerrar_Socket );
                exit( procesa_datos( Sock_work ) );
            case -1: 
                wtlog( "(fork) ERROR" );
                so_close( Sock_work );
                break;
            default: 
                /* proceso padre */
                wtlog( "(fork) PROCESO PADRE" );
                Cant_hijos++;
                wtlog( "(fork) Cant_hijos = %d", Cant_hijos );
                so_close( Sock_work );
                break;
            }   /* del switch */

        }/*FOREVER*/

    /* Se cierra socket de trabajo */
    so_close( Sock_work );
    Sock_work = -1;

    /* Se desconecta de la base de datos */
    bbr_mysql_disconnect();

    exit( 0 );

    }


/*****************************************************************
******************************************************************
PROCESAMIENTO PRINCIPAL
******************************************************************
******************************************************************/

/********** f u n c i o n e s    p r o c e s a m i e n t o    ***********/
int procesa_datos (int sock) {
	HEADER hreq;
	char aux[200], error_glosa_aux[44];
	char buff[SIZE_HEADER+1];
	char *buff_resp, *buff_data, *buff_final;
	char tipo[3], datetime[30], indicat;
	long nro_cotizacion, aux_long=0, boleta;
	int rc, len_header=0, len_data=0, len_resp=0, len_final=0, cod_local;

	REG_PLU prod;
	int i=0, num_rows, num_fields, consumo, decimales;
	unsigned long *lengths;
	long precio, descuento, cantidad_long;
	char query[512], campo[50];
	

	wtlog("procesa_datos : inicio ");

	//wait
    rc = so_wait( sock, MAX_TIMEOUT );
    if ( rc < 0 ) {
        /* Se finaliza */
        bbr_my_clean_up( 0 );
        return( -1 );
        }

	// lee header
	memset( (char *) &hreq, '0', SIZE_HEADER );
	
    len_header = bbr_read_tcp( sock, (char *) &hreq, SIZE_HEADER );
	if ( len_header <= 0 ) {
		welog("procesa_datos : el header no se ha leido correctamente rc=%d",len_header);
		return( -1 );
		}

	memset( buff, 0, sizeof(buff) );
	memcpy( (char *) &buff, (char *) &hreq, SIZE_HEADER);
	wtlog("procesa_datos : HEADER recibido [%s] : %d", buff, len_header);


	//obtengo codigo de BOLETA
	memset(aux,0,sizeof(aux));
	memcpy( (char *) &aux, (char *) &hreq.boleta, 10 );
	boleta = atol(aux);
	wtlog("procesa_datos : header boleta aux : %aux : atol(%aux)ato hreq : %hreq.boleta : %ld   :boleta : %boleta",boleta);

	//obtengo DATETIME YYMMDDHHMMSS  12
	memset(datetime,0,sizeof(datetime));
	memcpy( (char *) &datetime, (char *) &hreq.fecha, 12 );
	wtlog("procesa_datos : header datetime: %s",datetime);

	//obtengo codigo de LOCAL
	memset(aux,0,sizeof(aux));
	memcpy( (char *) &aux, (char *) &hreq.local, 3 );
	cod_local = atoi(aux);
	
	//obtiene LARGO DATA y ajusta buffer de respuesta
	memset( buff, 0, sizeof(buff) );
	memcpy( (char *) &buff, (char *) &hreq.largo_data, 4 );

	len_data = atoi(buff);
	buff_data = (char *)malloc( len_data+1 );
	
	wtlog("procesa_datos : largo de la data es %d", len_data);
	memset( buff_data, 0, len_data+1 );
	
	
	//obtiene tipo
	memset( tipo, 0, sizeof(tipo) );
	memcpy ((char *) &tipo, (char *) &hreq.tipo_trx, 2);
	wtlog("procesa_datos : tipo de trx es %s", tipo);

	// lee data
	rc = bbr_read_tcp( sock, buff_data, len_data );	
	if ( rc <= 0 ) {
		welog("procesa_datos : la data no se ha leido correctamente rc=%d",rc);
		return( -1 );
		}
		
		
	/* cod error + glosa */
	memset(error_glosa_aux, ' ',  sizeof(error_glosa_aux) );
	memcpy(error_glosa_aux,"000", 3);
		
	wtlog("procesa_datos : data leida %d : [%s] ",rc, buff_data);
	if (tipo[1]=='1'){
		wtlog("procesa_datos : la consulta es de tipo C1: COTIZACION");
		// procesa C1 : Consulta por cotizacion solo viene un numero
		// con el numero de cotizacion se buscan los productos y se envian uno a uno
		memset( buff, 0, sizeof(buff) );
		memcpy( (char *) &buff, buff_data, 12 );
		nro_cotizacion = atol(buff);
		wtlog("procesa_datos : nro_cotizacion %ld = %s", nro_cotizacion, buff);

		// no se puede consultar precios para una cotizacion que ya esta cancelada
		/******************************************************/
		wtlog("procesaC1 : la cotizacion consultada es %ld", nro_cotizacion);

		//verfica que el local de la cotizacion sea el local que viene en el header
		if (!verifica_local(cod_local,nro_cotizacion))	{
			welog("procesaC1 : La cotizacion %ld no proviene de un local valido[%d]", nro_cotizacion, cod_local);
			memset( aux, 0, sizeof(aux) );
			sprintf(aux, "001 LLa cotizacion no pertenece a este local %cod_local cod_local001 numero cot %nro_cotizacion ");
			memcpy((char *)&error_glosa_aux, aux, strlen(aux));
			wtlog("procesaC1 : Error en la consulta, se envia respuesta con error dummy:[%s]", aux);
			len_resp = 43;
			}
		else {//verifica_local OK

			// verifica vencimiento de la cotizacion, si esta vencida envia error y glosa
			if (!verifica_fecha_venc(nro_cotizacion))	{
				welog("procesaC1 : La cotizacion %ld esta vencida", nro_cotizacion);
				memset( aux, 0, sizeof(aux) );
				sprintf(aux, "001Error la cotizacion esta vencida        ");
				memcpy((char *)&error_glosa_aux, aux, strlen(aux));
				wtlog("procesaC1 : Error en la consulta, se envia respuesta con error dummy:[%s]", aux);
				len_resp = 43;
				}
			else {//verifica si no esta vencida la cotizacion
				//consulta los productos de la cotizacion OJO no se usan PLU's si no CODBARRAS
				sprintf(query, "SELECT od.cod_barra, od.osde_cantidad, od.osde_descuento, od.osde_precio , s.id_estado, (mod(od.osde_cantidad,1 )*1000 ) as decim, ind_dec FROM os s JOIN os_detalle od ON (od.id_os = s.id_os) WHERE s.id_os=%d", nro_cotizacion);
			
				//genera la consulta
				rc = mysql_query( &Mysql, query );
				wtlog("procesaC1 : resultado [%s] =  mysql_query = %d", query,rc);
				if ( rc != 0 ) {
					//no hay productos
					welog("procesaC1 : no puede rescatar productos = %d : %s",rc, mysql_error( &Mysql ));
					/* No viene respuesta hay que generarla con error */
					memset( aux, 0, sizeof(aux) );
					sprintf(aux, "001Error en la consulta de cotizacion      ");
					memcpy((char *)&error_glosa_aux, aux, strlen(aux));
					wtlog("procesaC1 : Error en la consulta , se envia respuesta con error dummy:[%s]", aux);
					len_resp = 43;

					}//if
				else {
					//agranda el buffer segun la cantidad de productos
					Result = mysql_store_result( &Mysql );
					num_rows = mysql_num_rows( Result );
					
					/* ve el tamaño del buffer */
					len_resp = num_rows*SIZE_REG_PLU;
					wtlog("procesaC1 : largo de memoria dinamica para respuesta : %d",len_resp);
				
					buff_resp = (char *)malloc(len_resp+1);
					memset(buff_resp, 0, len_resp+1);
				
					wtlog("procesaC1 : se encontraron %d productos, el largo de la respuesta sera de %d*%d = %d", num_rows,num_rows, SIZE_REG_PLU, len_resp );
				
					consumo = 0;
				
					while( Row = mysql_fetch_row( Result ) ) {
						precio=0L;
						descuento=0L;
						cantidad_long=0L;
				
				
						num_fields = mysql_num_fields( Result );
						wtlog("procesaC1 : producto %d : numero de campos = %d", i);
						lengths = mysql_fetch_lengths( Result );
				
						// por cada linea genera un registro PLU
						memset((char *)&prod,'0',SIZE_REG_PLU);
				
						// PLU 12
						memset (aux, 0, sizeof(aux));
						memset (campo, 0, sizeof(campo));
						memcpy( aux, Row[0], (int)lengths[0] );

						aux_long = atol(aux);
						wtlog("------------Revisa prod %s -- traspaso a long : %ld", aux, aux_long);
						//<2005-11-02 17:45:45> --> Revisa prod 720206251227 -- traspaso a long : 2147483647
						//bbr_ltoafix(&campo[0],13,aux_long);
						sprintf(campo, "%013s",aux);
						wtlog("procesaC1 : producto %d : plu = [%s]", i, campo);
						memcpy((char *)&prod.plu[0], campo, 12);
								
						// DESCUENTO
						memset (aux, 0, sizeof(aux));
						memcpy( aux, Row[2], (int)lengths[2] );
						descuento = atol(aux);
						wtlog("procesaC1 : producto %d : descuento %ld", i, descuento);
								
						// PRECIO 8 : CAMBIA SI LA CANTIDAD NO TIENE DECIMALES
						memset (aux, 0, sizeof(aux));
						memset (campo, 0, sizeof(campo));
						memcpy( aux, Row[3], (int)lengths[3] );
						precio = atol(aux);

						// CANTIDAD 6
						memset (aux, 0, sizeof(aux));
						memset (campo, 0, sizeof(campo));
						memcpy( aux, Row[1], (int)lengths[1] );
						cantidad_long = atol(aux);
						wtlog("procesaC1 : cantidad_long :[%ld]", cantidad_long);
						sprintf(campo, "%06ld",cantidad_long);
						wtlog("procesaC1 : producto %d : cantidad = [%s]", i, campo);
						memcpy((char *)&prod.cantidad[0], campo, 6);
				
						// IND_DEC 1 :  SE ASUME SIEMPRE COMO CANTIDAD = '0'
						memset (aux, 0, sizeof(aux));
						memcpy( aux, Row[6], (int)lengths[6] );
						prod.indicat[0] = aux[0];
						wtlog("procesaC1 : ind_dec :[%c]", aux[0]);

						// DECIM son los decimales de la cantidad
						//verifica si la cantidad es decimal
						memset (aux, 0, sizeof(aux));
						memcpy( aux, Row[5], (int)lengths[5] );
						decimales= atol(aux);
						wtlog("procesaC1 : decimales :[%d]", decimales);
						
						//DECIMAL 3
						memset (aux, 0, sizeof(aux));
						sprintf(aux, "%03.3d",decimales);
						memcpy((char *)&prod.decimal[0],aux,3);

						// 2005.11.24 cambio de formula de descuento
						wtlog("procesaC1 : producto %d : precio[%ld] - descto:[%ld]",	i,precio, descuento);
						//precio = ((precio - descuento)*cantidad_float);
						precio = ((precio - descuento));
						wtlog("procesaC1 : producto %d : precio final : [%ld]",i,precio);

						// ESTADO 
						memset (aux, 0, sizeof(aux));
						memcpy( aux, Row[4], (int)lengths[4] );
						
						if (memcmp(aux,"SN",2)==0) {
							wtlog("procesaC1 : No se puede consultar sobre una cotizacion [%d] anulada (con estado=SN)", nro_cotizacion);
							memset( aux, 0, sizeof(aux) );
							sprintf(aux, "001Error la cotizacion fue anulada         ");
							memcpy((char *)&error_glosa_aux, aux, strlen(aux));
							wtlog("procesa : Error en la consulta, se envia respuesta con error dummy:[%s]", aux);
							len_resp = 43;
							break;
							}
							

						if (memcmp(aux,"SP",2)==0) {
							wtlog("procesaC1 : No se puede consultar sobre una cotizacion [%d] ya cancelada (con estado=SP)", nro_cotizacion);
							memset( aux, 0, sizeof(aux) );
							sprintf(aux, "001Error la cotizacion ya fue pagada       ");
							memcpy((char *)&error_glosa_aux, aux, strlen(aux));
							wtlog("procesa : Error en la consulta, se envia respuesta con error dummy:[%s]", aux);
							len_resp = 43;
							break;
							}

						//obtiene precio sin descuentos
						//wtlog("procesaC1 : producto %d : precio final = %ld : precio[%ld] - (descto:[%ld] / cantidad[%lf])",
						//	i,(precio - (descuento/cantidad_float)), precio, descuento, cantidad_float);
						//precio = precio - (descuento/cantidad_float);
						

						// PRECIO FINAL
						sprintf(campo, "%08ld",precio);
						wtlog("procesaC1 : producto %d : precio = [%s]", i, campo);
						memcpy((char *)&prod.precio[0], campo, 8); // 2005.10.26 se corrige el error del precio antes eran pasado solamente 6 bytes 
						
						// 2005.10.26 comentado no va el descuento
						//wtlog("procesaC1 : producto %d : descuento = [%s]", i, aux);
				
						//almacena en el buffer la respuesta por cada producto
						memcpy(buff_resp + consumo, (char *)&prod, SIZE_REG_PLU);
						wtlog("procesaC1 : respuesta = [%s]", buff_resp);
						consumo = consumo + SIZE_REG_PLU;
						wtlog("procesaC1 : consumo final = %d", consumo);
						i++;
						}
					wtlog("procesaC1 : Productos de la cotizacion nro [%ld] : %d producto(s)", nro_cotizacion, i);
					}//else if mysql rc
				/*****************************************************/
				if (len_resp > 0)  {
					wtlog("procesa_datos : respuesta desde CONSULTA DE COTIZACION [%s]",buff_resp);
					}		
				}//verifica vencimiento  nro_cotizacion
			}//verifica_local	
		}//tipo 1
	else if (tipo[1]=='2'){
		wtlog("procesa_datos : la consulta es de tipo C2: PAGO");
		// procesa C2 : Pago , 3 bytes cod_error + n*12 cotizaciones
		// marca cada cotizacion como pagada
		procesaC2(buff_data, len_data, boleta, datetime);
		len_resp=0;
		}
		
	wtlog("procesa_datos : requerimiento C1 trae %d bytes de respuesta",len_resp);
	//marca el header de respuesta con el largo

        wtlog("procesa_datos : header copiado");

	if (len_resp>0) 
		len_final = SIZE_HEADER + 43 + len_resp;
	else
		len_final = SIZE_HEADER;
		
	wtlog("procesa_datos : el buffer final es de %d bytes", len_final);
	
	/* el copia el header y le agrega el largo final de la respuesta sin el header */
	memset(aux, 0,  sizeof(aux) );
	sprintf(aux, "%04d", len_final-SIZE_HEADER);
	memcpy((char *)&hreq.largo_data, aux, 4);
	
	/* aumenta el buffer al largo final +1 y le copia el header */
	buff_final = (char *) malloc(len_final+1);
	memset(buff_final, 0, len_final+1);
	memcpy (buff_final, (char *)&hreq, SIZE_HEADER );
	wtlog("procesa_datos : buffer final con header copiado : [%s]", buff_final);
	
	/* si trae respuesta le agrega el cod error y la glosa */
	if (len_resp>0)	{
		memcpy (buff_final + SIZE_HEADER , error_glosa_aux, 43);
		wtlog("procesa_datos : agrega cor_error y glosa [%s] : %d", buff_final, (SIZE_HEADER+43) );
		
		if (buff_resp!=NULL) {
			wtlog ("procesa_datos : Buffer respuesta : [%s]", buff_resp);
			memcpy (buff_final + SIZE_HEADER+43 , buff_resp, len_resp);
			wtlog("procesa_datos : copiando %d bytes a la data de respuesta [%s]", len_resp, buff_final);
			/* libera memoria del buffer */
			free (buff_resp);
			wtlog("procesa_datos : puntero respuesta liberado");
			}
		}//len_resp
	
	wtlog("procesa_datos : envio al controlador [%s]", buff_final);
	// responde 
	bbr_send_tcp( sock, buff_final, (len_final+SIZE_HEADER) );
	wtlog("procesa_datos : mensaje de respuesta enviado al controlador");

	if (buff_data!=NULL) {
		free (buff_data);
		wtlog("procesa_datos : puntero datos liberado");
		}
	if (buff_final!=NULL) {
		free (buff_final);
		wtlog("procesa_datos : puntero final liberado");
		}

	/* Se espera que el proceso cliente finalize la conexion */
    rc = so_wait( sock, MAX_TIMEOUT );    
    wtlog( "procesa_datos : cierro socket server so_wait = %d", rc  );

	so_close( sock );

	return(1);
	}

int verifica_local(int local,long cotizacion){
	char qry[512], aux[20];
	unsigned long *lengths;
	int rc=0, num_fields, num_rows;

	memset(qry, 0, sizeof(qry));

	sprintf(qry,"SELECT IF(cod_local_pos = %d,1,0) ok FROM os join locales l on os.id_local = l.id_local WHERE id_os = %ld",local,cotizacion);
	wtlog("verifica_local : query : %s", qry);

	rc = mysql_query( &Mysql, qry );
	if(rc != 0) {
		welog("verifica_local : no puede verificar el local = %d : %s",rc, mysql_error( &Mysql ));
		return 0;
		}

	//Result = mysql_store_result( &Mysql );
	Result = mysql_store_result( &Mysql );

	wtlog("verifica_local : Result Query= %d", (int)Result );

	num_rows = mysql_num_rows( Result );
	wtlog("verifica_local : numero de filas= %d", num_rows);

	if (num_rows==0){
		welog("verifica_local : filas retornadas = 0 no puede verificar la cotizacion %ld", cotizacion);
		return 0;
		}

	Row = mysql_fetch_row( Result );
	num_fields = mysql_num_fields( Result );
	wtlog("verifica_local : numero de campos = %d", num_fields);
	lengths = mysql_fetch_lengths( Result );
	wtlog("verifica_local : transfiere largo de campos");

	memset (aux, 0, sizeof(aux));

	wtlog("verifica_local : Antes de asignacion");
	memcpy( aux, Row[0], (int)lengths[0] );
	wtlog("verifica_local : Despues de asignacion");
	wtlog("verifica_local : respuesta select IF %s", aux);
	return atoi(aux);
	}//verifica_local

int verifica_fecha_venc( long cotizacion ){
	char qry[512], aux[20];
	unsigned long *lengths;
	int rc=0, num_fields, num_rows;

	memset(qry, 0, sizeof(qry));
	
	sprintf(qry,"SELECT count(*) FROM os WHERE (date(now())<= date(os_fechaestimacion)) AND id_os = %ld ",cotizacion);

	wtlog("verifica_fecha_venc : query : %s", qry);

	rc = mysql_query( &Mysql, qry );

	if(rc != 0) {
		welog("verifica_fecha_venc : no puede verificar la fecha de vencimiento = %d : %s",rc, mysql_error( &Mysql ));
		return 0;
		}

	Result = mysql_store_result( &Mysql );
	num_rows = mysql_num_rows( Result );
	
	Row = mysql_fetch_row( Result );
	num_fields = mysql_num_fields( Result );
	wtlog("verifica_fecha_venc : numero de campos = %d", num_fields);
	lengths = mysql_fetch_lengths( Result );

	memset (aux, 0, sizeof(aux));
	memcpy( aux, Row[0], (int)lengths[0] );
	wtlog("verifica_fecha_venc : cumple la fecha %s", aux);
	return atoi(aux);
	}//verifica_fecha_venc




int generarOt(long nro_cotizacion){
	char queryHist[512], querysel[512], querytrx[512], query_2[512], aux[100];
	char nc[]="USUARIO POS";
	int rc, grupo_tdesp, success=1, num_rows, num_fields;
	unsigned long *lengths, *lengths2;
	char osde_tipoprod[3], osde_subtipoprod[3];
	double osde_cantidad;
	int i,id_producto,id_tipodespacho,id_os_detalle, last_id, valid_stock;

	wtlog("generarOt : comienzo nombre de usuario %s", nc);

	/*query que inserta evento en historial*/
	memset(queryHist,0,sizeof(queryHist));
	sprintf(queryHist,"Insert into historial (id_os,ot_id,hist_fecha,hist_usuario,hist_descripcion) values (%ld,1,now(),'%s','Deja cotizacion en estado Pagada')",
		(nro_cotizacion+0) ,nc);

	rc = mysql_query( &Mysql, queryHist );
	wtlog("generarOt : Historial %s resultado:%d", queryHist, rc);

	/* Se genera las OT a partir de la OS */
	memset(querysel,0,sizeof(querysel));
	sprintf(querysel , "SELECT osde_tipoprod,osde_cantidad,osde_subtipoprod,id_producto,id_tipodespacho,id_os_detalle FROM os_detalle WHERE os_detalle.id_os = %ld ORDER BY osde_tipoprod, id_tipodespacho", nro_cotizacion);

	rc = mysql_query( &Mysql, querysel );
	if ( rc != 0 ) {
		welog("generarOt : No puede seleccionar los detalles de la OS %s mysqlerror :[%s]",querysel, mysql_error( &Mysql ));
		return 0;
		}
	wtlog("generarOt : selecciona detalles %s resultado:%d", querysel, rc);

	Result = mysql_store_result( &Mysql );
	num_rows = mysql_num_rows( Result );

	wtlog("generarOt : numero de lineas : %d", num_rows);


	grupo_tdesp = 0;
	i=0;
	while( (Row = mysql_fetch_row( Result )) && success ) {
		wtlog("generarOt %d : Fila", i);

		//agranda el buffer segun la cantidad de productos
		num_fields = mysql_num_fields( Result );
		lengths = mysql_fetch_lengths( Result );

		// osde_tipoprod
		memset(osde_tipoprod, 0, sizeof(osde_tipoprod));
		memcpy( osde_tipoprod, Row[0], (int)lengths[0] );

		wtlog("generarOt %d : OSDE_TIPOPROD [%s]",i,osde_tipoprod);

		//osde_cantidad
		memset(aux, 0, sizeof(aux));
		memcpy( aux, Row[1], (int)lengths[1] );
		osde_cantidad = atof(aux);

		wtlog("generarOt %d : OSDE_CANTIDAD [%d]",i,osde_cantidad);

		//osde_subtipoprod
		memset(osde_subtipoprod, 0, sizeof(osde_subtipoprod));
		memcpy(osde_subtipoprod, Row[2], (int)lengths[2] );

		wtlog("generarOt %d : OSDE_SUBTIPOPROD [%s]",i,osde_subtipoprod);

		//id_producto
		memset(aux, 0, sizeof(aux));
		memcpy( aux, Row[3], (int)lengths[3] );
		id_producto = atoi(aux);
		
		wtlog("generarOt %d : ID_PRODUCTO [%d]",i,id_producto);

		//id_tipodespacho
		memset(aux, 0, sizeof(aux));
		memcpy( aux, Row[4], (int)lengths[4] );
		id_tipodespacho = atoi(aux);

		wtlog("generarOt %d : ID_TIPODESPACHO [%d]",i,id_tipodespacho);

		//id_os_detalle
		memset(aux, 0, sizeof(aux));
		memcpy( aux, Row[5], (int)lengths[5] );
		id_os_detalle = atoi(aux);

		wtlog("generarOt %d : ID_OS_DETALLE [%d]",i,id_os_detalle);

		//COMIENZA EL PROCESO
		if (memcmp(osde_tipoprod,"PE",2)==0){

			wtlog("generarOt %d : PE",i);

			/*
			$querytrx ="INSERT INTO ot (id_estado, id_os,                 id_tipodespacho,                                                       ot_tipo, ot_fechacreacion) 
			VALUES                         ('EC',    ".($id_os+0).", ".$res_1['id_tipodespacho'].", 'PE',     now()) ";
			$success = $success && tep_db_query($querytrx);
			$last_id = tep_db_insert_id( '' );
			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);
			$grupo_tdesp = 0;*/

			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "INSERT INTO ot (id_estado, id_os, id_tipodespacho, ot_tipo, ot_fechacreacion,ot_freactivacion) VALUES ('EC',%ld,%d, 'PE',now(),now()) ", (nro_cotizacion+0), id_tipodespacho);

			wtlog("generarOt %d : insert ot %s",i,querytrx);

			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			

			last_id = mysql_insert_id(&Mysql);

			wtlog("generarOt %d : id nuevo %ld",i,last_id);

			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx,"UPDATE os_detalle SET ot_id = %d WHERE id_os_detalle = %d",(last_id+0),(id_os_detalle+0));
			wtlog("generarOt %d : update ot detalle %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			grupo_tdesp = 0;

			wtlog("generarOt %d : SUCCESS ? [%d]",i,success);
			}//PE
		if ( (memcmp(osde_tipoprod,"SV",2)==0) && (memcmp(osde_subtipoprod,"DE",2)!=0)  && (memcmp(osde_subtipoprod,"AR",2)!=0)  && (memcmp(osde_subtipoprod,"SE",2)!=0)){
			wtlog("generarOt %d : SV",i);
			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "INSERT INTO ot (id_estado, id_os,id_tipodespacho,ot_tipo, ot_fechacreacion,ot_freactivacion) VALUES ('VC',  %ld, %d, 'SV', now(),now()) ", (nro_cotizacion+0), id_tipodespacho);
			wtlog("generarOt %d : insert ot %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			last_id = mysql_insert_id(&Mysql);
			wtlog("generarOt %d : id nuevo %ld",i,last_id);

			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "UPDATE os_detalle SET ot_id = %d WHERE id_os_detalle = %d",(last_id+0),(id_os_detalle+0));
			wtlog("generarOt %d : actualiza ot detalle %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			grupo_tdesp = 0;

			wtlog("generarOt %d : SUCCESS ? [%d]",i,success);
			}//SV

		if ( (memcmp(osde_tipoprod,"SV",2)==0) && (memcmp(osde_subtipoprod,"AR",2)==0)){
			wtlog("generarOt %d : SV",i);
			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "INSERT INTO ot (id_estado, id_os,id_tipodespacho,ot_tipo, ot_fechacreacion,ot_freactivacion) VALUES ('AP',  %ld, %d, 'SV', now(),now()) ", (nro_cotizacion+0), id_tipodespacho);
			wtlog("generarOt sv ar %d : insert ot %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			last_id = mysql_insert_id(&Mysql);
			wtlog("generarOt sv ar %d : id nuevo %ld",i,last_id);

			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "UPDATE os_detalle SET ot_id = %d WHERE id_os_detalle = %d",(last_id+0),(id_os_detalle+0));
			wtlog("generarOt sv ar %d : actualiza ot detalle %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			grupo_tdesp = 0;

			wtlog("generarOt sv ar %d : SUCCESS ? [%d]",i,success);
			}//SV AR

		if ( (memcmp(osde_tipoprod,"SV",2)==0) && (memcmp(osde_subtipoprod,"SE",2)==0)){
			wtlog("generarOt %d : SV",i);
			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "INSERT INTO ot (id_estado, id_os,id_tipodespacho,ot_tipo, ot_fechacreacion,ot_freactivacion) VALUES ('SG',  %ld, %d, 'SV', now(),now()) ", (nro_cotizacion+0), id_tipodespacho);
			wtlog("generarOt sv ar %d : insert ot %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			last_id = mysql_insert_id(&Mysql);
			wtlog("generarOt sv ar %d : id nuevo %ld",i,last_id);

			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "UPDATE os_detalle SET ot_id = %d WHERE id_os_detalle = %d",(last_id+0),(id_os_detalle+0));
			wtlog("generarOt sv se %d : actualiza ot detalle %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			grupo_tdesp = 0;

			wtlog("generarOt sv se %d : SUCCESS ? [%d]",i,success);
			}//SV SE


		if (memcmp(osde_tipoprod,"PS",2)==0) {
			/*
			if($grupo_tdesp != $res_1['id_tipodespacho']) {
				$querytrx ="INSERT INTO ot (id_estado, id_os,  ot_tipo, ot_fechacreacion, id_tipodespacho) VALUES ('PC',    ".($id_os+0).", 'PS',    now(),".($res_1['id_tipodespacho']+0).") ";
				$success = $success && tep_db_query($querytrx);
				$last_id = tep_db_insert_id( '' );
				}

			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);

			$grupo_tdesp = $res_1['id_tipodespacho'];
			*/

			wtlog("generarOt %d : PS",i);
			if (grupo_tdesp != id_tipodespacho)	{
				memset(querytrx,0,sizeof(querytrx));
				sprintf(querytrx ,"INSERT INTO ot (id_estado, id_os, ot_tipo, ot_fechacreacion, id_tipodespacho,ot_freactivacion) VALUES ('PC', %ld , 'PS',    now(),%d,now()) ",(nro_cotizacion+0), id_tipodespacho);
				wtlog("generarOt %d : insert ot %s",i,querytrx);
				rc = mysql_query( &Mysql, querytrx );
				success = success && !rc;
				last_id = mysql_insert_id(&Mysql);
				}
			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx, "UPDATE os_detalle SET ot_id = %d WHERE id_os_detalle = %d",(last_id+0),(id_os_detalle+0));
			wtlog("generarOt %d : actualiza ot detalle %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			grupo_tdesp = id_tipodespacho;
			wtlog("generarOt %d : grupo_tdesp [%d]",i,id_tipodespacho);
			wtlog("generarOt %d : SUCCESS ? [%d]",i,success);
			}//PS

		//rescata el last_insert_id
		if (last_id!=0) {
				/*
				$queryHist="Insert into historial (id_os,ot_id,hist_fecha,hist_usuario,hist_descripcion) values (".($id_os+0).",".($last_id+0).",now(),'$nc',' Inserta OT Nº(".($last_id+0).") a Cotización Nº ".($id_os+0)."')";
				$hist = tep_db_query($queryHist);
				*/
				memset(queryHist,0,sizeof(queryHist));
				sprintf(queryHist,"Insert into historial (id_os,ot_id,hist_fecha,hist_usuario,hist_descripcion) values (%ld,%d,now(),'%s',' Inserta OT Nº(%d) a Cotización Nº %ld'",(nro_cotizacion+0), (last_id+0),nc,(last_id+0),(nro_cotizacion+0) );
				rc = mysql_query( &Mysql, queryHist );
				wtlog("generarOt %d : Historial %s resultado:%d",i,  queryHist, rc);
				}
		//Generamos la rebaja de Stock para el producto del listado sólo si está marcado en la tabla tipo_subtipo
		/*
		$query_2="SELECT valid_stock FROM productos p join tipo_subtipo t on t.prod_tipo = p.prod_tipo and t.prod_subtipo = p.prod_subtipo WHERE id_producto = " . ($res_1['id_producto']+0) ; 
		$tdq_2 = tep_db_query($query_2);
		$res_2 = tep_db_fetch_array( $tdq_2 );
		if ($res_2['valid_stock']) {
				   $querytrx ="UPDATE productos SET stock_proveedor = stock_proveedor - " . ($res_1['osde_cantidad']+0) . " WHERE id_producto = " . ($res_1['id_producto']+0);
				   $success = $success && tep_db_query($querytrx);

		*/
		memset(query_2,0,sizeof(query_2));
		sprintf(query_2,"SELECT valid_stock FROM productos p join tipo_subtipo t on t.prod_tipo = p.prod_tipo and t.prod_subtipo = p.prod_subtipo WHERE id_producto = %d ",(id_producto+0));

		wtlog("generarOt %d : select productos %s",i,query_2);
		rc = mysql_query( &Mysql, query_2 );
		Result2 = mysql_store_result( &Mysql );
		if (Row2 = mysql_fetch_row( Result2 )){
			lengths2 = mysql_fetch_lengths( Result2 );
			memset (aux, 0, sizeof(aux));
			memcpy( aux, Row[0], (int)lengths[0] );
			valid_stock = atoi(aux);
			memset(querytrx,0,sizeof(querytrx));
			sprintf(querytrx,"UPDATE productos SET stock_proveedor = (stock_proveedor - %.2f) WHERE id_producto = %d ",(osde_cantidad+0),(id_producto+0) );
			wtlog("generarOt %d : update productos %s",i,querytrx);
			rc = mysql_query( &Mysql, querytrx );
			success = success && !rc;
			wtlog("generarOt %d : update productos SUCCESS [%d]",i,success);
			}
		i++;
		}//while
	return success;	
	}//generarOt


/* Procesa requerimientos C2 : Actualiza pagos */
void procesaC2( char *buff_data, int len_data, long boleta,char *datetime) {
	char estado[4], aux[200], fechahora[30];
	int i, e, rc=0, consumo=0, cod_error, size_cotizacion=12;
	long cotizacion;
	char query[512];

	if (len_data<=0){
		welog("procesaC2 : no hay data en el requerimiento de pagos");
		return;
		}
	//fechahora YYYYMMDDHHMMSS

	memset (fechahora, 0, sizeof(fechahora));
	memcpy (fechahora, datetime, 12);
	wtlog("procesaC2 : la boleta es %ld y la fechahora es %s", boleta, fechahora);

	//extrae 3 bytes error

	memset (aux, 0, sizeof(aux));
	memcpy (aux, buff_data, 3);
	cod_error = atoi(aux);
	memset (estado, 0, sizeof(estado));

	if (cod_error==1){ //error en PLU's id_estado=SN : anulada
		strcpy (estado, "SN");
		wtlog("procesaC2 : las cotizaciones son erroneas");
		}
	else { // estado final OS id_estado=SP : pagada
		strcpy (estado, "SP");
		wtlog("procesaC2 : las cotizaciones estan ok");
		}

	consumo = 3;
	i=0;
	e=0;
	while(consumo<len_data) {
		memset (aux, 0, sizeof(aux));
		memcpy((char *)&aux[0], buff_data+consumo , size_cotizacion);
		wtlog("procesaC2 : Cotizacion recibida [%s], consumido [%d], total data : %ld", aux, consumo, len_data);
		cotizacion = atol(aux);

		if (!verifica_fecha_venc(cotizacion))	{
			welog("procesaC2 : La cotizacion %ld esta vencida", cotizacion);
			e++;
			continue;
			}

		consumo = consumo + size_cotizacion;
		wtlog("procesaC2 : consumo : %d", consumo);

		if(cod_error!=1) { //pagos SELECT CAST('051115182413' AS DATETIME);
			rc = mysql_autocommit(&Mysql, 0);
			wtlog("procesaC2 : Desactiva el autocommit :%d", rc );
			sprintf(query ,"UPDATE os SET id_estado = '%s', os_numboleta = %ld,os_fechaboleta=CAST('%s' as DATETIME) WHERE id_os = %ld", 
				estado,boleta,fechahora,cotizacion);
			}
		else {//anulaciones
			sprintf(query ,"UPDATE os SET id_estado = '%s' WHERE id_os = %ld", estado,cotizacion);
			}
		
		wtlog("procesaC2 : Antes de query %s", query);
		rc = mysql_query( &Mysql, query );
		if (rc!=0){
			welog( "procesaC2 : al actualizar la cotizacion %s:(mysql-update) mysql_error(%s) : %s",query ,mysql_errno( &Mysql ) , mysql_error( &Mysql ) );	
			continue;
			}
		wtlog("procesaC2 : Despues de query rc:%d ", rc);	

		if (rc != 0){
	        /* update fallo */
			welog( "procesaC2 : (mysql-update) mysql_error(%s) : %s", mysql_errno( &Mysql ) , mysql_error( &Mysql ) );			
			e++;
			continue;
			}
		wtlog("procesaC2 : actualizacion ok de cotizacion %ld : estado :%s", cotizacion, estado);
		/* si era pago entonces debe generar el historial y las OT */
		if(cod_error!=1){			
			if (!generarOt(cotizacion)){
				welog("procesaC2 : Error al generar OT's aplica rollback");
				mysql_rollback(&Mysql);
				}
			else{
				wtlog("procesaC2 : OT's generadas con exito");
				mysql_commit(&Mysql);
				}
			wtlog("procesaC2 : Activa autocommit");
			mysql_autocommit(&Mysql, 1);
			}//cod_error
		i++;
		}
	wtlog("procesaC2 : procesados= %d , errores =%d",i, e);
	return;
	}

/*****************************************************************
******************************************************************
******************************************************************
******************************************************************/



/* Funcion: kill_process                                */
/* Desc: Llama a la fn. wait3 para completar el termino */
/*         del proceso hijo que acaba de hacer un exit. */
/* Salida: 1  Esta OK                                   */
void kill_process( int a ) {
    int rc;
    /*union wait status;*/
    int status;

    wtlog( "(kill_process) iniciando... " );

    while( (rc = wait3( &status, WNOHANG, ( struct rusage *)0 )) > 0 ) {
        wtlog( "(kill_process) pid = (%d)", rc );
        wtlog( "(kill_process) Cant_hijos = (%d)", Cant_hijos );
        Cant_hijos--;
        wtlog( "(kill_process) Cant_hijos = (%d)", Cant_hijos );
        }

    wtlog( "(kill_process) rc = (%d)", rc );
    (void) signal( SIGCHLD, kill_process );

    wtlog( "(kill_process) finalizando... " );

    return;

    }   /* de la fn. kill process */


/*  Funcion: Cerrar_Socket                              */
/*  Desc: Cierra el socket del proc. hijo y sale del    */
/*        programa. Esta fn. es llamada cuando el proc. */
/*        hijo recibe la se¤al SIGTERM.                 */
void Cerrar_Socket( int a ) {

    so_close( a );

    exit( 1 );
    }




/* Subrutina que carga la configuracion de parametros requerida */
void bbr_load_config( void ) {
    FILE *fp;
    char line[128], tok1[48], tok2[48];

    /* Abro archivo de configuracion */
    if ( ( fp = fopen( CONFIG_FILE, "r" ) ) == NULL ) {
        sprintf( Bbr_trace,  "bbr_load_config(): Problemas de lectura archivo %s", CONFIG_FILE);
		printf(Bbr_trace);
        bbr_end_program( Bbr_trace, 0 );
        }

    /* Seteo variables de ambiente */
    Tcp_port = 0;
	/*
    memset( Mail_from, 0, 64+1 );
    memset( Mail_to[0], 0, 64+1 );
    memset( Mail_to[1], 0, 64+1 );
    memset( Mail_to[2], 0, 64+1 );
    memset( Mail_to[3], 0, 64+1 );
	*/

    memset( &Mysql_ip[0], 0, sizeof(Mysql_ip) );
    memset( &Mysql_db[0], 0, sizeof(Mysql_db) );
    memset( &Mysql_user[0], 0, sizeof(Mysql_user) );
    memset( &Mysql_pwd[0], 0, sizeof(Mysql_pwd) );

    /* Leo cada registro del archivo y proceso su contenido */
    while ( fgets( line, 128 , fp ) != NULL ) {
        /* Descompongo cada linea en dos token's */
        bbr_config_get2tokens( line, tok1, tok2 );
        /* Comparo con cada nombre asignado como parametro para asignar valores */
        if ( strcmp( tok1, "TCP_PORT" ) == 0 )
            Tcp_port = atoi( tok2 );
		/*
        if ( strcmp( tok1, "MAIL1" ) == 0 )
            strcpy( Mail_to[0], tok2 );
        if ( strcmp( tok1, "MAIL2" ) == 0 )
            strcpy( Mail_to[1], tok2 );
        if ( strcmp( tok1, "MAIL3" ) == 0 )
            strcpy( Mail_to[2], tok2 );
        if ( strcmp( tok1, "MAIL4" ) == 0 )
            strcpy( Mail_to[3], tok2 );
        if ( strcmp( tok1, "REMITENTE" ) == 0 )
            strcpy( Mail_from, tok2 );
		*/
        if ( strcmp( tok1, "MYSQL_IP" ) == 0 )
            strcpy( Mysql_ip, tok2 );
        if ( strcmp( tok1, "MYSQL_DB" ) == 0 )
            strcpy( Mysql_db, tok2 );
        if ( strcmp( tok1, "MYSQL_USER" ) == 0 )
            strcpy( Mysql_user, tok2 );
        if ( strcmp( tok1, "MYSQL_PWD" ) == 0 )
            strcpy( Mysql_pwd, tok2 );

        }
    /* Cierro el archivo de configuracion */
    fclose( fp );

    /* Valido que existan todos los campos dentro del archivo */
    if ( Tcp_port == 0 )
        bbr_end_program( "bbr_load_config(): TCP_PORT no especificado", 0 );
          
	/*
    if ( Mail_to[0][0] == '\0' )
        bbr_end_program( "bbr_load_config(): MAIL1 no definido", 0 );
                         
    if ( Mail_to[1][0] == '\0' )
        bbr_end_program( "bbr_load_config(): MAIL2 no definido", 0 );
                         
    if ( Mail_to[2][0] == '\0' )
        bbr_end_program( "bbr_load_config(): MAIL3 no definido", 0 );
                         
    if ( Mail_to[3][0] == '\0' )
        bbr_end_program( "bbr_load_config(): MAIL4 no definido", 0 );
                         
    if ( Mail_from[0] == '\0' )
        bbr_end_program( "bbr_load_config(): REMITENTE no definido", 0 );
	*/
    if ( Mysql_ip[0] == '\0' )
        bbr_end_program( "bbr_load_config(): MYSQL_IP no definido", 1 );
    if ( Mysql_db[0] == '\0' )
        bbr_end_program( "bbr_load_config(): MYSQL_DB no definido", 1 );
    if ( Mysql_user[0] == '\0' )
        bbr_end_program( "bbr_load_config(): MYSQL_USER no definido", 1 );
    if ( Mysql_pwd[0] == '\0' )
        bbr_end_program( "bbr_load_config(): MYSQL_PWD no definido", 1 );

    return;
    }

/* Subrutina que permite rescatar los dos primeros tokens de una linea */
void bbr_config_get2tokens( char *line, char *tok1, char *tok2 ) {
    char    *p;

    /* Me salto los blancos y tabs del principio */
    for ( p = line; *p == ' ' || *p == '\t'; p++ )
        ;

    /* Si nada queda se finaliza */
    if ( *p == 0 )
        bbr_end_program( "Archivo de configuracion invalido", 0 );

    /* Se obtiene el primer token */
    while( *p != ' ' && *p != '\t' && *p != '\n' && *p != 0 ) {
        *tok1 = (char) toupper( (int) *p );
        tok1++;
        p++;
        }
    *tok1 = 0;

    /* Si solo se encuentra se finaliza */
    if ( *p == '\n' || *p == 0 )
        bbr_end_program( "Archivo de configuracion invalido", 0 );

    /* Se saltan los blancos y tabs */
    for ( ; *p == ' ' || *p == '\t'; p++ )
        ;

    /* Si solo se encuentra se finaliza */
    if ( *p == '\n' || *p == 0 )
        bbr_end_program( "Archivo de configuracion invalido", 0 );

    /* Se obtiene el segundo token */
    while( *p != ' ' && *p != '\t' && *p != '\n' && *p != 0 )
        *tok2++ = *p++;
    *tok2 = 0;

    return;

    }

/* Subrutina que permite enviar un mensaje de error al operador, al */
/* archivo de logging y finaliza la ejecucion del programa          */
void bbr_end_program( char *msg, int flag_email ) {

    wtlog( msg );

    /* Se envia e-mail de situacion de excepcion solo si se puede */
    if ( flag_email == 1 ) {
        memset( Mail_subject, 0, 256+1 );
        memset( Mail_msg, 0, 512+1 );
        sprintf( Mail_subject, "BBRUPRO: stop" );
        strcpy( Mail_msg, msg );
        //bbr_send_mail( Mail_subject, Mail_msg );
        }

    exit( 2 );
    }
    
/* Subrutina de termino controlado */
void bbr_my_clean_up( int sig ) {

    /* Se finaliza cerrando los servicios habilitados */
    wtlog(  "bbr_my_clean_up(): Se finaliza normalmente" );

    /* Se desconecta de la base de datos */
    bbr_mysql_disconnect();

    /* Se liberan los sockets abiertos */
    if ( Sock_work >= 0 )
        so_close( Sock_work );
    if ( Sock_listen >= 0 )
        so_close( Sock_listen );
             
    exit( 4 );
    }

/* Subrutina para conexion de cliente interno */
void bbr_open_server_local( int with_listen ) {

    /* Se levanta el servicio de listen TCP/IP en el puerto especificado */
    /* de manera de esperar la conexion de algun cliente                 */
    if ( with_listen == WITH_LISTEN ) {
        Sock_listen = so_passive( Tcp_port, MAX_LISTEN_TCP );
        if ( Sock_listen < 0 )
	        bbr_end_program( "Error apertura socket listen local", 1 );
    	}

     return;
    }

/* Subrutina que lee desde un puerto TCP/IP */
int bbr_read_tcp( int sock_read, char *buf_tcp, int size_tcp ) {
    int len_read_data;

    /* Se lee bloqueado */
    len_read_data = so_fixread( sock_read, buf_tcp, size_tcp );
    if ( len_read_data <= 0 ) {
        /* Se envia mensaje y logging indicando este hecho */
        /* Se obtiene la direccion IP del cliente */
        wtlog( "Error de lectura (%d) o desconexion IP (%s)", 
                 len_read_data, so_getpeername( sock_read ) );
        }
     
    return( len_read_data );
    }

/* Subrutina que envia el requerimiento al proceso cliente */
void bbr_send_tcp( int sock, char *trx, int len_trx ) {
    int len_write_data;

    /* Enviamos requerimiento */
    len_write_data = so_write( sock, trx, len_trx );
    /* Se controla condiciones de error */
    if ( len_write_data <= 0 ) {
        wtlog( "Error en so_write, ret_code = %d", len_write_data );
        }

    return;
    }

/* Funcion que se ejecuta al ocurrir alguna segnal en el programa    */
/* Estas segnales son aquellas que podrian ocurrir y con las cuales  */
/* no se deberia caer en teoria el programa                          */
void bbr_signal_func( int signo ) {
    /* Se agrega de forma de debug las segnales mas frecuentes que   */
    /* ocurren en un programa. Si no son estas descomentariar las    */
    /* segnales indicadas abajo                                      */
    switch ( signo ) {
        /* Por esta es imposible que se caiga (ALARM) pero segun   */
        /* (JKL) en el SCO-UNIX se caia por esta segnal            */
        case SIGALRM:
            wtlog( "SIGALARM" );
            signal( SIGALRM, bbr_signal_func );
            break;
        case SIGPIPE:
            wtlog( "SIGPIPE" );
            signal( SIGPIPE, bbr_signal_func );
            break;
        }
        
    return;
    
    }

/* Subrutina que envia un e-mail a un conjunto de usuarios 
void bbr_send_mail( char *bbr_subject, char *bbr_msg ) {
    char str_buffer[150];
    int contador, cantidad_to, sock, aux, segmento;
    MSG_MAIL header;
    char msg[512+1];
    char subject[256+1];

    // Se carga los datos de encabezamiento del e-mail
    memset( (char *) &header, '\0', sizeof( MSG_MAIL ));
    memset( subject, 0, 256+1 );
    strcpy( subject, bbr_subject );
    bbr_load_msg_mail( Mail_from, Mail_to, subject, &header );

    // Se habilita el servicio de e-mail
    aux = bbr_send_msg_mail( header, &sock );
    if ( aux < 0 ) {
        return;
        }

    // Se envia el e-mail a la lista de usuarios definidos 
    memset( msg, 0, 512+1 );
    strcpy( msg, bbr_msg );
    aux = bbr_send_msg_data( msg, sock );

    // Se finaliza el servicio de e-mail                          
    aux = bbr_send_msg_end( sock );
    if ( aux < 0 )
        wtlog( "Error en Mail_End: %d\n", aux );
    else 
        wtlog( "e-mail de alerta enviado exitosamente" );

    return;
    } bbr_send_mail*/


int bbr_mysql_connect( void ) {
    char msg[512];
    /* Controlo si hay problemas al conectarme */
    if ( !mysql_real_connect( &Mysql, &Mysql_ip[0], &Mysql_user[0], &Mysql_pwd[0], &Mysql_db[0], 0, NULL, 0 ) ) {
        wtlog( "bbr_mysql_connect(): Error al conectar a mysql" );
        wtlog( "bbr_mysql_connect(): db = (%s) | ip = (%s)", &Mysql_db[0], &Mysql_ip[0]  );
        wtlog( "bbr_mysql_connect(): user = (%s) | passwd = (%s)", &Mysql_user[0], &Mysql_pwd[0] );
        wtlog( "bbr_mysql_connect(): %s", mysql_error( &Mysql ) );
        /* Envio de email con error
        memset( msg, 0, sizeof(msg) );
        sprintf( msg, "%s", mysql_error( &Mysql ) ); */
        //bbr_send_mail( "BBRUPRO: bbr_mysql_connect()", msg );
        return( 0 );
        }

    return( 1 );
    }

void bbr_mysql_disconnect( void ) {
    /* Se controla que para desconectar */
    /* se haya conectado previamente    */
    if ( !&Mysql ) {
        wtlog( "bbr_mysql_disconnect(): desconectando mysql" );
        /* Se libera memoria y se cierra conexion al mysql */
        mysql_free_result( Result );
        mysql_close( &Mysql );
        }
    return;
    }




void bbr_mysql_query( char *query ) {
    int rc;

    /* sprintf( Bbr_trace, "QUERY = (%s)", &query[0] );
    wtlog( Bbr_trace ); */

    rc = -1;
    while( rc != 0 ) {

        /* Ejecuto la query */
        rc = mysql_query( &Mysql, &query[0] );
        /* Se controla si no se ejecuta correctamente */
        if( rc != 0 ){
            wtlog( "(bbr_mysql_query) rc = (%d)", rc );
            /* Se controla que se haya ejecutado correctamente la sentencia update */
            wtlog( "(bbr_mysql_query) mysql_error() : %s", mysql_error( &Mysql ) );
            sprintf( Bbr_trace, "(bbr_mysql_query) mysql_errno() : %s", mysql_errno( &Mysql ) );
            wtlog( Bbr_trace );
            /* Envio email indicando error */
            //bbr_send_mail( "BBRUPRO: bbr_mysql_query()", Bbr_trace );

            /* Espero 5 segundos antes de re-intentar de ejecutar la consulta */
            sleep( 5 );

            /* Se desconecta de la base de datos */
            bbr_mysql_disconnect();

            /* Inicializo el manejador de conexion */
            mysql_init( &Mysql );
            wtlog( "(bbr_mysql_query) Inicializando.... ok!" );
            wtlog( "(bbr_mysql_query) Conectando a mysql..." );
            /* Entro a ciclo para conectarme al servidor de mysql */
            while ( bbr_mysql_connect() == 0 ) {
                sleep( 10 );
                wtlog( "(bbr_mysql_query) Re-intentando conexion a mysql..." );
                }
            /* Se establece la conexion con la base de datos*/
            wtlog( "(bbr_mysql_query) Conexion.... OK!" );
            }/*if*/

        }/*while*/

    return;
    }
