package mo.umac.wikianalysis.diff.sentence;

import java.util.Arrays;

public class SentenceSplit extends SentenceEdit {

	private Sentence[] newSentences;
	private int[] newSentencesPos;

	public SentenceSplit(int oldPos, int[] newPos, 
			Sentence oldS, Sentence[] newS, double mr) {
		super();
		this.oldPos = oldPos;
		this.newPos = newPos[0];
		this.newSentencesPos = newPos;
		this.oldSentence = oldS;
		this.newSentence = newS[0];
		this.newSentences = newS;
		this.matchingRate = mr;
	}
	
	@Override
	public String descString() {
		return "SentenceSplit(" + oldPos + ", " + Arrays.toString(newSentencesPos) + ", " + matchingRate + ")";
	}

	public Sentence[] getNewSentences() {
		return newSentences;
	}

	public int[] getNewSentencesPos() {
		return newSentencesPos;
	}

}
