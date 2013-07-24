package mo.umac.wikianalysis.util.array;

public class Sparse2DCell {

	private double value;
	
	private int rowIndex;
	private int colIndex;
	private Sparse2DCell nextRow;
	private Sparse2DCell nextCol;

	public Sparse2DCell(double val, int row, int col) {
		this.value = val;
		this.rowIndex = row;
		this.colIndex = col;
		this.nextRow = null;
		this.nextCol = null;
	}

	public double getValue() {
		return this.value;
	}

	public void setValue(double val) {
		this.value = val;
	}
	
	public int getColumn() {
		return this.colIndex;
	}

	public int getRow() {
		return this.rowIndex;
	}

	public Sparse2DCell nextRow() {
		return this.nextRow;
	}

	public void setNextRow(Sparse2DCell cell) {
		this.nextRow = cell;
	}

	public Sparse2DCell nextColumn() {
		return this.nextCol;
	}

	public void setNextColumn(Sparse2DCell cell) {
		this.nextCol = cell;
	}
}
