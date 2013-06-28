<?php
include_once( dirname(__FILE__) . '/categorizer/ActionCategorizer.php');

class WikiDiffFormatter {
	
	private $output;
	private $acter;
	
	public function __construct($oldWikitext, $newWikitext)
	{
		$this->acter = new ActionCategorizer($oldWikitext, $newWikitext);
		$this->output = "";
	}
	
	public function categorize()
	{
		return $this->acter->categorize();
	}
	
	public function outputDiff()
	{
		$sentenceDeletesCount = 0;
		$replacementCount = 0;
		$movesCount = 0;
		$deletesCount = 0;
		$sentenceInsertsCount = 0;
		$insertsCount = 0;
		$sentenceMatchingRate = 0;
		
		/*
		$jsonurl2 = "http://en.wikipedia.org/w/api.php?action=query&prop=revisions&format=json&rvprop=content&rvlimit=10&rvuser=Chris857&titles=square&converttitles=";
	
		$json2 = file_get_contents($jsonurl2, true);

		$obj2 = json_decode($json2, true);
		
		$queries2 = $obj2['query'];
		$pages = $queries2['pages'];
		$revisions = $pages['659939'];
		$revision = $revisions['revisions'];
		$content = $revision['*'];
		
		var_dump($revision); */
		
		$this->acter->printResult();
		$basicEdits = $this->acter->getBasicEdits();
		$sentenceEdits = $this->acter->getSentenceEdits();
		
		$sDelPosition = array();
		$sInsPosition = array();
		$sChangeOldPosition = array();
		$sChangeNewPosition = array();
		
		$delPosition = array();
		$insPosition = array();
		$movOldPosition = array();
		$movNewPosition = array();
		
		for ($i = 0; $i < count($sentenceEdits); $i++)
		{
			$se = $sentenceEdits[$i];
			if ($se instanceof SentenceDel) {
				$sDelPosition[$se->getOldStartPos()] = $se->getOldEndPos();
				$sentenceDeletesCount++;
			}			
			elseif ($se instanceof SentenceIns) {
				$sInsPosition[$se->getNewStartPos()] = $se->getNewEndPos();	
				$sentenceInsertsCount++;
			}
			elseif ($se instanceof SentenceMatch) {
				if ($se->getMatchingRate() < 1.0)
				{
					$sChangeOldPosition[$se->getOldStartPos()] = $se->getOldEndPos();
					$sChangeNewPosition[$se->getNewStartPos()] = $se->getNewEndPos();			
				}
				$sentenceMatchingRate = $se->getMatchingRate();
			}
		}
		
		for ($i = 0; $i < count($basicEdits); $i++)
		{
			$edit = $basicEdits[$i];
			if ($edit instanceof Deletion) {
				$e = $edit;
				$delStartPos = $e->getPos();
				$delEndPos = $delStartPos + $e->getLength() - 1;
				$delPosition[$delStartPos] = $delEndPos;
				$deletesCount++;
			} elseif ($edit instanceof Insertion) {
				$e = $edit;
				$insStartPos = $e->getPos();
				$insEndPos = $insStartPos + $e->getLength() - 1;
				$insPosition[$insStartPos] = $insEndPos;
				$insertsCount++;
			} elseif ($edit instanceof Replacement) {
				$e = $edit;
				$delStartPos = $e->getOldPos();
				$delEndPos = $delStartPos + $e->getDeletedLength() - 1;
				$delPosition[$delStartPos] = $delEndPos;
					
				$insStartPos = $e->getNewPos();
				$insEndPos = $insStartPos + $e->getInsertedLength() - 1;
				$insPosition[$insStartPos] = $insEndPos;
				$replacementCount++;
			} elseif ($edit instanceof Movement) {
				$e = $edit;
				$movOldStartPos = $e->getOldPos();
				$movNewStartPos = $e->getNewPos();
				$movLen = $e->getLength();
				
				$movOldPosition[$movOldStartPos] = $movOldStartPos + $movLen - 1;
				$movNewPosition[$movNewStartPos] = $movNewStartPos + $movLen - 1;
				$movesCount++;
			}
		}

		
		$tokenOld = $this->acter->getTokenOld();
		$tokenNew = $this->acter->getTokenNew();

		
		$this->output .= ("<table border=\"0\" width=\"100%\" align=\"center\" style=\"table-layout:fixed;\">\n");
		$this->output .= ("<col width=\"50%\" />\n");
		$this->output .= ("<col width=\"50%\" />\n");
		$this->output .= ("<tr><th>Old version</th><th>New version</th></tr>\n");
		$this->output .= ("<tr><td style=\"vertical-align:text-top;\"><div style=\"word-wrap:break-word; overflow:auto;\">\n");
		
		$delEnd = -1;
		$sDelEnd = -1;
		$tagClosed = true;
		$sTagClosed = true;
		
		////////////// Analysis of the old text //////////////
		
		
		for($i = 0; $i < count($tokenOld); $i++)
		{
			$tt = $tokenOld[$i];
			
			if (array_key_exists($i, $sDelPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				if (!$sTagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style='background-color:#FF9999;'>");
				$sTagClosed = false;
				$sDelEnd = $sDelPosition[$i];
				if (!$tagClosed) $this->output .= ("<span style=\"color:red; font-weight:bold;\">");
			}
			elseif (array_key_exists($i, $sChangeOldPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				if (!$sTagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style='background-color:#FFFF99;'>");
				$sTagClosed = false;
				$sDelEnd = $sChangeOldPosition[$i];
				if (!$tagClosed) $this->output .= ("<span style=\"color:red; font-weight:bold;\">");
			}
			
			if (array_key_exists($i, $delPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style=\"color:red; font-weight:bold;\">");
				$tagClosed = false;
				$delEnd = $delPosition[$i];
			}
			elseif (array_key_exists($i, $movOldPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style=\"color:blue; font-weight:bold;\">");
				$tagClosed = false;
				$delEnd = $movOldPosition[$i];
			}

			$this->output .= nl2br(htmlspecialchars($tt));
			//$this->output .= "test";	
			
			if ($i == $delEnd)
			{
				$this->output .= ("</span>");
				$tagClosed = true;
			}
			if ($i == $sDelEnd)
			{
				if (!$tagClosed) $this->output .= ("</span>");
				$this->output .= ("</span>");
				$sTagClosed = true;
				if (!$tagClosed) $this->output .= ("<span style=\"color:red; font-weight:bold;\">");
			}
		}

		if (!$tagClosed) $this->output .= ("</span>");
		if (!$sTagClosed) $this->output .= ("</span>");
		
		$this->output .= ("</div></td><td style=\"vertical-align:text-top;\"><div style=\"word-wrap:break-word; overflow:auto;\">\n");
		
		$insEnd = -1;
		$sInsEnd = -1;
		$tagClosed = true;
		$sTagClosed = true;

		////////////// Analysis of the new text //////////////
		
		for($i = 0; $i < count($tokenNew); $i++)
		{
			$tt = $tokenNew[$i];
			
			if (array_key_exists($i, $sInsPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				if (!$sTagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style='background-color:#99FF99;'>");
				$sTagClosed = false;
				$sInsEnd = $sInsPosition[$i];
				if (!$tagClosed) $this->output .= ("<span style=\"color:green; font-weight:bold;\">");
			}
			elseif (array_key_exists($i, $sChangeNewPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				if (!$sTagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style='background-color:#FFFF99;'>");
				$sTagClosed = false;
				$sInsEnd = $sChangeNewPosition[$i];
				if (!$tagClosed) $this->output .= ("<span style=\"color:green; font-weight:bold;\">");
			}
			
			if (array_key_exists($i, $insPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style='color:green; font-weight:bold;'>");
				$tagClosed = false;
				$insEnd = $insPosition[$i];
			}
			elseif (array_key_exists($i, $movNewPosition))
			{
				if (!$tagClosed) $this->output .= ("</span>");
				$this->output .= ("<span style='color:blue; font-weight:bold;'>");
				$tagClosed = false;
				$insEnd = $movNewPosition[$i];
			}
			$this->output .= nl2br(htmlspecialchars($tt));
			//$this->output .= "test!";
			
			if ($i == $insEnd)
			{
				$this->output .= ("</span>");
				$tagClosed = true;
			}			
			if ($i == $sInsEnd)
			{
				if (!$tagClosed) $this->output .= ("</span>");
				$this->output .= ("</span>");
				$sTagClosed = true;
				if (!$tagClosed) $this->output .= ("<span style=\"color:green; font-weight:bold;\">");
			}
		}
		
		if (!$tagClosed) $this->output .= ("</span>");
		if (!$sTagClosed) $this->output .= ("</span>");
		$this->output .= ("</div></td></tr></table>");
		
		$this->output .= ("<br/>");
		$this->output .= "Number of words deleted : ".$sentenceDeletesCount."";
		$this->output .= ("<br/>");
		$this->output .= "Number of global deletes : ".$deletesCount."";
		$this->output .= ("<br/>");
		$this->output .= "Number of replacements : ".$replacementCount."";
		$this->output .= ("<br/>");
		$this->output .= "Number of moves : ".$movesCount."";
		$this->output .= ("<br/>");
		$this->output .= "Number of words inserted : ".$sentenceInsertsCount."";
		$this->output .= ("<br/>");
		$this->output .= "Number of global inserts : ".$insertsCount."";
		$this->output .= ("<br/>");
		$this->output .= "sentence matching rate : ".$sentenceMatchingRate."";
		
		$totalScore = $sentenceDeletesCount;
		$this->output .= ("<br/>");
		$this->output .= "Score : ".$totalScore."";
		
		
		return $this->output;
	}

}
