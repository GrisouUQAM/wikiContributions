<?php
include_once('SentenceEdit.php');

class SentenceIns extends SentenceEdit {

	public function __construct($newPos, $newS, $mr) {
		$this->oldPos = -1;
		$this->newPos = $newPos;
		$this->oldSentence = null;
		$this->newSentence = $newS;
		$this->matchingRate = $mr;
	}
	
	public function getStartPos() {
		return $this->newSentence->startPos;
	}
	
	public function getEndPos() {
		return $this->newSentence->endPos;
	}
	
	public function getLength() {
		return $this->newSentence->length;
	}
	
	public function descString() {
		return ("SentenceIns " . $this->newSentence);
	}
}
