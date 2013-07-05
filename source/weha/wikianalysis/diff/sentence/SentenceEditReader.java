package mo.umac.wikianalysis.diff.sentence;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

public class SentenceEditReader {

	public static List<SentenceEdit> read(String revSDiff, 
										  ArrayList<Sentence> sOld, 
										  ArrayList<Sentence> sNew)
	{
		List<SentenceEdit> se = new ArrayList<SentenceEdit>();
		
		BufferedReader br = new BufferedReader( new StringReader(revSDiff) );
		
		try {
			String curLine;
			while ((curLine = br.readLine()) != null) {
				int actionNameIndex = curLine.indexOf('(');
				String actionName = curLine.substring(0, actionNameIndex);
				String actionPara = curLine.substring(actionNameIndex + 1, curLine.length() - 1);
				
				if (actionName.equals("SentenceDel"))
				{
					int delPos = Integer.parseInt(actionPara);
					se.add(new SentenceDel(delPos, sOld.get(delPos), 0.0));
				}
				else if (actionName.equals("SentenceIns"))
				{
					int insPos = Integer.parseInt(actionPara);
					se.add(new SentenceIns(insPos, sNew.get(insPos), 0.0));					
				}
				else if (actionName.equals("SentenceMatch"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int oldPos = sc.nextInt();
					int newPos = sc.nextInt();
					double mr = sc.nextDouble();
					se.add(new SentenceMatch(oldPos, newPos, sOld.get(oldPos), sNew.get(newPos), mr));
				}
				else if (actionName.equals("SentenceMove"))
				{
					Scanner sc = new Scanner(actionPara);
					sc.useDelimiter(", ");
					int oldPos = sc.nextInt();
					int newPos = sc.nextInt();
					double mr = sc.nextDouble();
					se.add(new SentenceMove(oldPos, newPos, sOld.get(oldPos), sNew.get(newPos), mr));
				}
				else if (actionName.equals("SentenceMerge"))
				{
					String oldPosStr = actionPara.substring(actionPara.indexOf('[') + 1, actionPara.indexOf(']'));
					int newPos = Integer.parseInt(actionPara.substring(actionPara.indexOf(']') + 3, actionPara.lastIndexOf(',')));
					double mr = Double.parseDouble(actionPara.substring(actionPara.lastIndexOf(',') + 2));
					
					ArrayList<Integer> oldPosArray = new ArrayList<Integer>();
					Scanner osc = new Scanner(oldPosStr);
					osc.useDelimiter(", ");
					while (osc.hasNextInt())
						oldPosArray.add(osc.nextInt());
					int[] oldPosA = new int[oldPosArray.size()];
					Sentence[] oldSentenceA = new Sentence[oldPosArray.size()];
					for (int i = 0; i < oldPosA.length; i++)
					{
						oldPosA[i] = oldPosArray.get(i);
						oldSentenceA[i] = sOld.get(oldPosA[i]);
					}
					
					se.add(new SentenceMerge(oldPosA, newPos, oldSentenceA, sNew.get(newPos), mr));
				}
				else if (actionName.equals("SentenceSplit"))
				{
					int oldPos = Integer.parseInt(actionPara.substring(0, actionPara.indexOf(',')));
					String newPosStr = actionPara.substring(actionPara.indexOf('[') + 1, actionPara.indexOf(']'));
					double mr = Double.parseDouble(actionPara.substring(actionPara.lastIndexOf(',') + 2));
					
					ArrayList<Integer> newPosArray = new ArrayList<Integer>();
					Scanner nsc = new Scanner(newPosStr);
					nsc.useDelimiter(", ");
					while (nsc.hasNextInt())
						newPosArray.add(nsc.nextInt());
					int[] newPosA = new int[newPosArray.size()];
					Sentence[] newSentenceA = new Sentence[newPosArray.size()];
					for (int i = 0; i < newPosA.length; i++)
					{
						newPosA[i] = newPosArray.get(i);
						newSentenceA[i] = sNew.get(newPosA[i]);
					}
					
					se.add(new SentenceSplit(oldPos, newPosA, sOld.get(oldPos), newSentenceA, mr));
				}
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return se;
	}

}
