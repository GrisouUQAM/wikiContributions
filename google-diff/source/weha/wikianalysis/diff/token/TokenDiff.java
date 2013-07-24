package mo.umac.wikianalysis.diff.token;

import java.io.*;
import java.util.*;

import mo.umac.wikianalysis.lexer.*;

public class TokenDiff {
	
	private final int maxMatches = 40;

	private WikiToken[] tokenOld;
	private WikiToken[] tokenNew;
	private Hashtable<TokenTuple,ArrayList<Integer>> indexNew;
	private PriorityQueue<Match> matches;

	private MatchInfo[] matchedOld;
	private MatchInfo[] matchedNew;
	
	private List<BasicEdit> diff;
	private LinkedList<BasicEdit> resultDiff;
	
	public WikiToken[] getTokenOld() {
		return tokenOld;
	}

	public WikiToken[] getTokenNew() {
		return tokenNew;
	}
	
	public MatchInfo[] getMatchedOld() {
		return matchedOld;
	}

	public MatchInfo[] getMatchedNew() {
		return matchedNew;
	}

	public TokenDiff(WikiToken[] oldToken, WikiToken[] newToken)
	{
		indexNew = new Hashtable<TokenTuple,ArrayList<Integer>>();
		matches = new PriorityQueue<Match>(11, new Comparator<Match>() {
			@Override
			public int compare(Match o1, Match o2) {
				int diff = o2.getLength() - o1.getLength();
				if (diff != 0)
					return diff;
				else
				{
					diff = o1.getOldPos() - o2.getOldPos();
					if (diff != 0)
						return diff;
					else
						return Math.abs(o1.getNewPos() - o1.getOldPos()) - Math.abs(o2.getNewPos() - o2.getOldPos()) ;
				}
			}
		});
		
		tokenOld = oldToken;
		tokenNew = newToken;
		
		// Calculate the hash table for every triple of new version token.
		for (int i = 0; i < tokenNew.length - 2; i++)
		{
			TokenTuple t = 
				new TokenTuple(tokenNew[i], tokenNew[i+1], tokenNew[i+2]);
			
			ArrayList<Integer> newVal;
			if (indexNew.containsKey(t))
				newVal = indexNew.get(t);
			else
				newVal = new ArrayList<Integer>();
			
			newVal.add(new Integer(i));
			indexNew.put(t, newVal);
		}
		
		matchedOld = new MatchInfo[tokenOld.length];
		matchedNew = new MatchInfo[tokenNew.length];
		
		for (int i = 0; i < tokenOld.length; i++)
			matchedOld[i] = new MatchInfo();
		for (int i = 0; i < tokenNew.length; i++)
			matchedNew[i] = new MatchInfo();
	}
	
	public TokenDiff(WikiToken[] oldToken, WikiToken[] newToken,
					 MatchInfo[] matchedOld, MatchInfo[] matchedNew)
	{
		indexNew = new Hashtable<TokenTuple,ArrayList<Integer>>();
		matches = new PriorityQueue<Match>(11, new Comparator<Match>() {
			@Override
			public int compare(Match o1, Match o2) {
				int diff = o2.getLength() - o1.getLength();
				if (diff != 0)
					return diff;
				else
				{
					diff = o1.getOldPos() - o2.getOldPos();
					if (diff != 0)
						return diff;
					else
						return Math.abs(o1.getNewPos() - o1.getOldPos()) - Math.abs(o2.getNewPos() - o2.getOldPos()) ;
				}
			}
		});
		
		tokenOld = oldToken;
		tokenNew = newToken;
		
		// Calculate the hash table for every triple of new version token.
		for (int i = 0; i < tokenNew.length - 2; i++)
		{
			TokenTuple t = 
				new TokenTuple(tokenNew[i], tokenNew[i+1], tokenNew[i+2]);
			
			ArrayList<Integer> newVal;
			if (indexNew.containsKey(t))
				newVal = indexNew.get(t);
			else
				newVal = new ArrayList<Integer>();
			
			newVal.add(new Integer(i));
			indexNew.put(t, newVal);
		}
		
		this.matchedOld = matchedOld;
		this.matchedNew = matchedNew;
	}
	
