<?php
require_once('diff_match_patch.php');

function dmp(){
	static $dmp = null;
	if(!$dmp){
		$dmp = new diff_match_patch();
	}
	return $dmp;
}

function getDiff ($text1, $text2) {
	$res = dmp()->diff_main($text1, $text2, false);	
	$newDmp = new diff_match_patch();
	$newDmp->diff_cleanupSemantic($res);
	return $res;
}


function prettyHtml($diffs) {
	$equalCount = 0;
	$deleteCount = 0;
	$insertCount = 0;
	$sentenceMatchingRate = 0.00;
	$results = dmp()->diff_prettyHtml($diffs);
	for ($x = 0; $x < sizeof($diffs); $x++) {
		switch ($diffs[$x][0]){
			case 0:
			$equalCount++;
			break;
			case 1:
			$insertCount++;
			break;
			case -1:
			$deleteCount++;
			break;
		}
	}
	$sentenceMatchingRate = $equalCount/sizeof($diffs);
	$results .= "Number of words deleted : ".$deleteCount."";
	$results .= ("<br/>");
	$results .= "Number of words inserted : ".$insertCount."";
	$results .= ("<br/>");
	$results .= "sentence matching rate : ".$sentenceMatchingRate."";
	return $results;
}


?>

