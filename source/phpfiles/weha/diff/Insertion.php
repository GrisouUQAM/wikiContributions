<?php
include_once('BasicEdit.php');

class Insertion extends BasicEdit {

	public function __construct($pos, array $content) {
		$this->oldPos = -1;
		$this->newPos = $pos;
		$this->oldTokens = null;
		$this->newTokens = $content;
	}
	
	public function setStartPos($pos) {
		$this->newPos = $pos;
	}
	
	public function getPos() {
		return $this->newPos;
	}
	
	public function getContent() {
		return $this->newTokens;
	}

	public function setContent($actionContent) {
		$this->newTokens = $actionContent;		
	}
	
	public function getLength() {
		return count($this->newTokens);
	}
	
	public function getDescription() {
		return sprintf("Ins(%s, %d)", implode('', $this->newTokens), $this->newPos);
	}

	public function getNewPos() {
		return $this->newPos;
	}

	public function getOldPos() {
		return $this->oldPos;
	}
}
