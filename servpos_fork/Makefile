CC      = gcc
C_FLAG  = -c
C_INC   = -I/usr/include/mysql
C_LIB   = -L/usr/lib/mysql
LFLAGS  = -lmysqlclient -lz
DIRPROD = /var/www/html/centroproy2/servpos_fork/
D_PROD  = -DBBRPROD

all: servpos_prod


servpos_prod:	clean servpos.o bbrlog.o bbrtcp.o
	$(CC) $(C_INC) $(C_LIB) -o servpos_cert servpos.o bbrlog.o bbrtcp.o $(LFLAGS) $(D_PROD) 

servpos.o:	servpos.c servpos.h
	$(CC) $(C_INC) $(C_LIB) $(C_FLAG) servpos.c

bbrlog.o:	bbrlog.c bbrlog.h
	$(CC) $(C_FLAG) -D BBRLOG bbrlog.c

bbrtcp.o:	bbrtcp.c bbrtcp.h
	$(CC) $(C_FLAG) -D BBRTCP bbrtcp.c

clean:
	rm -f k.o servpos.o bbrlog.o bbrtcp.o log/*.log *.bak
