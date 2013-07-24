package mo.umac.wikianalysis.diff.sentence;

public class SentenceMove extends SentenceMatch {

	public SentenceMove(int oldPos, int newPos, 
						Sentence oldS, Sentence newS, double mr)
	{
		super(oldPos, newPos, oldS, newS, mr);
	}
	
	public SentenceMove(SentenceMatch m)
	{
		super(m);
	}
	
	public String descString()
	{
		return "SentenceMove(" + this.oldPos + ", " + this.newPos + ", " + this.matchingRate + ")";
	}
}
