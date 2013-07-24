<?php
include_once('Deletion.php');
include_once('Insertion.php');
include_once('Match.php');
include_once('Movement.php');
include_once('Replacement.php');
include_once('TokenTuple.php');

class MatchHeap extends SplHeap {
    public function compare($match1, $match2)
    {
        $values1 = $match1->getLength();
        $values2 = $match2->getLength();
        if ($values1 === $values2) return 0;
        return $values1 < $values2 ? -1 : 1;
    }
}

class TokenDiff {
	
	const maxMatches = 40;

	private $tokenOld;
	private $tokenNew;
	private $indexNew;
	private $matches;

	private $matchedOld;
	private $matchedNew;
	
	private $diff;
	private $resultDiff;
	
	public function getTokenOld() {
		return $this->tokenOld;
	}

	public function getTokenNew() {
		return $this->tokenNew;
	}
	
	public function getMatchedOld() {
		return $this->matchedOld;
	}

	public function getMatchedNew() {
		return $this->matchedNew;
	}
	
	public function __construct($oldToken, $newToken,
					  $matchedOld = null, $matchedNew = null)
	{
		$this->indexNew = array();
		$this->matches = new MatchHeap();
		
		$this->tokenOld = $oldToken;
		$this->tokenNew = $newToken;
		
		// Calculate the hash table for every triple of new version token.
		for ($i = 0; $i < count($this->tokenNew) - 2; $i++)
		{
			$t = new TokenTuple($this->tokenNew[$i], $this->tokenNew[$i+1], $this->tokenNew[$i+2]);
			$t_hashCode = $t->hashCode();
			if (!isset($this->indexNew[$t_hashCode]))
				$this->indexNew[$t_hashCode] = array();
			$this->indexNew[$t_hashCode][] = $i;
		}
		
		if ($matchedOld === null) {
			for ($i = 0; $i < count($this->tokenOld); $i++)
				$this->matchedOld[$i] = new MatchInfo();
		}
		else {
			$this->matchedOld = $matchedOld;
		}
			
		if ($matchedNew === null) {
			for ($i = 0; $i < count($this->tokenNew); $i++)
				$this->matchedNew[$i] = new MatchInfo();
		}
		else {
			$this->matchedNew = $matchedNew;
		}
	}

	private function array_remove(array &$a_Input, $m_SearchValue, $b_Strict = False) {
	    $a_Keys = array_keys($a_Input, $m_SearchValue, $b_Strict);
	    foreach($a_Keys as $s_Key) {
	        unset($a_Input[$s_Key]);
	    }
	    $a_Input = array_values($a_Input);
	    return $a_Input;
	}
	
