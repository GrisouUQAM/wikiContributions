<?php
	include_once("MWJsonDecoder.class.php");
	class ArticleWiki {
	/*
		Contient les informations sur l'article à étudier
	*/
		private  $_article;
		private  $_wiki;
		private	 $_id;
		
		private	$_userArray;
		private $_revisionArray;
		private $_sectionArray;
		
		/*
			Creation par un Wiki (tel que: en.wikipedia.org) et du nom de l'article 
		*/
		public function createByWikiArticle($wiki, $article){
			$object = new self();
			$object->setWiki($wiki);
			$object->setArticle($article);
			ArticleWiki::setupArticle($object);
			return $object;
		}
		
		/*
			Creation par un URL (excluant le http://)
		*/
		public static function createByURL($url){
			$object = new self();
			$object->setArticleAndWiki($url);
			ArticleWiki::setupArticle($object);
			return $object;
		}
		
		private static function setupArticle(ArticleWiki $wiki){
			$wiki->setID(MWJsonDecoder::decodePageId(Requeteur::getInfos($wiki)));
			$wiki->setUserArray(MWJsonDecoder::getUserArray(Requeteur::getUsers($wiki)));
			$wiki->setRevisionArray(MWJsonDecoder::getRevisionArray(Requeteur::getRevisionsIds($wiki)));
			$wiki->setSectionArray(MWJsonDecoder::getSectionArray(Requeteur::getSections($wiki)));
		}

		// Getters
		public function getArticle(){
			return $this->_article;
		}

		public function getWiki(){
			return $this->_wiki;
		}
		
		public function getID(){
			return $this->_id;
		}
		
		public function getUserArray(){
			return $this->_userArray;
		}
		
		public function getRevisionArray(){
			return $this->_revisionArray;
		}
		
		public function getSectionArray(){
			return $this->_sectionArray;
		}
		
		//Setters 
		public function setArticle($art){	
			$this->_article = $art;
		}
		
		public function setWiki($w){
			$this->_wiki = "http://".$w;
		}
		
		public function setID($id){
			$this->_id = $id;
		}
		
		public function setUserArray($array){
			$this->_userArray = $array;
		}
		
		public function setRevisionArray($array){
			$this->_revisionArray = $array;
		}
		
		public function setSectionArray($array){
			$this->_sectionArray = $array;
		}
		
		/*
			Pour set les variables wiki et article par l'intermédiare d'une URL
		*/
		private function setArticleAndWiki($url){
			$this->setWiki(substr($url, 0,strpos($url,"/")));
			$this->setArticle(substr(strrchr($url,"/"), 1));
		}
	}

?>