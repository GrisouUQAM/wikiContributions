<?php
require_once('AbstractEditAction.php');

class Dewikify extends AbstractEditAction {

	public function __construct($b1 = null, $b2 = null) {
		if ($b1 === null && $b2 === null)
			$this->be = null;
		else {
			$this->be = array();
			$this->be[] = $b1;
			$this->be[] = $b2;
		}
	}
	
	public function isAction($edit) {
		
		if (!($edit instanceof Deletion) && 
			!($edit instanceof Replacement))
			return false;
		elseif ($edit instanceof Deletion)
		{
			$delEdit = $edit;
			$content = $delEdit->getContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_BEGIN ||
					$content[$i]->kind == WikiLexerConstants::INT_LINK_END ||
					$content[$i]->kind == WikiLexerConstants::BOLD ||
					$content[$i]->kind == WikiLexerConstants::ITALIC)
					return true;
			}
		}
		elseif ($edit instanceof Replacement)
		{
			$replEdit = $edit;
			$content  = $replEdit->getDeletedContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_BEGIN ||
					$content[$i]->kind == WikiLexerConstants::INT_LINK_END ||
					$content[$i]->kind == WikiLexerConstants::BOLD ||
					$content[$i]->kind == WikiLexerConstants::ITALIC)
					return true;
			}
		}
		
		return false;
	}
	
	public function isMarkupOpen($edit) {
		$flag = false;
		
		if (!($edit instanceof Deletion) && !($edit instanceof Replacement))
			return false;
		elseif ($edit instanceof Deletion)
		{
			$delEdit = $edit;
			$content = $delEdit->getContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == WikiLexerConstants::BOLD ||
					$content[$i]->kind == WikiLexerConstants::ITALIC)
					$flag = !($flag);
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_BEGIN)
					$flag = true;
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_END)
					$flag = false;
			}
		}
		elseif ($edit instanceof Replacement)
		{
			$replEdit = $edit;
			$content  = $replEdit->getDeletedContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == WikiLexerConstants::BOLD ||
					$content[$i]->kind == WikiLexerConstants::ITALIC)
					$flag = !($flag);
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_BEGIN)
					$flag = true;
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_END)
					$flag = false;
			}
		}
		
		return $flag;
	}
	
	public function isMarkupClose($edit) {
		$flag = false;
		
		if (!($edit instanceof Deletion) && !($edit instanceof Replacement))
			return false;
		elseif ($edit instanceof Deletion)
		{
			$delEdit = $edit;
			$content = $delEdit->getContent();
			
			for ($i = count($content) - 1; $i >= 0; $i--)
			{
				if ($content[$i]->kind == WikiLexerConstants::BOLD ||
					$content[$i]->kind == WikiLexerConstants::ITALIC)
					$flag = !($flag);
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_BEGIN)
					$flag = false;
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_END)
					$flag = true;
			}
		}
		elseif ($edit instanceof Replacement)
		{
			$replEdit = $edit;
			$content = ( $replEdit->getDeletedContent() );
			
			for ($i = count($content) - 1; $i >= 0; $i--)
			{
				if ($content[$i]->kind == WikiLexerConstants::BOLD ||
					$content[$i]->kind == WikiLexerConstants::ITALIC)
					$flag = !($flag);
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_BEGIN)
					$flag = false;
				if ($content[$i]->kind == WikiLexerConstants::INT_LINK_END)
					$flag = true;
			}
		}
		
		return $flag;
	}
	
	private function array_remove(array &$a_Input, $m_SearchValue, $b_Strict = False) {
	    $a_Keys = array_keys($a_Input, $m_SearchValue, $b_Strict);
	    foreach($a_Keys as $s_Key) {
	        unset($a_Input[$s_Key]);
	    }
	    $a_Input = array_values($a_Input);
	    return $a_Input;
	}
	
	public function classify(&$editList) {
		$ret = array();
		$newEditList = array();
		
		if (count($editList) < 2)
			return $ret;
		
		usort($editList, function($arg0, $arg1) {
			return ($arg0->getOldPos() - $arg1->getOldPos());
		});
		
		for ($i = 0; $i < count($editList) - 1; $i++)
		{
			$b1 = $editList[$i];
			$b2 = $editList[$i+1];
			while (($b2 instanceof Match) && $i < count($editList) - 2 )
				$b2 = $editList[(++$i)+1];
			
			if ($this->isMarkupOpen($b1) && $this->isMarkupClose($b2))
			{
				$newEditList = $this->array_remove($newEditList, $b1);
				if ($b2 instanceof Deletion)
				{
					$ie = $b2;
					$content = $ie->getContent();
					
					for ($j = 0; $j < count($content); $j++)
					{
						if ($content[$j]->kind == WikiLexerConstants::INT_LINK_END ||
							$content[$j]->kind == WikiLexerConstants::BOLD ||
							$content[$j]->kind == WikiLexerConstants::ITALIC)
						{
							$actionContent = array_slice($content, 0, $j+1);
							$remainder = array_slice($content, $j+1, count($content)-$j-1);
							if (count($remainder) > 0)
							{
								$ie->setContent($actionContent);
								$editList[] = new Deletion($ie->getPos()+$j+1, $remainder);
							}
							break;
						}
					}
					$ret[] = new Dewikify($b1, $ie);
					$i++;
				}
				else
				{
					$ret[] = new Dewikify($b1, $b2);
					$i++;
				}
			}
			else {
				if (! in_array($b1, $newEditList)) 
					$newEditList[] = $b1;
				if (! in_array($b2, $newEditList)) 
					$newEditList[] = $b2;
			}
		}
		
		$editList = $newEditList;
		
		return $ret;
	}
}
