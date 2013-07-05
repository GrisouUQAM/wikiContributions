package mo.umac.wikianalysis.diff.token;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

import mo.umac.wikianalysis.lexer.WikiToken;

public class BasicEditReader {

	public static BasicEdit[] read(String revDiff, WikiToken[] oldToken, WikiToken[] newToken)
	{
		List<BasicEdit> be = new ArrayList<BasicEdit>();
		
		BufferedReader br = new BufferedReader( new StringReader(revDiff) );
		
		try {
			String curLine;
			while ((curLine = br.readLine()) != null) {
				int actionNameIndex = curLine.indexOf('(');
				String actionName = curLine.substring(0, actionNameIndex);
				String actionPara = curLine.substring(actionNameIndex + 1, curLine.length() - 1);
				
				if (actionName.equals("Del"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int delLen = sc.nextInt();
					int delPos = sc.nextInt();
					
					WikiToken[] delToken = new WikiToken[delLen];
					for (int i = 0; i < delLen; i++)
						delToken[i] = oldToken[delPos + i];
					
					be.add(new Deletion(delPos, delToken));
				}
				else if (actionName.equals("Ins"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int insLen = sc.nextInt();
					int insPos = sc.nextInt();
					
					WikiToken[] insToken = new WikiToken[insLen];
					for (int i = 0; i < insLen; i++)
						insToken[i] = newToken[insPos + i];
					
					be.add(new Insertion(insPos, insToken));				
				}
				else if (actionName.equals("Match"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int len = sc.nextInt();
					int oldPos = sc.nextInt();
					int newPos = sc.nextInt();

					be.add(new Match(len, oldPos, newPos));
				}
				else if (actionName.equals("Mov"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int len = sc.nextInt();
					int oldPos = sc.nextInt();
					int newPos = sc.nextInt();

					be.add(new Movement(len, oldPos, newPos));
				}
				else if (actionName.equals("Repl"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int delLen = sc.nextInt();
					int delPos = sc.nextInt();
					int insLen = sc.nextInt();
					int insPos = sc.nextInt();
					
					WikiToken[] delToken = new WikiToken[delLen];
					for (int i = 0; i < delLen; i++)
						delToken[i] = oldToken[delPos + i];
					
					WikiToken[] insToken = new WikiToken[insLen];
					for (int i = 0; i < insLen; i++)
						insToken[i] = newToken[insPos + i];
					
					be.add(new Replacement(delPos, delToken, insPos, insToken));
				}
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		BasicEdit[] beArray = new BasicEdit[0];
		beArray = be.toArray(beArray);
		
		return beArray;
	}

}
