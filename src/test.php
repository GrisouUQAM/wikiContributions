<?php
include "deplacements.php";
include "diff_match_patch.php";

$text1 = " it has had some of its flow diverted into Lake Coleridge as part of a hydroelectricity project. This diversion and boosted the output of the Coleridge Power Station and was established in 1977.[2]¶
An early proposal for the route of ";

$text2 = " it has had some of its flow diverted into Lake Coleridge as part of a project. This diversion was established in 1977 and boosted the output of the Coleridge Power Station.[2]¶
An early proposal for the route of ";



$dmp = new diff_match_patch();
$newDmp = new diff_match_patch();
$donnees = $dmp->diff_main(strip_tags($text1), strip_tags($text2), false);
$newDmp->diff_cleanupSemantic($donnees);
// $donnees = array(
//     array(-1, "Goo"),
//     array(1, "Ba"),
//     array(-1, "Boo")
// );

$t = new Deplacement($donnees);
var_dump($t->getDeplacement());

?>
