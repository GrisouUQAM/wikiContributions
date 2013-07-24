<?php
require_once('AbstractEditAction.php');

class PunctuationCorrection extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {
		
		if (($edit instanceof Match) || ($edit instanceof Movement))
		{
			return false;
		}
		elseif ($edit instanceof Replacement)
		{
			$replEdit = $edit;
			$insContent = $replEdit->getInsertedContent();
			$delContent = $replEdit->getDeletedContent();
			
			for ($i = 0; $i < count($insContent); $i++)
				if ($insContent[$i]->kind != 
					WikiLexerConstants::SYMBOL)
					return false;

			for ($i = 0; $i < count($delContent); $i++)
				if ($delContent[$i]->kind != 
					WikiLexerConstants::SYMBOL)
					return false;
		}
		elseif ($edit instanceof Insertion)
		{
			$insEdit = $edit;
			$insContent = $insEdit->getContent();
			
			for ($i = 0; $i < count($insContent); $i++)
				if ($insContent[$i]->kind != 
					WikiLexerConstants::SYMBOL)
					return false;
		}
		elseif ($edit instanceof Deletion)
		{
			$delEdit = $edit;
			$delContent = $delEdit->getContent();

			for ($i = 0; $i < count($delContent); $i++)
				if ($delContent[$i]->kind != 
					WikiLexerConstants::SYMBOL)
					return false;
		}
		
		return true;
	}
	
	public function classify(&$editList) {
		$ret = array();
		$newEditList = array();
		
		for ($i = 0; $i < count($editList); $i++)
		{
			$b = $editList[$i];
			if ($this->isAction($b))
				$ret[] = new PunctuationCorrection($b);
			else {
				$newEditList[] = $b;
			}
		}
		
		$editList = $newEditList;
		
		return $ret;
	}

}
