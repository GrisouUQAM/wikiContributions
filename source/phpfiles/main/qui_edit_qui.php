<?php 
	include_once("../classes/traitementRequetes/requeteur.class.php");
	include_once("../utils/weha/categorizer/ActionCategorizerQuiEditQui.php");
	ini_set('user_agent', 'ProjetWiki (https://github.com/yolo-hipster/yolo-hipster; rlamour2@yahoo.ca)');
	
	var_dump(QuiEditQui::QuiEditerTexteDeQui("en.wikipedia.org/wiki/Alfred_Poupart"));
	
	class QuiEditQui{
	
		/*
		 * A function that return a JSON string contains 
		 * Who is editing who's text. See Yolo-Hipster Wiki for more informations.
		 * 
		 * Uses Weha/diff/SentenceIns to detect a new Sentence.
		 * Uses Weha/diff/SentenceDel to detect a sentence that has been deleted.
		 * 
		 * 
		 */
		static function QuiEditerTexteDeQui($url)
		{
			//Get revisions from the article
			$wikipage = ArticleWiki::createByURL($url);
			$wikiRevs = Requeteur::getAllRevisions($wikipage);
			
			//Init class that manage the output
			$organizer = new QuiEditQuiOutPutOrganizer();
			
			if(!empty($wikiRevs))
			{
				//Create the first iteration. (Insertion only)
				$EditId = 1;
				$lexer = new WikiLexer($wikiRevs[0]["*"]);
				$newText = $lexer->getWikiTokens($wikiRevs[0]["user"],$EditId);
				$oldText = Array(); //Empty first text
				$ac = new ActionCategorizer($oldText , $newText);
				$ac->printResult(); //Necessary to 
				$ac->categorize();  //Track modifications.
				$newEdit = new QuiEditQuiElem($EditId, $wikiRevs[0]["user"]);
				$organizer->AddEdit($newEdit);
				foreach ($ac->getSentenceEdits() as $edit){
					$newEdit->LinesAdded++;
				}
				
				//Init the master array of tokens where all the modifications should be registered
				$oldText = $newText;
				$tokenMasterList = new MainTextArray($oldText);
				
				//Loop from the second revision to the last one.
				for($i = 1;$i < count($wikiRevs); $i++){
					$currentEditId = $i + 1; //human id
					//Tokenize the new revision and compare it with the previous revision (as $oldText).
					$lexer = new WikiLexer($wikiRevs[$i]["*"]);
					$newText = $lexer->getWikiTokens($wikiRevs[$i]["user"],$currentEditId);	
					$ac = new ActionCategorizer($oldText , $newText);
					$ac->printResult();
					$ac->categorize();
					
					foreach($ac->getSentenceEdits() as $edit){
						$newEdit = new QuiEditQuiElem($currentEditId, $wikiRevs[$i]["user"]);
						
						// Uses weha/diff/SentenceIns to detect a new sentence
						// Get the token before to associate the edit to an existing one.
						if($edit instanceof SentenceIns){
							$newEdit->LinesAdded = 1;
							$hashbefore = null;
							if($edit->getOldPos() > -1){
								$newEdit->RelatedUser = $oldText[$edit->getOldPos()]->userName;
								$newEdit->RelatedId = $oldText[$edit->getOldPos()]->editionId;
								$hashbefore = $oldText[$edit->getOldPos()]->getHash();
							} else if(count($oldText) > 0){
								$newEdit->RelatedUser = $oldText[0]->userName;
								$newEdit->RelatedId = $oldText[0]->editionId;
							}
							
							$organizer->addEdit($newEdit);
							//Get the tokens to add to the master text
							$tempInsArr = array_slice($newText, $edit->getNewPos() + 1, $edit->getNewLength());
							//Register the modifications to the master text
							$tokenMasterList->InsertTokens($tempInsArr, $hashbefore); 
							
							// Uses the Weha/diff/SentenceDel to detect sentence that has been removed.
							// Uses the removed tokens to detect who has written this sentence.
						} else if($edit instanceof SentenceDel){
							
							$newEdit->RelatedUser = $oldText[$edit->getOldPos()]->userName;
							$newEdit->RelatedId = $oldText[$edit->getOldPos()]->editionId;
							$newEdit->LinesRemoved = 1;
							
							
							$firstHash = $oldText[$edit->getOldPos() + 1]->getHash();
							//To be enhance. Have to return the number of tokens to remove
							$length = $edit->getOldLength() - $edit->getNewLength(); 
							$organizer->addEdit($newEdit);
							//Register the modification to the master text
							$tokenMasterList->DeleteTokens($firstHash, $length);
							
							//This part is to be analysed. How to detect modificaitons.
						} else {
							$newEdit->RelatedUser = $oldText[$edit->getOldPos()]->userName;
							$newEdit->RelatedId = $oldText[$edit->getOldPos()]->editionId;
							$newEdit->LinesModified = 1;
							//
							$organizer->addEdit($newEdit);
						}
						
					} //fin boucle edits
					//Set the the master token list as the last modification.
					$oldText = $tokenMasterList->getArray();
				} //fin boucle revisions
				
			}
			//var_dump($organizer->getEditList());
			//echo json_encode($organizer->getEditList());
			return json_encode($organizer->getEditList());
		}
	}
	
	/*
	 * Not working yet
	 * Class that contains the main tokens repository 
	 * You can merge content from other revisions by using
	 * methods.
	 */
	class MainTextArray{
		private $innerList;
		
		public function __construct($array = null){
			if($array != null && is_array($array)){
				$this->innerList = $array;
			} else {
				$this->innerList = Array();
			}
		}
		
		/*
		 * NotWorking
		 * Will insert the $array after the hashcode specified.
		 * If hashcode is null, will insert at index 0.
		 */
		public function InsertTokens($array, $hashbefore = null){
			$list = $this->innerList;
			$splitId = 0;
			if($hashbefore != null){
				$splitId = $this->findNode($hashbefore);
			}
			
			$firstPart = array_slice($list, 0, $splitId);
			$lastPart = array_slice($list, $splitId, count($list) - $splitId);
			
			$this->innerList = array_merge($firstPart, $array, $lastPart);
		}
		
		private function findNode($hashcode){
			$list = $this->innerList;
			reset($list);
			$nodeIndex = 0;
			while(current($list) != null && current($list)->getHash() != $hashcode){
				next($list);
				$nodeIndex++;
			}
			
			if(current($list) == null){
				return -1;
			} else {
				return $nodeIndex;
			}
		}
		
		public function DeleteTokens($FirstHash, $length){
			$nodeIndex = $this->findNode($FirstHash);
			//echo "node index: " . $nodeIndex;
			if($nodeIndex >= 0){
				array_splice($this->innerList, $nodeIndex, $length);
			}
		}
		
		public function getArray(){
			return $this->innerList;
		}
	}
	
	class QuiEditQuiOutPutOrganizer{
		private $EditList;
		private $InnerEditList;
		
		public function __construct(){
			$this->EditList = Array();
			$this->InnerEditList = Array();
		}
		
		public function AddEdit(QuiEditQuiElem $elem){
			
			if(!array_key_exists($elem->Id, $this->InnerEditList)){
				$this->InnerEditList[$elem->Id] = Array();
				$this->InnerEditList[$elem->Id][$elem->RelatedId] = $elem;  
				$this->EditList[] = $elem;
			} else {
				if(!array_key_exists($elem->RelatedId, $this->InnerEditList[$elem->Id])){
					$this->InnerEditList[$elem->Id][$elem->RelatedId] = $elem;
					$this->EditList[] = $elem;
				} else {
					$RegisteredElem = $this->InnerEditList[$elem->Id][$elem->RelatedId];
					$RegisteredElem->LinesAdded += $elem->LinesAdded;
					$RegisteredElem->LinesRemoved += $elem->LinesRemoved;
					$RegisteredElem->LinesModified += $elem->LinesModified;
				}
			}
		}
		
		public function getEditList(){
			return $this->EditList;
		}
	}
	
	class QuiEditQuiElem{
		public $Id;
		public $UserName;
		public $LinesAdded = 0;
		public $LinesRemoved = 0;
		public $LinesModified = 0;
		public $RelatedId;
		public $RelatedUser;
		
		public function __construct($Id, $UserName){
			$this->Id = $Id;
			$this->UserName = $UserName;
		}
	}
?>