<?php

class SentenceSplitter {
	
	public static function separateSentence($tokens)
	{
		$pos = array();
		$sentences = array();

		/*
		 * Break token stream into sentences according to token encountered.
		 */

		$pos[] = 0; 			// 0th position is added as beginning of first sentence.
		$inRef = false;			// Flag if content is in reference tag or not.
		$inTable = 0;			// Flag that represents levels of tables that is
								// surrounding current token.
		
		for ($i = 0; $i < count($tokens) - 1; $i++) {
			/*
			 * If current token is the beginning of a list item, header or
			 * table, consider as a new sentence.
			 */
			if ($tokens[$i]->kind == WikiLexerConstants::D_LIST_ITEM ||
				$tokens[$i]->kind == WikiLexerConstants::I_LIST_ITEM ||
				$tokens[$i]->kind == WikiLexerConstants::D_HEADER_BEGIN ||
				$tokens[$i]->kind == WikiLexerConstants::I_HEADER_BEGIN)
			{
				if (!$inRef && $inTable == 0 && !in_array($i, $pos, true))
					$pos[] = $i;
			}
			/*
			 * If newline is encountered, consider next token belongs to a new
			 * sentence.
			 */
			elseif ($tokens[$i]->kind == WikiLexerConstants::NL)
			{
				if (!$inRef && $inTable == 0 && !in_array($i, $pos, true))
					$pos[] = $i + 1;
			}
			elseif ($tokens[$i]->kind == WikiLexerConstants::D_TABLE_BEGIN
					|| $tokens[$i]->kind == WikiLexerConstants::I_TABLE_BEGIN)
			{
				if (!$inRef && $inTable == 0 && !in_array($i, $pos, true))
					$pos[] = $i;
				$inTable++;
			}
			elseif ($tokens[$i]->kind == WikiLexerConstants::D_TABLE_END 
					|| $tokens[$i]->kind == WikiLexerConstants::I_TABLE_END)
			{
				$inTable--;
				if (!$inRef && $inTable ==  0)
					$pos[] = $i + 1;
			}
			/*
			 * If current token is a beginning reference tag, consider as a new
			 * sentence, and includes all subsequent tokens until the
			 * corresponding ending reference tag is encountered.
			 */
			elseif ($tokens[$i]->kind == WikiLexerConstants::REF_BEGIN )
			{
				$inRef = true;
			}
			/*
			 * If current token is a period, exclamation mark or question mark,
			 * check if next token or next next token is start with upper case
			 * character or not. If yes, a new sentence starts at next or next
			 * next word.
			 */
			elseif ($tokens[$i]->kind == WikiLexerConstants::REF_END ||
					($tokens[$i]->kind == WikiLexerConstants::SYMBOL &&
						($tokens[$i]->image == "." ||
						 $tokens[$i]->image == "!" ||
						 $tokens[$i]->image == "?" )
					 ))
			{
				if ($tokens[$i]->kind == WikiLexerConstants::REF_END)
				{
					$inRef = false;
				}
				// Ignore checking if current token is in ref tag or table.
				if ($inRef || $inTable > 0 )
					continue;
				elseif (ctype_upper($tokens[$i+1]->image[0]) ||
						 $tokens[$i+1]->kind == WikiLexerConstants::REF_BEGIN)
					$pos[] = $i + 1;
				elseif (!ctype_alnum($tokens[$i+1]->image[0])
						&& $i + 2 < count($tokens)
						&& ctype_upper($tokens[$i+2]->image[0]) )
				{
					if ($tokens[$i+1]->kind == WikiLexerConstants::INT_LINK_BEGIN ||
						$tokens[$i+1]->kind == WikiLexerConstants::D_LIST_ITEM ||
						$tokens[$i+1]->kind == WikiLexerConstants::I_LIST_ITEM ||
						$tokens[$i+1]->kind == WikiLexerConstants::D_HEADER_BEGIN ||
						$tokens[$i+1]->kind == WikiLexerConstants::I_HEADER_BEGIN)
						$pos[] = $i + 1;
					else
						$pos[] = $i + 2;
					$i++;
				}
			}
		}
		// Last position is added as ending of last sentence.
		if ( !in_array(count($tokens), $pos, true) )
			$pos[] = count($tokens);

		/*
		 * Separate each pair of position into a Sentence structure, fill in
		 * information about each sentence.
		 */
		$start = 0;
		foreach ($pos as $iter)
		{
			if ($iter === 0) continue;
			$end = $iter - 1;
			$len = $end - $start + 1;

			$s = new Sentence();

			$s->startPos = $start;
			$s->endPos = $end;
			$s->length = $len;
			$s->tokens = array();

			for ($i = $start; $i <= $end; $i++)
				$s->tokens[$i-$start] = $tokens[$i];

			$sentences[] = $s;

			$start = $end + 1;
		}

		return $sentences;
	}
	
}
