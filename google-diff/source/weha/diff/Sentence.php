<?php

class Sentence {

	public $tokens;
	public $startPos;
	public $endPos;
	public $length;
	
	public function __construct()
	{
		$tokens = null;
		$startPos = -1;
		$endPos = -1;
		$length = 0;
	}
}