	public TokenDiff(String oldText, String newText)
	{
		indexNew = new Hashtable<TokenTuple,ArrayList<Integer>>();
		matches = new PriorityQueue<Match>(11, new Comparator<Match>() {
			@Override
			public int compare(Match o1, Match o2) {
				int diff = o2.getLength() - o1.getLength();
				if (diff != 0)
					return diff;
				else
				{
					diff = o1.getOldPos() - o2.getOldPos();
					if (diff != 0)
						return diff;
					else
						return Math.abs(o1.getNewPos() - o1.getOldPos()) - Math.abs(o2.getNewPos() - o2.getOldPos()) ;
				}
			}
		});
		
		// Break old version text into tokens.
		tokenOld = WikitextTokenizer.tokenize(oldText);
		
		// Break new version text into tokens.
		tokenNew = WikitextTokenizer.tokenize(newText);
		
		// Calculate the hash table for every tuple of new version token.
		for (int i = 0; i < tokenNew.length - 2; i++)
		{
			TokenTuple t = 
				new TokenTuple(tokenNew[i], tokenNew[i+1], tokenNew[i+2]);
			
			ArrayList<Integer> newVal;
			if (indexNew.containsKey(t))
				newVal = indexNew.get(t);
			else
				newVal = new ArrayList<Integer>();
			
			newVal.add(new Integer(i));
			indexNew.put(t, newVal);
		}
		
		matchedOld = new MatchInfo[tokenOld.length];
		matchedNew = new MatchInfo[tokenNew.length];
		
		for (int i = 0; i < tokenOld.length; i++)
			matchedOld[i] = new MatchInfo();
		for (int i = 0; i < tokenNew.length; i++)
			matchedNew[i] = new MatchInfo();
	}
	
