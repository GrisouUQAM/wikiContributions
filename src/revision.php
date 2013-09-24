<?php

class Revision {
    
    public $previousRevisionId;
    public $userRevisionId;
    public $usertimestamp;
    public $userRevisionContent;
    
    private $previousRevision;
    
    static public function fetch($contributor,$pageId){
	$revisions = array();
	$api = new APIhelper();
	$json = $api->query_revisions($contributor,$pageId);
	foreach($json['query']['pages'][$pageId]['revisions'] as $rev){
	    $revisions[] = new Revision($rev);
	}
    }
    
    public function __construct($rev) {
	$api = new APIhelper();
	$this->previousRevisionId = $rev->parentid;
	$this->userRevisionId = $rev->revid;
	$this->usertimestamp = $rev->timestamp;
	$oldParsed = $api->action_parse($this->oldVersion);
	$this->previousRevisionContent = $oldParsed['parse']['text']['*'];
	$userParsed = $api->action_parse($this->userVersion);
	$this->userRevisionContent = $userParsed['parse']['text']['*'];
    }
    
    public function getPreviousRevision(){
	$api = new APIhelper();
	$json = $api->query_getRevisionById($this->previousRevision);
	print $json;
    }
}

?>
