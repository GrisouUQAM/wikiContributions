<?php
include_once( dirname(__FILE__) . '/../diff/SentenceDiff.php');
include_once( dirname(__FILE__) . '/../WikiLexer.php');
include_once('EditorialComments.php');
include_once('Categorize.php');
include_once('InterwikiLinks.php');
include_once('Wikify.php');
include_once('Dewikify.php');
include_once('PunctuationCorrection.php');
include_once('ImageAttribute.php');
include_once('TypoCorrection.php');
include_once('References.php');
include_once('ContentRemoval.php');
include_once('ContentAddition.php');
include_once('ContentSubstitution.php');
include_once('ContentMovement.php');
include_once('Uncategorized.php');

class ActionCategorizer {
	private $oldTokens;
	private $newTokens;
	private $basicEdits;
	private $sdiff;
	private $actions;
	
	public function __construct($oldStr, $newStr) {
		$scanner = new WikiLexer($oldStr);
		$this->oldTokens = $scanner->getWikiTokens();
		
		$scanner = new WikiLexer($newStr);
		$this->newTokens = $scanner->getWikiTokens();
		
		$this->sdiff = new SentenceDiff($this->oldTokens, $this->newTokens);
		
		$this->actions = array();
		$this->actions[] = new EditorialComments();
		$this->actions[] = new Categorize();
		$this->actions[] = new InterwikiLinks();
		$this->actions[] = new Wikify();
		$this->actions[] = new Dewikify();
		$this->actions[] = new PunctuationCorrection();
		$this->actions[] = new ImageAttribute();
		$this->actions[] = new TypoCorrection();
		$this->actions[] = new References();
		$this->actions[] = new ContentSubstitution();
		$this->actions[] = new ContentRemoval();
		$this->actions[] = new ContentAddition();
		$this->actions[] = new ContentMovement();
		$this->actions[] = new Uncategorized();
	}

	public function getBasicEdits() {
		return $this->basicEdits;
	}

	public function printResult()
	{
		$retString = array();
		$outStr = "";
		
		$retString[] = $this->sdiff->separateSentences();
		$this->sdiff->exactMatch();
		$retString[] = $this->sdiff->diff();
		
		$this->basicEdits = $this->sdiff->outputDiff();
		$retString[] = $this->sdiff->matchingSentence();
		$retString[] = $this->sdiff->mergeSentences();
		
		for ($i = 0; $i < count($this->basicEdits); $i++)
		{
			if (!($this->basicEdits[$i] instanceof Match))
				$outStr .= $this->basicEdits[$i]->getDescription();
				$outStr .= "\n";
		}
		$outStr .= "\n";
		
		$retString[] = $outStr;
		
		return $retString;
	}
	
	public function categorize()
	{
		$retString = "";
		$beList = $this->basicEdits;
		$ret = array();
		
		for ($j = 0; $j < count($this->actions); $j++) {
			$ae = $this->actions[$j];
			$ret = array_merge($ret, $ae->classify($beList));
		}
		
		// Print the categorized actions 
		foreach ($ret as $action)
		{
			$retString .= get_class($action) . " ";
			for ($i = 0; $i < count($action->be); $i++)
				$retString .= $action->be[$i]->getDescription() . "; ";
			$retString .= "\n";
		}
		
		return $retString;
	}
	
	public function getTokenOld() {
		return $this->oldTokens;
	}
	
	public function getTokenNew() {
		return $this->newTokens;
	}
	
	public function getSentenceEdits() {
		return $this->sdiff->getSentenceEdits();
	}
}
