package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Match;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class Uncategorized extends AbstractEditAction {

	{
		weight = 0.25;
	}
	
	public Uncategorized() {
		this.be = null;
	}
	
	public Uncategorized(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}

	@Override
	public boolean isAction(BasicEdit edit) {

		WikiToken[] content = edit.getContent();
		if (content != null && 
			content.length == 1 && 
			content[0].kind == MediawikiScannerConstants.NL)
			return false;
		
		if (!(edit instanceof Match))
			return true;
		
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
				ret.add(new Uncategorized(b));
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
