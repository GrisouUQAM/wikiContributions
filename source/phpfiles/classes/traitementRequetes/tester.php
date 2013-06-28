<?php
	/*
		Effectue une série de tests.
	*/
	
	include("contentFetcher.php");
	ini_set('user_agent', 'ProjetWiki (https://github.com/yolo-hipster/yolo-hipster; ralphdsanon@hotmail.com)'); //Requis pour éviter une erreur 403
	
	function testContentFetcher(){
		$site = "http://en.wikipedia.org";
		$article = "Celine%20Dion";
		$skipper = "<br><br>";
		
		$content = "Tous les utilisateurs";
		$content.= $skipper;
		$content.= getAllUsers($site, $article);
		$content.= $skipper;
		$content.= "Tous id de revisions";
		$content.= $skipper;
		$content.= getAllRevId($site, $article);
		$content.= $skipper;
		$content.= "Revisions d'un utilisateur";
		$content.= $skipper;
		$content.= getUserRevIds($site, $article, "");
		$content.= $skipper;
		$content.= "Contenu des revisions de section";
		$content.= $skipper;
		$content.= getAllSectionRevContent($site, $article, 2);
		$content.= $skipper;
		$content.= "Tous les sections";
		$content.= $skipper;
		$content.= getAllSections($site, $article);
		$content.= $skipper;
		$content.= "Autheur d'une revision";
		$content.= $skipper;
		$content.= getRevisionUser($site, $article,500712829);
		$content.= $skipper;
		$content.= "Le contenu d'une section";
		$content.= $skipper;
		$content.= getSpecificSectionContent($site, $article,5);
		echo $content;
	}
	
	testContentFetcher();

?>