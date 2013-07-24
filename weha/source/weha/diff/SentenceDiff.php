<?php
include_once('Sentence.php');
include_once('SentenceDel.php');
include_once('SentenceIns.php');
include_once('SentenceMatch.php');
include_once('SentenceMove.php');
include_once('SentenceSplitter.php');
include_once('MatchInfo.php');
include_once('TokenDiff.php');

class SentenceDiff {

	private $oldTokens;
	private $newTokens;
	private $oldSentences;
	private $newSentences;
	
	private $tokenDiff;
	
	private $matchingRate;
	private $edit;
	private $sentenceEdits;
	private $oldMatchedSentences;
	private $newMatchedSentences;
	
	private $oldMatches;
	private $newMatches;
	
	const threshold = 0.333;
	
	public function __construct($oldTokens, $newTokens)
	{
		$this->oldTokens = $oldTokens;
		$this->newTokens = $newTokens;
	
		$this->oldSentences = array();
		$this->newSentences = array();
		$this->sentenceEdits = array();
	}
	
	public function getOldTokens() {
		return $this->oldTokens;
	}

	public function getNewTokens() {
		return $this->newTokens;
	}

	public function diff()
	{
		// Execute the token differencing algorithm.
		if ($this->oldMatches != null)
			$this->tokenDiff = new TokenDiff($this->oldTokens, $this->newTokens, $this->oldMatches, $this->newMatches);
		else
			$this->tokenDiff = new TokenDiff($this->oldTokens, $this->newTokens);
		
		return "";
	}
	
	public function outputDiff()
	{
		$this->edit = $this->tokenDiff->outputDiff();
		
		return $this->edit;
	}

	public function separateSentences()
	{
		$retString = '';
		
		// Separate old tokens stream into sentences.
		$this->oldSentences = SentenceSplitter::separateSentence($this->oldTokens);

		foreach ($this->oldSentences as $s)
		{
			$retString .= ($s->startPos + ": ");
			$retString .= (implode(' ', $s->tokens) + "\n");
		}
		
		// Separate new tokens stream into sentences.
		$this->newSentences = SentenceSplitter::separateSentence($this->newTokens);
		
		foreach ($this->newSentences as $s)
		{
			$retString .= ($s->startPos + ": ");
			$retString .= (implode(' ', $s->tokens) + "\n");
		}
		
		return $retString;
	}
	
	public function exactMatch()
	{	
		$this->oldMatchedSentences = array();
		$this->newMatchedSentences = array();
		
		$this->oldMatches = array();
		$this->newMatches = array();
		$matchId = 0;

		for ($i = 0; $i < count($this->oldTokens); $i++)
			$this->oldMatches[$i] = new MatchInfo();
		for ($i = 0; $i < count($this->newTokens); $i++)
			$this->newMatches[$i] = new MatchInfo();
		
		$this->matchingRate = array();

		for ($i = 0; $i < count($this->oldSentences); $i++)
		{
			if (in_array($i, $this->oldMatchedSentences)) 
				continue;
			
			$oldStart = $this->oldSentences[$i]->startPos;
			$oldLen = $this->oldSentences[$i]->length;

			for ($j = 0; $j < count($this->newSentences); $j++)
			{
				if (in_array($j, $this->newMatchedSentences)) 
					continue;
				
				$match = true;
				$newStart = $this->newSentences[$j]->startPos;
				$newLen = $this->newSentences[$j]->length;

				if ($oldLen != $newLen) 
					continue;

				for ($k = 0; $k < $oldLen; $k++)
				{
					if ($this->oldTokens[$oldStart + $k] != $this->newTokens[$newStart + $k])
					{
						$match = false;
						break;
					}
				}

				if ($match)
				{
					$this->matchingRate[$i][$j] = 1.0;
					$this->oldMatchedSentences[] = $i;
					$this->newMatchedSentences[] = $j;
					
					$matchId--;
					for ($k = 0; $k < $oldLen; $k++)
					{
						$this->oldMatches[$oldStart + $k]->matchId  = $matchId;
						$this->oldMatches[$oldStart + $k]->matchPos = $k;
						$this->newMatches[$newStart + $k]->matchId  = $matchId;
						$this->newMatches[$newStart + $k]->matchPos = $k;
					}
					
					break;
				}
			}
		}
	}
	
