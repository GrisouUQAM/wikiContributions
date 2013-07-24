package mo.umac.wikianalysis.diff.sentence;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashSet;
import java.util.LinkedList;
import java.util.List;
import java.util.ListIterator;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Match;
import mo.umac.wikianalysis.diff.token.MatchInfo;
import mo.umac.wikianalysis.diff.token.TokenDiff;
import mo.umac.wikianalysis.lexer.MediawikiScanner;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.ParseException;
import mo.umac.wikianalysis.lexer.SentenceSplitter;
import mo.umac.wikianalysis.lexer.Token;
import mo.umac.wikianalysis.lexer.WikiToken;
import mo.umac.wikianalysis.lexer.WikitextTokenizer;
import mo.umac.wikianalysis.util.array.Sparse2DArray;
import mo.umac.wikianalysis.util.array.Sparse2DArrayColumnIterator;
import mo.umac.wikianalysis.util.array.Sparse2DArrayRowIterator;
import mo.umac.wikianalysis.util.array.Sparse2DCell;

public class SentenceDiff {

	private WikiToken[] oldTokens;
	private WikiToken[] newTokens;
	private ArrayList<Sentence> oldSentences;
	private ArrayList<Sentence> newSentences;
	
	private TokenDiff tokenDiff;
	
	private Sparse2DArray matchingRate;
	private BasicEdit[] edit;
	private ArrayList<SentenceEdit> sentenceEdits;
	private HashSet<Integer> oldMatchedSentences;
	private HashSet<Integer> newMatchedSentences;
	
	private MatchInfo[] oldMatches;
	private MatchInfo[] newMatches;
	
	private final double threshold = 1.0 / 3.0;
	
	public SentenceDiff(String oldText, String newText)
	{
		oldTokens = WikitextTokenizer.tokenize(oldText);
		newTokens = WikitextTokenizer.tokenize(newText);

		oldSentences = new ArrayList<Sentence>();
		newSentences = new ArrayList<Sentence>();
		sentenceEdits = new ArrayList<SentenceEdit>();
	}

	public SentenceDiff(WikiToken[] oldT, WikiToken[] newT)
	{
		oldTokens = oldT;
		newTokens = newT;

		oldSentences = new ArrayList<Sentence>();
		newSentences = new ArrayList<Sentence>();
		sentenceEdits = new ArrayList<SentenceEdit>();
	}
	
	public WikiToken[] getOldTokens() {
		return oldTokens;
	}

	public WikiToken[] getNewTokens() {
		return newTokens;
	}

	public String diff()
	{
		// Execute the token differencing algorithm.
		if (oldMatches != null)
			tokenDiff = new TokenDiff(oldTokens, newTokens, oldMatches, newMatches);
		else
			tokenDiff = new TokenDiff(oldTokens, newTokens);
		
		return "";
	}
	
	public BasicEdit[] outputDiff()
	{
		edit = tokenDiff.outputDiff();
		
		return edit;
	}

	public String separateSentences()
	{
		StringBuffer retString = new StringBuffer();
		
		// Separate old tokens stream into sentences.
		this.oldSentences = SentenceSplitter.separateSentence(oldTokens);

		ListIterator<Sentence> oldIterator = this.oldSentences.listIterator();
		while (oldIterator.hasNext())
		{
			Sentence s = oldIterator.next();
			retString.append(s.startPos + ": ");
			retString.append(Arrays.toString(s.tokens) + "\n");
		}
		
		// Separate new tokens stream into sentences.
		this.newSentences = SentenceSplitter.separateSentence(newTokens);
		
		ListIterator<Sentence> newIterator = this.newSentences.listIterator();
		while (newIterator.hasNext())
		{
			Sentence s = newIterator.next();
			retString.append(s.startPos + ": ");
			retString.append(Arrays.toString(s.tokens) + "\n");
		}
		
		return retString.toString();
	}
	
