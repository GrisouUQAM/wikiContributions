<?php
class WikiToken {

	public $kind;
	public $image;
	public $displayString;
	public $userName;
	public $editionId;
	private $tokenHash;
	
	/**
	 * Constructs a new token for the specified Image and Kind.
	 * @param kind
	 * @param image
	 * @param $displayString
	 */
	public function __construct($kind, $image, $displayString = null, $userName = null, $revisionId = null) {
		$this->kind = $kind;
		$this->image = $image;
		$this->userName = $userName;
		$this->editionId = $revisionId;
		
		if ($displayString === null)
			$this->displayString = $image;
		else
			$this->displayString = $displayString;
		
		$this->tokenHash = uniqid();
	}
	
	public function createWikiToken($token, $displayString = null) {
		$this->kind = $token->kind;
		$this->image = $token->image;
		
		if ($displayString === null)
			$this->displayString = $token->image;
		else
			$this->displayString = $displayString;
	}
	
	public function __toString() {
		return $this->displayString;
	}
	
	public function hashCode() {
		$prime = 31;
		$result = 1;
		$result = $prime * $result + (($this->image == null) ? 0 : crc32($this->image));
		$result = $prime * $result + $this->kind;
		return $result;
	}

	public function equals($obj) {
		if ($this === $obj)
			return true;
		if ($obj === null)
			return false;
		if (get_class() != get_class($obj))
			return false;
		$other = $obj;
		if ($this->image === null) {
			if ($other->image !== null)
				return false;
		} else if ($this->image != $other->image)
			return false;
		if ($this->kind != $other->kind)
			return false;
		return true;
	}
	
	public function getHash(){
		return $this->tokenHash;
	}
	
	
}
