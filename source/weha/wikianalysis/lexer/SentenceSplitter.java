package mo.umac.wikianalysis.lexer;

import java.util.ArrayList;
import java.util.Iterator;

import mo.umac.wikianalysis.diff.sentence.Sentence;

public class SentenceSplitter {
	
	public static ArrayList<Sentence> separateSentence(WikiToken[] tokens)
	{
		ArrayList<Integer> pos = new ArrayList<Integer>();
		ArrayList<Sentence> sentences = new ArrayList<Sentence>();

		/*
		 * Break token stream into sentences according to token encountered.
		 */

		pos.add(0); 			// 0th position is added as beginning of first sentence.
		boolean inRef = false;	// Flag if content is in reference tag or not.
		int inTable = 0;		// Flag that represents levels of tables that is
								// surrounding current token.
		
		for (int i = 0; i < tokens.length - 1; i++) {
			/*
			 * If current token is the beginning of a list item, header or
			 * table, consider as a new sentence.
			 */
			if (tokens[i].kind == MediawikiScannerConstants.D_LIST_ITEM ||
				tokens[i].kind == MediawikiScannerConstants.I_LIST_ITEM ||
				tokens[i].kind == MediawikiScannerConstants.D_HEADER_BEGIN ||
				tokens[i].kind == MediawikiScannerConstants.I_HEADER_BEGIN)
			{
				if (!inRef && inTable == 0 && !pos.contains(i))
					pos.add(i);
			}
			/*
			 * If newline is encountered, consider next token belongs to a new
			 * sentence.
			 */
			else if (tokens[i].kind == MediawikiScannerConstants.NL)
			{
				if (!inRef && inTable == 0 && !pos.contains(i))
					pos.add(i+1);
			}
			else if (tokens[i].kind == MediawikiScannerConstants.D_TABLE_BEGIN
					|| tokens[i].kind == MediawikiScannerConstants.I_TABLE_BEGIN)
			{
				if (!inRef && inTable == 0 && !pos.contains(i))
					pos.add(i);
				inTable++;
			}
			else if (tokens[i].kind == MediawikiScannerConstants.D_TABLE_END
					|| tokens[i].kind == MediawikiScannerConstants.I_TABLE_END)
			{
				inTable--;
				if (!inRef && inTable ==  0)
					pos.add(i+1);
			}
			/*
			 * If current token is a beginning reference tag, consider as a new
			 * sentence, and includes all subsequent tokens until the
			 * corresponding ending reference tag is encountered.
			 */
			else if (tokens[i].kind == MediawikiScannerConstants.REF_BEGIN)
			{
				inRef = true;
			}
			/*
			 * If current token is a period, exclamation mark or question mark,
			 * check if next token or next next token is start with upper case
			 * character or not. If yes, a new sentence starts at next or next
			 * next word.
			 */
			else if (tokens[i].kind == MediawikiScannerConstants.REF_END ||
					 (tokens[i].kind == MediawikiScannerConstants.SYMBOL &&
						(tokens[i].image.equals(".") ||
						 tokens[i].image.equals("!") ||
						 tokens[i].image.equals("?") )
					 )
					)
			{
				if (tokens[i].kind == MediawikiScannerConstants.REF_END)
				{
					inRef = false;
				}
				// Ignore checking if current token is in ref tag or table.
				if (inRef || inTable > 0 )
					continue;
				else if (Character.isUpperCase(tokens[i+1].image.charAt(0)) ||
						 tokens[i+1].kind == MediawikiScannerConstants.REF_BEGIN)
					pos.add(i+1);
				else if (!Character.isLetterOrDigit(tokens[i+1].image.charAt(0))
						&& i + 2 < tokens.length
						&& Character.isUpperCase(tokens[i+2].image.charAt(0)))
				{
					if (tokens[i+1].kind == MediawikiScannerConstants.INT_LINK_BEGIN ||
						tokens[i+1].kind == MediawikiScannerConstants.D_LIST_ITEM ||
						tokens[i+1].kind == MediawikiScannerConstants.I_LIST_ITEM ||
						tokens[i+1].kind == MediawikiScannerConstants.D_HEADER_BEGIN ||
						tokens[i+1].kind == MediawikiScannerConstants.I_HEADER_BEGIN)
						pos.add(i+1);
					else
						pos.add(i+2);
					i++;
				}
			}
		}
		// Last position is added as ending of last sentence.
		if (!pos.contains(tokens.length))
			pos.add(tokens.length);

		/*
		 * Separate each pair of position into a Sentence structure, fill in
		 * information about each sentence.
		 */
		Iterator<Integer> iter = pos.iterator();
		int start = iter.next();
		while (iter.hasNext())
		{
			int end = iter.next() - 1;
			int len = end - start + 1;

			Sentence s = new Sentence();

			s.startPos = start;
			s.endPos = end;
			s.length = len;
			s.tokens = new WikiToken[s.length];

			for (int i = start; i <= end; i++)
				s.tokens[i-start] = tokens[i];

			sentences.add(s);

			start = end + 1;
		}

		return sentences;
	}
	
}
