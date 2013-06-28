<?php
include_once('BasicEdit.php');

class Deletion extends BasicEdit {
	
	public function __construct($pos, $content) {
		$this->oldPos = $pos;
		$this->newPos = -1;
		$this->oldTokens = $content;
		$this->newTokens = null;
	}
	
	public function getPos() {
		return $this->oldPos;
	}

	public function setPos($pos) {
		$this->oldPos = $pos;
	}

	public function getContent() {
		return $this->oldTokens;
	}

	public function setContent($actionContent) {
		$this->oldTokens = $actionContent;		
	}
	
	public function getLength() {
		return count($this->oldTokens);
	}

	public function getDescription() {
		return sprintf("Del(%s, %d)", implode('', $this->oldTokens), $this->oldPos);
	}

	public function getNewPos() {
		return $this->newPos;
	}

	public function getOldPos() {
		return $this->oldPos;
	}
}
