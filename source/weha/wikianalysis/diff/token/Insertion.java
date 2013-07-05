package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.WikiToken;

public class Insertion extends BasicEdit {

	public Insertion(int pos, WikiToken[] content) {
		super();
		this.oldPos = -1;
		this.newPos = pos;
		this.oldTokens = null;
		this.newTokens = content;
	}
	
	public void setStartPos(int pos) {
		this.newPos = pos;
	}
	
	public int getPos() {
		return newPos;
	}
	
	public WikiToken[] getContent() {
		return newTokens;
	}

	public void setContent(WikiToken[] actionContent) {
		this.newTokens = actionContent;		
	}
	
	public int getLength() {
		return newTokens.length;
	}
	
	public String getDescription() {
		return String.format("Ins(%d, %d)", newTokens.length, newPos);
	}

	@Override
	public int getNewPos() {
		return newPos;
	}

	@Override
	public int getOldPos() {
		return oldPos;
	}

	@Override
	public String getLinkedDesc() {
		return String.format("Ins(%d, <a href='#n%d'>%d</a>)", newTokens.length, newPos, newPos);
	}
}
