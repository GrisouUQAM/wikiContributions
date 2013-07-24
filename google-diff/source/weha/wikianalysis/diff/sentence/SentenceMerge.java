package mo.umac.wikianalysis.diff.sentence;

import java.util.Arrays;

public class SentenceMerge extends SentenceEdit {

	private Sentence[] oldSentences;
	private int[] oldSentencesPos;

	public SentenceMerge(int[] oldPos, int newPos, 
			Sentence[] oldS, Sentence newS, double mr) {
		super();
		this.oldPos = oldPos[0];
		this.oldSentencesPos = oldPos;
		this.newPos = newPos;
		this.oldSentence = oldS[0];
		this.oldSentences = oldS;
		this.newSentence = newS;
		this.matchingRate = mr;
	}
	
	@Override
	public String descString() {
		return "SentenceMerge(" + Arrays.toString(oldSentencesPos) + ", " + newPos + ", " + matchingRate + ")";
	}

	public Sentence[] getOldSentences() {
		return oldSentences;
	}

	public int[] getOldSentencesPos() {
		return oldSentencesPos;
	}

}
