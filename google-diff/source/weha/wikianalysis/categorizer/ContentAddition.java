package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Insertion;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class ContentAddition extends AbstractEditAction {

	private int wordCount;
	
	{
		weight = 1.0;
	}
	
	public ContentAddition() {
		this.be = null;
	}
	
	public ContentAddition(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
		this.wordCount = 0;
	}

	public ContentAddition(BasicEdit b, int wordCount) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
		this.wordCount = wordCount;
	}
	
	public boolean isAction(BasicEdit edit) {

		boolean retVal = false;
		this.wordCount = 0;
		if (!(edit instanceof Insertion))
			return false;
		else
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] content = insEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.WORD)
				{
					 retVal = true;
					 wordCount++;
				}
			}
		}
		
		return retVal;
	}

	@Override
	public List<AbstractEditAction> classify(List<BasicEdit> editList) {
		ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
		ArrayList<BasicEdit> newEditList = new ArrayList<BasicEdit>();
		
		for (int i = 0; i < editList.size(); i++)
		{
			BasicEdit b = editList.get(i);
			if (this.isAction(b))
				ret.add(new ContentAddition(b, this.wordCount));
			else {
				newEditList.add(b);
			}
		}
		
		editList.clear();
		editList.addAll(newEditList);
		
		return ret;
	}

	@Override
	public int lengthCount() {

		if (wordCount == 0)
		{
			Insertion insEdit = (Insertion) be[0];
			WikiToken[] content = insEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.WORD)
					wordCount++;
			}
		}
		
		return wordCount;
		
	}

}
