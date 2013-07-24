package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.WikiToken;

public class Deletion extends BasicEdit {

	public int getPos() {
		return oldPos;
	}

	public void setPos(int pos) {
		this.oldPos = pos;
	}

	public WikiToken[] getContent() {
		return oldTokens;
	}

	public void setContent(WikiToken[] actionContent) {
		this.oldTokens = actionContent;		
	}
	
	public int getLength() {
		return oldTokens.length;
	}

	public Deletion(int pos, WikiToken[] content) {
		super();
		this.oldPos = pos;
		this.newPos = -1;
		this.oldTokens = content;
		this.newTokens = null;
	}

	public String getDescription() {
		return String.format("Del(%d, %d)", oldTokens.length, oldPos);
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
		return String.format("Del(%d, <a href='#o%d'>%d</a>)", oldTokens.length, oldPos, oldPos);
	}
}
