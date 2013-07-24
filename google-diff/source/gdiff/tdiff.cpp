/* 
 * File:   tdiff.cpp
 * Author: Alexandre Poupart
 * Adaptation de la librairie Google-diff_match_patch
 * Le programme prend deux string en parametre et liste les
 * differences entre les deux textes.
 * 
 * Created on 24 juillet 2013, 15:17
 */

#include "diff_match_patch.h"
#include <cstdlib>
#include <list>

using namespace std;

/*
 * 
 */
int main(int argc, char** argv) {
    if(argc!=2) return 0;
    
    string text1 = argv[0]
            , text2 = argv[1];
    
    
    
    return 0;
}

