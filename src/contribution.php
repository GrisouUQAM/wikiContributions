<?php

class Contribution {
    
    public $title;
    public $pageId;
    public $revisions;
    
    static public function fetch($contributor){
	$contributions = array();
	$api = new APIhelper();
	$json = $api->query_usercontribs($contributor);
	foreach($json['query']['usercontribs'] as $contrib){
	    $contributions[] = new Contribution($contrib,$contributor);
	}
	return $contributions;
    }
    
    public function __construct($contrib,$contributor) {
	$this->title = $contrib['title'];
	$this->pageId = $contrib['pageId'];
	$this->revisions = revision::fetch($contributor,$this->pageId);
    }
}

?>
