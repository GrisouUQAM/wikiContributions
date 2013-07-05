package mo.umac.wikianalysis.diff.token;

import java.util.Comparator;

public class DiffOrder implements Comparator<BasicEdit> {

	@Override
	public int compare(BasicEdit o1, BasicEdit o2) {
		
		if (o1 instanceof Insertion)
		{
			if (o2 instanceof Insertion)
			{
				Insertion i1 = (Insertion) o1;
				Insertion i2 = (Insertion) o2;
				
				return (i1.getPos() - i2.getPos());
			}
			else
				return -1;
		}
		else if (o1 instanceof Deletion)
		{
			if (o2 instanceof Insertion)
				return 1;
			else if (o2 instanceof Deletion)
			{
				Deletion d1 = (Deletion) o1;
				Deletion d2 = (Deletion) o2;
				
				return (d1.getPos() - d2.getPos());
			}
			else
				return -1;
		}
		else if (o1 instanceof Replacement)
		{
			if (o2 instanceof Insertion || o2 instanceof Deletion)
				return 1;
			else if (o2 instanceof Replacement)
			{
				Replacement r1 = (Replacement) o1;
				Replacement r2 = (Replacement) o2;
				
				return (r1.getOldPos() - r2.getOldPos());
			}
			else
				return -1;
		}
		else if (o1 instanceof Match)
		{
			if (o2 instanceof Insertion ||
				o2 instanceof Deletion ||
				o2 instanceof Replacement)
				return 1;
			else if (o2 instanceof Match)
			{
				Match m1 = (Match) o1;
				Match m2 = (Match) o2;
				
				return (m1.getOldPos() - m2.getOldPos());
			}
			else
				return -1;
		}
		else if (o1 instanceof Movement)
		{
			if (!(o2 instanceof Movement))
				return 1;
		}
		
		return 0;
	}

}