	/**
	 * Perform exact sentence matching.
	 */
	public void exactMatch()
	{	
		oldMatchedSentences = new HashSet<Integer>();
		newMatchedSentences = new HashSet<Integer>();
		
		oldMatches = new MatchInfo[oldTokens.length];
		newMatches = new MatchInfo[newTokens.length];
		int matchId = 0;

		for (int i = 0; i < oldTokens.length; i++)
			oldMatches[i] = new MatchInfo();
		for (int i = 0; i < newTokens.length; i++)
			newMatches[i] = new MatchInfo();
		
		matchingRate = new Sparse2DArray(oldSentences.size(), newSentences.size());

		for (int i = 0; i < oldSentences.size(); i++)
		{
			if (oldMatchedSentences.contains(i)) continue;
			int oldStart = oldSentences.get(i).startPos;
			int oldLen = oldSentences.get(i).length;

			for (int j = 0; j < newSentences.size(); j++)
			{
				if (newMatchedSentences.contains(j)) continue;
				boolean match = true;
				int newStart = newSentences.get(j).startPos;
				int newLen = newSentences.get(j).length;

				if (oldLen != newLen) 
					continue;

				for (int k = 0; k < oldLen; k++)
				{
					if (!oldTokens[oldStart + k].equals(newTokens[newStart + k]))
					{
						match = false;
						break;
					}
				}

				if (match)
				{
					matchingRate.set(1.0, i, j);
					oldMatchedSentences.add(i);
					newMatchedSentences.add(j);
					
					matchId--;
					for (int k = 0; k < oldLen; k++)
					{
						oldMatches[oldStart + k].matchId = matchId;
						oldMatches[oldStart + k].matchPos = k;
						newMatches[newStart + k].matchId = matchId;
						newMatches[newStart + k].matchPos = k;
					}
					
					break;
				}
			}
		}
	}
	
