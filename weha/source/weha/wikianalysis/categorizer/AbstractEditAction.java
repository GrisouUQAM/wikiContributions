package mo.umac.wikianalysis.categorizer;

import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.lexer.WikiToken;

public abstract class AbstractEditAction {
	protected BasicEdit[] be;
	protected static double weight;
	
	public abstract boolean isAction(BasicEdit edit);
	public abstract List<AbstractEditAction> classify(List<BasicEdit> editList);
	public abstract int lengthCount();
	
	public int tokenCount() {
		int retVal = 0;
		
		for (int i = 0; i < be.length; i++) {
			WikiToken[] content = be[i].getContent();
			if (content != null) 
				retVal += content.length;
		}
		return retVal;
	}
	
	public double getWeight() {
		return weight;
	}
	
	public double significanceValue() {
		return (this.getWeight() * this.lengthCount());
	}
	
	public BasicEdit[] getBasicEditList() {
		return be;
	}
	public void setBasicEditList(BasicEdit[] be) {
		this.be = be;
	}
}
