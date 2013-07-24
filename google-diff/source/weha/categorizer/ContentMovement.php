<?php
require_once('AbstractEditAction.php');

class ContentMovement extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {
		if (!($edit instanceof Movement))
			return false;
		else
			return true;
	}

	public function classify(&$editList) {
		$ret = array();
		$newEditList = array();
		
		for ($i = 0; $i < count($editList); $i++)
		{
			$b = $editList[$i];
			if ($this->isAction($b))
				$ret[] = new ContentMovement($b);
			else {
				$newEditList[] = $b;
			}
		}
		
		$editList = $newEditList;
		
		return $ret;
	}

}
