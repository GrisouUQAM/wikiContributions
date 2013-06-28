<?php
	/*
		Série de tests de requêtes (hardcoded)
	*/


	/*
		Retourne les utilisateurs (ainsi que le temps où ils ont édité) jusqu'à aujourd'hui
	*/
	function getAllUsers($website, $article){
		$url = $website."/w/api.php?action=query&prop=revisions&rvstart=basetimestamp&rvprop=user|timestamp&format=json&redirects&titles=".$article;
		return file_get_contents($url); 
	}
	
	/*
		Retourne les id des revisions et leur auteur
		revid : id de la revision
		parentid : id de la revision qui a été revisé
	*/
	function getAllRevId($website, $article){
		$url = $website."/w/api.php?action=query&prop=revisions&rvstart=basetimestamp&rvprop=user|ids&format=json&redirects&titles=".$article;
		return file_get_contents($url); 
	}
	/*
		Retourne le contenu dans les revisions
		Désagréable à voir
	*/
	function getAllRevContent($website, $article){
		$url = $website."/w/api.php?action=query&prop=revisions&rvprop=ids|content&rvdir=newer&format=json&redirects&titles=".$article;
		return file_get_contents($url); 
	}
	
	
	/*
		Retourne les revisions(ainsi que la date) fait par un utilisateur spécifique.
		L'utilisateur par défaut est Sola2012 (aucune raison particulière).
	*/
	function getUserRevIds($website, $article, $user){
	
		if (empty($user)){
			$user = "Sola2012";
		}
		
		$url = $website."/w/api.php?action=query&prop=revisions&rvstart=basetimestamp&rvuser=".$user."&rvprop=user|ids|timestamp&format=json&redirects&titles=".$article;
		return file_get_contents($url); 
	}
	/*
		Retourne le contenu des revisions
	*/
	function getAllSectionRevContent($website, $article, $section){
		$url = $website."/w/api.php?action=query&prop=revisions&rvstart=basetimestamp&rvsection=".$section."&format=json&redirects&titles=".$article;
		return file_get_contents($url); 
	}

	function getRecentChanges($website, $article){
		$url = $website."/w/api.php?action=query&prop=revisions&rvprop=user&rvdifftotext&rvcontentformat=text/x-wiki&format=json&redirects&titles=".$article;
		return file_get_contents($url); 
	}
	/*
	Retourne les sections (dans le tableau content) et leur id
	*/
	function getAllSections($website, $article){
		$url = $website."/w/api.php?action=mobileview&prop=sections&sectionprop=toclevel|level|number|line&format=json&page=".$article;
		return file_get_contents($url); 
	}
	
	function getRevisionUser($website, $article, $revision_id){
		$url = $website."/w/api.php?action=query&prop=revisions&revids=".$revision_id."&rvprop=user&format=json";
		return file_get_contents($url); 
	}
	
	/*
	Text brute du section (dans ce cas, la section ayant le id 2)
	*/
	function getSpecificSectionContent($website, $article, $section){
		$url = $website."/w/index.php?action=raw&title=".$article."&section=".$section;
		return file_get_contents($url); 
	}
	
	/*
	Differences de versions par wikipedia (important impact visuel)
	*/
	function getDiff($website, $article, $rev){
		$url = $website."/w/index.php?title=".$article."&diff=cur&oldid=".$rev;
		return file_get_contents($url); 
	}
	
	

?>