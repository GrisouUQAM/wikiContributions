<?php
include_once("WikiDiffFormatter.php");

function ShowDiff(){

//$myFile = "NewText.txt";
/*$fh = fopen($myFile, 'r');*/
//$newText = fread($fh, filesize($myFile));
$newText = "allo";
//fclose($fh);	

//$myFile = "OldText.txt";
//$fh = fopen($myFile, 'r');
//$oldText = fread($fh, filesize($myFile));
$oldText = "Le poisson rouge bleu est vert.";
//fclose($fh);

$ac = new WikiDiffFormatter($oldText, $newText);
$analysis = $ac->outputDiff();

return $analysis;

}

ShowDiff();