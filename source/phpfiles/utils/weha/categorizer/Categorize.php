<?php
require_once('AbstractEditAction.php');

class Categorize extends AbstractEditAction {

	public function __construct($b = null)
	{
		$this->be = array();
		$this->be[] = $b;
	}
	
	public function isAction($edit) {
		if (!($edit instanceof Insertion) && 
			!($edit instanceof Replacement))
			return false;
		elseif ($edit instanceof Insertion)
		{
			$insEdit = $edit;
			$content = $insEdit->getContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == 
					WikiLexerConstants::INT_LINK_PREFIX && 
					$content[$i]->image == "Category:")
					return true;
			}
		}
		else
		{
			$replEdit = $edit;
			$content  = $replEdit->getInsertedContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == 
					WikiLexerConstants::INT_LINK_PREFIX && 
					$content[$i]->image == "Category:")
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
			{
				if ($b instanceof Insertion)
				{
					$ie = $b;
					$content = $ie->getContent();
					$catStartPos = -1;
					
					for ($j = 0; $j < count($content); $j++)
					{
						if ($content[$j]->kind == 
							WikiLexerConstants::INT_LINK_PREFIX && 
							strpos($content[$j]->image, "Category:") !== false)
						{
							$catStartPos = $j - 1;
							continue;
						}
						
						if ($catStartPos >= 0 && $content[$j]->kind == 
							WikiLexerConstants::INT_LINK_END)
							{
							$beforeContent = array_slice($content, 0, $catStartPos);
							$actionContent = array_slice($content, $catStartPos, $j+1-$catStartPos);
							$remainder = array_slice($content, $j+1, count($content)-$j-1);
							
							if (count($beforeContent) > 0)
								$editList[] = new Insertion($ie->getPos(), $beforeContent);
							
							if (count($remainder) > 0)
								$editList[] = new Insertion($ie->getPos()+$j+1, $remainder);
							
							$ie->setContent($actionContent);
							
							break;
						}
					}
					$ret[] = new Categorize($ie);
				}
				elseif ($b instanceof Replacement)
				{
					$ret[] = new Categorize($b);
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
