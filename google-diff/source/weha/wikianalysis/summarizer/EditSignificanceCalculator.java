package mo.umac.wikianalysis.summarizer;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

import mo.umac.wikianalysis.categorizer.AbstractEditAction;
import mo.umac.wikianalysis.categorizer.ActionCategorizer;
import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.lexer.WikiToken;
import mo.umac.wikianalysis.lexer.WikitextTokenizer;

public class EditSignificanceCalculator {

	private List<AbstractEditAction> actionList;
	private Map<String, ArrayList<AbstractEditAction>> actionMap;
	private Map<String, Integer> rawCount;
	private Map<String, Integer> tokenCount;
	private int tokenCountTotal;

	public EditSignificanceCalculator(List<AbstractEditAction> actionList) {
		super();
		this.actionList = actionList;
		this.actionMap = new TreeMap<String, ArrayList<AbstractEditAction>>();
		this.rawCount = new TreeMap<String, Integer>();
		this.tokenCount = new TreeMap<String, Integer>();
		this.tokenCountTotal = 0;
	}
	
	public EditSignificanceCalculator(Map<String, Integer> actionCount, Map<String, Integer> tokenCount) {
		super();
		this.actionList = null;
		this.actionMap = new TreeMap<String, ArrayList<AbstractEditAction>>();
		this.rawCount = actionCount;
		this.tokenCount = tokenCount;
		this.tokenCountTotal = 0;
		
		Map<String, Integer> resultMap = this.outputTokenCount();
		Set<String> keySet = resultMap.keySet();
		
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			this.tokenCountTotal += resultMap.get(key);
		}
	}
	
	public Map<String, Double> outputStatistic()
	{
		Map<String, Double> resultMap = new TreeMap<String, Double>();

		if (rawCount.isEmpty())
			this.outputRawCount();
		
		Set<String> keySet = rawCount.keySet();
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			AbstractEditAction ae;
			try {
				ae = (AbstractEditAction) Class.forName("mo.umac.wikianalysis.categorizer." + key).newInstance();
				resultMap.put(key, Double.valueOf(ae.getWeight() * rawCount.get(key).doubleValue()));
			} catch (InstantiationException e) {
				e.printStackTrace();
			} catch (IllegalAccessException e) {
				e.printStackTrace();
			} catch (ClassNotFoundException e) {
				e.printStackTrace();
			}
		}

		return resultMap;
	}
	
	public Map<String, Integer> outputRawCount()
	{
		if (rawCount.isEmpty())
		{
			for (int i = 0; i < actionList.size(); i++)
			{
				AbstractEditAction ea = actionList.get(i);
				String actionName = ea.getClass().getSimpleName();

				if (rawCount.containsKey(actionName))
				{
					int preValue = rawCount.get(actionName).intValue();
					rawCount.put(actionName, Integer.valueOf(preValue + ea.lengthCount()));
				}
				else
					rawCount.put(actionName, Integer.valueOf(ea.lengthCount()));
				
				if (actionMap.containsKey(actionName))
				{
					ArrayList<AbstractEditAction> preList = actionMap.get(actionName);
					preList.add(ea);
					actionMap.put(actionName, preList);
				}
				else
				{
					ArrayList<AbstractEditAction> preList = new ArrayList<AbstractEditAction>();
					preList.add(ea);
					actionMap.put(actionName, preList);
				}
			}
		}
		
		return rawCount;
	}
	
	public Map<String, Integer> outputTokenCount()
	{
		if (tokenCount.isEmpty())
		{
			for (int i = 0; i < actionList.size(); i++)
			{
				AbstractEditAction ea = actionList.get(i);
				String actionName = ea.getClass().getSimpleName();
				
				tokenCountTotal += ea.tokenCount();
				
				if (tokenCount.containsKey(actionName))
				{
					int preValue = tokenCount.get(actionName).intValue();
					tokenCount.put(actionName, Integer.valueOf(preValue + ea.tokenCount()));
				}
				else
					tokenCount.put(actionName, Integer.valueOf(ea.tokenCount()));
			}
		}
		
		return tokenCount;
	}
	
	public String printStatistic()
	{
		StringBuffer retVal = new StringBuffer();
		Map<String, Double> resultMap = this.outputStatistic();
		Set<String> keySet = resultMap.keySet();
		
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			retVal.append(key + ": " + resultMap.get(key) + "\n");
		}
		
		return retVal.toString();
	}
	
	public String printDetailStatistic()
	{
		StringBuffer retVal = new StringBuffer();
		Map<String, Double> resultMap = this.outputStatistic();
		Set<String> keySet = resultMap.keySet();
		
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			retVal.append("<p><b><a href='#' onclick='toggleExpand(\"actiondetail-" + key.toLowerCase() + "\");'>" + key + ": " + resultMap.get(key) + "</a></b>\n");
			
			retVal.append("<div class='editaction' id='actiondetail-" + key.toLowerCase() + "'><ul>\n");
			if (actionMap.containsKey(key))
			{
				ArrayList<AbstractEditAction> aeList = actionMap.get(key);
				ListIterator<AbstractEditAction> aeIter = aeList.listIterator();
				while (aeIter.hasNext())
				{
					AbstractEditAction ae = aeIter.next();
					retVal.append("<li>");
					BasicEdit[] be = ae.getBasicEditList();
					for (int i = 0; i < be.length; i++)
						retVal.append(be[i].getLinkedDesc() + "; ");
					retVal.append("</li>\n");
				}
			}
			retVal.append("</ul></div></p>");
		}
		
		return retVal.toString();
	}
	
	public String printRawCount()
	{
		StringBuffer retVal = new StringBuffer();
		Map<String, Integer> resultMap = this.outputRawCount();
		Set<String> keySet = resultMap.keySet();
		
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			retVal.append(key + ": " + resultMap.get(key) + "\n");
		}
		
		return retVal.toString();
	}
	
	public String printTokenCount()
	{
		StringBuffer retVal = new StringBuffer();
		Map<String, Integer> resultMap = this.outputTokenCount();
		Set<String> keySet = resultMap.keySet();
		
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			retVal.append(key + ": " + resultMap.get(key) + "\n");
		}

		return retVal.toString();
	}
	
	public double calculateSignificance() {
		
		double retVal = 0.0;
		
		if (rawCount.isEmpty())
			this.outputRawCount();
		
		Set<String> keySet = rawCount.keySet();
		Iterator<String> iter = keySet.iterator();
		
		while (iter.hasNext())
		{
			String key = iter.next();
			AbstractEditAction ae;
			try {
				ae = (AbstractEditAction) Class.forName("mo.umac.wikianalysis.categorizer." + key).newInstance();
				retVal += ae.getWeight() * rawCount.get(key).doubleValue();
			} catch (InstantiationException e) {
				e.printStackTrace();
			} catch (IllegalAccessException e) {
				e.printStackTrace();
			} catch (ClassNotFoundException e) {
				e.printStackTrace();
			}
		}
		
		return retVal;
	}
	
	public static void main(String[] args)
	{
		/*
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
		acter.printResult();
		EditSignificanceCalculator esCalc = new EditSignificanceCalculator(acter.categorize());
	
		System.out.println(esCalc.printRawCount());
		System.out.println(esCalc.printTokenCount());
		System.out.println(esCalc.printStatistic());
		System.out.println(esCalc.calculateSignificance());
		*/
		Map<String, Integer> ac = ActionCountReader.read("Categorize: 1\nContentAddition: 67\n"); 
		EditSignificanceCalculator esCalc2 = new EditSignificanceCalculator(ac, ac);
		
		System.out.println(esCalc2.calculateSignificance());
		System.out.println(esCalc2.printRawCount());
		System.out.println(esCalc2.printTokenCount());
		System.out.println(esCalc2.printStatistic());
		
	}

	public int getTokenCountTotal() {
		return tokenCountTotal;
	}
	
}
