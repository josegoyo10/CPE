/********************************************************************/
/* Conjunto de servicios que permiten la conectividad cliente       */
/* servidor TCP/IP                                                  */
/********************************************************************/

/* Inclusion de archivos */
#include "bbrtcp.h"
#include "bbrlog.h"

/* Definicion de variables locales */
int Timeout_connect = 5;         /* Timeout de espera para        */
                                 /* conexion a Host               */
int Timeout_write = 5;           /* Timeout de espera para        */
                                 /* escritura                     */

/********************************************************
*  Funcion: so_passive                                  *
********************************************************/
int so_passive( int port, int maxlisten ) {
    int s;
    struct sockaddr_in sin;

    memset( (char *) &sin, 0, sizeof( struct sockaddr_in ) );
    sin.sin_port = htons( port );
    sin.sin_family = AF_INET;

    if ( ( s = socket( AF_INET, SOCK_STREAM, 0 ) ) < 0 ) {
        welog("so_passive socket : errno = (%d)", errno );
        return( -1 );
        }

    if ( bind( s, (struct sockaddr *) &sin, 
               sizeof( struct sockaddr_in ) ) < 0 ) {
        welog( "so_passive bind : errno = (%d)", errno );
        so_close( s );
        return( -2 );
        }

    if ( listen( s, maxlisten ) < 0 ) {
        welog("so_passive listen : errno = (%d)", errno );
        so_close( s );
        return( -3 );
        }

    return( s );
    
    }

/********************************************************
*  Funcion : so_accept                                  *
********************************************************/
int so_accept( int sock ) {
    int alen, s;
    struct sockaddr_in sin;

    alen = sizeof( struct sockaddr_in );
    memset( ( char *)&sin, 0, alen );
    s = accept( sock, (struct sockaddr *) &sin, &alen );
    if ( s < 0 ) {
        welog("so_accept accept : errno = (%d)", errno );
        }
        
    return( s );
    
    }

/********************************************************
*  Funcion : so_connect                                 *
********************************************************/
int so_connect( char *host, int port, int flag_block ) {
    int alen, s, dontblock, rc;
    u_long lhost;
    struct sockaddr_in sin;
    struct hostent *hp;
    fd_set write_fds;
    struct timeval connect_time;

    alen = sizeof( struct sockaddr_in );
    memset( (char *)&sin, 0, alen );
    sin.sin_family = AF_INET;
    sin.sin_port = htons(port);

    if ( *host >= '0' && *host <= '9' ) {
        lhost = inet_addr( host );
        if ( lhost < 0 ) {
            welog( "so_connect inet_addr : errno = (%d)",errno );
            return( -1 );
            }
        memcpy( (char *)&sin.sin_addr, (char *)&lhost, sizeof( long ) );
        }
    else {
        if ( ( hp = gethostbyname( host ) ) == NULL ) {
            welog("so_connect gethostbyname : errno = (%d)",h_errno );
            return( -2 );
            }
        memcpy( (char *)&sin.sin_addr, (char *)hp->h_addr, hp->h_length );
        }

    if ( ( s = socket( AF_INET, SOCK_STREAM, 0 ) ) < 0 ) {
        welog("so_connect socket : errno = (%d)", errno );
        return( -3 );
        }

    /* Se setea el socket para operar non-blocking solo si el flag */
    /* esta prendido                                               */
    if ( flag_block == 1 ) {
        dontblock = 1;
        rc = ioctl( s, FIONBIO, (char *) &dontblock, sizeof( dontblock ) );
        if ( rc < 0 ) {
            welog("so_connect ioctl : errno = (%d)", errno );
            so_close( s );
            return( -4 );
            }
        }

    /* Nos tratamos de conectar */
    rc = connect( s, (struct sockaddr *) &sin, alen );
    if ( rc < 0 ) {
        rc = errno;
        welog("so_connect connect : errno = (%d)", rc );
        if ( flag_block == 1 && rc != EINPROGRESS )
            return( -5 );
	else if ( flag_block == 0 ) 
	    return( -5 );
        }

    /* Si estamos con el flag de no-bloquedos se retorna */
    if ( flag_block == 0 ) 
        return( s );

    /* Se espera que la conexion se establezca, en caso contrario */
    /* se retorna error                                           */    
    connect_time.tv_sec = Timeout_connect;
    connect_time.tv_usec = 0;
    FD_ZERO( &write_fds );
    FD_SET( s, &write_fds );
    welog("so_connect select0 : rc = (%d)", rc );
    rc = select( s+1, NULL, &write_fds, NULL, &connect_time );
    welog("so_connect select1 : rc = (%d)", rc );
    if ( rc <= 0 ) {
        welog("so_connect select : rc = (%d)", rc );
        so_close( s );
        return( -6 );
        }

    return( s );
    
    }

