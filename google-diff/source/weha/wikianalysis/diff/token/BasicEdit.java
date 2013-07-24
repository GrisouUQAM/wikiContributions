package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.WikiToken;

public abstract class BasicEdit {
	protected int oldPos;
	protected int newPos;
	protected WikiToken[] oldTokens;
	protected WikiToken[] newTokens;
	
	public abstract String getDescription();
	public abstract String getLinkedDesc();
	public abstract WikiToken[] getContent();
	public abstract int getOldPos();
	public abstract int getNewPos();
}
