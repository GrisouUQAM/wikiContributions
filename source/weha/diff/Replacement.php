<?php
include_once('BasicEdit.php');

class Replacement extends BasicEdit {

	public function __construct($oldPos, array $delContent, $newPos, array $insContent) {
		$this->oldPos = $oldPos;
		$this->newPos = $newPos;
		$this->oldTokens = $delContent;
		$this->newTokens = $insContent;
	}
	
	public function getOldPos() {
		return $this->oldPos;
	}

	public function setOldPos($oldPos) {
		$this->oldPos = $oldPos;
	}

	public function getNewPos() {
		return $this->newPos;
	}

	public function setNewPos($newPos) {
		$this->newPos = $newPos;
	}

	public function getInsertedContent() {
		return $this->newTokens;
	}
	
	public function getInsertedLength() {
		return count($this->newTokens);
	}
	
	public function getDeletedContent() {
		return $this->oldTokens;
	}
	
	public function getDeletedLength() {
		return count($this->oldTokens);
	}
	
	public function getContent() {
		return array_merge($this->oldTokens, $this->newTokens);
	}
	
	public function getDescription() {
		return sprintf("Repl(%s, %d, %s, %d)", implode('', $this->oldTokens), 
			$this->oldPos, implode('', $this->newTokens), $this->newPos);
	}
	
}
