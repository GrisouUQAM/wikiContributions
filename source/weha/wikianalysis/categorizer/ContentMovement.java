package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Movement;

public class ContentMovement extends AbstractEditAction {

	{
		weight = 0.25;
	}
	
	public ContentMovement() {
		this.be = null;
	}
	
	public ContentMovement(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}

	public boolean isAction(BasicEdit edit) {
		
		if (!(edit instanceof Movement))
			return false;
		else
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
				ret.add(new ContentMovement(b));
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

		return ((Movement) be[0]).getLength();
	}
	
}
