<?php
	/*
		Effectue une série de tests.
	*/
	
	include("../classes/traitementRequetes/contentFetcher.php");
	ini_set('user_agent', 'ProjetWiki (https://github.com/yolo-hipster/yolo-hipster; rlamour2@yahoo.ca)'); //Requis pour éviter une erreur 403
	
	function trouvePosition($contenu, $recherche){
		return strpos($contenu, $recherche);
	}
	
	function combienTrouver($contenu, $recherche){
		return substr_count($contenu, $recherche);
	}
	
	function afficherUtilisateurs(){
		$site = "http://fr.wikipedia.org";
		$article = "Baobab%20africain";
		$skipper = "<br><br>";
		
		$content = getAllUsers($site, $article);
		print "contenu Original: " . $content;
		print $skipper;
		$trouveMoi = "\"user\":";
		$nb = combienTrouver($content, $trouveMoi);
		print "nombre de valeur: " . $nb;
		print $skipper;
		for($i=0; $i<$nb; $i++){
			$position = trouvePosition($content, $trouveMoi);
			print "position: " . $position;
			print $skipper;
			
			$content = substr ($content, $position+8);
			print "contenu: " . $content;
			print $skipper;
			
			$tailleTrouve = trouvePosition($content, "\"");
			print "Longueur du string user: " . $tailleTrouve;
			print $skipper;
			$user = substr($content,0, $tailleTrouve);
			print $user;
			print $skipper;
		}
	}

	//afficherUtilisateurs();
	
	include("../utils/weha/categorizer/Categorize.php");
	include("../classes/traitementRequetes/MWJsonDecoder.class.php");
	
	function test(){
		$site = "http://fr.wikipedia.org";
		$article = "Baobab%20africain";
		$skipper = "<br><br>";
		$content = getAllRevId($site, $article);
		
		$cat = new Categorize();
		
		//var_dump($content);
		$unTableau = MWJsonDecoder::getRevisionArray($content);
	
	//print $unTableau['query']['pages'][$jj->getPageId($content)]['revisions'];
		//foreach($unTableau as $elem){
			//print $elem;
			//print $skipper;
		//}
	}
	
	test();
?>