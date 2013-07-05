package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Deletion;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class ContentRemoval extends AbstractEditAction {

	private int wordCount;
	
	{
		weight = 0.5;
	}
	
	public ContentRemoval() {
		this.be = null;
	}
	
	public ContentRemoval(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
		this.wordCount = 0;
	}	
	
	public ContentRemoval(BasicEdit b, int wordCount) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
		this.wordCount = wordCount;
	}

	public boolean isAction(BasicEdit edit) {
		
		boolean retVal = false;
		this.wordCount = 0;
		
		if (!(edit instanceof Deletion))
			return false;
		else
		{
			Deletion delEdit = (Deletion) edit;
			WikiToken[] content = delEdit.getContent();
			
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
				ret.add(new ContentRemoval(b, this.wordCount));
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
			Deletion delEdit = (Deletion) be[0];
			WikiToken[] content = delEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.WORD)
					wordCount++;
			}
		}

		return wordCount;
	}
	
}
