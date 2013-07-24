<?php
require_once('AbstractEditAction.php');

class ImageAttribute extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {
		$content = $edit->getContent();
		
		if ($content !== null)
		{
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind != 
					WikiLexerConstants::IMAGE_ATTR )
					return false;
			}
			
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
				$ret[] = new ImageAttribute($b);
			else 
				$newEditList[] = $b;
		}
		
		$editList = $newEditList;
		
		return $ret;
	}

}