	private void doDiff()
	{
		diff = new ArrayList<BasicEdit>();
		//Set<Integer> prevMatches = new HashSet<Integer>();
		
		for (int i = 0; i < tokenOld.length - 2; i++)
		{
			TokenTuple t =
				new TokenTuple(tokenOld[i],tokenOld[i+1], tokenOld[i+2]);
			if (indexNew.containsKey(t))
			{
				List<Integer> index = indexNew.get(t);
				if (index.size() > maxMatches)
					indexNew.remove(t);
				else
				{
					Iterator<Integer> iter = index.iterator();
					while (iter.hasNext())
					{
						int i2 = iter.next().intValue();
						//if (!prevMatches.contains(i2))
						//{
							int len = 2;
							while ( i + len < tokenOld.length &&
									i2 + len < tokenNew.length &&
									tokenOld[i + len].equals(tokenNew[i2 + len]))
								len++;
							matches.add(new Match(len, i, i2));
						//}
					}
				}
				//prevMatches.addAll(index);
			}
		}

		int matchId = 0;
		
		while (!matches.isEmpty())
		{
			matchId++;
			Match m = matches.poll();
			
			if (matchedOld[m.getOldPos()].matchId == 0 &&
				matchedNew[m.getNewPos()].matchId == 0)
			{
				if (matchedOld[m.getOldPos() + m.getLength() - 1].matchId == 0 &&
					matchedNew[m.getNewPos() + m.getLength() - 1].matchId == 0)
				{
					diff.add(m);
					for (int i = 0; i < m.getLength(); i++)
					{
						matchedOld[m.getOldPos() + i].matchId = matchId;
						matchedOld[m.getOldPos() + i].matchPos = i;
						matchedNew[m.getNewPos() + i].matchId = matchId;
						matchedNew[m.getNewPos() + i].matchPos = i;
						
					}
				}
				else
				{
					int k = m.getLength() - 1;
					while (matchedOld[m.getOldPos() + k].matchId != 0 ||
						   matchedNew[m.getNewPos() + k].matchId != 0)
						k--;
					
					int residualLen = k + 1;
					if (residualLen > 1)
						matches.add(new Match(residualLen,
										m.getOldPos(), m.getNewPos()));
				}
			}
			else
			{
				if (matchedOld[m.getOldPos() + m.getLength() - 1].matchId == 0 &&
					matchedNew[m.getNewPos() + m.getLength() - 1].matchId == 0)
				{
					int j = 1;
					while (matchedOld[m.getOldPos() + j].matchId != 0 ||
							matchedNew[m.getNewPos() + j].matchId != 0)
						j++;

					int residualLen = m.getLength() - j;
					if (residualLen > 1)
						matches.add(new Match(residualLen,
										m.getOldPos() + j, m.getNewPos() + j));
				}
				else
				{
					int j = 1;
					while (j < m.getLength() - 1 &&
						   (matchedOld[m.getOldPos() + j].matchId != 0 ||
							matchedNew[m.getNewPos() + j].matchId != 0))
						j++;

					int k = j + 1;
					while (k < m.getLength() - 1 &&
						   !(matchedOld[m.getOldPos() + k].matchId != 0 ||
						   matchedNew[m.getNewPos() + k].matchId != 0))
						k++;

					int residualLen = k - j;
					if (residualLen > 1)
						matches.add(new Match(residualLen,
										m.getOldPos() + j, m.getNewPos() + j));
				}
			}
		}
		/*
		StringBuffer output = new StringBuffer();
		
		for (int i = 0; i < tokenOld.length; i++)
		{
			output.append(i + ":" + matchedOld[i] + "\tK" + tokenOld[i].kind + "\t" + tokenOld[i].image);
			output.append("\n");
		}
		output.append("\n");
		for (int i = 0; i < tokenNew.length; i++)
		{
			output.append(i + ":" + matchedNew[i] + "\tK" + tokenNew[i].kind + "\t" + tokenNew[i].image);
			output.append("\n");
		}
		
		try {
			FileWriter tokenMatchLog = new FileWriter("/home/peter/Desktop/Examples/tokenLog.txt");
			tokenMatchLog.write(output.toString());
			tokenMatchLog.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
		*/
		// Marking movements
		LinkedList<Match> matchDiff = new LinkedList<Match>();
		for (int i = 0; i < diff.size(); i++)
			if (diff.get(i) instanceof Match)
				matchDiff.add((Match) diff.get(i));
		
		Collections.sort(matchDiff, new Comparator<Match>() {
			@Override
			public int compare(Match arg0, Match arg1) {
				return arg0.getNewPos() - arg1.getNewPos();
			}
		});
		
		ListIterator<Match> li1 = matchDiff.listIterator();
		for (int order = 0; li1.hasNext();)
		{
			Match m = li1.next();
			m.setOldOrder(order);
			order += m.getLength();
		}
		
		Collections.sort(matchDiff, new Comparator<Match>() {
			@Override
			public int compare(Match arg0, Match arg1) {
				return arg0.getOldPos() - arg1.getOldPos();
			}
		});
		li1 = matchDiff.listIterator();
		for (int order = 0; li1.hasNext();)
		{
			Match m = li1.next();
			m.setNewOrder(order);
			order += m.getLength();
		}
		
		boolean sorted;
		do {
			sorted = true;
			li1 = matchDiff.listIterator();
			for (int oldOrder = -1; li1.hasNext();)
			{
				int tmp = li1.next().getOldOrder();
				if (oldOrder >= tmp)
				{
					sorted = false;
					break;
				}
				else
					oldOrder = tmp;
			}
	
			if (sorted) break;
			
			int[] distance = new int[matchDiff.size()];
			int maxDistance = 0, maxPos = 0;
			li1 = matchDiff.listIterator();
			for (int i = 0; li1.hasNext(); i++)
			{
				Match m = li1.next();
				distance[i] = Math.abs(m.getOldOrder() - m.getNewOrder());
				if (maxDistance < distance[i])
				{
					maxDistance = distance[i];
					maxPos = i;
				}
			}
			
			Match removedMatch = matchDiff.get(maxPos);
			li1 = matchDiff.listIterator();
			while (li1.hasNext())
			{
				Match m = li1.next();
				
				if (m.getOldOrder() > removedMatch.getOldOrder())
					m.setOldOrder(m.getOldOrder() - removedMatch.getLength());
				
				if (m.getNewOrder() > removedMatch.getNewOrder())
					m.setNewOrder(m.getNewOrder() - removedMatch.getLength());
			}
			
			removedMatch.setOldOrder(0);
			removedMatch.setNewOrder(0);
			
			int wordTokenCount = 0;
			int pos = removedMatch.getOldPos();
			for (int i = 0; i < removedMatch.getLength(); i++)
			{
				if (tokenOld[pos+i].kind == MediawikiScannerConstants.WORD)
					wordTokenCount++;
			}
			
			if (wordTokenCount > 3)
				diff.add(new Movement(removedMatch));
			else
			{
				int oldPos = removedMatch.getOldPos();
				int newPos = removedMatch.getNewPos();
				for (int i = 0; i < removedMatch.getLength(); i++)
				{
					matchedOld[oldPos+i].matchId = 0;
					matchedOld[oldPos+i].matchPos = 0;
					matchedNew[newPos+i].matchId = 0;
					matchedNew[newPos+i].matchPos = 0;
				}
				
			}
			
			diff.remove(removedMatch);
			matchDiff.remove(removedMatch);
			
		} while (!sorted);
		
		//Marking delete and insert
		boolean inMatched = true;
		int unmatchedStart = 0;
		
		int delStart = diff.size();
		for (int i = 0; i < tokenOld.length; i++)
		{
			if (inMatched && matchedOld[i].matchId == 0)
			{
				inMatched = false;
				unmatchedStart = i;
			}
			
			if (!inMatched && matchedOld[i].matchId != 0)
			{
				inMatched = true;
				if (i > unmatchedStart)
				{
					WikiToken[] delTokens = new WikiToken[i - unmatchedStart];
					for (int ii = 0; ii < delTokens.length; ii++)
						delTokens[ii] = tokenOld[unmatchedStart + ii];
					
					diff.add(new Deletion(unmatchedStart, delTokens));
				}
			}
		}
		
		if (!inMatched && tokenOld.length > unmatchedStart)
		{
			WikiToken[] delTokens = new WikiToken[tokenOld.length - unmatchedStart];
			for (int ii = 0; ii < delTokens.length; ii++)
				delTokens[ii] = tokenOld[unmatchedStart + ii];
			
			diff.add(new Deletion(unmatchedStart, delTokens));
		}
		
		inMatched = true;
		unmatchedStart = 0;
		
		int insStart = diff.size();
		for (int i = 0; i < tokenNew.length; i++)
		{
			if (inMatched && matchedNew[i].matchId == 0)
			{
				inMatched = false;
				unmatchedStart = i;
			}
			
			if (!inMatched && matchedNew[i].matchId != 0)
			{
				inMatched = true;
				if (i > unmatchedStart)
				{
					WikiToken[] insTokens = new WikiToken[i - unmatchedStart];
					for (int ii = 0; ii < insTokens.length; ii++)
						insTokens[ii] = tokenNew[unmatchedStart + ii];
					
					diff.add(new Insertion(unmatchedStart, insTokens));
				}
			}
		}
		
		if (!inMatched && tokenNew.length > unmatchedStart)
		{
			WikiToken[] insTokens = new WikiToken[tokenNew.length - unmatchedStart];
			for (int ii = 0; ii < insTokens.length; ii++)
				insTokens[ii] = tokenNew[unmatchedStart + ii];
			
			diff.add(new Insertion(unmatchedStart, insTokens));
		}
		
		// Marking replacements
		LinkedList<BasicEdit> replDiff = new LinkedList<BasicEdit>(diff);
		for (int i = delStart; i < insStart; i++)
		{
			int delBegin = ((Deletion) diff.get(i)).getPos();
			int delEnd = delBegin + ((Deletion) diff.get(i)).getLength();
			WikiToken[] delContent = ((Deletion) diff.get(i)).getContent();
			
			for (int j = insStart; j < diff.size(); j++)
			{
				int insBegin = ((Insertion) diff.get(j)).getPos();
				int insEnd = insBegin + ((Insertion) diff.get(j)).getLength();
				WikiToken[] insContent = ((Insertion) diff.get(j)).getContent();
				
				if ((matchedOld[Math.max(delBegin - 1, 0)].matchId == 
					 matchedNew[Math.max(insBegin - 1, 0)].matchId) &&
					(matchedOld[Math.min(delEnd, matchedOld.length - 1)].matchId == 
					 matchedNew[Math.min(insEnd, matchedNew.length - 1)].matchId))
				{
					replDiff.remove(diff.get(i));
					replDiff.remove(diff.get(j));
					replDiff.add(new Replacement(delBegin, delContent, insBegin, insContent));
				}
				
			}
		}
		
		// Pair tag add/remove hack: deal with wiki markup add/remove
		// e.g. Test -> [[Test]]
		resultDiff = new LinkedList<BasicEdit>(replDiff);
		
		for (int i = 0; i < replDiff.size(); i++)
		{
			BasicEdit e = replDiff.get(i);
			
			if (e instanceof Replacement)
			{
				Replacement r = (Replacement) e;
				List<WikiToken> insContent = Arrays.asList(r.getInsertedContent());
				List<WikiToken> delContent = Arrays.asList(r.getDeletedContent());
				
				if (insContent.size() < 100 && delContent.size() < 100)
				{
				
					if (insContent.size() <= delContent.size())
					{
						int maxDelIndex = -1;
						int maxInsIndex = -1;
						int maxLength = 0;
						
						for (int j = 0; j < insContent.size(); j++)
						{
							int delIndex = delContent.indexOf(insContent.get(j));
						
							if (delIndex > -1)
							{
								int containLength = 1;
								
								for (int k = 1; j + k < insContent.size() && k + delIndex < delContent.size(); k++)
								{
									if (insContent.get(j+k).equals(delContent.get(delIndex+k)))
										containLength++;
									else
										break;
								}
								
								if (containLength > maxLength)
								{
									maxDelIndex = delIndex;
									maxInsIndex = j;
									maxLength = containLength;
								}
							}
						}
						
						if (maxLength > 0)
						{
							WikiToken[] d1 = new WikiToken[0], d2 = new WikiToken[0];
							WikiToken[] i1 = new WikiToken[0], i2 = new WikiToken[0];
							
							d1 = delContent.subList(0, maxDelIndex).toArray(d1);
							d2 = delContent.subList(maxDelIndex + maxLength, delContent.size()).toArray(d2);
							i1 = insContent.subList(0, maxInsIndex).toArray(i1);
							i2 = insContent.subList(maxInsIndex + maxLength, insContent.size()).toArray(i2);
							
							if (d1.length > 0 && i1.length == 0)
								resultDiff.add(new Deletion(r.getOldPos(), d1));
							else if (d1.length > 0 && i1.length > 0)
							{
								Replacement newRepl = new Replacement(r.getOldPos(), 
										 d1, r.getNewPos(), i1);
								
								replDiff.add(newRepl);
								resultDiff.add(newRepl);
							}
							else if (d1.length == 0 && i1.length > 0)
								resultDiff.add(new Insertion(r.getNewPos(), i1));
							
							if (d2.length > 0 && i2.length == 0)
								resultDiff.add(new Deletion(r.getOldPos() + maxDelIndex + maxLength, d2));
							else if (d2.length > 0 && i2.length > 0) 
							{
								Replacement newRepl = new Replacement(r.getOldPos() + maxDelIndex + maxLength, 
										 d2, r.getNewPos() + maxInsIndex + maxLength, i2);
								
								replDiff.add(newRepl);
								resultDiff.add(newRepl);
							}
							else if (d2.length == 0 && i2.length > 0)
								resultDiff.add(new Insertion(r.getNewPos() + maxInsIndex + maxLength, i2));
							
							resultDiff.remove(r);
						}
					}
					else if (insContent.size() > delContent.size())
					{
						int maxDelIndex = -1;
						int maxInsIndex = -1;
						int maxLength = 0;
						
						for (int j = 0; j < delContent.size(); j++)
						{
							int insIndex = insContent.indexOf(delContent.get(j));
						
							if (insIndex > -1)
							{
								int containLength = 1;
								
								for (int k = 1; j + k < delContent.size() && k + insIndex < insContent.size(); k++)
								{
									if (delContent.get(j+k).equals(insContent.get(insIndex+k)))
										containLength++;
									else
										break;
								}
								
								if (containLength > maxLength)
								{
									maxDelIndex = j;
									maxInsIndex = insIndex;
									maxLength = containLength;
								}
							}
						}
						
						if (maxLength > 0)
						{
							WikiToken[] d1 = new WikiToken[0], d2 = new WikiToken[0];
							WikiToken[] i1 = new WikiToken[0], i2 = new WikiToken[0];
							
							d1 = delContent.subList(0, maxDelIndex).toArray(d1);
							d2 = delContent.subList(maxDelIndex + maxLength, delContent.size()).toArray(d2);
							i1 = insContent.subList(0, maxInsIndex).toArray(i1);
							i2 = insContent.subList(maxInsIndex + maxLength, insContent.size()).toArray(i2);
							
							if (i1.length > 0 && d1.length == 0)
								resultDiff.add(new Insertion(r.getNewPos(), i1));
							else if (i1.length > 0 && d1.length > 0)
							{
								Replacement newRepl = new Replacement(r.getOldPos(), 
										 d1, r.getNewPos(), i1);
								
								replDiff.add(newRepl);
								resultDiff.add(newRepl);
							}
							else if (i1.length == 0 && d1.length > 0)
								resultDiff.add(new Deletion(r.getOldPos(), d1));
							
							if (i2.length > 0 && d2.length == 0)
								resultDiff.add(new Insertion(r.getNewPos() + maxInsIndex + maxLength, i2));
							else if (i2.length > 0 && d2.length > 0) 
							{
								Replacement newRepl = new Replacement(r.getOldPos() + maxDelIndex + maxLength, 
										 d2, r.getNewPos() + maxInsIndex + maxLength, i2);
								
								replDiff.add(newRepl);
								resultDiff.add(newRepl);
							}
							else if (i2.length == 0 && d2.length > 0)
								resultDiff.add(new Deletion(r.getOldPos() + maxDelIndex + maxLength, d2));
							
							resultDiff.remove(r);
						}
					}
				}

			}
		}
		
		// Sort the diff output
		Collections.sort(resultDiff, new DiffOrder());
	}
	
