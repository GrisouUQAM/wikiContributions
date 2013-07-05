package mo.umac.wikianalysis.util.array;

public class Sparse2DArray {

	private int rowCount;
	private int colCount;
	
	private Sparse2DCell[] rowIndexArray;
	private Sparse2DCell[] colIndexArray;
	
	public Sparse2DArray(int row, int col) {
		
		this.rowCount = row;
		this.colCount = col;
		
		this.rowIndexArray = new Sparse2DCell[this.rowCount];
		this.colIndexArray = new Sparse2DCell[this.colCount];
		
	}
	
	public double get(int row, int col) {
		
		if (row >= this.rowCount || col >= this.colCount)
			throw new IndexOutOfBoundsException();
		
		if (this.rowCount < this.colCount)
		{
			Sparse2DArrayRowIterator riter = new Sparse2DArrayRowIterator(rowIndexArray[row]);
			
			while (riter.hasNext())
			{
				Sparse2DCell cell = riter.next();
				if (cell.getColumn() == col)
					return cell.getValue();
			}
		}
		else
		{
			Sparse2DArrayColumnIterator citer = new Sparse2DArrayColumnIterator(colIndexArray[col]);
			
			while (citer.hasNext())
			{
				Sparse2DCell cell = citer.next();
				if (cell.getRow() == row)
					return cell.getValue();
			}
		}
		
		return 0.0;
	}
	
	private Sparse2DCell getCell(int row, int col) {
		
		if (row >= this.rowCount || col >= this.colCount)
			throw new IndexOutOfBoundsException();
		
		if (this.rowCount < this.colCount)
		{
			Sparse2DArrayRowIterator riter = new Sparse2DArrayRowIterator(rowIndexArray[row]);
			
			while (riter.hasNext())
			{
				Sparse2DCell cell = riter.next();
				if (cell.getColumn() == col)
					return cell;
			}
		}
		else
		{
			Sparse2DArrayColumnIterator citer = new Sparse2DArrayColumnIterator(colIndexArray[col]);
			
			while (citer.hasNext())
			{
				Sparse2DCell cell = citer.next();
				if (cell.getRow() == row)
					return cell;
			}
		}
		
		return null;
	}
	
	public Sparse2DArrayRowIterator getRowIterator(int row) {
		return new Sparse2DArrayRowIterator(this.rowIndexArray[row]);
	}

	
	public Sparse2DArrayColumnIterator getColumnIterator(int col) {
		return new Sparse2DArrayColumnIterator(this.colIndexArray[col]);
	}
	
	public void set(double val, int row, int col) {
		
		if (row >= this.rowCount || col >= this.colCount)
			throw new IndexOutOfBoundsException();
		
		Sparse2DCell cell = this.getCell(row, col);
		if (cell != null)
		{
			cell.setValue(val);
			return;
		}
		
		cell = new Sparse2DCell(val, row, col);
		
		Sparse2DCell rowPrev = this.rowIndexArray[row];
		Sparse2DCell rowNext;
		
		if (rowPrev == null)
			this.rowIndexArray[row] = cell;
		else if (rowPrev.getColumn() > col)
		{
			rowNext = this.rowIndexArray[row];
			this.rowIndexArray[row] = cell;
			cell.setNextRow(rowNext);
		}
		else
		{
			Sparse2DArrayRowIterator riter = new Sparse2DArrayRowIterator(rowPrev);
			
			while (riter.hasNext())
			{
				Sparse2DCell temp = riter.next();
				if (cell.getColumn() < col)
					rowPrev = temp;
				else
					break;
			}
			rowNext = rowPrev.nextRow();
			
			rowPrev.setNextRow(cell);
			cell.setNextRow(rowNext);
		}
		
		Sparse2DCell colPrev = this.colIndexArray[col];
		Sparse2DCell colNext;
		
		if (colPrev == null)
			this.colIndexArray[col] = cell;
		else if (colPrev.getRow() > row)
		{
			colNext = this.colIndexArray[col];
			this.colIndexArray[col] = cell;
			cell.setNextColumn(colNext);
		}
		else
		{
			Sparse2DArrayColumnIterator citer = new Sparse2DArrayColumnIterator(colPrev);
			
			while (citer.hasNext())
			{
				Sparse2DCell temp = citer.next();
				if (cell.getRow() < row)
					colPrev = temp;
				else
					break;
			}
			colNext = colPrev.nextColumn();
			
			colPrev.setNextColumn(cell);
			cell.setNextColumn(colNext);
		}
	}
	
	public static void main(String[] args) {
		Sparse2DArray arr = new Sparse2DArray(10, 10);
		
		arr.set(1.0, 3, 2);
		arr.set(2.0, 5, 4);
		arr.set(3.0, 3, 4);
		arr.set(4.0, 5, 2);
		arr.set(5.0, 4, 3);
		arr.set(6.0, 4, 4);
		
		Sparse2DArrayColumnIterator riter = arr.getColumnIterator(4);
		while (riter.hasNext()) {
			Sparse2DCell temp = riter.next();
			System.out.printf("(%d, %d) = %f\n", temp.getRow(), temp.getColumn(), temp.getValue());
		}
	}
}
