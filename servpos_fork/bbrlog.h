#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdarg.h>
#include <time.h>
#include <errno.h>

FILE        *__Log_fp_bbr;       /* File Pointer al archivo de Errores              */
FILE        *__Logtrace_fp_bbr;  /* File Pointer al archivo de Log                  */
short       __Act_log;           /* Variable que indica si el info log esta         */
                                 /* activo o no                                     */
char Bbr_trace[512];             /* para ayuda en logs */
#ifdef BBRPROD
	#define PROGRAM_NAME "servpos"
	#define LOG               "/var/www/html/centroproy2/servpos_fork/log/servpos.log"
	#define LOGBAK            "/var/www/html/centroproy2/servpos_fork/log/servpos.bak"
	#define LOGTRACE          "/var/www/html/centroproy2/servpos_fork/log/servpos_trace.log"
	#define LOGTRACEBAK       "/var/www/html/centroproy2/servpos_fork/log/servpos_trace.bak"
#else
	#define PROGRAM_NAME "servpos_cert"
	#define LOG               "/var/www/html/centroproy2/servpos_fork/log/servpos_cert.log"
	#define LOGBAK            "/var/www/html/centroproy2/servpos_fork/log/serpos_cert.bak"
	#define LOGTRACE          "/var/www/html/centroproy2/servpos_fork/log/servpos_cert_trace.log"
	#define LOGTRACEBAK       "/var/www/html/centroproy2/servpos_fork/log/servpos_cert_trace.bak"
#endif
	#define MAX_LEN_LOG       5000000L

void open_logs( int  );
void close_logs();
void welog( char *, ... );
void wtlog( char *, ... );
char *bbr_get_time( char *, int);
void bbr_itoafix( char *, int , int  );
int bbr_afixtoi( char *, int  );
long bbr_afixtol( char *, int  );
void bbr_ltoafix( char *, int , long  );