	private static function compare_oldpos($o1, $o2) {
		return ($o1->oldPos - $o2->oldPos);
	}
	
	private static function compare_newpos($a1, $a2) {
		return ($a1->newPos - $a2->newPos);
	}
	
	public function matchingSentence()
	{	
		$retString = '';
		
		$matchedOld = $this->tokenDiff->getMatchedOld();
		$matchedNew = $this->tokenDiff->getMatchedNew();
		
		$this->matchingRate = array();
		
		for ($i = 0; $i < count($this->oldSentences); $i++)
		{
			$oldStart = $this->oldSentences[$i]->startPos;
			$oldEnd = $this->oldSentences[$i]->endPos;
			$oldLen = $this->oldSentences[$i]->length;
			
			// Exact sentence match.
			if ($matchedOld[$oldStart]->matchId !== 0 &&
				$matchedOld[$oldStart]->matchId === $matchedOld[$oldEnd]->matchId)
			{
				for ($j = 0; $j < count($this->newSentences); $j++)
				{
					$newStart = $this->newSentences[$j]->startPos;
					if (($matchedOld[$oldStart]->matchId ===
						 $matchedNew[$newStart]->matchId) &&
						($matchedOld[$oldStart]->matchPos ===
						 $matchedNew[$newStart]->matchPos))
						$this->matchingRate[$i][$j] = 1.0;
				}
			}
			// Partial sentence match.
			else
			{
				for ($ii = $oldStart; $ii <= $oldEnd; $ii++)
				{
					if ($matchedOld[$ii]->matchId != 0)
					{
						$jj = -1;
						$newEnd = 0;
						
						for ($k = 0; $k < count($this->edit); $k++)
						{
							if ($this->edit[$k] instanceof Match)
							{
								$oldPos = $this->edit[$k]->getOldPos();
								$len = $this->edit[$k]->getLength();
								if ($oldPos === $ii)
								{
									$jj = $this->edit[$k]->getNewPos();
									$newEnd = $this->edit[$k]->getNewPos() + $len;
									break;
								}
								else if ($oldPos < $ii && $oldPos + $len > $ii)
								{
									$jj = $this->edit[$k]->getNewPos() - $oldPos + $ii;
									$newEnd = $this->edit[$k]->getNewPos() + $len;
									break;
								}
									
							}
						}
						
						if ($jj != -1)
						{
							for ($j = 0; $j < count($this->newSentences); $j++)
							{
								if (($jj >= $this->newSentences[$j]->startPos &&
									 $jj <= $this->newSentences[$j]->endPos) &&
									$matchedOld[$ii]->matchId  === $matchedNew[$jj]->matchId &&
									$matchedOld[$ii]->matchPos === $matchedNew[$jj]->matchPos)
								{
									$newEnd = min($newEnd, $this->newSentences[$j]->endPos + 1);
									if (!isset($this->matchingRate[$i]))
										$this->matchingRate[$i] = array();
									if (!isset($this->matchingRate[$i][$j]))
										$this->matchingRate[$i][$j] = 0.0;
									$this->matchingRate[$i][$j] += ($newEnd - $jj) * 2.0 /
															($this->newSentences[$j]->length + $oldLen);
									$ii += $newEnd - $jj - 1;
								}
							}
						}
					}
				}
			}
		}
		
		for ($i = 0; $i < count($this->oldSentences); $i++)
		{
			$maxMatchingRate = 0.0;
			$maxMatchingS = -1;
			for ($j = 0; $j < count($this->newSentences); $j++)
				if (isset($this->matchingRate[$i][$j]) && $this->matchingRate[$i][$j] > 0.0)
				{
					$retString .= sprintf("%4d<=>%4d: %.4f\n",
											$this->oldSentences[$i]->startPos,
											$this->newSentences[$j]->startPos,
											$this->matchingRate[$i][$j]);
					$maxMatchingRate = max($maxMatchingRate, $this->matchingRate[$i][$j]);
					if ($maxMatchingRate === $this->matchingRate[$i][$j])
						$maxMatchingS = $j;
				}
			
			if ($maxMatchingRate == 0.0)
			{
				$retString .= sprintf("%4d<=> Del\n", $this->oldSentences[$i]->startPos);
			}
			
			if ($maxMatchingRate < $this::threshold)
				$this->sentenceEdits[] = new SentenceDel($i, $this->oldSentences[$i], $maxMatchingRate);
			else
				$this->sentenceEdits[] = new SentenceMatch($i, $maxMatchingS, 
						$this->oldSentences[$i], $this->newSentences[$maxMatchingS], $maxMatchingRate);
		}
		
		for ($j = 0; $j < count($this->newSentences); $j++)
		{
			$maxMatchingRate = 0.0;
			$maxMatchingS = -1;
			for ($i = 0; $i < count($this->oldSentences); $i++)
			{
				if (isset($this->matchingRate[$i][$j]))
					$maxMatchingRate = max($maxMatchingRate, $this->matchingRate[$i][$j]);
				if (isset($this->matchingRate[$i][$j]) && $maxMatchingRate == $this->matchingRate[$i][$j])
					$maxMatchingS = $i;
			}
			
			if ($maxMatchingRate == 0.0)
			{
				$retString .= sprintf(" Ins<=>%4d\n", $this->newSentences[$j]->startPos);
			}
			
			if ($maxMatchingRate < $this::threshold)
				$this->sentenceEdits[] = new SentenceIns($j, $this->newSentences[$j], $maxMatchingRate);
			else
				$this->sentenceEdits[] = new SentenceMatch($maxMatchingS, $j,
						$this->oldSentences[$maxMatchingS], $this->newSentences[$j], $maxMatchingRate);
		}
		
		// Sentence movement detection (incomplete)
		/*
		$matchDiff = array();
		for ($i = 0; $i < count($this->sentenceEdits); $i++)
			if ($this->sentenceEdits[$i] instanceof SentenceMatch)
				$matchDiff[] = $this->sentenceEdits[$i];
				
		usort($matchDiff, array($this, "compare_newpos"));
		$order = 0;
		foreach ($matchDiff as $m)
		{
			$m->setOldOrder($order);
			$order += $m->getOldLength();
		}
		
		usort($matchDiff, array($this, "compare_oldpos"));
		$order = 0;
		foreach ($matchDiff as $m)
		{
			$m->setNewOrder($order);
			$order += $m->getNewLength();
		}
		
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
					$m->setOldOrder($m->getOldOrder() - $removedMatch->getOldLength());
				
				if ($m->getNewOrder() > removedMatch.getNewOrder())
					$m->setNewOrder($m->getNewOrder() - $removedMatch->getNewLength());
			}
			
			$removedMatch->setOldOrder(0);
			$removedMatch->setNewOrder(0);
			
			$wordTokenCount = 0;
			$this->newTokens = $removedMatch->newSentence->tokens;
			for ($i = 0; $i < $removedMatch->getNewLength(); $i++)
			{
				if ($this->newTokens[$i]->kind === MediawikiScannerConstants.WORD)
					$wordTokenCount++;
			}
			
			if ($wordTokenCount > 3)
				printf("Sentence movement");
			else
			{
				$oldPos = $removedMatch->getOldStartPos();
				$newPos = $removedMatch->getNewStartPos();
				for ($i = 0; $i < $removedMatch->getOldLength(); $i++)
				{
					$matchedOld[$oldPos+$i]->matchId  = 0;
					$matchedOld[$oldPos+$i]->matchPos = 0;
					$matchedNew[$newPos+$i]->matchId  = 0;
					$matchedNew[$newPos+$i]->matchPos = 0;
				}
				
			}
			
			$matchDiff->remove($removedMatch);
			
		} while (!$sorted);
		*/
		
		return $retString;
	}
	
