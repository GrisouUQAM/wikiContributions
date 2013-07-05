package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Replacement;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class ContentSubstitution extends AbstractEditAction {

	public ContentSubstitution() {
		this.be = null;
	}
	
	{
		weight = 0.25;
	}
	
	public ContentSubstitution(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}

	public boolean isAction(BasicEdit edit) {
		
		if (!(edit instanceof Replacement))
			return false;
		else
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] insContent = replEdit.getInsertedContent();
			WikiToken[] delContent = replEdit.getDeletedContent();
			
			boolean flag = false;
			
			for (int i = 0; i < insContent.length; i++)
			{
				if (insContent[i].kind == MediawikiScannerConstants.WORD)
				{
					flag = true;
					break;
				}
			}		
			if (!flag) return false;
			
			for (int i = 0; i < delContent.length; i++)
			{
				if (delContent[i].kind == MediawikiScannerConstants.WORD)
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
				ret.add(new ContentSubstitution(b));
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
		Replacement replEdit = (Replacement) be[0];
		return (replEdit.getDeletedLength() + replEdit.getInsertedLength());
	}

}
