package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class ImageAttribute extends AbstractEditAction {

	{
		weight = 0.5;
	}
	
	public ImageAttribute() {
		super();
	}

	public ImageAttribute(BasicEdit b) {
		super();
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}

	@Override
	public List<AbstractEditAction> classify(List<BasicEdit> editList) {
		ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
		ArrayList<BasicEdit> newEditList = new ArrayList<BasicEdit>();
		
		for (int i = 0; i < editList.size(); i++)
		{
			BasicEdit b = editList.get(i);
			if (this.isAction(b))
				ret.add(new ImageAttribute(b));
			else {
				newEditList.add(b);
			}
		}
		
		editList.clear();
		editList.addAll(newEditList);
		
		return ret;
	}

	@Override
	public boolean isAction(BasicEdit edit) {
		WikiToken[] content = edit.getContent();
		
		if (content != null)
		{
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind != MediawikiScannerConstants.IMAGE_ATTR)
					return false;
			}
			
			return true;
		}
		
		return false;
	}

	@Override
	public int lengthCount() {
		return 1;
	}

}