	public BasicEdit[] outputDiff()
	{	
		this.doDiff();

		BasicEdit[] a = new BasicEdit[resultDiff.size()];
		resultDiff.toArray(a);
		return a;
	}
	
	public static void main(String [] args)
	{
		StringBuffer oldTextBuf = new StringBuffer();
		StringBuffer newTextBuf = new StringBuffer();
		String tmpText;
		
		try {
			BufferedReader oldFile = 
				new BufferedReader(new FileReader("/home/peter/Desktop/Examples/oldText.txt"));
			
			do {
				tmpText = oldFile.readLine();
				if (tmpText != null)
				oldTextBuf.append(tmpText);
				oldTextBuf.append("\n");
			} while (tmpText != null);
			
			oldFile.close();
			
			BufferedReader newFile = 
				new BufferedReader(new FileReader("/home/peter/Desktop/Examples/newText.txt"));
			
			do {
				tmpText = newFile.readLine();
				if (tmpText != null)
				newTextBuf.append(tmpText);
				newTextBuf.append("\n");
			} while (tmpText != null);
			
			newFile.close();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		TokenDiff diff = new TokenDiff(oldTextBuf.toString(), newTextBuf.toString());
		
		BasicEdit[] edit = diff.outputDiff();
		
		for (int i = 0; i < edit.length; i++)
			System.out.println(edit[i].getDescription());
	}
	
}
