package mo.umac.wikianalysis.lexer;

public class WikiToken extends Token {

	private static final long serialVersionUID = 7724510731264682048L;

	public String displayString;
	
	/**
	 * No-argument constructor
	 */
	public WikiToken() {
		super();
		this.displayString = new String();
	}

	/**
	 * Constructs a new token for the specified Lind.
	 * @param kind
	 */
	public WikiToken(int kind) {
		super(kind);
		this.displayString = new String();
	}

	/**
	 * Constructs a new token for the specified Image and Kind.
	 * @param kind
	 * @param image
	 */
	public WikiToken(int kind, String image) {
		super(kind, image);
		this.displayString = new String(image);
	}

	public WikiToken(int kind, String image, String displayString) {
		super(kind, image);
		this.displayString = new String(displayString);
	}
	
	public WikiToken(Token token) {
		super(token.kind, token.image);
		this.displayString = new String(token.image);
	}
	
	public WikiToken(Token token, String displayString) {
		super(token.kind, token.image);
		this.displayString = new String(displayString);
	}
	
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((image == null) ? 0 : image.hashCode());
		result = prime * result + kind;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Token other = (Token) obj;
		if (image == null) {
			if (other.image != null)
				return false;
		} else if (!image.equals(other.image))
			return false;
		if (kind != other.kind)
			return false;
		return true;
	}

	@Override
	public String toString() {
		return this.displayString;
	}
	
}
