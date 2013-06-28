<?php
require_once('AbstractEditAction.php');

class References extends AbstractEditAction {

	public function __construct($b = null) {
		$this->be = array();
		$this->be[] = $b;
	}

	public function isAction($edit) {
		if (!($edit instanceof Insertion) && 
			!($edit instanceof Replacement) )
			return false;
		elseif ($edit instanceof Insertion)
		{
			$insEdit = $edit;
			$content = $insEdit->getContent();

			if ($content[0]->kind == WikiLexerConstants::REF_EMPTY ||
				$content[0]->kind == WikiLexerConstants::REF_BEGIN)
				return true;
		}
		else
		{
			$replEdit = $edit;
			$content  = $replEdit->getInsertedContent();

			if ($content[0]->kind == WikiLexerConstants::REF_EMPTY ||
				$content[0]->kind == WikiLexerConstants::REF_BEGIN)
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
			{
				if ($b instanceof Insertion)
				{
					$ie = $b;
					$content = $ie->getContent();
					
					for ($j = 0; $j < count($content); $j++)
					{
						if ($content[$j]->kind == 
							WikiLexerConstants::REF_END)
						{
							$actionContent = array_slice($content, 0, $j+1);
							$remainder = array_slice($content, $j+1, count($content)-$j-1);
							if (count($remainder) > 0)
							{
								$ie->setContent($actionContent);
								$editList[] = new Insertion($ie->getPos()+$j+1, $remainder);
							}
							break;
						}
					}
					$ret[] = new References($ie);
				}
				else
				{
					$ret[] = new References($b);
				}
			}
			else {
				$newEditList[] = $b;
			}
		}
		
		$editList = $newEditList;
		
		return $ret;
	}

}
