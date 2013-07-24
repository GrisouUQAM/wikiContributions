package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Insertion;
import mo.umac.wikianalysis.diff.token.Replacement;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class Categorize extends AbstractEditAction {

	{
		weight = 0.5;
	}
	
	public Categorize()
	{
		this.be = null;
	}
	
	public Categorize(BasicEdit[] be)
	{
		this.be = be;
	}
	
	public Categorize(BasicEdit be)
	{
		this.be = new BasicEdit[1];
		this.be[0] = be;
	}
	
	public boolean isAction(BasicEdit edit) {
		if (!(edit instanceof Insertion) && !(edit instanceof Replacement))
			return false;
		else if (edit instanceof Insertion)
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] content = insEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_PREFIX &&
					content[i].image.equals("Category:"))
					return true;
			}
		}
		else
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] content = replEdit.getInsertedContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_PREFIX &&
					content[i].image.equals("Category:"))
					return true;
			}
		}
		
		return false;
	}

	@Override
	public List<AbstractEditAction> classify(List<BasicEdit> editList) {
		ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
		ArrayList<BasicEdit> newEditList = new ArrayList<BasicEdit>();
		
		for (int i = 0; i < editList.size(); i++)
		{
			BasicEdit b = editList.get(i);
			if (this.isAction(b))
			{
				if (b instanceof Insertion)
				{
					Insertion ie = (Insertion) b;
					WikiToken[] content = ie.getContent();
					int catStartPos = -1;
					
					for (int j = 0; j < content.length; j++)
					{
						if (content[j].kind == MediawikiScannerConstants.INT_LINK_PREFIX && 
							content[j].image.contains("Category:"))
						{
							catStartPos = j - 1;
							continue;
						}
						
						if (catStartPos >= 0 && 
							content[j].kind == MediawikiScannerConstants.INT_LINK_END)
						{
							WikiToken[] beforeContent = Arrays.copyOfRange(content, 0, catStartPos);
							WikiToken[] actionContent = Arrays.copyOfRange(content, catStartPos, j+1);
							WikiToken[] remainder = Arrays.copyOfRange(content, j+1, content.length);
							
							if (beforeContent.length > 0)
								editList.add(new Insertion(ie.getPos(), beforeContent));
							
							if (remainder.length > 0)
								editList.add(new Insertion(ie.getPos()+j+1, remainder));
							
							ie.setContent(actionContent);
							
							break;
						}
					}
					ret.add(new Categorize(ie));
				}
				else if (b instanceof Replacement)
				{
					ret.add(new Categorize(b));
				}
			}
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
		return 1;
	}

}
