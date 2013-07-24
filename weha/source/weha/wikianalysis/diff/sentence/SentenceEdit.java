package mo.umac.wikianalysis.diff.sentence;

public abstract class SentenceEdit{
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		long temp;
		temp = Double.doubleToLongBits(matchingRate);
		result = prime * result + (int) (temp ^ (temp >>> 32));
		result = prime * result + newPos;
		result = prime * result + oldPos;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		SentenceEdit other = (SentenceEdit) obj;
		if (Double.doubleToLongBits(matchingRate) != Double
				.doubleToLongBits(other.matchingRate))
			return false;
		if (newPos != other.newPos)
			return false;
		if (oldPos != other.oldPos)
			return false;
		return true;
	}

	protected int oldPos;
	protected int newPos;
	protected Sentence oldSentence;
	protected Sentence newSentence;
	protected double matchingRate;
	
	public int getOldStartPos() {
		return this.oldSentence.startPos;
	}
	
	public int getOldEndPos() {
		return this.oldSentence.endPos;
	}
	
	public int getOldLength() {
		return this.oldSentence.length;
	}
	
	public int getNewStartPos() {
		return this.newSentence.startPos;
	}
	
	public int getNewEndPos() {
		return this.newSentence.endPos;
	}
	
	public int getNewLength() {
		return this.newSentence.length;
	}
	
	public double getMatchingRate() {
		return this.matchingRate;
	}
	
	public abstract String descString();
}
