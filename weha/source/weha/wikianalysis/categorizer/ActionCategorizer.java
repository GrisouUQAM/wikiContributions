package mo.umac.wikianalysis.categorizer;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.ListIterator;

import mo.umac.wikianalysis.diff.sentence.SentenceDiff;
import mo.umac.wikianalysis.diff.sentence.SentenceEdit;
import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Match;
import mo.umac.wikianalysis.diff.token.Movement;
import mo.umac.wikianalysis.lexer.WikiToken;
import mo.umac.wikianalysis.lexer.WikitextTokenizer;

public class ActionCategorizer {
	private BasicEdit[] basicEdits;
	private String basicEditStr;
	private SentenceDiff sdiff;
	private ArrayList<AbstractEditAction> actions;
	private ArrayList<AbstractEditAction> actionList;
	
	public ActionCategorizer(String oldStr, String newStr) {
		this.sdiff = new SentenceDiff(oldStr, newStr);
		
		this.actions = new ArrayList<AbstractEditAction>();
		this.actions.add(new EditorialComments());
		this.actions.add(new Categorize());
		this.actions.add(new Interwiki());
		this.actions.add(new Wikify());
		this.actions.add(new Dewikify());
		this.actions.add(new PunctuationCorrection());
		this.actions.add(new ImageAttribute());
		this.actions.add(new TypoCorrection());
		this.actions.add(new ImageAddition());
		this.actions.add(new ImageRemoval());
		this.actions.add(new References());
		this.actions.add(new ContentSubstitution());
		this.actions.add(new ContentRemoval());
		this.actions.add(new ContentAddition());
		this.actions.add(new ContentMovement());
		this.actions.add(new Uncategorized());
	}
	
	public ActionCategorizer(WikiToken[] oldT, WikiToken[] newT) {
		this.sdiff = new SentenceDiff(oldT, newT);
		
		this.actions = new ArrayList<AbstractEditAction>();
		this.actions.add(new EditorialComments());
		this.actions.add(new Categorize());
		this.actions.add(new Interwiki());
		this.actions.add(new Wikify());
		this.actions.add(new Dewikify());
		this.actions.add(new PunctuationCorrection());
		this.actions.add(new ImageAttribute());
		this.actions.add(new TypoCorrection());
		this.actions.add(new ImageAddition());
		this.actions.add(new ImageRemoval());
		this.actions.add(new References());
		this.actions.add(new ContentSubstitution());
		this.actions.add(new ContentRemoval());
		this.actions.add(new ContentAddition());
		this.actions.add(new ContentMovement());
		this.actions.add(new Uncategorized());
	}

	public BasicEdit[] getBasicEdits() {
		return basicEdits;
	}

	public String printBasicEdits() {
		if (basicEditStr != null)
			return basicEditStr;
		
		StringBuffer retVal = new StringBuffer();
		
		for (int i = 0; i < basicEdits.length; i++)
			retVal.append(basicEdits[i].getDescription() + "\n");
		
		return retVal.toString();
	}
	
	public String[] printResult()
	{
		String[] retString = new String[4];
		StringBuffer outStr = new StringBuffer();
		
		retString[0] = sdiff.separateSentences();
		sdiff.exactMatch();
		retString[1] = sdiff.diff();
		
		basicEdits = sdiff.outputDiff();
		basicEditStr = this.printBasicEdits();
		retString[2] = sdiff.matchingSentence();
		
		for (int i = 0; i < basicEdits.length; i++)
		{
			if (!(basicEdits[i] instanceof Match) ||
				 (basicEdits[i] instanceof Movement))
				outStr.append(basicEdits[i].getDescription() + "\n");
		}
		outStr.append("\n");
		
		retString[3] = outStr.toString();
		
		return retString;
	}
	
	public List<AbstractEditAction> categorize()
	{
		if (this.actionList == null)
		{
			ArrayList<BasicEdit> beList = new ArrayList<BasicEdit>(Arrays.asList(basicEdits));
			ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
			
			for (int j = 0; j < actions.size(); j++) {
				AbstractEditAction ae = actions.get(j);
				ret.addAll(ae.classify(beList));
			}
			
			this.actionList = ret;
		}
		return this.actionList;
	}
	
	public String printCategorize()
	{
		StringBuffer retString = new StringBuffer();
		
		// Print the categorized actions 
		ListIterator<AbstractEditAction> iter = actionList.listIterator();
		while (iter.hasNext())
		{
			AbstractEditAction action = iter.next();
			retString.append(action.getClass().getSimpleName() + " ");
			for (int i = 0; i < action.be.length; i++)
				retString.append(action.be[i].getDescription() + "; ");
			retString.append("\n");
		}
		
		return retString.toString();
	}
	
	public String[] printLinkedCategorize()
	{
		ArrayList<String> catList = new ArrayList<String>();
		String prevName = new String();
		StringBuffer retString = new StringBuffer();
		
		// Print the categorized actions 
		ListIterator<AbstractEditAction> iter = actionList.listIterator();
		while (iter.hasNext())
		{
			AbstractEditAction action = iter.next();
			String curName = action.getClass().getSimpleName();
			if (!curName.equals(prevName)) {
				catList.add(prevName + "\n" + retString.toString());
				retString = new StringBuffer();
			}
			for (int i = 0; i < action.be.length; i++)
				retString.append(action.be[i].getLinkedDesc() + "; ");
			retString.append("\n");

			prevName = curName;
		}
		
		catList.add(prevName + "\n" + retString.toString());
		String[] catArray = new String[0];
		catArray = catList.toArray(catArray);
		
		return catArray;
	}
	
	public WikiToken[] getTokenOld() {
		return sdiff.getOldTokens();
	}
	
	public WikiToken[] getTokenNew() {
		return sdiff.getNewTokens();
	}
	
	public List<SentenceEdit> getSentenceEdits() {
		return sdiff.getSentenceEdits();
	}
	
	public String printSentenceEdits() {
		return sdiff.printSentenceEdits();
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
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		WikiToken[] to = WikitextTokenizer.tokenize(oldTextBuf.toString());
		WikiToken[] tn = WikitextTokenizer.tokenize(newTextBuf.toString());
		
		ActionCategorizer acter = new ActionCategorizer(to, tn);
		
		String[] result = acter.printResult();
		for (int i = 0; i < result.length; i++)
			System.out.println(result[i]);
		
		acter.categorize();
		System.out.println(acter.printCategorize());

		System.out.println(acter.printBasicEdits());
		System.out.println(acter.printSentenceEdits());
	}
	
}
