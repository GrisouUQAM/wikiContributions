package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.WikiToken;

public class Movement extends Match {
	
	public void setOldPos(int oldPos) {
		this.oldPos = oldPos;
	}

	public void setNewPos(int newPos) {
		this.newPos = newPos;
	}

	public Movement(int length, int oldPos, int newPos) {
		super(length, oldPos, newPos);
	}

	public Movement(Match match) {
		super(match.getLength(), match.getOldPos(), match.getNewPos());
	}

	public String getDescription() {
		return String.format("Mov(%d, %d, %d)", length, oldPos, newPos);
	}

	public String getLinkedDesc() {
		return String.format("Mov(%d, <a href='#o%d'>%d</a>, <a href='#n%d'>%d</a>)", length, oldPos, oldPos, newPos, newPos);
	}
	
}
