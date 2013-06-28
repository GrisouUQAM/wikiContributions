<?php

	include_once("wikiArticle.class.php");
		/*
			Contient et execute les requetes
		*/
	class Requeteur {
		
		
		/*
			Retourne des infos sur l'article
		*/
		public static function getInfos(ArticleWiki $wiki){
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=info&format=json&redirects&titles=".$wiki->getArticle();
			return file_get_contents($url); 
		}
		
		/*
		 * 	Retourne tous les revisions d'un article en ordre chronologique 
		 * croissant sous forme de tableau.
		 */
		public static function getAllRevisions(ArticleWiki $wiki){
			
			if(!isset($wiki)){
				return null;
			}
			
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=revisions&rvprop=ids|content|user&rvlimit=500&format=json&redirects&rvdir=newer&titles=".$wiki->getArticle();
			$jsonFile = file_get_contents($url);
			$jsonObjs = json_decode($jsonFile, true);
			
			$revisions = $jsonObjs["query"]["pages"];
			$revisions = current($revisions);
			$revisions = $revisions["revisions"];
			return $revisions;
		}
		
		/*
			Retourne les auteurs d'un article
			
			limite: 500
		*/
		public static function getUsers(ArticleWiki $wiki){
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=revisions&rvstart=basetimestamp&rvprop=user|timestamp&rvlimit=500&format=json&redirects&titles=".$wiki->getArticle();
			return file_get_contents($url); 
		}
			
		/*
			Retourne les id revisions ainsi que les auteurs de ceux-ci
			
			limite: 500
		*/
		public static function getRevisionsIds(ArticleWiki $wiki){
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=revisions&rvlimit=500&rvstart=basetimestamp&rvprop=user|ids&format=json&redirects&titles="
			.$wiki->getArticle();
			return file_get_contents($url); 
		}
		
		/*
			Retourne les id de revisions d'un auteur
		*/
		public static function getAllUserRevisions(ArticleWiki $wiki, $auteur){
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=revisions&rvstart=basetimestamp&rvuser=".$auteur."&rvprop=ids&format=json&redirects&titles=".$wiki->getArticle();
			return file_get_contents($url); 
		}
		
		/*
			Retourne l'auteur d'une revision
		*/
		public static function getRevisionUser(ArticleWiki $wiki, $revision){
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=revisions&revids=".$revision."&rvprop=user&format=json";
			return file_get_contents($url); 
		}
		
		/*
			Comportement etrange
		*/
		public static function getSectionRevisions(ArticleWiki $wiki, $section){
			$url = $wiki->getWiki()."/w/api.php?action=query&prop=revisions&rvsection=".$section."&rvlimit=10&format=json&redirects&titles=".$wiki->getArticle();
			return file_get_contents($url); 
		}
		
		/*
			Retourne les sections (du tableau contenu) et leur id
		*/
		public static function getSections(ArticleWiki $wiki){
			$url =  $wiki->getWiki()."/w/api.php?action=mobileview&prop=sections&sectionprop=toclevel|level|number|line&format=json&page=".$wiki->	getArticle();
			return file_get_contents($url); 
		}
	}

?>