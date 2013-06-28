<?php
include_once('SentenceEdit.php');

class SentenceMatch extends SentenceEdit {

	private $oldOrder;
	private $newOrder;

	public function __construct($oldPos, $newPos, $oldS, $newS, $mr) {
		$this->oldPos = $oldPos;
		$this->newPos = $newPos;
		$this->oldSentence = $oldS;
		$this->newSentence = $newS;
		$this->matchingRate = $mr;
		$this->oldOrder = -1;
		$this->newOrder = -1;
	}

	public function descString() {
		return ("SentenceMatch " . $this->oldSentence . " " . $this->newSentence);
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
}
