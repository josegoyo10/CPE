/******************************************************************************/
/* Declaracion de macros y funciones comunes para acceso a servicios TCP/IP   */
/******************************************************************************/
#include <string.h> 
#include <sys/types.h> 
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <sys/ioctl.h>
#include <sys/time.h>
#include <unistd.h>

#ifndef BBRFTP
extern int so_passive( int, int );
extern int so_accept( int );
extern int so_connect( char *, int, int );
extern int so_write( int, char *, int );
extern int so_read( int, char *, int );
extern int so_fixread( int, char *, int );
extern int so_close( int );
extern int so_wait( int, int );
extern char *so_getpeername( int );
#else
int so_passive( int, int );
int so_accept( int );
int so_connect( char *, int, int );
int so_write( int, char *, int );
int so_read( int, char *, int );
int so_fixread( int, char *, int );
int so_close( int );
int so_wait( int, int );
char *so_getpeername( int );
#endif
