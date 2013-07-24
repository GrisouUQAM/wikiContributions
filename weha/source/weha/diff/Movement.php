<?php
class Movement extends BasicEdit {

	private $length;
	
	public function getLength() {
		return $this->length;
	}

	public function getOldPos() {
		return $this->oldPos;
	}

	public function getNewPos() {
		return $this->newPos;
	}
	
	public function setOldPos($oldPos) {
		$this->oldPos = $oldPos;
	}

	public function setNewPos($newPos) {
		$this->newPos = $newPos;
	}

	public function __construct($length, $oldPos, $newPos) {
		$this->length = $length;
		$this->oldPos = $oldPos;
		$this->newPos = $newPos;
		$this->oldTokens = null;
		$this->newTokens = null;
	}

	public function getDescription() {
		return sprintf("Mov(%d, %d, %d)", $this->length, $this->oldPos, $this->newPos);
	}

	public function getContent() {
		return null;
	}

}
