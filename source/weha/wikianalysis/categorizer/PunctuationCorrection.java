package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.*;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class PunctuationCorrection extends AbstractEditAction {

	{
		weight = 0.5;
	}
	
	public PunctuationCorrection() {
		this.be = null;
	}
	
	public PunctuationCorrection(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}

	public boolean isAction(BasicEdit edit) {
		
		if ((edit instanceof Match) || (edit instanceof Movement))
		{
			return false;
		}
		else if (edit instanceof Replacement)
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] insContent = replEdit.getInsertedContent();
			WikiToken[] delContent = replEdit.getDeletedContent();
			
			for (int i = 0; i < insContent.length; i++)
				if (insContent[i].kind != MediawikiScannerConstants.SYMBOL)
					return false;

			for (int i = 0; i < delContent.length; i++)
				if (delContent[i].kind != MediawikiScannerConstants.SYMBOL)
					return false;
		}
		else if (edit instanceof Insertion)
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] insContent = insEdit.getContent();
			
			for (int i = 0; i < insContent.length; i++)
				if (insContent[i].kind != MediawikiScannerConstants.SYMBOL)
					return false;
		}
		else if (edit instanceof Deletion)
		{
			Deletion delEdit = (Deletion) edit;
			WikiToken[] delContent = delEdit.getContent();

			for (int i = 0; i < delContent.length; i++)
				if (delContent[i].kind != MediawikiScannerConstants.SYMBOL)
					return false;
		}
		
		return true;
	}
	
	@Override
	public List<AbstractEditAction> classify(List<BasicEdit> editList) {
		ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
		ArrayList<BasicEdit> newEditList = new ArrayList<BasicEdit>();
		
		for (int i = 0; i < editList.size(); i++)
		{
			BasicEdit b = editList.get(i);
			if (this.isAction(b))
				ret.add(new PunctuationCorrection(b));
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
