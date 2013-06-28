<?php
require_once('AbstractEditAction.php');

class ContentRemoval extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {
		if (!($edit instanceof Deletion))
			return false;
		else
		{
			$delEdit = $edit;
			$content = $delEdit->getContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == 
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
				$ret[] = new ContentRemoval($b);
			else {
				$newEditList[] = $b;
			}
		}

		$editList = $newEditList;

		return $ret;
	}

}
