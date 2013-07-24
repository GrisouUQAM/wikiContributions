<?php
require_once('AbstractEditAction.php');

class EditorialComments extends AbstractEditAction {

	private $editorialTemplates;
	
	public function __construct($b = null) {
		if ($b === null)
		{
			$this->editorialTemplates = array();
			$this->editorialTemplates[] = "{{fact";
			$this->editorialTemplates[] = "{{unreferenced";
			$this->editorialTemplates[] = "{{stub";
			$this->editorialTemplates[] = "{{vgood-small";
			$this->be = null;
		}
		else
		{		
			$this->be = array();
			$this->be[] = $b;
		}
	}
	
	public function isAction($edit) {
		
		if (!($edit instanceof Insertion))
			return false;
		else
		{
			$insEdit = $edit;
			$content = $insEdit->getContent();
			
			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == 
					WikiLexerConstants::TEMPLATE_BEGIN &&
					in_array(strtolower($content[$i]->image), $this->editorialTemplates) )
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
					
					for ($j = 0; $j < count($content); $j++)
					{
						if ($content[$j]->kind == 
							WikiLexerConstants::TEMPLATE_END)
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
					$ret[] = new EditorialComments($ie);
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
