package mo.umac.wikianalysis.test;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.ListIterator;

import mo.umac.wikianalysis.categorizer.ActionCategorizer;
import mo.umac.wikianalysis.lexer.WikiToken;
import mo.umac.wikianalysis.lexer.WikitextTokenizer;
import mo.umac.wikianalysis.summarizer.EditSignificanceCalculator;

public class HistoryBatchAnalyzer {
	public static void main(String[] args) {
		ArrayList<Integer> revList = new ArrayList<Integer>();
		BufferedReader reader;
		try {
			reader = new BufferedReader(new FileReader("/home/peter/Desktop/Canada/revisions.txt"));
			
			String line = null;
			while ((line = reader.readLine()) != null) {
				String tmp_line = line;
				if (!"".equals(tmp_line))
				revList.add(Integer.parseInt(tmp_line));
				tmp_line = null;
			}
			reader.close();
			
		} catch (FileNotFoundException e) {
			e.printStackTrace();
			System.exit(0);
		} catch (NumberFormatException e) {
			e.printStackTrace();
			System.exit(0);
		} catch (IOException e) {
			e.printStackTrace();
			System.exit(0);
		}

		ListIterator<Integer> iter = revList.listIterator();
		
		int prevId, revId = 0;
		if (iter.hasNext())
			revId = iter.next();
		
		while (iter.hasNext())
		{			
			prevId = revId;
			revId = iter.next();

			File outFile = new File("/home/peter/Desktop/Canada/Result/" + revId + ".txt");
			if (outFile.exists())
				continue;
				
			StringBuffer outputText = new StringBuffer();
			
			String prevText = WikipediaTextFetcher.fetch(prevId);
			String revText = WikipediaTextFetcher.fetch(revId);
			
			WikiToken[] to = WikitextTokenizer.tokenize(prevText);
			WikiToken[] tn = WikitextTokenizer.tokenize(revText);
			
			ActionCategorizer acter = new ActionCategorizer(to, tn);

			outputText.append("======= Difference between version " + prevId + " and " + revId + " =======\n");
			outputText.append("======= Sentence and token breakdown =======\n");
			String[] result = acter.printResult();
			outputText.append(result[0] + "\n");
			
			EditSignificanceCalculator esCalc = new EditSignificanceCalculator(acter.categorize());
			
			outputText.append("======= Categorized edit =======\n");
			outputText.append(acter.printCategorize() + "\n");
			outputText.append("======= Sentence edit =======\n");
			outputText.append(acter.printSentenceEdits() + "\n");

			outputText.append("======= Raw edit count =======\n");
			outputText.append(esCalc.printRawCount() + "\n");
			outputText.append("======= Edit significance =======\n");
			outputText.append(esCalc.printStatistic() + "\n");
			outputText.append("======= Total edit significance =======\n");
			outputText.append(esCalc.calculateSignificance() + "\n");

			try {
				if (outFile.createNewFile())
				{
					FileWriter writer = new FileWriter(outFile);
					writer.write(outputText.toString());
					writer.close();
				}
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				System.exit(0);
			}
		}
	}

}