	/**
	 * Perform sentence level differencing.
	 * @return A string with matching rate of every sentence pair.
	 */
	public String matchingSentence()
	{	
		StringBuffer retString = new StringBuffer();
		
		MatchInfo[] matchedOld = tokenDiff.getMatchedOld();
		MatchInfo[] matchedNew = tokenDiff.getMatchedNew();
		
		matchingRate = new Sparse2DArray(oldSentences.size() + 1, newSentences.size() + 1);
		
		for (int i = 0; i < oldSentences.size(); i++)
		{
			int oldStart = oldSentences.get(i).startPos;
			int oldEnd = oldSentences.get(i).endPos;
			int oldLen = oldSentences.get(i).length;
			
			boolean exactMatched = false;
			
			// Exact sentence match.
			if (matchedOld[oldStart].matchId != 0 &&
				matchedOld[oldStart].matchId == matchedOld[oldEnd].matchId)
			{
				for (int j = 0; j < newSentences.size(); j++)
				{
					int newStart = newSentences.get(j).startPos;
					int newEnd = newSentences.get(j).endPos;
					
					if ((matchedOld[oldStart].matchId ==
						 matchedNew[newStart].matchId) &&
						(matchedOld[oldStart].matchPos ==
						 matchedNew[newStart].matchPos) &&
						(matchedNew[newStart].matchId ==
						 matchedNew[newEnd].matchId) )
					{
						matchingRate.set(1.0, i, j);
						exactMatched = true;
						break;
					}
				}
			}
			// Partial sentence match.
			if (!exactMatched)
			{
				for (int ii = oldStart; ii <= oldEnd; ii++)
				{
					if (matchedOld[ii].matchId != 0)
					{
						int jj = -1;
						int k;
						int newEnd = 0;
						
						for (k = 0; k < edit.length; k++)
						{
							if (edit[k] instanceof Match)
							{
								int oldPos = ((Match) edit[k]).getOldPos();
								int len = ((Match) edit[k]).getLength();
								if (oldPos == ii)
								{
									jj = ((Match) edit[k]).getNewPos();
									newEnd = ((Match) edit[k]).getNewPos() + len;
									break;
								}
								else if (oldPos < ii && ii < oldPos + len)
								{
									jj = ((Match) edit[k]).getNewPos() - oldPos + ii;
									newEnd = ((Match) edit[k]).getNewPos() + len;
									break;
								}
									
							}
						}
						
						if (jj != -1)
						{
							for (int j = 0; j < newSentences.size(); j++)
							{
								if ((jj >= newSentences.get(j).startPos &&
									jj <= newSentences.get(j).endPos) &&
									matchedOld[ii].matchId == matchedNew[jj].matchId &&
									matchedOld[ii].matchPos == matchedNew[jj].matchPos)
								{
									newEnd = Math.min(newEnd, newSentences.get(j).endPos + 1);
									double mr = matchingRate.get(i, j) + 
												Math.min(newEnd - jj, oldEnd - ii + 1) * 2.0 / 
												(newSentences.get(j).length + oldLen);
									matchingRate.set(mr, i, j);
									ii += Math.min(newEnd - jj, oldEnd - ii + 1) - 1;
								}
							}
						}
					}
				}
			}
		}
		
		// Mark sentence matching for each sentence in old version.
		for (int i = 0; i < oldSentences.size(); i++)
		{
			double maxMatchingRate = 0.0;
			int maxMatchingS = -1;
			
			Sparse2DArrayRowIterator riter = matchingRate.getRowIterator(i);
			while (riter.hasNext())
			{
				Sparse2DCell tempCell = riter.next();
				if (tempCell.getValue() > 0.0)
				{
					int j = tempCell.getColumn();
					retString.append(String.format("%4d<=>%4d: %.4f\n",
							oldSentences.get(i).startPos,
							newSentences.get(j).startPos,
							tempCell.getValue()));
					maxMatchingRate = Math.max(maxMatchingRate, tempCell.getValue());
					if (maxMatchingRate == tempCell.getValue())
						maxMatchingS = j;
				}
			}
			
			if (maxMatchingRate == 0.0)
			{
				retString.append(String.format("%4d<=> Del\n", 
						oldSentences.get(i).startPos) );
			}
			
			if (maxMatchingRate < threshold)
				sentenceEdits.add(new SentenceDel(i, oldSentences.get(i), maxMatchingRate));
			else
			{
				SentenceMatch sm = new SentenceMatch(i, maxMatchingS, 
						oldSentences.get(i), newSentences.get(maxMatchingS), maxMatchingRate);
				if (!sentenceEdits.contains(sm))
					sentenceEdits.add(sm);
			}
		}
		
		// Mark sentence matching for each sentence in new version.
		for (int j = 0; j < newSentences.size(); j++)
		{
			double maxMatchingRate = 0.0;
			int maxMatchingS = -1;
			
			Sparse2DArrayColumnIterator citer = matchingRate.getColumnIterator(j);
			while (citer.hasNext())
			{
				Sparse2DCell tempCell = citer.next();
				int i = tempCell.getRow();
				maxMatchingRate = Math.max(maxMatchingRate, tempCell.getValue());
				if (maxMatchingRate == tempCell.getValue())
					maxMatchingS = i;
			}
			
			if (maxMatchingRate == 0.0)
			{
				retString.append(String.format(" Ins<=>%4d\n",
						newSentences.get(j).startPos) );
			}
			
			if (maxMatchingRate < threshold)
				sentenceEdits.add(new SentenceIns(j, newSentences.get(j), maxMatchingRate));
			else
			{
				SentenceMatch sm = new SentenceMatch(maxMatchingS, j,
						oldSentences.get(maxMatchingS), newSentences.get(j), maxMatchingRate);
				if (!sentenceEdits.contains(sm))
					sentenceEdits.add(sm);
			}
		}
		
		// Sentence movement detection
		LinkedList<SentenceMatch> matchDiff = new LinkedList<SentenceMatch>();
		for (int i = 0; i < sentenceEdits.size(); i++)
			if (sentenceEdits.get(i) instanceof SentenceMatch)
				matchDiff.add((SentenceMatch) sentenceEdits.get(i));
		
		Collections.sort(matchDiff, new Comparator<SentenceMatch>() {
			@Override
			public int compare(SentenceMatch arg0, SentenceMatch arg1) {
				return arg0.newPos - arg1.newPos;
			}
		});

		ListIterator<SentenceMatch> li1 = matchDiff.listIterator();
		for (int order = 0; li1.hasNext();)
		{
			SentenceMatch m = li1.next();
			m.setOldOrder(order++);
		}
		
		Collections.sort(matchDiff, new Comparator<SentenceMatch>() {
			@Override
			public int compare(SentenceMatch o1, SentenceMatch o2) {
				return o1.oldPos - o2.oldPos;
			}
		});
		
		li1 = matchDiff.listIterator();
		for (int order = 0; li1.hasNext();)
		{
			SentenceMatch m = li1.next();
			m.setNewOrder(order++);
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
				SentenceMatch m = li1.next();
				distance[i] = Math.abs(m.getOldOrder() - m.getNewOrder());
				if (maxDistance < distance[i])
				{
					maxDistance = distance[i];
					maxPos = i;
				}
			}
			
			SentenceMatch removedMatch = matchDiff.get(maxPos);
			li1 = matchDiff.listIterator();
			while (li1.hasNext())
			{
				SentenceMatch m = li1.next();
				
				if (m.getOldOrder() > removedMatch.getOldOrder())
					m.setOldOrder(m.getOldOrder() - 1);
				
				if (m.getNewOrder() > removedMatch.getNewOrder())
					m.setNewOrder(m.getNewOrder() - 1);
			}
			
			removedMatch.setOldOrder(0);
			removedMatch.setNewOrder(0);
			/*
			int wordTokenCount = 0;
			Token[] newTokens = removedMatch.newSentence.tokens;
			for (int i = 0; i < removedMatch.getNewLength(); i++)
			{
				if (newTokens[i].kind == MediawikiScannerConstants.WORD)
					wordTokenCount++;
			}
			
			if (wordTokenCount > 3)*/
				sentenceEdits.add(new SentenceMove(removedMatch));
			/* else
			{
				int oldPos = removedMatch.getOldStartPos();
				int newPos = removedMatch.getNewStartPos();
				for (int i = 0; i < removedMatch.getOldLength(); i++)
				{
					matchedOld[oldPos+i].matchId = 0;
					matchedOld[oldPos+i].matchPos = 0;
					matchedNew[newPos+i].matchId = 0;
					matchedNew[newPos+i].matchPos = 0;
				}
				
			}
			*/
			matchDiff.remove(removedMatch);
			
		} while (!sorted);
		
