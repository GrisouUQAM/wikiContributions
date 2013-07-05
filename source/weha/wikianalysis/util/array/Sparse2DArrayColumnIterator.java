package mo.umac.wikianalysis.util.array;

import java.util.Iterator;

public class Sparse2DArrayColumnIterator implements Iterator<Sparse2DCell> {

	private Sparse2DCell cell;
	
	public Sparse2DArrayColumnIterator(Sparse2DCell sparse2dCell) {
		this.cell = sparse2dCell;
	}

	@Override
	public boolean hasNext() {
		return (this.cell != null);
	}

	@Override
	public Sparse2DCell next() {
		Sparse2DCell retCell = this.cell;
		this.cell = this.cell.nextColumn();
		return retCell;
	}

	@Override
	public void remove() {
		throw new UnsupportedOperationException();
	}

}
