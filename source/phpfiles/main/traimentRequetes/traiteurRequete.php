<?php
	/*
		Premier jet 
		Phase test
	*/
	include_once("../../classes/traitementRequetes/enums/nomsRequetes.enum.php"); 
	include_once("../../classes/traitementRequetes/wikiArticle.class.php"); 
	include_once("../../classes/traitementRequetes/requeteur.class.php"); 
	include_once("../../classes/traitementRequetes/MWJsonDecoder.class.php");
	
	ini_set('user_agent', 'ProjetWiki (https://github.com/yolo-hipster/yolo-hipster; ralphdsanon@hotmail.com)'); //Contrer erreur 403
	
	//Un simple exemple
	try {
		RequestExecutor::printArray(RequestExecutor::executeRequest("tous_auteurs"));
	} catch (Exception $e){
		echo "Erreur de dans le traitement";
	}
	
	class RequestExecutor {
	
	/*
		Execute et presente les requetes vers MediaWiki	
	*/

		/*
			Retourne un array de donnees selon la requete.
		*/
		public static function executeRequest($requete){
                    
			$wiki = ArticleWiki::createByURL($_GET['url']); //Simple exemple
			$array;
			
			try {
				switch ($requete) {
					case NomsRequetes::ALL_USERS:
						$array = MWJsonDecoder::getUserArray(Requeteur::getUsers($wiki));
						break;
					case NomsRequetes::ALL_REVISIONS:
						$array = MWJsonDecoder::getRevisionArray(Requeteur::getRevisionsIds($wiki));
						break;
					case NomsRequetes::	ALL_SECTIONS:
						$array = MWJsonDecoder::getSectionArray(Requeteur::getSections($wiki));
						break;
					default:
						echo "Requête invalide";
					break;
				}
			} catch (Exception $e){
				echo "Erreur dans la demande vers MediaWiki";
			}
		
			return $array;
		}
		
		/*
			Simple fonction pour afficher les elements d'un array
		*/
		public static function printArray($array){
			if (!empty($array)){
				$content = "";
				foreach ($array as $a){
					$content.= "<br>".$a;
				}
				echo $content;
			}
		}
	
	}
?>