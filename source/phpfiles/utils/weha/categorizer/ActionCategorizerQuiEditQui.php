<?php
include_once( dirname(__FILE__) . '/../WikiLexer.php');
include_once( dirname(__FILE__) . '/../diff/SentenceDiff.php');
include_once('ContentRemoval.php');
include_once('ContentAddition.php');
include_once('ContentSubstitution.php');
include_once('ContentMovement.php');

class ActionCategorizer {
	private $oldTokens;
	private $newTokens;
	private $basicEdits;
	private $sdiff;
	private $actions;
	
	public function __construct(array $oldTokens, array $newTokens) {
		$this->oldTokens = $oldTokens; 
		
		$this->newTokens = $newTokens;
		
		$this->sdiff = new SentenceDiff($this->oldTokens, $this->newTokens);
		
		$this->actions = array();
		$this->actions[] = new ContentSubstitution();
		$this->actions[] = new ContentRemoval();
		$this->actions[] = new ContentAddition();
		//$this->actions[] = new ContentMovement();
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
