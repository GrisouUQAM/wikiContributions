package mo.umac.wikianalysis.categorizer;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.List;
import java.util.ListIterator;
import java.util.Scanner;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.BasicEditReader;
import mo.umac.wikianalysis.lexer.WikiToken;

public class ActionListReader {

	public static List<AbstractEditAction> read(String revDiff, WikiToken[] oldToken, WikiToken[] newToken)
	{
		List<AbstractEditAction> aeList = new ArrayList<AbstractEditAction>();
		
		BufferedReader br = new BufferedReader( new StringReader(revDiff) );
		
		try {
			String curLine;
			while ((curLine = br.readLine()) != null) {

				if (curLine.startsWith("Revert") || 
					curLine.startsWith("CopyVandal") ||
					curLine.isEmpty())
				{
					return aeList;
				}
				
				int actionNameIndex = curLine.indexOf(' ');
				String actionName = curLine.substring(0, actionNameIndex);
				String actionPara = curLine.substring(actionNameIndex + 1, curLine.length() - 1);
				
				String beEdit = new String();
				Scanner sc = new Scanner(actionPara);
				sc.useDelimiter(";");
				
				while(sc.hasNext())
					beEdit += sc.next().trim() + "\n";		
				
				BasicEdit[] beArray = BasicEditReader.read(beEdit, oldToken, newToken);
				try {
					AbstractEditAction ae = (AbstractEditAction) Class.forName("mo.umac.wikianalysis.categorizer." + actionName).newInstance();
					ae.setBasicEditList(beArray);
					
					aeList.add(ae);
					
				} catch (InstantiationException e) {
					e.printStackTrace();
				} catch (IllegalAccessException e) {
					e.printStackTrace();
				} catch (ClassNotFoundException e) {
					e.printStackTrace();
				}

			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return aeList;
	}
	
	public static String[] printLinkedCategorize(List<AbstractEditAction> aeList) {
		ArrayList<String> catList = new ArrayList<String>();
		String prevName = new String();
		StringBuffer retString = new StringBuffer();
		
		// Print the categorized actions 
		ListIterator<AbstractEditAction> iter = aeList.listIterator();
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
	
	public static void main(String[] args)
	{
		String revDiff = "Wikify Ins(1, 0); Ins(1, 2); \nTypoCorrection Repl(1, 7, 1, 9); \nContentAddition Ins(15, 13); \n";
		ActionListReader.read(revDiff, null, null);
	}

}
