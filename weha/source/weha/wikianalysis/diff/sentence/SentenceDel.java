package mo.umac.wikianalysis.diff.sentence;

public class SentenceDel extends SentenceEdit {


	public SentenceDel(int oldPos, Sentence oldS, double mr) {
		super();
		this.oldPos = oldPos;
		this.newPos = -1;
		this.oldSentence = oldS;
		this.newSentence = null;
		this.matchingRate = mr;
	}
	
	public int getStartPos() {
		return this.oldSentence.startPos;
	}
	
	public int getEndPos() {
		return this.oldSentence.endPos;
	}
	
	public int getLength() {
		return this.oldSentence.length;
	}
	
	@Override
	public String descString() {
		return "SentenceDel(" + this.oldPos + ")";
	}
}
