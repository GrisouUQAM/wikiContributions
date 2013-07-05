package mo.umac.wikianalysis.summarizer;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.StringReader;
import java.util.Map;
import java.util.TreeMap;

public class ActionCountReader {

	public static Map<String, Integer> read(String actionCount)
	{
		Map<String, Integer> actionCountList = new TreeMap<String, Integer>();
		
		BufferedReader br = new BufferedReader( new StringReader(actionCount) );
		
		try {
			String curLine;
			while ((curLine = br.readLine()) != null) {

				if (curLine.startsWith("Revert"))
				{
					return actionCountList;
				}
				
				int actionNameIndex = curLine.indexOf(':');
				String actionName = curLine.substring(0, actionNameIndex);
				String actionPara = curLine.substring(actionNameIndex + 1, curLine.length()).trim();
				
				actionCountList.put(actionName, Integer.valueOf(actionPara));

			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return actionCountList;
	}
	
	public static void main(String[] args)
	{
		String ac = "ContentAddition: 6\nContentMovement: 5\nContentRemoval: 6\nContentSubstitution: 2\nDewikify: 3\nTypoCorrection: 1\nUncategorized: 1";
		ActionCountReader.read(ac);
	}
	
}