		//Sentence merge detection
		for (int i = 0; i < oldSentences.size(); i++)
		{
			for (int j = 0; j < newSentences.size() - 1; j++)
			{
				int k = j + 1;
				if (matchingRate.get(i, j) > threshold && matchingRate.get(i, k) > 0.0)
				{
					int oldLen = oldSentences.get(i).length;
					int newLen = newSentences.get(j).length;
					int extLen = newSentences.get(k).length;
					
					double newMatchingRate = matchingRate.get(i, j) * (oldLen + newLen) + 
						matchingRate.get(i, k) * (oldLen + extLen);
					newMatchingRate /= oldLen + newLen + extLen;
										
					if (matchingRate.get(i, j) < newMatchingRate)
					{
						retString.append("Merge " + oldSentences.get(i).startPos +
								" <=> " + newSentences.get(j).startPos + " + " +
								newSentences.get(k).startPos + "\n");
						
						retString.append(String.format("%4d<=>%4d: %.4f\n", 
								oldSentences.get(i).startPos, 
								newSentences.get(j).startPos, 
								newMatchingRate) );
						
						Sentence[] newS = new Sentence[2];
						newS[0] = newSentences.get(j);
						newS[1] = newSentences.get(k);
						
						int[] newSPos = new int[2];
						newSPos[0] = j;
						newSPos[1] = k;
						sentenceEdits.add(new SentenceSplit(i, newSPos, 
								oldSentences.get(i), newS, newMatchingRate));
						//j--;
					}				
				}
			}
		}
		

