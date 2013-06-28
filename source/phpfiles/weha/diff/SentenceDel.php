<?php
include_once('SentenceEdit.php');

class SentenceDel extends SentenceEdit {

	public function __construct($oldPos, $oldS, $mr) {
		$this->oldPos = $oldPos;
		$this->newPos = -1;
		$this->oldSentence = $oldS;
		$this->newSentence = null;
		$this->matchingRate = $mr;
	}
	
	public function getStartPos() {
		return $this->oldSentence->startPos;
	}
	
	public function getEndPos() {
		return $this->oldSentence->endPos;
	}
	
	public function getLength() {
		return $this->oldSentence->length;
	}
	
	public function descString() {
		return ("SentenceDel " . $this->oldSentence);
	}
}
