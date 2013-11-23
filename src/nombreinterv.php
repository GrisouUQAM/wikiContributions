<?php

/**
 * @author     Group ASSA INF6150 Automne 2013
 * @copyright  2013 The PHP Group
 * @version    1.0.1
 */

/*
 * Fonction qui retoune une table des intervention
 * @var:  $obj - objet JSON
 * @return: $result - tableau des 'timestamp'
 */
function tabIntervention($obj){
  $queries = $obj['query'];
  $pages= $queries['pages'];
  $result=array();
  foreach ($pages as $page) {
	 $i=0;
	 $articlerevisions = $page['revisions'];
	 $articlerevisions = $page['revisions'];
     foreach ($articlerevisions as $revision){
	  $dateintrv=$revision['timestamp'];
	  $result[$i]= $dateintrv;
	  $i++;
     }
  }
   $nombre=count($articlerevisions);
   return $result;
}

/*
 * Fonction compteur des intervention
 * @var:  $date - objet Date
 * @return: $nb - nombre des interventions
 */
function compterNbreInterv($date){
	$listes=array();
	$nb=0;
	$article = $_GET["spellcheckinput"];
	$wikiurl = $_GET["wiki"];
	$user=$_GET["user"];
	$withoutSlash = explode('/', $wikiurl);
	$url = $withoutSlash[0];
	$completeUrl = "http://";
	$completeUrl.= $url;
	$jsonurl=$completeUrl."/w/api.php?action=query&prop=revisions&format=json&rvlimit=max&rvexcludeuser=".$user."&titles=".$article;
	$json = file_get_contents($jsonurl, true);
	$obj = json_decode($json, true);
	$listes=tabIntervention($obj);
	foreach ($listes as $liste){
		if($liste>=$date){
			$nb++;	
		}	
	}
	return $nb;
}
?>