	public function mergeSentences()
	{
		$retString = '';
		
		for ($i = 0; $i < count($this->oldSentences); $i++)
		{
			for ($j = 0; $j < count($this->newSentences) - 1; $j++)
			{
				$k = $j + 1;
				if (isset($this->matchingRate[$i][$j]) && 
					$this->matchingRate[$i][$j] > $this::threshold && 
					isset($this->matchingRate[$i][$k]) && 
					$this->matchingRate[$i][$k] > 0.0)
				{
					$oldLen = $this->oldSentences[$i]->length;
					$newLen = $this->newSentences[$j]->length;
					$extLen = $this->newSentences[$k]->length;
					
					$newMatchingRate = $this->matchingRate[$i][$j] * ($oldLen + $newLen) + 
						$this->matchingRate[$i][$k] * ($oldLen + $extLen);
					$newMatchingRate /= $oldLen + $newLen + $extLen;
										
					if ($this->matchingRate[$i][$j] < $newMatchingRate)
					{
						$retString .= ("Merge " . $this->oldSentences[$i]->startPos .
								" <=> " . $this->newSentences[$j]->startPos . " + " .
								$this->newSentences[$k]->startPos . "\n");
						// newSentences.set(k, newSentences.get(k + 1));
						$this->matchingRate[$i][$j] = $newMatchingRate;
						$this->matchingRate[$i][$k] = 0.0;
						$retString .= sprintf("%4d<=>%4d: %.4f\n", 
								$this->oldSentences[$i]->startPos, 
								$this->newSentences[$j]->startPos, 
								$this->matchingRate[$i][$j]);
						$j--;
					}				
				}
			}
		}
		

		for ($j = 0; $j < count($this->newSentences); $j++)
		{
			for ($i = 0; $i < count($this->oldSentences) - 1; $i++)
			{
				$k = $i + 1;
				if (isset($this->matchingRate[$i][$j]) && 
					$this->matchingRate[$i][$j] > $this::threshold && 
					isset($this->matchingRate[$k][$j]) && 
					$this->matchingRate[$k][$j] > 0.0)
				{			
					$oldLen = $this->oldSentences[$i]->length;
					$newLen = $this->newSentences[$j]->length;
					$extLen = $this->oldSentences[$k]->length;
					
					$newMatchingRate = $this->matchingRate[$i][$j] * ($oldLen + $newLen) + $this->matchingRate[$k][$j] * ($extLen + $newLen);
					$newMatchingRate /= $oldLen + $newLen + $extLen;
					
					if ($this->matchingRate[$i][$j] < $newMatchingRate)
					{
						$retString .= ("Merge " . $this->oldSentences[$i]->startPos . 
								" + " . $this->oldSentences[$k]->startPos . " <=> " + 
								$this->newSentences[$j]->startPos . "\n");
						// oldSentences.set(k, oldSentences.get(k + 1));
						$this->matchingRate[$i][$j] = $newMatchingRate;
						$this->matchingRate[$k][$j] = 0.0;
						$retString .= sprintf("%4d<=>%4d: %.4f\n",
								$this->oldSentences[$i]->startPos,
								$this->newSentences[$j]->startPos, 
								$this->matchingRate[$i][$j]);
						$i--;
					}
				}
			}
		}
		
		return $retString;
	}

	public function getSentenceEdits() {
		return $this->sentenceEdits;
	}

}
