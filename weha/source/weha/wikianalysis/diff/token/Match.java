package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.WikiToken;

public class Match extends BasicEdit {

	protected int length;

	private int oldOrder;
	private int newOrder;
	
	public int getLength() {
		return length;
	}

	public int getOldPos() {
		return oldPos;
	}

	public int getNewPos() {
		return newPos;
	}
	
	public int getOldOrder() {
		return oldOrder;
	}

	public void setOldOrder(int oldOrder) {
		this.oldOrder = oldOrder;
	}

	public int getNewOrder() {
		return newOrder;
	}

	public void setNewOrder(int newOrder) {
		this.newOrder = newOrder;
	}

	public Match(int length, int oldPos, int newPos) {
		super();
		this.length = length;
		this.oldPos = oldPos;
		this.newPos = newPos;
		this.oldTokens = null;
		this.newTokens = null;
	}

	public String getDescription() {
		return String.format("Match(%d, %d, %d)", length, oldPos, newPos);
	}

	@Override
	public WikiToken[] getContent() {
		return null;
	}

	@Override
	public String getLinkedDesc() {
		return String.format("Match(%d, <a href='#o%d'>%d</a>, <a href='#n%d'>%d</a>)", length, oldPos, oldPos, newPos, newPos);
	}

}
