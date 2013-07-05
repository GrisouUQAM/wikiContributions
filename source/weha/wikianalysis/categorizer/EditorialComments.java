package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Insertion;
import mo.umac.wikianalysis.lexer.MediawikiScannerConstants;
import mo.umac.wikianalysis.lexer.WikiToken;

public class EditorialComments extends AbstractEditAction {

	private Set<String> editorialTemplates;
	
	{
		weight = 0.5;
	}
	
	public EditorialComments() {
		editorialTemplates = new HashSet<String>();
		editorialTemplates.add("{{fact");
		editorialTemplates.add("{{unreferenced");
		editorialTemplates.add("{{stub");
		this.be = null;
	}
	
	public EditorialComments(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}
	
	public boolean isAction(BasicEdit edit) {
		
		if (!(edit instanceof Insertion))
			return false;
		else
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] content = insEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.TEMPLATE_BEGIN &&
					editorialTemplates.contains(content[i].image.toLowerCase()))
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
					
					for (int j = 0; j < content.length; j++)
					{
						if (content[j].kind == MediawikiScannerConstants.TEMPLATE_END)
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
					ret.add(new EditorialComments(ie));
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
