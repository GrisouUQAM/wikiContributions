package mo.umac.wikianalysis.test;

import java.io.BufferedReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.ListIterator;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class WikipediaRevisionFetcher {
	
	public static void main(String[] args) {
		String retVal = new String();
		int rvStartId = 0;
		try {
			URL url = new URL("http://en.wikipedia.org/w/api.php?action=query&prop=revisions&titles=Canada&rvprop=ids|timestamp&rvdir=newer&rvlimit=500&format=json");
	
			URLConnection conn = url.openConnection();
			conn.setDoOutput(true);
			InputStream in = null;
			in = url.openStream();
			retVal = pipe(in, "utf-8");
			
		} catch (Exception e) {
			e.printStackTrace();
		}
		
		ArrayList<Integer> revIDList = new ArrayList<Integer>();
		try {
			JSONObject result = new JSONObject(retVal);
			JSONArray revArray = result.getJSONObject("query").getJSONObject("pages").getJSONObject("5042916").getJSONArray("revisions");
			
			for (int i = 0; i < revArray.length(); i++)
			{
				revIDList.add(revArray.getJSONObject(i).getInt("revid"));
			}
			
			if (result.has("query-continue"))
				rvStartId = result.getJSONObject("query-continue").getJSONObject("revisions").getInt("rvstartid");
			else
				rvStartId = -1;
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		System.out.println(revIDList.size());
		
		while (rvStartId > 0)
		{
			try {
				URL url = new URL("http://en.wikipedia.org/w/api.php?action=query&prop=revisions&titles=Canada&rvprop=ids|timestamp&rvstartid=" + rvStartId + "&rvdir=newer&rvlimit=500&format=json");
		
				URLConnection conn = url.openConnection();
				conn.setDoOutput(true);
				InputStream in = null;
				in = url.openStream();
				retVal = pipe(in, "utf-8");
				
			} catch (Exception e) {
				e.printStackTrace();
			}
			
			try {
				JSONObject result = new JSONObject(retVal);
				JSONArray revArray = result.getJSONObject("query").getJSONObject("pages").getJSONObject("5042916").getJSONArray("revisions");
				
				for (int i = 0; i < revArray.length(); i++)
				{
					revIDList.add(revArray.getJSONObject(i).getInt("revid"));
				}
	
				if (result.has("query-continue"))
					rvStartId = result.getJSONObject("query-continue").getJSONObject("revisions").getInt("rvstartid");
				else
					rvStartId = -1;
			} catch (JSONException e) {
				e.printStackTrace();
			}
			System.out.println(revIDList.size());
		}
		
		ListIterator<Integer> iter = revIDList.listIterator();
		try {
			FileWriter writer = new FileWriter("/home/peter/Desktop/Canada/revisions1.txt");
			while (iter.hasNext())
			{
				int revID = iter.next().intValue();
				writer.write(Integer.toString(revID) + "\n");
			}
			writer.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		System.out.println(revIDList.size());
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
}
