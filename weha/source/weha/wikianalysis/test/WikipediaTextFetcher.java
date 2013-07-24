package mo.umac.wikianalysis.test;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.ListIterator;

public class WikipediaTextFetcher {

	public static String fetch(int revId) {
		String retVal = new String();
		
		File cachedTextFile = new File("/home/peter/Desktop/Canada/" + revId + ".txt");
		
		if (cachedTextFile.exists()) {
			FileInputStream in;
			try {
				in = new FileInputStream(cachedTextFile);
				retVal = pipe(in, "utf-8");
			} catch (FileNotFoundException e) {
				e.printStackTrace();
				System.exit(0);
			} catch (IOException e) {
				e.printStackTrace();
				System.exit(0);
			}
		}
		else {
			try {
				URL url = new URL("http://en.wikipedia.org/w/index.php?action=raw&oldid=" + revId);
		
				URLConnection conn = url.openConnection();
				conn.setDoOutput(true);
				InputStream in = null;
				in = url.openStream();
				retVal = pipe(in, "utf-8");
				
				File outFile = new File("/home/peter/Desktop/Canada/" + revId + ".txt");
				if (outFile.createNewFile())
				{
					FileWriter writer = new FileWriter(outFile);
					writer.write(retVal);
					writer.close();
				}
				
			} catch (Exception e) {
				e.printStackTrace();
				System.exit(0);
			}
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

		return s.toString().substring(0, Math.max(s.length() - 1, 0));
	}
	
	public static void main(String[] args) {
		ArrayList<Integer> revList = new ArrayList<Integer>();
		BufferedReader reader;
		try {
			reader = new BufferedReader(new FileReader("/home/peter/Desktop/Canada/revisions.txt"));
			
			String line = null;
			while ((line = reader.readLine()) != null) {
				String tmp_line = line;
				if (!"".equals(tmp_line))
				revList.add(Integer.parseInt(tmp_line));
				tmp_line = null;
			}
			reader.close();
			
		} catch (FileNotFoundException e) {
			e.printStackTrace();
			System.exit(0);
		} catch (NumberFormatException e) {
			e.printStackTrace();
			System.exit(0);
		} catch (IOException e) {
			e.printStackTrace();
			System.exit(0);
		}

		ListIterator<Integer> iter = revList.listIterator();
		/*
		while (iter.hasNext())
		{
			int revId = iter.next();
			System.out.println("Revision " + revId);
			WikipediaTextFetcher.fetch(revId);
		}*/
	}
}
