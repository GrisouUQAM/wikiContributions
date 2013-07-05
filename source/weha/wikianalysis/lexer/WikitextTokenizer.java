package mo.umac.wikianalysis.lexer;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;

public class WikitextTokenizer {

	public static WikiToken[] tokenize(String wikitext)
	{
		StringReader reader = new StringReader(wikitext);
		MediawikiScanner scanner = new MediawikiScanner(reader);
		scanner.tokens = new ArrayList<WikiToken>();
		try {
			scanner.parse();
		} catch (ParseException e) {
			e.printStackTrace();
			System.exit(0);
		}
		return scanner.getTokens();
	}
	
	public static void main(String[] args) 
	{
		StringBuffer newTextBuf = new StringBuffer();
		String tmpText;
		WikiToken[] tokens;
		
		BufferedReader newFile;
		try {
			newFile = new BufferedReader(new FileReader("/home/peter/Desktop/Examples/newText.txt"));
			do {
				tmpText = newFile.readLine();
				if (tmpText != null)
				newTextBuf.append(tmpText);
				newTextBuf.append("\n");
			} while (tmpText != null);
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		tokens = WikitextTokenizer.tokenize(newTextBuf.toString());
		for (int i = 0; i < tokens.length; i++)
			System.out.print(tokens[i]);
	}
}