	private function doDiff()
	{
		$this->diff = array();
		
		for ($i = 0; $i < count($this->tokenOld) - 2; $i++)
		{
			$t = new TokenTuple($this->tokenOld[$i], $this->tokenOld[$i+1], $this->tokenOld[$i+2]);
			$t_hashCode = $t->hashCode();
			if (isset($this->indexNew[$t_hashCode]))
			{
				$index = $this->indexNew[$t_hashCode];
				if (count($index) > $this::maxMatches)
					continue;
				else
				{
					foreach ($index as $i2)
					{
						$len = 0;
						while ( $i  + $len < count($this->tokenOld) &&
								$i2 + $len < count($this->tokenNew) &&
								$this->tokenOld[$i + $len]->equals($this->tokenNew[$i2 + $len]) ) {
									$len++;									
								}
						if ($len > 2)
							$this->matches->insert(new Match($len, $i, $i2));
					}
				}
			}
		}

		$matchId = 0;
		
		while (!$this->matches->isEmpty())
		{
			$matchId++;
			$m = $this->matches->extract();
			
			if ($this->matchedOld[$m->getOldPos()]->matchId === 0 &&
				$this->matchedNew[$m->getNewPos()]->matchId === 0)
			{
				if ($this->matchedOld[$m->getOldPos() + $m->getLength() - 1]->matchId === 0 &&
					$this->matchedNew[$m->getNewPos() + $m->getLength() - 1]->matchId === 0)
				{
					$this->diff[] = $m;
					for ($i = 0; $i < $m->getLength(); $i++)
					{
						$this->matchedOld[$m->getOldPos() + $i]->matchId  = $matchId;
						$this->matchedOld[$m->getOldPos() + $i]->matchPos = $i;
						$this->matchedNew[$m->getNewPos() + $i]->matchId  = $matchId;
						$this->matchedNew[$m->getNewPos() + $i]->matchPos = $i;
						
					}
				}
				else
				{
					$k = $m->getLength() - 1;
					while ($this->matchedOld[$m->getOldPos() + $k]->matchId !== 0 ||
						   $this->matchedNew[$m->getNewPos() + $k]->matchId !== 0)
						$k--;
					
					$residualLen = $k + 1;
					if ($residualLen > 1)
						$this->matches->insert(new Match($residualLen,
										$m->getOldPos(), $m->getNewPos()));
				}
			}
			else
			{
				if ($this->matchedOld[$m->getOldPos() + $m->getLength() - 1]->matchId === 0 &&
					$this->matchedNew[$m->getNewPos() + $m->getLength() - 1]->matchId === 0)
				{
					$j = 1;
					while ($this->matchedOld[$m->getOldPos() + $j]->matchId !== 0 ||
						   $this->matchedNew[$m->getNewPos() + $j]->matchId !== 0)
						$j++;

					$residualLen = $m->getLength() - $j;
					if ($residualLen > 1)
						$this->matches->insert(new Match($residualLen,
										$m->getOldPos() + $j, $m->getNewPos() + $j));
				}
				else
				{
					$j = 1;
					while ($j < $m->getLength() - 1 &&
						   ($this->matchedOld[$m->getOldPos() + $j]->matchId !== 0 ||
							$this->matchedNew[$m->getNewPos() + $j]->matchId !== 0))
						$j++;

					$k = $j + 1;
					while ($k < $m->getLength() - 1 &&
						   !($this->matchedOld[$m->getOldPos() + $k]->matchId !== 0 ||
						   $this->matchedNew[$m->getNewPos() + $k]->matchId !== 0))
						$k++;

					$residualLen = $k - $j;
					if ($residualLen > 1)
						$this->matches->insert(new Match($residualLen,
										$m->getOldPos() + $j, $m->getNewPos() + $j));
				}
			}
		}
		
		// Marking movements
		$matchDiff = array();
		for ($i = 0; $i < count($this->diff); $i++)
			if ($this->diff[$i] instanceof Match)
				$matchDiff[] = $this->diff[$i];
		
		usort($matchDiff, function($arg0, $arg1) {
				return ($arg0->getNewPos() - $arg1->getNewPos());
			}
		);
		
		$order = 0;
		foreach ($matchDiff as $m)
		{
			$m->setOldOrder($order);
			$order += $m->getLength();
		}
		
		usort($matchDiff, function($arg0, $arg1) {
				return ($arg0->getOldPos() - $arg1->getOldPos());
			}
		);
		
		$order = 0;
		foreach ($matchDiff as $m)
		{
			$m->setNewOrder($order);
			$order += $m->getLength();
		}
		
		$sorted = false;
		do {
			$sorted = true;
			$oldOrder = -1;
			foreach ($matchDiff as $m)
			{
				$tmp = $m->getOldOrder();
				if ($oldOrder >= $tmp)
				{
					$sorted = false;
					break;
				}
				else
					$oldOrder = $tmp;
			}
	
			if ($sorted) break;
			
			$distance = array();
			$maxDistance = 0;
			$maxPos = 0;
			$i = 0;
			foreach ($matchDiff as $m)
			{
				$distance[$i] = abs($m->getOldOrder() - $m->getNewOrder());
				if ($maxDistance < $distance[$i])
				{
					$maxDistance = $distance[$i];
					$maxPos = $i;
				}
				$i++;
			}
			
			$removedMatch = $matchDiff[$maxPos];
			foreach ($matchDiff as $m)
			{
				if ($m->getOldOrder() > $removedMatch->getOldOrder())
					$m->setOldOrder($m->getOldOrder() - $removedMatch->getLength());
				
				if ($m->getNewOrder() > $removedMatch->getNewOrder())
					$m->setNewOrder($m->getNewOrder() - $removedMatch->getLength());
			}
			
			$removedMatch->setOldOrder(0);
			$removedMatch->setNewOrder(0);
			
			$wordTokenCount = 0;
			$pos =$removedMatch->getOldPos();
			for ($i = 0; $i < $removedMatch->getLength(); $i++)
			{
				if ($this->tokenOld[$pos+$i]->kind == 
					WikiLexerConstants::WORD)
					$wordTokenCount++;
			}
			
			if ($wordTokenCount > 3)
				$this->diff->insert(new Movement($removedMatch));
			else
			{
				$oldPos = $removedMatch->getOldPos();
				$newPos = $removedMatch->getNewPos();
				for ($i = 0; $i < $removedMatch->getLength(); $i++)
				{
					$this->matchedOld[$oldPos+$i]->matchId  = 0;
					$this->matchedOld[$oldPos+$i]->matchPos = 0;
					$this->matchedNew[$newPos+$i]->matchId  = 0;
					$this->matchedNew[$newPos+$i]->matchPos = 0;
				}
				
			}
			
			$this->diff = $this->array_remove($this->diff, $removedMatch);
			$matchDiff = $this->array_remove($matchDiff, $removedMatch);
			
		} while (!$sorted);
		
		//Marking delete and insert
		$inMatched = true;
		$unmatchedStart = 0;
		
		$delStart = count($this->diff);
		for ($i = 0; $i < count($this->tokenOld); $i++)
		{
			if ($inMatched && $this->matchedOld[$i]->matchId === 0)
			{
				$inMatched = false;
				$unmatchedStart = $i;
			}
			
			if (!$inMatched && $this->matchedOld[$i]->matchId !== 0)
			{
				$inMatched = true;
				if ($i > $unmatchedStart)
				{
					$delTokens = array();
					for ($ii = 0; $ii < $i - $unmatchedStart; $ii++)
						$delTokens[$ii] = $this->tokenOld[$unmatchedStart + $ii];
					
					$this->diff[] = new Deletion($unmatchedStart, $delTokens);
				}
			}
		}
		
		if (!$inMatched && count($this->tokenOld) > $unmatchedStart)
		{
			$delTokens = array();
			for ($ii = 0; $ii < count($this->tokenOld) - $unmatchedStart; $ii++)
				$delTokens[$ii] = $this->tokenOld[$unmatchedStart + $ii];
			
			$this->diff[] = new Deletion($unmatchedStart, $delTokens);
		}
		
		$inMatched = true;
		$unmatchedStart = 0;
		
		$insStart = count($this->diff);
		for ($i = 0; $i < count($this->tokenNew); $i++)
		{
			if ($inMatched && $this->matchedNew[$i]->matchId === 0)
			{
				$inMatched = false;
				$unmatchedStart = $i;
			}
			
			if (!$inMatched && $this->matchedNew[$i]->matchId !== 0)
			{
				$inMatched = true;
				if ($i > $unmatchedStart)
				{
					$insTokens = array();
					for ($ii = 0; $ii < $i - $unmatchedStart; $ii++)
						$insTokens[$ii] = $this->tokenNew[$unmatchedStart + $ii];
					
					$this->diff[] = new Insertion($unmatchedStart, $insTokens);
				}
			}
		}
		
		if (!$inMatched && count($this->tokenNew) > $unmatchedStart)
		{
			$insTokens = array();
			for ($ii = 0; $ii < count($this->tokenNew) - $unmatchedStart; $ii++)
				$insTokens[$ii] = $this->tokenNew[$unmatchedStart + $ii];
			
			$this->diff[] = new Insertion($unmatchedStart, $insTokens);
		}
		
		// Marking replacements
		$replDiff = $this->diff;
		for ($i = $delStart; $i < $insStart; $i++)
		{
			$delBegin = $this->diff[$i]->getPos();
			$delEnd = $delBegin + $this->diff[$i]->getLength();
			$delContent = $this->diff[$i]->getContent();
			
			for ($j = $insStart; $j < count($this->diff); $j++)
			{
				$insBegin = $this->diff[$j]->getPos();
				$insEnd = $insBegin + $this->diff[$j]->getLength();
				$insContent = $this->diff[$j]->getContent();
				
				if (($this->matchedOld[max($delBegin - 1, 0)]->matchId === 
					 $this->matchedNew[max($insBegin - 1, 0)]->matchId) &&
					($this->matchedOld[min($delEnd, count($this->matchedOld) - 1)]->matchId === 
					 $this->matchedNew[min($insEnd, count($this->matchedNew) - 1)]->matchId))
				{
					$replDiff = $this->array_remove($replDiff, $this->diff[$i]);
					$replDiff = $this->array_remove($replDiff, $this->diff[$j]);
					$replDiff[] = new Replacement($delBegin, $delContent, $insBegin, $insContent);
				}
				
			}
		}
		
		// Pair tag add/remove hack: deal with wiki markup add/remove
		// e.g. Test -> [[Test]]
		$this->resultDiff = $replDiff;
		
		for ($i = 0; $i < count($replDiff); $i++)
		{
			$e = $replDiff[$i];
			
			if ($e instanceof Replacement)
			{
				$r = $e;
				$insContent = $r->getInsertedContent();
				$delContent = $r->getDeletedContent();
				
				if (count($insContent) <= count($delContent))
				{
					$maxDelIndex = -1;
					$maxInsIndex = -1;
					$maxLength = 0;
					
					for ($j = 0; $j < count($insContent); $j++)
					{
						$delIndex = false;
						for($k = 0; $k < count($delContent); $k++) {
							$tmp = $delContent[$k];
							if ($insContent[$j]->kind  == $tmp->kind &&
								$insContent[$j]->image == $tmp->image) {
								$delIndex= $k;
								break;
							}
						}
					
						if ($delIndex !== false)
						{
							$containLength = 1;
							
							for ($k = 1; $j + $k < count($insContent) && 
								 $k + $delIndex < count($delContent); $k++)
							{
								if ($insContent[$j+$k]->equals($delContent[$delIndex+$k]))
									$containLength++;
								else
									break;
							}
							
							if ($containLength > $maxLength)
							{
								$maxDelIndex = $delIndex;
								$maxInsIndex = $j;
								$maxLength = $containLength;
							}
						}
					}
					
					if ($maxLength > 0)
					{
						$d1 = array_slice($delContent, 0, $maxDelIndex);
						$d2 = array_slice($delContent, $maxDelIndex + $maxLength, count($delContent) - $maxDelIndex - $maxLength);
						$i1 = array_slice($insContent, 0, $maxInsIndex);
						$i2 = array_slice($insContent, $maxInsIndex + $maxLength, count($insContent) - $maxInsIndex - $maxLength);
						
						if (count($d1) > 0 && count($i1) == 0)
							$this->resultDiff[] = new Deletion($r->getOldPos(), $d1);
						elseif (count($d1) > 0 && count($i1) > 0)
						{
							$newRepl = new Replacement($r->getOldPos(), 
									 $d1, $r->getNewPos(), $i1);
							
							$replDiff[] = $newRepl;
							$this->resultDiff[] = $newRepl;
						}
						
						if (count($d2) > 0 && count($i2) == 0)
							$this->resultDiff[] = new Deletion($r->getOldPos() + $maxDelIndex + $maxLength, $d2);
						elseif (count($d2) > 0 && count($i2) > 0) 
						{
							$newRepl = new Replacement($r->getOldPos() + $maxDelIndex + $maxLength, 
									 $d2, $r->getNewPos() + $maxInsIndex + $maxLength, $i2);
							
							$replDiff[] = $newRepl;
							$this->resultDiff[] = $newRepl;
						}
						
						$this->resultDiff = $this->array_remove($this->resultDiff, $r);
					}
				}
				elseif (count($insContent) > count($delContent))
				{
					$maxDelIndex = -1;
					$maxInsIndex = -1;
					$maxLength = 0;
					
					for ($j = 0; $j < count($delContent); $j++)
					{
						$insIndex = false;
						for($k = 0; $k < count($insContent); $k++) {
							$tmp = $insContent[$k];
							if ($delContent[$j]->kind  == $tmp->kind &&
								$delContent[$j]->image == $tmp->image) {
								$insIndex= $k;
								break;
							}
						}

						if ($insIndex !== false)
						{
							$containLength = 1;
							
							for ($k = 1; $j + $k < count($delContent) && $k + $insIndex < count($insContent); $k++)
							{
								if ($delContent[$j+$k]->equals($insContent[$insIndex+$k]))
									$containLength++;
								else
									break;
							}
							
							if ($containLength > $maxLength)
							{
								$maxDelIndex = $j;
								$maxInsIndex = $insIndex;
								$maxLength = $containLength;
							}
						}
					}
					
					if ($maxLength > 0)
					{
						$d1 = array_slice($delContent, 0, $maxDelIndex);
						$d2 = array_slice($delContent, $maxDelIndex + $maxLength, count($delContent) - $maxDelIndex - $maxLength);
						$i1 = array_slice($insContent, 0, $maxInsIndex);
						$i2 = array_slice($insContent, $maxInsIndex + $maxLength, count($insContent) - $maxInsIndex - $maxLength);
						
						if (count($i1) > 0 && count($d1) == 0)
							$this->resultDiff[] = new Insertion($r->getNewPos(), $i1);
						elseif (count($i1) > 0 && count($d1) > 0)
						{
							$newRepl = new Replacement($r->getOldPos(), 
									 $d1, $r->getNewPos(), $i1);
							
							$replDiff[] = $newRepl;
							$this->resultDiff[] = $newRepl;
						}
						
						if (count($i2) > 0 && count($d2) == 0)
							$this->resultDiff[] = new Insertion($r->getNewPos() + $maxInsIndex + $maxLength, $i2);
						elseif (count($i2) > 0 && count($d2) > 0) 
						{
							$newRepl = new Replacement($r->getOldPos() + $maxDelIndex + $maxLength, 
									 $d2, $r->getNewPos() + $maxInsIndex + $maxLength, $i2);
							
							$replDiff[] = $newRepl;
							$this->resultDiff[] = $newRepl;
						}
						
						$this->resultDiff = $this->array_remove($this->resultDiff, $r);
					}
				}

			}
		}
		
		
		// Sort the diff output
		usort($this->resultDiff, function($o1, $o2) {
			
			if ($o1 instanceof Insertion)
			{
				if ($o2 instanceof Insertion)
					return ($o1->getPos() - $o2->getPos());
				else
					return -1;
			}
			elseif ($o1 instanceof Deletion)
			{
				if ($o2 instanceof Insertion)
					return 1;
				elseif ($o2 instanceof Deletion)
					return ($o1->getPos() - $o2->getPos());
				else
					return -1;
			}
			elseif ($o1 instanceof Replacement)
			{
				if ($o2 instanceof Insertion || $o2 instanceof Deletion)
					return 1;
				elseif ($o2 instanceof Replacement)
					return ($o1->getOldPos() - $o2->getOldPos());
				else
					return -1;
			}
			elseif ($o1 instanceof Match)
			{
				if ($o2 instanceof Insertion ||
					$o2 instanceof Deletion ||
					$o2 instanceof Replacement)
					return 1;
				elseif ($o2 instanceof Match)
					return ($o1->getOldPos() - $o2->getOldPos());
				else
					return -1;
			}
			elseif ($o1 instanceof Movement)
			{
				if (!($o2 instanceof Movement))
					return 1;
			}
			
			return 0;
		});
	}
	
	public function outputDiff()
	{	
		$this->doDiff();
		return $this->resultDiff;
	}
	
}
