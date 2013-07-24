package mo.umac.wikianalysis.categorizer;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;

import mo.umac.wikianalysis.lexer.*;
import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Insertion;
import mo.umac.wikianalysis.diff.token.Match;
import mo.umac.wikianalysis.diff.token.Replacement;

public class Wikify extends AbstractEditAction {

	{
		weight = 0.5;
	}
	
	public Wikify() {
		this.be = null;
	}
	
	public Wikify(BasicEdit b1, BasicEdit b2) {
		this.be = new BasicEdit[2];
		this.be[0] = b1;
		this.be[1] = b2;
	}
	
	public Wikify(BasicEdit[] be) {
		this.be = be;
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
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_BEGIN ||
					content[i].kind == MediawikiScannerConstants.INT_LINK_END ||
					content[i].kind == MediawikiScannerConstants.BOLD ||
					content[i].kind == MediawikiScannerConstants.ITALIC)
					return true;
			}
		}
		else if (edit instanceof Replacement)
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] content = replEdit.getInsertedContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_BEGIN ||
					content[i].kind == MediawikiScannerConstants.INT_LINK_END ||
					content[i].kind == MediawikiScannerConstants.BOLD ||
					content[i].kind == MediawikiScannerConstants.ITALIC)
					return true;
			}
		}
		
		return false;
	}
	
	public boolean isMarkupOpen(BasicEdit edit) {
		boolean flag = false;
		
		if (!(edit instanceof Insertion) && !(edit instanceof Replacement))
			return false;
		else if (edit instanceof Insertion)
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] content = insEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.BOLD ||
					content[i].kind == MediawikiScannerConstants.ITALIC)
					flag = !flag;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_BEGIN)
					flag = true;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_END)
					flag = false;
			}
		}
		else if (edit instanceof Replacement)
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] content = replEdit.getInsertedContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.BOLD ||
					content[i].kind == MediawikiScannerConstants.ITALIC)
					flag = !flag;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_BEGIN)
					flag = true;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_END)
					flag = false;
			}
		}
		
		return flag;
	}
	
	public boolean isMarkupClose(BasicEdit edit) {
		boolean flag = false;
		
		if (!(edit instanceof Insertion) && !(edit instanceof Replacement))
			return false;
		else if (edit instanceof Insertion)
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] content = insEdit.getContent();
			
			for (int i = content.length - 1; i >= 0; i--)
			{
				if (content[i].kind == MediawikiScannerConstants.BOLD ||
					content[i].kind == MediawikiScannerConstants.ITALIC)
					flag = !flag;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_BEGIN)
					flag = false;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_END)
					flag = true;
			}
		}
		else if (edit instanceof Replacement)
		{
			Replacement replEdit = (Replacement) edit;
			WikiToken[] content = replEdit.getInsertedContent();
			
			for (int i = content.length - 1; i >= 0; i--)
			{
				if (content[i].kind == MediawikiScannerConstants.BOLD ||
					content[i].kind == MediawikiScannerConstants.ITALIC)
					flag = !flag;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_BEGIN)
					flag = false;
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_END)
					flag = true;
			}
		}
		
		return flag;
	}
	
	@Override
	public List<AbstractEditAction> classify(List<BasicEdit> editList) {
		ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
		ArrayList<BasicEdit> newEditList = new ArrayList<BasicEdit>();
		
		if (editList.size() < 2)
			return ret;
		
		Collections.sort(editList, new Comparator<BasicEdit>() {
			@Override
			public int compare(BasicEdit arg0, BasicEdit arg1) {
				return arg0.getNewPos() - arg1.getNewPos();
			}
		});
		
		for (int i = 0; i < editList.size() - 1; i++)
		{
			BasicEdit b1 = editList.get(i);
			BasicEdit b2 = editList.get(i+1);
			
			for(int inc = 2; b2 instanceof Match && (i + inc) < (editList.size() - 1); inc++)
				b2 = editList.get(i + inc);
			
			if (this.isMarkupOpen(b1) && this.isMarkupClose(b2))
			{
				// Check if there is a markup close after b1 or a markup open after b2

				newEditList.remove(b1);
				if (b2 instanceof Insertion)
				{
					Insertion ie = (Insertion) b2;
					WikiToken[] content = ie.getContent();
					
					for (int j = 0; j < content.length; j++)
					{
						if (content[j].kind == MediawikiScannerConstants.INT_LINK_END ||
							content[j].kind == MediawikiScannerConstants.BOLD ||
							content[j].kind == MediawikiScannerConstants.ITALIC)
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
					ret.add(new Wikify(b1, ie));
					i++;
				}
				else
				{
					ret.add(new Wikify(b1, b2));
					i++;
				}
			}
			else {
				if (!newEditList.contains(b1)) newEditList.add(b1);
				if (!newEditList.contains(b2)) newEditList.add(b2);
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
