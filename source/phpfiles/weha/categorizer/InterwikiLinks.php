<?php
require_once('AbstractEditAction.php');

class InterwikiLinks extends AbstractEditAction {

	private $langCode;

	public function __construct($b = null) {
		if ($b === null)
		{
			$this->be = null;
			$this->langCode = array();

			$this->langCode[] = 'aa';
			$this->langCode[] = 'ab';
			$this->langCode[] = 'af';
			$this->langCode[] = 'ak';
			$this->langCode[] = 'als';
			$this->langCode[] = 'am';
			$this->langCode[] = 'an';
			$this->langCode[] = 'ang';
			$this->langCode[] = 'ar';
			$this->langCode[] = 'arc';
			$this->langCode[] = 'arz';
			$this->langCode[] = 'as';
			$this->langCode[] = 'ast';
			$this->langCode[] = 'av';
			$this->langCode[] = 'ay';
			$this->langCode[] = 'az';
			$this->langCode[] = 'ba';
			$this->langCode[] = 'bar';
			$this->langCode[] = 'bat-smg';
			$this->langCode[] = 'bcl';
			$this->langCode[] = 'be';
			$this->langCode[] = 'be-x-old';
			$this->langCode[] = 'bg';
			$this->langCode[] = 'bh';
			$this->langCode[] = 'bi';
			$this->langCode[] = 'bm';
			$this->langCode[] = 'bn';
			$this->langCode[] = 'bo';
			$this->langCode[] = 'bpy';
			$this->langCode[] = 'br';
			$this->langCode[] = 'bs';
			$this->langCode[] = 'bug';
			$this->langCode[] = 'bxr';
			$this->langCode[] = 'ca';
			$this->langCode[] = 'cbk-zam';
			$this->langCode[] = 'cdo';
			$this->langCode[] = 'ce';
			$this->langCode[] = 'ceb';
			$this->langCode[] = 'ch';
			$this->langCode[] = 'cho';
			$this->langCode[] = 'chr';
			$this->langCode[] = 'chy';
			$this->langCode[] = 'closed-zh-tw';
			$this->langCode[] = 'co';
			$this->langCode[] = 'cr';
			$this->langCode[] = 'crh';
			$this->langCode[] = 'cs';
			$this->langCode[] = 'csb';
			$this->langCode[] = 'cu';
			$this->langCode[] = 'cv';
			$this->langCode[] = 'cy';
			$this->langCode[] = 'cz';
			$this->langCode[] = 'da';
			$this->langCode[] = 'de';
			$this->langCode[] = 'diq';
			$this->langCode[] = 'dk';
			$this->langCode[] = 'dsb';
			$this->langCode[] = 'dv';
			$this->langCode[] = 'dz';
			$this->langCode[] = 'ee';
			$this->langCode[] = 'el';
			$this->langCode[] = 'eml';
			$this->langCode[] = 'en';
			$this->langCode[] = 'eo';
			$this->langCode[] = 'epo';
			$this->langCode[] = 'es';
			$this->langCode[] = 'et';
			$this->langCode[] = 'eu';
			$this->langCode[] = 'ext';
			$this->langCode[] = 'fa';
			$this->langCode[] = 'ff';
			$this->langCode[] = 'fi';
			$this->langCode[] = 'fiu-vro';
			$this->langCode[] = 'fj';
			$this->langCode[] = 'fo';
			$this->langCode[] = 'fr';
			$this->langCode[] = 'frp';
			$this->langCode[] = 'fur';
			$this->langCode[] = 'fy';
			$this->langCode[] = 'ga';
			$this->langCode[] = 'gan';
			$this->langCode[] = 'gd';
			$this->langCode[] = 'gl';
			$this->langCode[] = 'glk';
			$this->langCode[] = 'gn';
			$this->langCode[] = 'got';
			$this->langCode[] = 'gu';
			$this->langCode[] = 'gv';
			$this->langCode[] = 'ha';
			$this->langCode[] = 'hak';
			$this->langCode[] = 'haw';
			$this->langCode[] = 'he';
			$this->langCode[] = 'hi';
			$this->langCode[] = 'hif';
			$this->langCode[] = 'ho';
			$this->langCode[] = 'hr';
			$this->langCode[] = 'hsb';
			$this->langCode[] = 'ht';
			$this->langCode[] = 'hu';
			$this->langCode[] = 'hy';
			$this->langCode[] = 'hz';
			$this->langCode[] = 'ia';
			$this->langCode[] = 'id';
			$this->langCode[] = 'ie';
			$this->langCode[] = 'ig';
			$this->langCode[] = 'ii';
			$this->langCode[] = 'ik';
			$this->langCode[] = 'ilo';
			$this->langCode[] = 'io';
			$this->langCode[] = 'is';
			$this->langCode[] = 'it';
			$this->langCode[] = 'iu';
			$this->langCode[] = 'ja';
			$this->langCode[] = 'jbo';
			$this->langCode[] = 'jp';
			$this->langCode[] = 'jv';
			$this->langCode[] = 'ka';
			$this->langCode[] = 'kaa';
			$this->langCode[] = 'kab';
			$this->langCode[] = 'kg';
			$this->langCode[] = 'ki';
			$this->langCode[] = 'kj';
			$this->langCode[] = 'kk';
			$this->langCode[] = 'kl';
			$this->langCode[] = 'km';
			$this->langCode[] = 'kn';
			$this->langCode[] = 'ko';
			$this->langCode[] = 'kr';
			$this->langCode[] = 'ks';
			$this->langCode[] = 'ksh';
			$this->langCode[] = 'ku';
			$this->langCode[] = 'kv';
			$this->langCode[] = 'kw';
			$this->langCode[] = 'ky';
			$this->langCode[] = 'la';
			$this->langCode[] = 'lad';
			$this->langCode[] = 'lb';
			$this->langCode[] = 'lbe';
			$this->langCode[] = 'lg';
			$this->langCode[] = 'li';
			$this->langCode[] = 'lij';
			$this->langCode[] = 'lmo';
			$this->langCode[] = 'ln';
			$this->langCode[] = 'lo';
			$this->langCode[] = 'lt';
			$this->langCode[] = 'lv';
			$this->langCode[] = 'map-bms';
			$this->langCode[] = 'mdf';
			$this->langCode[] = 'mg';
			$this->langCode[] = 'mh';
			$this->langCode[] = 'mi';
			$this->langCode[] = 'minnan';
			$this->langCode[] = 'mk';
			$this->langCode[] = 'ml';
			$this->langCode[] = 'mn';
			$this->langCode[] = 'mo';
			$this->langCode[] = 'mr';
			$this->langCode[] = 'ms';
			$this->langCode[] = 'mt';
			$this->langCode[] = 'mus';
			$this->langCode[] = 'my';
			$this->langCode[] = 'myv';
			$this->langCode[] = 'mzn';
			$this->langCode[] = 'na';
			$this->langCode[] = 'nah';
			$this->langCode[] = 'nan';
			$this->langCode[] = 'nap';
			$this->langCode[] = 'nb';
			$this->langCode[] = 'nds';
			$this->langCode[] = 'nds-nl';
			$this->langCode[] = 'ne';
			$this->langCode[] = 'new';
			$this->langCode[] = 'ng';
			$this->langCode[] = 'nl';
			$this->langCode[] = 'nn';
			$this->langCode[] = 'no';
			$this->langCode[] = 'nomcom';
			$this->langCode[] = 'nov';
			$this->langCode[] = 'nrm';
			$this->langCode[] = 'nv';
			$this->langCode[] = 'ny';
			$this->langCode[] = 'oc';
			$this->langCode[] = 'om';
			$this->langCode[] = 'or';
			$this->langCode[] = 'os';
			$this->langCode[] = 'pa';
			$this->langCode[] = 'pag';
			$this->langCode[] = 'pam';
			$this->langCode[] = 'pap';
			$this->langCode[] = 'pdc';
			$this->langCode[] = 'pi';
			$this->langCode[] = 'pih';
			$this->langCode[] = 'pl';
			$this->langCode[] = 'pms';
			$this->langCode[] = 'pnt';
			$this->langCode[] = 'ps';
			$this->langCode[] = 'pt';
			$this->langCode[] = 'qu';
			$this->langCode[] = 'rm';
			$this->langCode[] = 'rmy';
			$this->langCode[] = 'rn';
			$this->langCode[] = 'ro';
			$this->langCode[] = 'roa-rup';
			$this->langCode[] = 'roa-tara';
			$this->langCode[] = 'ru';
			$this->langCode[] = 'rw';
			$this->langCode[] = 'sa';
			$this->langCode[] = 'sah';
			$this->langCode[] = 'sc';
			$this->langCode[] = 'scn';
			$this->langCode[] = 'sco';
			$this->langCode[] = 'sd';
			$this->langCode[] = 'se';
			$this->langCode[] = 'sg';
			$this->langCode[] = 'sh';
			$this->langCode[] = 'si';
			$this->langCode[] = 'simple';
			$this->langCode[] = 'sk';
			$this->langCode[] = 'sl';
			$this->langCode[] = 'sm';
			$this->langCode[] = 'sn';
			$this->langCode[] = 'so';
			$this->langCode[] = 'sq';
			$this->langCode[] = 'sr';
			$this->langCode[] = 'srn';
			$this->langCode[] = 'ss';
			$this->langCode[] = 'st';
			$this->langCode[] = 'stq';
			$this->langCode[] = 'su';
			$this->langCode[] = 'sv';
			$this->langCode[] = 'sw';
			$this->langCode[] = 'szl';
			$this->langCode[] = 'ta';
			$this->langCode[] = 'te';
			$this->langCode[] = 'tet';
			$this->langCode[] = 'tg';
			$this->langCode[] = 'th';
			$this->langCode[] = 'ti';
			$this->langCode[] = 'tk';
			$this->langCode[] = 'tl';
			$this->langCode[] = 'tn';
			$this->langCode[] = 'to';
			$this->langCode[] = 'tokipona';
			$this->langCode[] = 'tp';
			$this->langCode[] = 'tpi';
			$this->langCode[] = 'tr';
			$this->langCode[] = 'ts';
			$this->langCode[] = 'tt';
			$this->langCode[] = 'tum';
			$this->langCode[] = 'tw';
			$this->langCode[] = 'ty';
			$this->langCode[] = 'udm';
			$this->langCode[] = 'ug';
			$this->langCode[] = 'uk';
			$this->langCode[] = 'ur';
			$this->langCode[] = 'uz';
			$this->langCode[] = 've';
			$this->langCode[] = 'vec';
			$this->langCode[] = 'vi';
			$this->langCode[] = 'vls';
			$this->langCode[] = 'vo';
			$this->langCode[] = 'wa';
			$this->langCode[] = 'war';
			$this->langCode[] = 'wo';
			$this->langCode[] = 'wuu';
			$this->langCode[] = 'xal';
			$this->langCode[] = 'xh';
			$this->langCode[] = 'yi';
			$this->langCode[] = 'yo';
			$this->langCode[] = 'za';
			$this->langCode[] = 'zea';
			$this->langCode[] = 'zh';
			$this->langCode[] = 'zh-cfr';
			$this->langCode[] = 'zh-classical';
			$this->langCode[] = 'zh-min-nan';
			$this->langCode[] = 'zh-yue';
			$this->langCode[] = 'zu';
			$this->langCode[] = 'zh-cn';
			$this->langCode[] = 'zh-tw';
			$this->langCode[] = 'mhr';
			$this->langCode[] = 'pnb';
			$this->langCode[] = 'ckb';
		}
		else
		{
			$this->be = array();
			$this->be[] = $b;
		}
	}

