<?php
require_once('AbstractEditAction.php');

class Uncategorized extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {

		$content = ( $edit->getContent() );
		if ($content != null && 
			count($content) == 1 && 
			$content[0]->kind == 
			WikiLexerConstants::NL)
			return false;
		
		if (!($edit instanceof Match))
			return true;
		
		return false;
	}

	public function classify(&$editList) {
		$ret = array();
		$newEditList = array();
		
		for ($i = 0; $i < count($editList); $i++)
		{
			$b = $editList[$i];
			if ($this->isAction($b))
				$ret[] = new Uncategorized($b);
			else
				$newEditList[] = $b;
		}
		
		$editList = $newEditList;
		
		return $ret;
	}
}
