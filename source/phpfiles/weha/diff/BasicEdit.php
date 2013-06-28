<?php
abstract class BasicEdit {
	protected $oldPos;
	protected $newPos;
	protected $oldTokens;
	protected $newTokens;
	
	public abstract function getDescription();
	public abstract function getContent();
	public abstract function getOldPos();
	public abstract function getNewPos();
}
