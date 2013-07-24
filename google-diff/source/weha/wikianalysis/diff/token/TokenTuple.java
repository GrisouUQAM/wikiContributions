package mo.umac.wikianalysis.diff.token;

import mo.umac.wikianalysis.lexer.*;

public class TokenTuple {
	private final int SIZE = 3;
	private Token tokens[];
	
	public TokenTuple(Token t0, Token t1, Token t2)
	{
		tokens = new Token[SIZE];
		tokens[0] = t0;
		tokens[1] = t1;
		tokens[2] = t2;
	}
	
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		
		for (int i = 0; i < SIZE; i++)
			result = prime * result + tokens[i].image.hashCode();

		for (int i = 0; i < SIZE; i++)
			result = prime * result + tokens[i].kind;
		
		return result;
	}

	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		
		TokenTuple other = (TokenTuple) obj;
		for (int i = 0; i < SIZE; i++) {
			if (!(tokens[i].equals(other.tokens[i])))
				return false;
		}
		
		return true;
	}
}