/********************************************************
*  Funcion : so_write                                   *
********************************************************/
int so_write( int sock, char *buf, int buflen ) {
    int rc;
    fd_set write_fds;
    struct timeval write_time;

    /* Se controla si estamos OK con la conexion para escribir */
    write_time.tv_sec = Timeout_write;
    write_time.tv_usec = 0;
    FD_ZERO( &write_fds );
    FD_SET( sock, &write_fds );
    rc = select( sock+1, NULL, &write_fds, NULL, &write_time );
    if ( rc <= 0 ) {
        welog("so_write select : rc = (%d)", rc );
        return( rc );
        }

    rc = send( sock, buf, buflen, 0 );
    if ( rc < 0 ) {
        welog("so_write send : errno = (%d)",
                 errno );
        }

    return( rc );
    
    }

/********************************************************
*  Funcion : so_read                                    *
********************************************************/
int so_read( int sock, char *buf, int buflen ) {
    int len;
        
    len = recv( sock, buf, buflen, 0 );
    if ( len <= 0 ) {
        welog("so_read recv : errno = (%d)", 
                 errno );
        }

    return( len );
    
    }

/********************************************************
*  Funcion : so_fixread                                 *
********************************************************/
int so_fixread( int sock, char *buf, int buflen ) {
    int totlen, len;

    for( totlen = 0; totlen < buflen; totlen += len ) {
        len = recv( sock, buf + totlen, buflen - totlen, 0 );
        if ( len <= 0 ) {
            welog("so_fixread recv : errno = (%d)", 
                     errno );
            return( -1 );
            }
        }

    return( totlen );
    
    }

/********************************************************
*  Funcion : so_close                                   *
********************************************************/
int so_close( int sock ) {

    if ( close( sock ) < 0 ) {
        welog("so_close close : errno = (%d)", 
                 errno );
        return( -1 );
        }

    return( 1 );
    
    } 

/********************************************************
*  Funcion : so_wait. Espera lectura en un socket por   *
*            "seconds" segundos                         *
********************************************************/
int so_wait( int sock, int seconds ) {
    int  rc;
    fd_set read_fds;
    struct timeval read_time;

    read_time.tv_sec = seconds;
    read_time.tv_usec = 0;
    FD_ZERO( &read_fds );
    FD_SET( sock, &read_fds );
    rc = select( sock+1, &read_fds, NULL, NULL, &read_time );
    if ( rc <= 0 ) {
        welog("so_wait select : rc = (%d)", rc );
        }
    
    return( rc );
    
    }

/********************************************************/
/* Funcion que permite obtener la direccion IP del      */
/* cliente que se conecta a un server                   */
/********************************************************/
char *so_getpeername( int sock ) {
    struct sockaddr_in name;
    int namelen, rc;
    
    namelen = sizeof( struct sockaddr_in );
    rc = getpeername( sock, (struct sockaddr *) &name, &namelen );
    if ( rc != 0 ) {
        welog("so_getpeername : errno = (%d)", errno );
        return( NULL );	
        }
        	
    return( inet_ntoa( name.sin_addr ) );
            	
    }
