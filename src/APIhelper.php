<?php

class APIhelper{
    private $wikiURL = "http://fr.wikipedia.org/";
    private $apiURL = "/w/api.php?";
    
    private $params = array();
    private $url;
    private $result;
    
    public function __construct($format = 'json'){
	$this->params['format'] = $format;
    }
    
    /**
     * Permet de faire un requête dont l'action est de type "query" sur l'API
     * @param type $list {string} Nom de la liste
     * @param type $ucuser {string} Nom du contributeur
     * @param type $ucprop {array}[optionel] Array des champs désirés
     * @param type $converttitles {boolean}[optionel] ???
     * @return type {mixed} retourne le résultat de la requête dans le format spécifié. JSON par défaut
     * @throws Exception
     */
    public function query_usercontribs($ucuser,$ucprop = array("ids","title","title"),$converttitles=false){
	$this->clear();
	$this->params['action'] = 'query';
	$this->params['list'] = 'usercontribs';
	$this->params['ucuser'] = $ucuser;
	if(!is_array($ucprop)) {
	    throw new Exception("Un array de propriétés est attendu");
	} else {
	    $this->params['ucprop'] = htmlentities(implode("|", $ucprop));
	}
	$this->params['converttitles'] = $converttitles;
	
	return $this->get();
    }
    
    public function query_revisions($contributor,$pageId,$rvprop = array("ids","timestamp","user")){
	$this->clear();
	$this->params['action'] = 'query';
	$this->params['prop'] = 'revisions';
	$this->params['rvuser'] = $contributor;
	$this->params['pageids'] = $pageId;
	
	if(!is_array($rvprop)) {
	    throw new Exception("Un array de propriétés est attendu");
	} else {
	    $this->params['rvprop'] = htmlentities(implode("|", $rvprop));
	}
	return $this->get();
    }
    
    // action=parse&format=json&oldid=".$oldVersion."&prop=text
    public function action_parse($oldVersion,$prop="text") {
	$this->clear();
	$this->params['action'] = "parse";
	$this->params['oldid'] = $oldVersion;
	$this->params['prop'] = $prop;
	return $this->get();
    }
    
    public function query_getRevisionById($rev_id){
	$this->clear();
	$this->params['action'] = 'query';
	$this->params['prop'] = 'revisions';
	$this->params['revids'] = $rev_id;
	$this->params['rvprops'] = htmlentities(implode("|", array("ids",'timestamp','user')));
	return $this->get();
    }
    
    /**
     * Prépare l'URL de la requête API.
     */
    private function prepareURL(){
	$params = array();
	foreach($this->params as $param => $value){
	    $params[] = $param."=".$value;
	}
	
	$this->url = $this->wikiURL . $this->apiURL . implode("&",$params);
	//print $this->url;
    }
    
    /**
     * Retourne le résultat de la requête sur l'API
     * @return type
     */
    public function get(){
	if(!$this->result){
	    if(!$this->url) {
		$this->prepareURL();
	    }
	    $this->result = file_get_contents($this->url, true);
	}
	
	if($this->params['format'] == 'json'){
	    return json_decode($this->result,true);
	} else {
	    return $this->result;
	}
	
    }
    
    /**
     * Réinitialise la requête
     */
    public function clear(){
	$this->params = array("format"=>$this->params['format']);
	$this->result = "";
    }
    
    /**
     * Principalement pour déboguage
     */
    public function getURL(){
	return $this->url;
    }
}

?>