		for (int j = 0; j < newSentences.size(); j++)
		{
			for (int i = 0; i < oldSentences.size() - 1; i++)
			{
				int k = i + 1;
				if (matchingRate.get(i, j) > threshold && matchingRate.get(k, j) > 0.0)
				{			
					int oldLen = oldSentences.get(i).length;
					int newLen = newSentences.get(j).length;
					int extLen = oldSentences.get(k).length;
					
					double newMatchingRate = matchingRate.get(i, j) * (oldLen + newLen) + matchingRate.get(k, j) * (extLen + newLen);
					newMatchingRate /= oldLen + newLen + extLen;
					
					if (matchingRate.get(i, j) < newMatchingRate)
					{
						retString.append("Merge " + oldSentences.get(i).startPos + 
								" + " + oldSentences.get(k).startPos + " <=> " + 
								newSentences.get(j).startPos + "\n");
						
						retString.append(String.format("%4d<=>%4d: %.4f\n",
								oldSentences.get(i).startPos,
								newSentences.get(j).startPos, 
								newMatchingRate));
						
						Sentence[] oldS = new Sentence[2];
						oldS[0] = oldSentences.get(i);
						oldS[1] = oldSentences.get(k);
						
						int[] oldSPos = new int[2];
						oldSPos[0] = i;
						oldSPos[1] = k;
						sentenceEdits.add(new SentenceMerge(oldSPos, j, 
								oldS, newSentences.get(j), newMatchingRate));
						
						//i--;
					}
				}
			}
		}
		
		return retString.toString();
	}

	public List<SentenceEdit> getSentenceEdits() {
		return Collections.unmodifiableList(sentenceEdits);
	}
	
	public String printSentenceEdits() {
		StringBuffer retVal = new StringBuffer();
		
		for (int i = 0; i < sentenceEdits.size(); i++)
		{
			if (sentenceEdits.get(i).getClass() != SentenceMatch.class || sentenceEdits.get(i).matchingRate < 1.0)
				retVal.append(sentenceEdits.get(i).descString() + "\n");
		}
		
		return retVal.toString();
	}
	
	public static void main(String [] args)
	{
		StringBuffer oldTextBuf = new StringBuffer();
		StringBuffer newTextBuf = new StringBuffer();
		String tmpText;
		
		try {
     		Reader oldIn = new InputStreamReader(new FileInputStream("/home/peter/Desktop/Examples/oldText.txt"), "UTF-8");
			BufferedReader oldFile = new BufferedReader(oldIn);
			
			do {
				tmpText = oldFile.readLine();
				if (tmpText != null)
				{
					oldTextBuf.append(tmpText);
					oldTextBuf.append("\n");
				}
			} while (tmpText != null);
			
			oldFile.close();
			
     		Reader newIn = new InputStreamReader(new FileInputStream("/home/peter/Desktop/Examples/newText.txt"), "UTF-8");
			BufferedReader newFile = new BufferedReader(newIn);
			
			do {
				tmpText = newFile.readLine();
				if (tmpText != null)
				{
					newTextBuf.append(tmpText);
					newTextBuf.append("\n");
				}
			} while (tmpText != null);
			
			newFile.close();
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		SentenceDiff sdiff = new SentenceDiff(oldTextBuf.toString(),
											  newTextBuf.toString());

		System.out.println(sdiff.separateSentences());
		sdiff.exactMatch();		
		sdiff.diff();
		
		BasicEdit[] edit = sdiff.outputDiff();
		for (int i = 0; i < edit.length; i++)
			System.out.println(edit[i].getDescription());
		
		System.out.println(sdiff.matchingSentence());
		System.out.println(sdiff.printSentenceEdits());
	}
}
