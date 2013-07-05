package mo.umac.wikianalysis.categorizer;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import mo.umac.wikianalysis.lexer.*;
import mo.umac.wikianalysis.diff.token.BasicEdit;
import mo.umac.wikianalysis.diff.token.Insertion;

public class Interwiki extends AbstractEditAction {

	private Set<String> langCode;
	
	{
		weight = 0.5;
	}
	
	public Interwiki() {
		langCode = new HashSet<String>();
		
		langCode.add("aa:");
		langCode.add("ab:");
		langCode.add("af:");
		langCode.add("ak:");
		langCode.add("als:");
		langCode.add("am:");
		langCode.add("an:");
		langCode.add("ang:");
		langCode.add("ar:");
		langCode.add("arc:");
		langCode.add("arz:");
		langCode.add("as:");
		langCode.add("ast:");
		langCode.add("av:");
		langCode.add("ay:");
		langCode.add("az:");
		langCode.add("ba:");
		langCode.add("bar:");
		langCode.add("bat-smg:");
		langCode.add("bcl:");
		langCode.add("be:");
		langCode.add("be-x-old:");
		langCode.add("bg:");
		langCode.add("bh:");
		langCode.add("bi:");
		langCode.add("bm:");
		langCode.add("bn:");
		langCode.add("bo:");
		langCode.add("bpy:");
		langCode.add("br:");
		langCode.add("bs:");
		langCode.add("bug:");
		langCode.add("bxr:");
		langCode.add("ca:");
		langCode.add("cbk-zam:");
		langCode.add("cdo:");
		langCode.add("ce:");
		langCode.add("ceb:");
		langCode.add("ch:");
		langCode.add("cho:");
		langCode.add("chr:");
		langCode.add("chy:");
		langCode.add("closed-zh-tw:");
		langCode.add("co:");
		langCode.add("cr:");
		langCode.add("crh:");
		langCode.add("cs:");
		langCode.add("csb:");
		langCode.add("cu:");
		langCode.add("cv:");
		langCode.add("cy:");
		langCode.add("cz:");
		langCode.add("da:");
		langCode.add("de:");
		langCode.add("diq:");
		langCode.add("dk:");
		langCode.add("dsb:");
		langCode.add("dv:");
		langCode.add("dz:");
		langCode.add("ee:");
		langCode.add("el:");
		langCode.add("eml:");
		langCode.add("en:");
		langCode.add("eo:");
		langCode.add("epo:");
		langCode.add("es:");
		langCode.add("et:");
		langCode.add("eu:");
		langCode.add("ext:");
		langCode.add("fa:");
		langCode.add("ff:");
		langCode.add("fi:");
		langCode.add("fiu-vro:");
		langCode.add("fj:");
		langCode.add("fo:");
		langCode.add("fr:");
		langCode.add("frp:");
		langCode.add("fur:");
		langCode.add("fy:");
		langCode.add("ga:");
		langCode.add("gan:");
		langCode.add("gd:");
		langCode.add("gl:");
		langCode.add("glk:");
		langCode.add("gn:");
		langCode.add("got:");
		langCode.add("gu:");
		langCode.add("gv:");
		langCode.add("ha:");
		langCode.add("hak:");
		langCode.add("haw:");
		langCode.add("he:");
		langCode.add("hi:");
		langCode.add("hif:");
		langCode.add("ho:");
		langCode.add("hr:");
		langCode.add("hsb:");
		langCode.add("ht:");
		langCode.add("hu:");
		langCode.add("hy:");
		langCode.add("hz:");
		langCode.add("ia:");
		langCode.add("id:");
		langCode.add("ie:");
		langCode.add("ig:");
		langCode.add("ii:");
		langCode.add("ik:");
		langCode.add("ilo:");
		langCode.add("io:");
		langCode.add("is:");
		langCode.add("it:");
		langCode.add("iu:");
		langCode.add("ja:");
		langCode.add("jbo:");
		langCode.add("jp:");
		langCode.add("jv:");
		langCode.add("ka:");
		langCode.add("kaa:");
		langCode.add("kab:");
		langCode.add("kg:");
		langCode.add("ki:");
		langCode.add("kj:");
		langCode.add("kk:");
		langCode.add("kl:");
		langCode.add("km:");
		langCode.add("kn:");
		langCode.add("ko:");
		langCode.add("kr:");
		langCode.add("ks:");
		langCode.add("ksh:");
		langCode.add("ku:");
		langCode.add("kv:");
		langCode.add("kw:");
		langCode.add("ky:");
		langCode.add("la:");
		langCode.add("lad:");
		langCode.add("lb:");
		langCode.add("lbe:");
		langCode.add("lg:");
		langCode.add("li:");
		langCode.add("lij:");
		langCode.add("lmo:");
		langCode.add("ln:");
		langCode.add("lo:");
		langCode.add("lt:");
		langCode.add("lv:");
		langCode.add("map-bms:");
		langCode.add("mdf:");
		langCode.add("mg:");
		langCode.add("mh:");
		langCode.add("mi:");
		langCode.add("minnan:");
		langCode.add("mk:");
		langCode.add("ml:");
		langCode.add("mn:");
		langCode.add("mo:");
		langCode.add("mr:");
		langCode.add("ms:");
		langCode.add("mt:");
		langCode.add("mus:");
		langCode.add("my:");
		langCode.add("myv:");
		langCode.add("mzn:");
		langCode.add("na:");
		langCode.add("nah:");
		langCode.add("nan:");
		langCode.add("nap:");
		langCode.add("nb:");
		langCode.add("nds:");
		langCode.add("nds-nl:");
		langCode.add("ne:");
		langCode.add("new:");
		langCode.add("ng:");
		langCode.add("nl:");
		langCode.add("nn:");
		langCode.add("no:");
		langCode.add("nomcom:");
		langCode.add("nov:");
		langCode.add("nrm:");
		langCode.add("nv:");
		langCode.add("ny:");
		langCode.add("oc:");
		langCode.add("om:");
		langCode.add("or:");
		langCode.add("os:");
		langCode.add("pa:");
		langCode.add("pag:");
		langCode.add("pam:");
		langCode.add("pap:");
		langCode.add("pdc:");
		langCode.add("pi:");
		langCode.add("pih:");
		langCode.add("pl:");
		langCode.add("pms:");
		langCode.add("pnt:");
		langCode.add("ps:");
		langCode.add("pt:");
		langCode.add("qu:");
		langCode.add("rm:");
		langCode.add("rmy:");
		langCode.add("rn:");
		langCode.add("ro:");
		langCode.add("roa-rup:");
		langCode.add("roa-tara:");
		langCode.add("ru:");
		langCode.add("rw:");
		langCode.add("sa:");
		langCode.add("sah:");
		langCode.add("sc:");
		langCode.add("scn:");
		langCode.add("sco:");
		langCode.add("sd:");
		langCode.add("se:");
		langCode.add("sg:");
		langCode.add("sh:");
		langCode.add("si:");
		langCode.add("simple:");
		langCode.add("sk:");
		langCode.add("sl:");
		langCode.add("sm:");
		langCode.add("sn:");
		langCode.add("so:");
		langCode.add("sq:");
		langCode.add("sr:");
		langCode.add("srn:");
		langCode.add("ss:");
		langCode.add("st:");
		langCode.add("stq:");
		langCode.add("su:");
		langCode.add("sv:");
		langCode.add("sw:");
		langCode.add("szl:");
		langCode.add("ta:");
		langCode.add("te:");
		langCode.add("tet:");
		langCode.add("tg:");
		langCode.add("th:");
		langCode.add("ti:");
		langCode.add("tk:");
		langCode.add("tl:");
		langCode.add("tn:");
		langCode.add("to:");
		langCode.add("tokipona:");
		langCode.add("tp:");
		langCode.add("tpi:");
		langCode.add("tr:");
		langCode.add("ts:");
		langCode.add("tt:");
		langCode.add("tum:");
		langCode.add("tw:");
		langCode.add("ty:");
		langCode.add("udm:");
		langCode.add("ug:");
		langCode.add("uk:");
		langCode.add("ur:");
		langCode.add("uz:");
		langCode.add("ve:");
		langCode.add("vec:");
		langCode.add("vi:");
		langCode.add("vls:");
		langCode.add("vo:");
		langCode.add("wa:");
		langCode.add("war:");
		langCode.add("wo:");
		langCode.add("wuu:");
		langCode.add("xal:");
		langCode.add("xh:");
		langCode.add("yi:");
		langCode.add("yo:");
		langCode.add("za:");
		langCode.add("zea:");
		langCode.add("zh:");
		langCode.add("zh-cfr:");
		langCode.add("zh-classical:");
		langCode.add("zh-min-nan:");
		langCode.add("zh-yue:");
		langCode.add("zu:");
		langCode.add("zh-cn:");
		langCode.add("zh-tw:");
		langCode.add("mhr:");
		langCode.add("pnb:");
		langCode.add("ckb:");
		
		this.be = null;
	}
	
