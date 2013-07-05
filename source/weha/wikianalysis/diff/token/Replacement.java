package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.WikiToken;

public class Replacement extends BasicEdit {

	public Replacement(int oldPos, WikiToken[] delContent, int newPos,
			WikiToken[] insContent) {
		super();
		this.oldPos = oldPos;
		this.newPos = newPos;
		this.oldTokens = delContent;
		this.newTokens = insContent;
	}
	
	public int getOldPos() {
		return oldPos;
	}

	public void setOldPos(int oldPos) {
		this.oldPos = oldPos;
	}

	public int getNewPos() {
		return newPos;
	}

	public void setNewPos(int newPos) {
		this.newPos = newPos;
	}

	public WikiToken[] getInsertedContent() {
		return newTokens;
	}
	
	public int getInsertedLength() {
		return newTokens.length;
	}
	
	public WikiToken[] getDeletedContent() {
		return oldTokens;
	}
	
	public int getDeletedLength() {
		return oldTokens.length;
	}
	
	public WikiToken[] getContent() {
		WikiToken[] retValue = new WikiToken[oldTokens.length + newTokens.length];
		System.arraycopy(oldTokens, 0, retValue, 0, oldTokens.length);
		System.arraycopy(newTokens, 0, retValue, oldTokens.length, newTokens.length);
		return retValue;
	}
	
	public String getDescription() {
		return String.format("Repl(%d, %d, %d, %d)", oldTokens.length, oldPos, newTokens.length, newPos);
	}

	@Override
	public String getLinkedDesc() {
		return String.format("Repl(%d, <a href='#o%d'>%d</a>, %d, <a href='#n%d'>%d</a>)", oldTokens.length, oldPos, oldPos, newTokens.length, newPos, newPos);
	}
	
}
