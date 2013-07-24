<?php
class EditHistoryAnalyzer {
    public static function setup() {
        # extension does setup stuff here.
        new EditHistoryAnalyzer;
        return true;
    }
    
    public function __construct() {
    	global $wgHooks;
    	
    	$wgHooks['SkinTemplateTabs'][] = $this;
    	$wgHooks['UnknownAction'][] = $this;
    }
    
    public function onSkinTemplateTabs( &$skin, &$content_actions ) {
    	global $wgTitle, $wgRequest;
    	
    	/* Only add tab when the page is an article in main namespce. */
		if ( $wgTitle->getNamespace() == 0 ) {
			
	    	$action = $wgRequest->getText( 'action' );
	    	
	    	$content_actions['actionanalysis'] = array(
	    		'class' => ($action == 'actionanalysis') ? 'selected' : false,
	    		'text' => wfMsg('weha_tabname'),
	    		'href' => $wgTitle->getLocalURL( 'action=actionanalysis' )
	    	);
	    	
		}
		
    	return true;
    }
    
    public function onUnknownAction( $action, $article ) {
    	global $wgOut, $wgRequest;
			
		if ($action == 'actionanalysis')
		{
			$wgOut->setPageTitle('Edit analysis of "'. $article->getTitle() .'"');
			
			$article_id = $article->getID();
			$latest_id = $article->getLatest();
			$input_revid = $wgRequest->getText('ea_revid');
			$input_oldid = $wgRequest->getText('ea_oldid');
	
			$dbr = wfGetDB( DB_SLAVE );
	
			$tbl_rev = $dbr->tableName('revision');
	
			if ( empty($input_revid) )
				$latest_rev = Revision::loadFromId($dbr, $latest_id);
			else
				$latest_rev = Revision::loadFromId($dbr, $input_revid);
	
			if ( empty($input_oldid) || $input_oldid == 'prev' )
				$previous_rev = Revision::loadFromId($dbr, $latest_rev->getParentId());
			else
				$previous_rev = Revision::loadFromId($dbr, $input_oldid);
				
			$next_rev = $dbr->select($tbl_rev, array('min(rev_id)'), "rev_page = $article_id AND rev_id > " . (empty($input_revid) ? $latest_id : $input_revid ) );
			$next_id = $next_rev->fetchRow();
			
			if ( !empty($previous_rev) )
			{
				$dbw = &wfGetDB(DB_MASTER);
				$dbw->begin();

				$current_exist = $dbw->select('weha_revision', array('rev_id', 'rev_md5'), 'rev_id=' . $latest_rev->getId());
				if ($current_exist->numRows() == 0)
				{
					$current_md5 = md5( $latest_rev->revText(), true );
					$dbw->insert('weha_revision',
						array('rev_id' => $latest_rev->getId(),
							  'rev_page' =>  $article_id,
							  'rev_md5' => $current_md5));
				}
				else
				{
					$row = $current_exist->fetchRow();
					$current_md5 = $row['rev_md5'];
				}
				
				$dbw->commit();
				
				$revert_exist = $dbr->select('weha_revision', array('max(rev_id)'),  "rev_page = $article_id AND rev_id < " . $latest_rev->getId() . " AND rev_md5 = UNHEX('" . bin2hex($current_md5) . "')");
				if ($revert_exist->numRows() > 0 ) {
					$revert_id = $revert_exist->fetchRow();
					if ( isset( $revert_id['max(rev_id)'] ) && $revert_id['max(rev_id)'] > 0) {
						$wgOut->addHTML( $wgOut->parse("__NOEDITSECTION__\n" . "== Revert detected ==") );
						$wgOut->addHTML( $revert_id['max(rev_id)'] . '<br />' );
					}
				}
				
				$dbw->begin();		
				$previous_exist = $dbw->select('weha_revision', array('rev_id', 'rev_md5'), 'rev_id=' . $previous_rev->getId());
				if ($previous_exist->numRows() == 0)
				{
					$previous_md5 = md5( $previous_rev->revText(), true );
					$dbw->insert('weha_revision',
						array('rev_id' => $previous_rev->getId(),
							  'rev_page' =>  $article_id,
							  'rev_md5' => $previous_md5));
				}
				else
				{
					$row = $previous_exist->fetchRow();
					$previous_md5 = $row['rev_md5'];
				}
				$dbw->commit();
			}
			
			$wgOut->addHTML('<form action="index.php" method="get" style="display: inline;">');
			$wgOut->addHTML('<input type="hidden" name="title" value="' . $article->getTitle() . '" />');
			$wgOut->addHTML('<input type="hidden" name="action" value="actionanalysis" />');
			$wgOut->addHTML('<input type="hidden" name="ea_revid" value="' . $latest_rev->getParentId() . '" />');
			$wgOut->addHTML('<input type="hidden" name="ea_oldid" value="prev" />');
			$wgOut->addHTML('<input type="submit" value="<<" />');
			$wgOut->addHTML('</form>');
	
			$wgOut->addHTML('<form action="index.php" method="get" style="display: inline;">');
			$wgOut->addHTML('<input type="hidden" name="title" value="' . $article->getTitle() . '" />');
			$wgOut->addHTML('<input type="hidden" name="action" value="actionanalysis" />');
	
			$options = '';
			$prev_options = '';
			$all_revs = $dbr->select($tbl_rev, array('rev_id', 'rev_timestamp'), "rev_page = $article_id", '__METHOD__', array('ORDER BY' => 'rev_id DESC'));
			
			$revs_count = $all_revs->numRows();
			
			foreach( $all_revs as $row ) {
				$options .= "<option value='" . $row->rev_id . "'". ($row->rev_id == $input_revid ? " selected='selected'" : ""). ">" . $revs_count . ":" . $row->rev_timestamp . "</option>\n";
				$prev_options .= "<option value='" . $row->rev_id . "'>"  . $revs_count-- . ":" . $row->rev_timestamp . "</option>\n";
			}
	
			$wgOut->addHTML('<select name="ea_revid">');
			$wgOut->addHTML($options);
			$wgOut->addHTML('</select>');
			$wgOut->addHTML('<select name="ea_oldid">');
			$wgOut->addHTML('<option value="prev" selected="selected">Previous revision</option>');
			$wgOut->addHTML($prev_options);
			$wgOut->addHTML('</select>');
			$wgOut->addHTML('<input type="submit" value="Analyze" />');
			$wgOut->addHTML('</form>');
	
			$wgOut->addHTML('<form action="index.php" method="get"  style="display: inline;">');
			$wgOut->addHTML('<input type="hidden" name="title" value="' . $article->getTitle() . '" />');
			$wgOut->addHTML('<input type="hidden" name="action" value="actionanalysis" />');
			$wgOut->addHTML('<input type="hidden" name="ea_revid" value="' . $next_id['min(rev_id)'] . '" />');
			$wgOut->addHTML('<input type="hidden" name="ea_oldid" value="prev" />');
			$wgOut->addHTML('<input type="submit" value=">>" />');
			$wgOut->addHTML('</form>');
			
			if ( !empty($previous_rev) )
			{
				$wgOut->addHTML('<br />Previous MD5: '. bin2hex($previous_md5) . '<br />');
				$wgOut->addHTML('Current MD5: ' . bin2hex($current_md5) . '<br />');
				
				if ($previous_md5 == $current_md5)
				{
					$wgOut->addHTML( $wgOut->parse("__NOEDITSECTION__\n" . "== Revert detected ==") );
				}
				else {
				require_once('WikiDiffFormatter.php');
				$ac = new WikiDiffFormatter( $previous_rev->revText(), $latest_rev->revText() );
				$result = $ac->outputDiff();
				$wgOut->addHTML( $wgOut->parse("__NOEDITSECTION__\n" . "== Categorized Edit Action ==") );
				$wgOut->addHTML(nl2br(htmlspecialchars($ac->categorize())));
				$wgOut->addHTML( $wgOut->parse("__NOEDITSECTION__\n" . "== Differences ==") );
				$wgOut->addHTML($result);
				}
			}
			else {
				$wgOut->addHTML( 'This is the earliest version of this article.' );
			}
			
			return false;
		}
		
		return true;
    }
    
}