	public Interwiki(BasicEdit b) {
		this.be = new BasicEdit[1];
		this.be[0] = b;
	}
	
	public boolean isAction(BasicEdit edit) {
		
		if (!(edit instanceof Insertion))
			return false;
		else
		{
			Insertion insEdit = (Insertion) edit;
			WikiToken[] content = insEdit.getContent();
			
			for (int i = 0; i < content.length; i++)
			{
				if (content[i].kind == MediawikiScannerConstants.INT_LINK_PREFIX &&
					langCode.contains(content[i].image.toLowerCase()))
					return true;
			}
		}
		
		return false;
	}

	@Override
	public List<AbstractEditAction> classify(List<BasicEdit> editList) {
		ArrayList<AbstractEditAction> ret = new ArrayList<AbstractEditAction>();
		ArrayList<BasicEdit> newEditList = new ArrayList<BasicEdit>();
		
		for (int i = 0; i < editList.size(); i++)
		{
			BasicEdit b = editList.get(i);
			if (this.isAction(b))
			{
				if (b instanceof Insertion)
				{
					Insertion ie = (Insertion) b;
					WikiToken[] content = ie.getContent();
					int iwStartPos = -1;

					for (int j = 0; j < content.length; j++)
					{
						if (content[j].kind == MediawikiScannerConstants.INT_LINK_PREFIX && 
							langCode.contains(content[j].image.toLowerCase()))
						{
							iwStartPos = j - 1;
							continue;
						}
						
						if (iwStartPos >= 0 && 
							content[j].kind == MediawikiScannerConstants.INT_LINK_END)
						{
							WikiToken[] beforeContent = Arrays.copyOfRange(content, 0, iwStartPos);
							WikiToken[] actionContent = Arrays.copyOfRange(content, iwStartPos, j+1);
							WikiToken[] remainder = Arrays.copyOfRange(content, j+1, content.length);
							
							if (beforeContent.length > 0)
								editList.add(new Insertion(ie.getPos(), beforeContent));
							
							if (remainder.length > 0)
								editList.add(new Insertion(ie.getPos()+j+1, remainder));
							
							ie.setContent(actionContent);
							
							break;
						}
					}
					ret.add(new Interwiki(ie));
				}
			}
			else {
				newEditList.add(b);
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

