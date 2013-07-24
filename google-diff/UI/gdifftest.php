<?php

/*
 * Author: Alexandre Poupart
 * Exemple d'utilisation de php avec exec 
 * pour executer un programme local au serveur.
 * 
 * Dans ce cas si, j'utilise la source en csharp 
 * car la source c++ utilise la librairie Qt que je ne
 * peux telecharger de l'endroit ou je suis.
 * 
 * Plus a venir.
 * 
 * Date 24 juillet 2013
 * 
 */

$text1 = "Le chien est noir.";
$text2 = "Le chien est brun.";

//exec('../source/gdiff/csharp/tdiff/tdiff/bin/Debug/tdiff.exe "'. $text1 . '" "'  . $text2 . '"' , $res = Array());
echo "exec result = ";
echo exec('"../source/gdiff/csharp/tdiff/tdiff/bin/Release/tdiff.exe" "Le chien est noir." "Le chien est brun."' , $res = Array());

echo "Resultat= ";
print_r($res);
?>
