<?php
abstract class AbstractEditAction {
	public $be;
	
	public abstract function isAction($edit);
	public abstract function classify(&$editList);
}