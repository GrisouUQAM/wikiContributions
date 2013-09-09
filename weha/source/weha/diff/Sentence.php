<?php

class Sentence {

	public $tokens;
	public $startPos;
	public $endPos;
	public $length;
	
	public function __construct()
	{
		$this->tokens = null;
		$this->startPos = -1;
		$this->endPos = -1;
		$this->length = 0;
	}
}