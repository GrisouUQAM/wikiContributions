<?php
class TokenTuple {
	const SIZE = 3;
	private $tokens;
	
	public function __construct($t0, $t1, $t2)
	{
		$this->tokens = array();
		$this->tokens[0] = $t0;
		$this->tokens[1] = $t1;
		$this->tokens[2] = $t2;
	}
	
	public function hashCode() {
		$prime = 31;
		$result = 1;
		
		for ($i = 0; $i < self::SIZE; $i++)
			$result = $prime * $result + $this->tokens[$i]->hashCode();

		return $result;
	}
	
}
