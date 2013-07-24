package mo.umac.wikianalysis.diff.token;

public class MatchInfo {

	public int matchId;
	public int matchPos;
	
	public String toString()
	{
		return matchId + "\t" + matchPos;
	}
}
