#include "bbrlog.h"


/* Procedimiento que abre los archivos de LOG                                       */
/* act: flag que indica si esta activado el log de informacion (>0) o no (=0)       */
void open_logs( int act ){
    /* Se inicializan los punteros a los logs                                       */
    __Log_fp_bbr = NULL;
    __Logtrace_fp_bbr = NULL;
    __Act_log = act;
    }

/* Cierra los archivos de LOG                                                       */
void close_logs( ){
    if ( __Log_fp_bbr != NULL )
        fclose( __Log_fp_bbr );
    if ( __Logtrace_fp_bbr != NULL )
        fclose( __Logtrace_fp_bbr );
    }

/* Procedimiento que escribe un mensaje en el archivo de LOGs de Errores            */
void welog( char * fmt, ... ){
    va_list args;
    char prog_name[]=PROGRAM_NAME;
    time_t tiempo;
    long tam_arch;
    struct tm * fecha;
    if( ( __Log_fp_bbr = fopen( LOG, "a+r" ) ) == NULL )
        return;
    fseek( __Log_fp_bbr, 0, SEEK_END );
    tam_arch = ftell( __Log_fp_bbr );
    if ( tam_arch > MAX_LEN_LOG ) {
        fclose( __Log_fp_bbr );
        if ( ( __Log_fp_bbr = fopen( LOGBAK, "r" ) ) != NULL ){
            fclose( __Log_fp_bbr );
            remove( LOGBAK );
            }
        rename( LOG, LOGBAK );
        if ( ( __Log_fp_bbr = fopen( LOG, "a+r" ) ) == NULL )
            return;
        }
    prog_name[0] = '\0';
    //strcpy( prog_name, __argv[0] );
    time( &tiempo );
    fecha = localtime( &tiempo );
    fprintf( __Log_fp_bbr, "[%.511s]<%04d-%02d-%02d %02d:%02d:%02d> --> ",
             prog_name, fecha->tm_year + 1900, fecha->tm_mon + 1, fecha->tm_mday,
             fecha->tm_hour, fecha->tm_min, fecha->tm_sec);
    va_start( args, fmt );
    vfprintf( __Log_fp_bbr, fmt, args );
    va_end( args );
    fprintf( __Log_fp_bbr, "\n" );
    fclose( __Log_fp_bbr );
    }

/* Procedimiento que escribe un mensaje en el archivo de LOGs de Informacion        */
void wtlog( char * fmt, ... ){
    va_list args;
    char prog_name[]=PROGRAM_NAME;
    time_t tiempo;
    long tam_arch;
    struct tm * fecha;

    if( ( __Act_log == 0 ) || ( __Logtrace_fp_bbr = fopen( LOGTRACE, "a+r" ) ) == NULL  ){
        return;
        }

    fseek( __Logtrace_fp_bbr, 0, SEEK_END );
    tam_arch = ftell( __Logtrace_fp_bbr );

    if ( tam_arch > MAX_LEN_LOG ) {
        fclose( __Logtrace_fp_bbr );
        if ( ( __Logtrace_fp_bbr = fopen( LOGTRACEBAK, "r" ) )  != NULL ) {
            fclose( __Logtrace_fp_bbr );
            remove( LOGTRACEBAK );
            }

        rename( LOGTRACE, LOGTRACEBAK );
        if ( ( __Logtrace_fp_bbr = fopen( LOGTRACE, "a+r" ) )  == NULL ){
            welog( "Error renombrando archivo de log %s a logbak", LOGTRACE, 
                              LOGTRACEBAK );
            return;
            }
        }

    prog_name[0]='\0';
    //strcpy( prog_name, __argv[0] );
    time( &tiempo );
    fecha = localtime( &tiempo );
    /*fprintf( __Logtrace_fp_bbr, "[%.511s]<%04d-%02d-%02d %02d:%02d:%02d> --> ",
             prog_name, fecha->tm_year + 1900, fecha->tm_mon + 1, fecha->tm_mday,
             fecha->tm_hour, fecha->tm_min, fecha->tm_sec);
			 */

	// Sin nombre de programa
	fprintf( __Logtrace_fp_bbr, "<%04d-%02d-%02d %02d:%02d:%02d> --> ",
             fecha->tm_year + 1900, fecha->tm_mon + 1, fecha->tm_mday,
             fecha->tm_hour, fecha->tm_min, fecha->tm_sec);
			 

    va_start( args, fmt );
    vfprintf( __Logtrace_fp_bbr, fmt, args );
    va_end( args );
    fprintf( __Logtrace_fp_bbr, "\n" );
    fclose( __Logtrace_fp_bbr );
    }

/* Subrutina que obtiene la fecha y hora en formato */
/* tipo:                                            */
/*     1 = DD/MM/AAAA HH:MM:SS                      */
char *bbr_get_time( char *msg_time, int t_format ) {
    time_t hora_actual;
    struct tm *hora_format;

    /* Se obtiene fecha y hora actual y se traspasa a mensaje */
    /* recibido como argumento                                */
    time( &hora_actual );
    hora_format = localtime( &hora_actual );
    bbr_itoafix( msg_time, 4, hora_format->tm_year + 1900 );
    msg_time[4] = '/';
    bbr_itoafix( msg_time+5, 2, hora_format->tm_mon + 1 );
    msg_time[7] = '/';
    bbr_itoafix( msg_time+8, 2, hora_format->tm_mday );
    msg_time[10] = ' ';
    bbr_itoafix( msg_time+11, 2, hora_format->tm_hour );
    msg_time[13] = ':';
    bbr_itoafix( msg_time+14, 2, hora_format->tm_min );
    msg_time[16] = ':';
    bbr_itoafix( msg_time+17, 2, hora_format->tm_sec );

    return( msg_time );
    }

/* Subrutina que permite convertir un numero entero a una cantidad fija */
/* de posiciones rellenando con ceros por la izquierda dentro de un     */
/* string                                                               */
void bbr_itoafix( char *s, int len, int value ) {
    char fmt[16], aux[255];

    sprintf( fmt, "%%0%dd", len );
    sprintf( aux, fmt, value );
    memcpy( s, aux, len );

    return;
    }

/* Subrutina que convierte un string numerico dado un largo fijo */
/* en un numero entero                                           */
int bbr_afixtoi( char *s, int len ) {
    char    aux[255];

    memcpy( aux, s, len );
    aux[len] = 0;

    return( atoi( aux ) );
    }

/*  afixtol: Entrega valor long de campo ascii de largo fijo dado       */
long bbr_afixtol( char *s, int len ) {
    char    aux[255];

    memcpy( aux, s, len );
    aux[len] = 0;

    return( atol( aux ) );
    }

/*  ltoafix: Llena campo ascii de largo fijo con valor long dado        */
void bbr_ltoafix( char *s, int len, long value ) {
    char    fmt[16], aux[255];

    sprintf( fmt, "%%0%dld", len );
    sprintf( aux, fmt, value );
    memcpy( s, aux, len );

    return;
    }

