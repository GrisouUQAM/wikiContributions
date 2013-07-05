package mo.umac.wikianalysis.util;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import mo.umac.wikianalysis.categorizer.ActionCategorizer;
import mo.umac.wikianalysis.diff.sentence.SentenceDel;
import mo.umac.wikianalysis.diff.sentence.SentenceEdit;
import mo.umac.wikianalysis.diff.sentence.SentenceIns;
import mo.umac.wikianalysis.diff.sentence.SentenceMatch;
import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Deletion;
import mo.umac.wikianalysis.diff.token.Insertion;
import mo.umac.wikianalysis.diff.token.Movement;
import mo.umac.wikianalysis.diff.token.Replacement;
import mo.umac.wikianalysis.lexer.WikiToken;

public class WikiDiffFormatter {
	
	private StringBuffer output;
	private ActionCategorizer acter;
	
	public WikiDiffFormatter(String oldWikitext, String newWikitext)
	{
		this.acter = new ActionCategorizer(oldWikitext, newWikitext);
		this.output = new StringBuffer();
	}
	
	public String printCategorize()
	{
		return this.acter.printCategorize();
	}
	
	public String outputDiff()
	{
		acter.printResult();
		BasicEdit[] basicEdits = acter.getBasicEdits();
		List<SentenceEdit> sentenceEdits = acter.getSentenceEdits();
		
		Map<Integer, Integer> sDelPosition = new HashMap<Integer, Integer>();
		Map<Integer, Integer> sInsPosition = new HashMap<Integer, Integer>();
		Map<Integer, Integer> sChangeOldPosition = new HashMap<Integer, Integer>();
		Map<Integer, Integer> sChangeNewPosition = new HashMap<Integer, Integer>();
		
		Map<Integer, Integer> delPosition = new HashMap<Integer, Integer>();
		Map<Integer, Integer> insPosition = new HashMap<Integer, Integer>();
		Map<Integer, Integer> movOldPosition = new HashMap<Integer, Integer>();
		Map<Integer, Integer> movNewPosition = new HashMap<Integer, Integer>();

		for (int i = 0; i < sentenceEdits.size(); i++)
		{
			SentenceEdit se = sentenceEdits.get(i);
			if (se instanceof SentenceDel) {
				sDelPosition.put(se.getOldStartPos(), se.getOldEndPos());
			}			
			else if (se instanceof SentenceIns) {
				sInsPosition.put(se.getNewStartPos(), se.getNewEndPos());				
			}
			else if (se instanceof SentenceMatch) {
				if (se.getMatchingRate() < 1.0)
				{
					sChangeOldPosition.put(se.getOldStartPos(), se.getOldEndPos());
					sChangeNewPosition.put(se.getNewStartPos(), se.getNewEndPos());			
				}
			}
		}
		
		for (int i = 0; i < basicEdits.length; i++)
		{
			BasicEdit edit = basicEdits[i];
			if (edit instanceof Deletion) {
				Deletion e = (Deletion) edit;
				int delStartPos = e.getPos();
				int delEndPos = delStartPos + e.getLength() - 1;
				delPosition.put(delStartPos, delEndPos);
			} else if (edit instanceof Insertion) {
				Insertion e = (Insertion) edit;
				int insStartPos = e.getPos();
				int insEndPos = insStartPos + e.getLength() - 1;
				insPosition.put(insStartPos, insEndPos);
			} else if (edit instanceof Replacement) {
				Replacement e = (Replacement) edit;
				int delStartPos = e.getOldPos();
				int delEndPos = delStartPos + e.getDeletedLength() - 1;
				delPosition.put(delStartPos, delEndPos);
					
				int insStartPos = e.getNewPos();
				int insEndPos = insStartPos + e.getInsertedLength() - 1;
				insPosition.put(insStartPos, insEndPos);
			} else if (edit instanceof Movement) {
				Movement e = (Movement) edit;
				int movOldStartPos = e.getOldPos();
				int movNewStartPos = e.getNewPos();
				int movLen = e.getLength();
				
				movOldPosition.put(movOldStartPos, movOldStartPos + movLen - 1);
				movNewPosition.put(movNewStartPos, movNewStartPos + movLen - 1);
			}
		}

		WikiToken[] tokenOld = acter.getTokenOld();
		WikiToken[] tokenNew = acter.getTokenNew();
		
		output.append("<table border=\"0\" width=\"100%\" align=\"center\" style=\"table-layout:fixed;\">\n");
		output.append("<col width=\"50%\" />\n");
		output.append("<col width=\"50%\" />\n");
		output.append("<tr><th>Old version</th><th>New version</th></tr>\n");
		output.append("<tr><td style=\"vertical-align:text-top;\"><div style=\"word-wrap:break-word; overflow:auto;\">\n");
		
		int delEnd = -1;
		int sDelEnd = -1;
		boolean tagClosed = true;
		boolean sTagClosed = true;
		
		for(int i = 0; i < tokenOld.length; i++)
		{
			WikiToken tt = tokenOld[i];
			
			if (sDelPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				if (!sTagClosed) output.append("</span>");
				output.append("<span style='background-color:#FF9999;'>");
				sTagClosed = false;
				sDelEnd = sDelPosition.get(i);
				if (!tagClosed) output.append("<span style=\"color:red; font-weight:bold;\">");
			}
			else if (sChangeOldPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				if (!sTagClosed) output.append("</span>");
				output.append("<span style='background-color:#FFFF99;'>");
				sTagClosed = false;
				sDelEnd = sChangeOldPosition.get(i);
				if (!tagClosed) output.append("<span style=\"color:red; font-weight:bold;\">");
			}
			
			if (delPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				output.append("<span style=\"color:red; font-weight:bold;\">");
				tagClosed = false;
				delEnd = delPosition.get(i);
			}
			else if (movOldPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				output.append("<span style=\"color:blue; font-weight:bold;\">");
				tagClosed = false;
				delEnd = movOldPosition.get(i);
			}

			output.append(WikiDiffFormatter.htmlSpecialChars(tt.toString()).replace("\n", "<br />\n"));
						
			if (i == delEnd)
			{
				output.append("</span>");
				tagClosed = true;
			}
			if (i == sDelEnd)
			{
				if (!tagClosed) output.append("</span>");
				output.append("</span>");
				sTagClosed = true;
				if (!tagClosed) output.append("<span style=\"color:red; font-weight:bold;\">");
			}
		}

		if (!tagClosed) output.append("</span>");
		if (!sTagClosed) output.append("</span>");
		
		output.append("</div></td><td style=\"vertical-align:text-top;\"><div style=\"word-wrap:break-word; overflow:auto;\">\n");
		
		int insEnd = -1;
		int sInsEnd = -1;
		tagClosed = true;
		sTagClosed = true;
		
		for(int i = 0; i <tokenNew.length; i++)
		{
			WikiToken tt = tokenNew[i];
			
			if (sInsPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				if (!sTagClosed) output.append("</span>");
				output.append("<span style='background-color:#99FF99;'>");
				sTagClosed = false;
				sInsEnd = sInsPosition.get(i);
				if (!tagClosed) output.append("<span style=\"color:green; font-weight:bold;\">");
			}
			else if (sChangeNewPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				if (!sTagClosed) output.append("</span>");
				output.append("<span style='background-color:#FFFF99;'>");
				sTagClosed = false;
				sInsEnd = sChangeNewPosition.get(i);
				if (!tagClosed) output.append("<span style=\"color:green; font-weight:bold;\">");
			}
			
			if (insPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				output.append("<span style='color:green; font-weight:bold;'>");
				tagClosed = false;
				insEnd = insPosition.get(i);
			}
			else if (movNewPosition.containsKey(i))
			{
				if (!tagClosed) output.append("</span>");
				output.append("<span style='color:blue; font-weight:bold;'>");
				tagClosed = false;
				insEnd = movNewPosition.get(i);
			}
			output.append(WikiDiffFormatter.htmlSpecialChars(tt.toString()).replace("\n", "<br />\n"));
			
			if (i == insEnd)
			{
				output.append("</span>");
				tagClosed = true;
			}			
			if (i == sInsEnd)
			{
				if (!tagClosed) output.append("</span>");
				output.append("</span>");
				sTagClosed = true;
				if (!tagClosed) output.append("<span style=\"color:green; font-weight:bold;\">");
			}
		}
		
		if (!tagClosed) output.append("</span>");
		if (!sTagClosed) output.append("</span>");
		output.append("</div></td></table>");
		
		return this.output.toString();
	}
	
	public static String htmlSpecialChars(String str) {
		str = str.replaceAll("&", "&amp;");
		str = str.replaceAll("<", "&lt;");
		str = str.replaceAll(">", "&gt;");
	    str = str.replace("\"", "&quot;");
	    str = str.replace("'", "&#039;");
	    return str;
	}
	
	public static void main(String[] args)
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
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		WikiDiffFormatter fmter = new WikiDiffFormatter(oldTextBuf.toString(),
														newTextBuf.toString());
		
		String result = fmter.outputDiff();
		System.out.println(result);
	}
}
