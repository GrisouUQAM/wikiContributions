<?php
require_once('AbstractEditAction.php');

class TypoCorrection extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}
	
	public function isAction($edit) {
		
		if (!($edit instanceof Replacement))
		{
			return false;
		}
		else
		{
			$replEdit = $edit;
			$insContent = $replEdit->getInsertedContent();
			$delContent = $replEdit->getDeletedContent();
			
			if (count($insContent) * 10 < count($delContent) ||
				count($delContent) * 10 < count($insContent) )
				return false;
			
			$insString = "";
			$delString = "";
			
			for ($i = 0; $i < count($insContent); $i++)
				$insString .= $insContent[$i]->displayString;
			
			for ($i = 0; $i < count($delContent); $i++)
				$delString .= $delContent[$i]->displayString;
			
			if (levenshtein($insString, $delString) < 
				max((strlen($insString) + strlen($delString)) * 0.2, 3))
				return true;
		}
		
		return false;
	}

	public function classify(&$editList) {
		$ret = array();
		$newEditList = array();
		
		for ($i = 0; $i < count($editList); $i++)
		{
			$b = $editList[$i];
			if ($this->isAction($b))
				$ret[] = new TypoCorrection($b);
			else {
				$newEditList[] = $b;
			}
		}
		
		$editList = $newEditList;
		
		return $ret;
	}

}
