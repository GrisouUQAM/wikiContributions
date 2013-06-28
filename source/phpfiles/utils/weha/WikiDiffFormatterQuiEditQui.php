<?php
include_once('categorizer/ActionCategorizerQuiEditQui.php');

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
			}			
			elseif ($se instanceof SentenceIns) {
				$sInsPosition[$se->getNewStartPos()] = $se->getNewEndPos();				
			}
			elseif ($se instanceof SentenceMatch) {
				if ($se->getMatchingRate() < 1.0)
				{
					$sChangeOldPosition[$se->getOldStartPos()] = $se->getOldEndPos();
					$sChangeNewPosition[$se->getNewStartPos()] = $se->getNewEndPos();			
				}
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
			} elseif ($edit instanceof Insertion) {
				$e = $edit;
				$insStartPos = $e->getPos();
				$insEndPos = $insStartPos + $e->getLength() - 1;
				$insPosition[$insStartPos] = $insEndPos;
			} elseif ($edit instanceof Replacement) {
				$e = $edit;
				$delStartPos = $e->getOldPos();
				$delEndPos = $delStartPos + $e->getDeletedLength() - 1;
				$delPosition[$delStartPos] = $delEndPos;
					
				$insStartPos = $e->getNewPos();
				$insEndPos = $insStartPos + $e->getInsertedLength() - 1;
				$insPosition[$insStartPos] = $insEndPos;
			} elseif ($edit instanceof Movement) {
				$e = $edit;
				$movOldStartPos = $e->getOldPos();
				$movNewStartPos = $e->getNewPos();
				$movLen = $e->getLength();
				
				$movOldPosition[$movOldStartPos] = $movOldStartPos + $movLen - 1;
				$movNewPosition[$movNewStartPos] = $movNewStartPos + $movLen - 1;
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
		$this->output .= ("</div></td></table>");
		
		return $this->output;
	}

}
