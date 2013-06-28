<?php
require_once('AbstractEditAction.php');

class ContentSubstitution extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {
		
		if (!($edit instanceof Replacement))
			return false;
		else
		{
			$replEdit = $edit;
			$insContent = $replEdit->getInsertedContent();
			$delContent = $replEdit->getDeletedContent();
			
			$flag = false;
			
			for ($i = 0; $i < count($insContent); $i++)
			{
				if ($insContent[$i]->kind == 
					WikiLexerConstants::WORD)
				{
					$flag = true;
					break;
				}
			}		
			if (!$flag) return false;
			
			for ($i = 0; $i < count($delContent); $i++)
			{
				if ($delContent[$i]->kind == 
					WikiLexerConstants::WORD)
					return true;
			}
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
				$ret[] = new ContentSubstitution($b);
			else {
				$newEditList[] = $b;
			}
		}
		
		$editList = $newEditList;
		
		return $ret;
	}

}
