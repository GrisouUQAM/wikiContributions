package mo.umac.wikianalysis.diff.sentence;

import mo.umac.wikianalysis.lexer.Token;

public class Sentence {

	public Token[] tokens;
	public int startPos;
	public int endPos;
	public int length;
	
	public Sentence()
	{
		tokens = null;
		startPos = -1;
		endPos = -1;
		length = 0;
	}
}