	public function isAction($edit) {

		if (!($edit instanceof Insertion))
			return false;
		else
		{
			$insEdit = $edit;
			$content = $insEdit->getContent();

			for ($i = 0; $i < count($content); $i++)
			{
				if ($content[$i]->kind == 
					WikiLexerConstants::INT_LINK_PREFIX && 
					in_array(strtolower(trim(($content[$i]->image), " \t:")), $this->langCode))
				return true;
			}
		}

		return false;
	}

	public function classify(&$editList) {
		$ret = array();
		$newEditList = array();

		for ($i = 0; $i < count($editList); $i++)
		{
			$b = $editList[$i];
			if ($this->isAction($b))
			{
				if ($b instanceof Insertion)
				{
					$ie = $b;
					$content = $ie->getContent();
					$iwStartPos = -1;

					for ($j = 0; $j < count($content); $j++)
					{
						if ($content[$j]->kind == 
							WikiLexerConstants::INT_LINK_PREFIX &&
							in_array(strtolower(trim(($content[$j]->image), " \t:")), $this->langCode))
						{
							$iwStartPos = $j - 1;
							continue;
						}

						if ($iwStartPos >= 0 && $content[$j]->kind == 
							WikiLexerConstants::INT_LINK_END)
						{
							$beforeContent = array_slice($content, 0, $iwStartPos);
							$actionContent = array_slice($content, $iwStartPos, $j+1-$iwStartPos);
							$remainder = array_slice($content, $j+1, count($content)-$j-1);

							if (count($beforeContent) > 0)
								$editList[] = new Insertion($ie->getPos(), $beforeContent);

							if (count($remainder) > 0)
								$editList[] = new Insertion($ie->getPos()+$j+1, $remainder);

							$ie->setContent($actionContent);

							break;
						}
					}
					$ret[] = new InterwikiLinks($ie);
				}
			}
			else {
				$newEditList[] = $b;
			}
		}

		$editList = $newEditList;
		
		return $ret;
	}
	
}
