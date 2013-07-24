package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Replacement;
import mo.umac.wikianalysis.lexer.WikiToken;
import mo.umac.wikianalysis.util.LevenshteinDistance;

public class TypoCorrection extends AbstractEditAction {

	{
		weight = 0.5;
	}
	
	public TypoCorrection() {
		this.be = null;
	}
	
	public TypoCorrection(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}
	
	public boolean isAction(BasicEdit edit) {
		
		if (!(edit instanceof Replacement))
		{
			return false;
		}
		else
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] insContent = replEdit.getInsertedContent();
			WikiToken[] delContent = replEdit.getDeletedContent();
			
			if (insContent.length > 100 ||
				delContent.length > 100 ||
				insContent.length * 10 < delContent.length ||
				delContent.length * 10 < insContent.length)
				return false;
			
			StringBuffer insString = new StringBuffer();
			StringBuffer delString = new StringBuffer();
			
			for (int i = 0; i < insContent.length; i++)
				insString.append(insContent[i].displayString);
			
			for (int i = 0; i < delContent.length; i++)
				delString.append(delContent[i].displayString);
			
			if (LevenshteinDistance.compute(insString.toString(), delString.toString()) < 
					Math.max((insString.length() + delString.length()) * 0.2, 3))
				return true;
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
				ret.add(new TypoCorrection(b));
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
