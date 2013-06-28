<?php
include_once('SentenceEdit.php');

class SentenceMove extends SentenceMatch {

	public function __construct($oldPos, $newPos, $oldS, $newS, $mr)
	{
		parent::__construct($oldPos, $newPos, $oldS, $newS, $mr);
	}

}
