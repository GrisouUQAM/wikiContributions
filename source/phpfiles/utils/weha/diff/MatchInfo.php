<?php

class MatchInfo {

	public $matchId;
	public $matchPos;

	public function __construct()
	{
		$this->matchId  = 0;
		$this->matchPos = 0;
	}
	
	public function __toString()
	{
		return ($this->matchId . "\t" . $this->matchPos);
	}
}
