# Makefile pour tdiff.
# C'est une application qui permet de lister les differences
# entre deux textes entree en parametres.
#

OPTIONS=-O3

all : tdiff

tdiff : tdiff.cpp diff_match_patch.o
	g++ ${OPTIONS} -o tdiff tdiff.cpp diff_match_patch.o

diff_match_patch.o : diff_match_patch.cpp diff_match_patch.h
	g++ ${OPTIONS} -c -o diff_match_patch.o diff_match_patch.cpp

clean:
	rm -rf t *~ *.o

