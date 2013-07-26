<?php
function getDiff ($text1, $text2) {


	$stringCommand = '"../source/gdiff/csharp/tdiff/tdiff/bin/Release/tdiff.exe" ';
	$stringCommand .='"';
	$stringCommand .= $text1;
	$stringCommand .='" "';
	$stringCommand .= $text2;
	$stringCommand .='"';
	$res = Array();

	exec($stringCommand , $res);
	
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
				$html .= '<ins style="background:#e6ffe6;">'.$data.'</ins>';
				break;
				case "(DELETE":
				$html .= '<del style="background:#ffe6e6;">'.$data.'</del>';
				break;
				case "(EQUAL":
				$html .= '<span>'.$data.'</span>';
				break;
			}
		}
	}
	
	return $html;
}


?>

