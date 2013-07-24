package mo.umac.wikianalysis.test;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;

public class WikiAPITextFetcher {

	public static String fetch(int revID) {
		String retVal = new String();
		
		try {
			URL url = new URL("http://en.wikipedia.org/w/index.php?action=raw&title=Canada&oldid=" + Integer.toString(revID));

			URLConnection conn = url.openConnection();
			conn.setDoOutput(true);
			InputStream in = null;
			in = url.openStream();
			retVal = pipe(in, "utf-8");
			
		} catch (Exception e) {
			e.printStackTrace();
		}
		
		return retVal;
	}

	private static String pipe(InputStream in, String charset) throws IOException {

		StringBuffer s = new StringBuffer();
		if (charset == null || "".equals(charset)) {
			charset = "utf-8";
		}

		String rLine = null;
		BufferedReader bReader = new BufferedReader(new InputStreamReader(in, charset));
		
		while ((rLine = bReader.readLine()) != null) {
			String tmp_rLine = rLine;
			s.append(tmp_rLine + "\n");
			tmp_rLine = null;
		}
		in.close();

		return s.toString().substring(0, s.length() - 1);
	}
	
	public static void main(String[] args) {
		System.out.println(WikiAPITextFetcher.fetch(349145579));
	}
}
