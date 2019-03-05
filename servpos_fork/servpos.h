/* Inclusion de funciones externas */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <signal.h>
#include <sys/wait.h>
#include <mysql.h>
#include "bbrlog.h"
#include "bbrtcp.h"
//#include "bbrmail.h"

#define TRUE                0
#define FALSE               1
#define MAX_TIMEOUT         120
#define MAX_BUFFER          512L
#define MAX_BUFFER_TCP      8192L
#define MAX_MYSQL_USER      10
#define MAX_MYSQL_PWD       10
#define MAX_MYSQL_DB        15

//#define FILE_LOG          "/home/bbr/IRS/bin/bbrupro.log"
//#define CONFIG_FILE       "/home/bbr/IRS/process/bbrupro.cfg"
#define CONFIG_FILE         "/var/www/html/centroproy2/servpos_fork/cfg/servpos.cfg"


#define FOREVER             for( ;; )
#define MAX_LISTEN_TCP      5   /* Maximo numero de clientes TCP que se */
                                /* permitira queden pendientes de       */
                                /* conexion con el servidor             */
#define WITH_LISTEN         1   /* Indicador de apertura de servidor IP */
                                /* haciendo listen                      */
#define WITHOUT_LISTEN      2   /* Indicador de apertura de servidor IP */
                                /* sin hacer listen, solo accept        */
#define MAX_NUM_HIJOS       10  /* Maxima cantidad de procesos hijos    */



/* Registro de encabezado para consultas recibidas */
typedef struct {
    char empresa[3];       /* Empresa						*/
	char local[3];         /* Numero de Local				*/
	char pos[3];           /* Numero de POS					*/
	char boleta[10];       /* Numero de Boleta				*/
	char trx_sa[4];        /* Numero de trx SA				*/
	char fecha[6];         /* Fecha de trx YYMMDD			*/
	char hora[6];          /* Hora de trx HHMMSS			*/
	char operador[8];      /* Codigo de vendedor			*/
	char tipo_trx[2];      /* Tipo Trx C1:consulta C2:pago	*/
	char version[1];       /* Version de mensajeria			*/
	char journal[6];       /* Numero de Journal				*/
	char largo_data[4];    /* largo de la data				*/
} HEADER;
#define SIZE_HEADER   		sizeof( HEADER )
#define SIZE_HEADER_ERROR 	SIZE_HEADER+43 

/* Registro de PLUs para armar la respuesta C1          */
typedef struct {
    char plu[12];       /* PLU							*/
    char indicat[1];    /* Indicador cantidad o peso , se marca 1 cuando viene la cantidad con decimales*/
    char cantidad[6];   /* Cantidad o peso				*/
	char decimal[3];    /* decimales de la cantidad     */
    char precio[8];     /* Precio						*/
} REG_PLU;
#define SIZE_REG_PLU   sizeof( REG_PLU )
