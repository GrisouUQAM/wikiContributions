<?php
function getDiff ($text1, $text2) {
        //Params
        $separator = chr(30); //LF pour separer les deux texte (norme Linux/Unix)
        $filename = 'textcomp.tmp';
        $mode = 'w'; // Creer/ecraser le fichier existant
        
        //Ecriture dans le fichier temporaire
        $file = fopen($filename, $mode);
        fwrite($file, $text1);
        fwrite($file, $separator);
        fwrite($file, $text2);
        fclose($file);
        
        //tdiff avec le fichier temporaire en argument
	$stringCommand = '"../bin/tdiff.exe" ';
        $stringCommand .= '"' . $filename . '"';
        $res = Array();
        exec($stringCommand, $res);
        
	return $res;
}


function prettyHtml($diffs) {
	$html = "";
	for ($x = 0; $x < sizeof($diffs); $x++) {
		$diffArray = explode('Diff', $diffs[$x]);
		for ($y = 0; $y < sizeof($diffArray); $y++) {
			$newArray = explode(',"', $diffArray[$y]);
			$op = $newArray[0];    // Operation (insert, delete, equal)
			if (sizeof($newArray) > 1) {
				$text = explode('")',$newArray[1]);  // Text of change.
				$data = $text[0];
			} else {
				$data = "";
			}			
			switch ($op) {
				case "(INSERT":
				$html .= '<ins style="background:#e6ffe6;">'."INSERT".'</ins>';
				break;
				case "(DELETE":
				$html .= '<del style="background:#ffe6e6;">'.'DELETE'.'</del>';
				break;
				//case "(EQUAL":
				//$html .= '<span>'.$data.'</span>';
				//break;
			}
		}
	}
	
	return $html;
}


?>

