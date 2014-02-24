<?php

/**
 * @author     Group ASSA INF6150 Automne 2013
 * @copyright  2013 The PHP Group
 * @version    1.0.1
 */

include('nombreinterv.php');

$article = $_GET["spellcheckinput"];
$wikiurl = $_GET["wiki"];
$user=$_GET["user"];
$withoutSlash = explode('/', $wikiurl);
$url = $withoutSlash[0];
$completeUrl = "http://";
$completeUrl.= $url;
$jsonurl=$completeUrl."/w/api.php?action=query&prop=revisions&format=json&rvlimit=max&rvuser=".$user."&titles=".$article;

$json = file_get_contents($jsonurl, true);
$obj = json_decode($json, true);

$listes=array();
$listes= afficherdateArticle($obj);

// Affichage du resultat
echo "<table><tr><th>Date Intervention</th><th>Nombre de jours</th><th>Nombre des interventions ajoutées</th></tr>";
 foreach ($listes as $liste){
    $nbr=compterNbreInterv($liste);
	$dateN=strtotime($liste);
	$resSus=time()-$dateN;
	$dateConv=dateConverter($resSus);
    echo "<tr><td>".$liste."</td><td>".$dateConv."</td><td>".$nbr."</td></tr>";
}
 echo "</table>";
 
/*
 * Fonction qui retoune un tableau de timestamps
 * @var:  objet Json decodé
 * @return: $result - tableau des 'timestamp'
 */
function afficherdateArticle($obj){
$queries = $obj['query'];
$pages= $queries['pages'];
$result=array();

 foreach ($pages as $page) {
	 $i=0;
	 $articlerevisions = $page['revisions'];
     foreach ($articlerevisions as $revision){
	 $dateintrv=$revision['timestamp'];
	 $result[$i]= $dateintrv;
	 $i++;
}
	}
	  return $result;
}


//Module recu de l'equipe OCTETS
/*
 * Fonction qui converti en seconde
 * @var:  date en timestamp ou en seconde
 * @return:date en seconde
 */
function dateConverter($givenDate) {
                if (gettype($givenDate) === 'integer') {
                        return round($givenDate / 86400);        
                }
                else {
                        $givenTime = strtotime($givenDate);
                        return round($givenTime / 86400);
                }
        }


?>
