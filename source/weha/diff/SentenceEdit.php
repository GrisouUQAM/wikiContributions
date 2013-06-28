<?php

abstract class SentenceEdit {
	protected $oldPos;
	protected $newPos;
	protected $oldSentence;
	protected $newSentence;
	protected $matchingRate;
	
	public function getOldStartPos() {
		return $this->oldSentence->startPos;
	}
	
	public function getOldEndPos() {
		return $this->oldSentence->endPos;
	}
	
	public function getOldLength() {
		return count($this->oldSentence);
	}
	
	public function getNewStartPos() {
		return $this->newSentence->startPos;
	}
	
	public function getNewEndPos() {
		return $this->newSentence->endPos;
	}
	
	public function getNewLength() {
		return count($this->newSentence);
	}
	
	public function getMatchingRate() {
		return $this->matchingRate;
	}
	
	public function getOldPos(){
		return $this->oldPos;
	}
	
	public function getNewPos(){
		return $this->newPos;
	}
	
	public abstract function descString();
}
