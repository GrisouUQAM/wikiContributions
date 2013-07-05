package mo.umac.wikianalysis.diff.sentence;

public class SentenceMatch extends SentenceEdit {

	private int oldOrder;
	private int newOrder;

	public SentenceMatch(int oldPos, int newPos, 
			Sentence oldS, Sentence newS, double mr) {
		super();
		this.oldPos = oldPos;
		this.newPos = newPos;
		this.oldSentence = oldS;
		this.newSentence = newS;
		this.matchingRate = mr;
		this.oldOrder = -1;
		this.newOrder = -1;
	}

	public SentenceMatch(SentenceMatch m) {
		super();
		this.oldPos = m.oldPos;
		this.newPos = m.newPos;
		this.oldSentence = m.oldSentence;
		this.newSentence = m.newSentence;
		this.matchingRate = m.matchingRate;
		this.oldOrder = -1;
		this.newOrder = -1;
	}
	
	@Override
	public String descString() {
		return "SentenceMatch(" + this.oldPos+ ", " + this.newPos + ", " + this.matchingRate + ")";
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
}
