package mo.umac.wikianalysis.diff.sentence;

public class SentenceIns extends SentenceEdit {


	public SentenceIns(int newPos, Sentence newS, double mr) {
		super();
		this.oldPos = -1;
		this.newPos = newPos;
		this.oldSentence = null;
		this.newSentence = newS;
		this.matchingRate = mr;
	}
	
	public int getStartPos() {
		return this.newSentence.startPos;
	}
	
	public int getEndPos() {
		return this.newSentence.endPos;
	}
	
	public int getLength() {
		return this.newSentence.length;
	}
	
	@Override
	public String descString() {
		return "SentenceIns(" + this.newPos + ")";
	}
}
