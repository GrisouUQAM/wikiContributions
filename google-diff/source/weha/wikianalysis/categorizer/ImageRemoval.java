package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Deletion;
import mo.umac.wikianalysis.diff.token.Insertion;
import mo.umac.wikianalysis.diff.token.Replacement;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class ImageRemoval extends AbstractEditAction {

	{
		weight = 1.0;
	}
	
	public ImageRemoval() {
		this.be = null;
	}
	
	public ImageRemoval(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}

	public boolean isAction(BasicEdit edit) {
		if (!(edit instanceof Deletion) && !(edit instanceof Replacement))
			return false;
		else if (edit instanceof Deletion)
		{
			Deletion insEdit = (Deletion) edit;
			WikiToken[] content = insEdit.getContent();

			if (content[0].kind == MediawikiScannerConstants.IMAGE_BEGIN)
				return true;
		}
		else
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] content = replEdit.getDeletedContent();

			if (content[0].kind == MediawikiScannerConstants.IMAGE_BEGIN)
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
			{
				if (b instanceof Deletion)
				{
					Deletion ie = (Deletion) b;
					WikiToken[] content = ie.getContent();
					
					for (int j = 0; j < content.length; j++)
					{
						if (content[j].kind == MediawikiScannerConstants.IMAGE_END)
						{
							WikiToken[] actionContent = Arrays.copyOfRange(content, 0, j+1);
							WikiToken[] remainder = Arrays.copyOfRange(content, j+1, content.length);
							if (remainder.length > 0)
							{
								ie.setContent(actionContent);
								editList.add(new Insertion(ie.getPos()+j+1, remainder));
							}
							break;
						}
					}
					ret.add(new ImageRemoval(ie));
				}
				else
				{
					ret.add(new ImageRemoval(b));
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
