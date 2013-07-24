<?php
include_once('BasicEdit.php');

class Match extends BasicEdit {

	private $length;

	private $oldOrder;
	private $newOrder;
	
	public function getLength() {
		return $this->length;
	}

	public function getOldPos() {
		return $this->oldPos;
	}

	public function getNewPos() {
		return $this->newPos;
	}
	
	public function getOldOrder() {
		return $this->oldOrder;
	}

	public function setOldOrder($oldOrder) {
		$this->oldOrder = $oldOrder;
	}

	public function getNewOrder() {
		return $this->newOrder;
	}

	public function setNewOrder($newOrder) {
		$this->newOrder = $newOrder;
	}

	public function __construct($length, $oldPos, $newPos) {
		$this->length = $length;
		$this->oldPos = $oldPos;
		$this->newPos = $newPos;
		$this->oldTokens = null;
		$this->newTokens = null;
	}

	public function getDescription() {
		return sprintf("Match(%d, %d, %d)", $this->length, $this->oldPos, $this->newPos);
	}

	public function getContent() {
		return null;
	}

}
