<?php
if (isset($_GET['user'])) {
    $contributor = $_GET['user'];
}

if (isset($_GET['articleName'])) {
    $articleName = $_GET['articleName'];
}

if (isset($_GET['wiki'])) {
    $wikiurl = $_GET['wiki'];
}



$withoutSlash = explode('/', $wikiurl);
$url = $withoutSlash[0];
$completeUrl = "http://";
$completeUrl.= $url;


////////////////////////////////////////////////////////////Intervention survival////////////////////////////////////////////////////////////////////////////////

$result = '';
$tabResult = array();


// OCTET code
function dateConverter($givenDate) {
		if (gettype($givenDate) === 'integer') {
				return round($givenDate / 86400);        
		}
		else {
				$givenTime = strtotime($givenDate);
				return round($givenTime / 86400);
		}
}

// ASSA code
// params:$obj = json_decode($json, true);
// returns: date list

function afficherdateArticle($obj){
	$queries = $obj['query'];
	$pages= $queries['pages'];
	$result=array();

	foreach ($pages as $page) {
		 $i=0;
		 
		 
		 if (isset($page['revisions'])) {
    		$articlerevisions = $page['revisions'];
    		
    		foreach ($articlerevisions as $revision){
			 $dateintrv = $revision['timestamp'];
			 $result[$i] = $dateintrv;
			 $i++;
			}
		 }				 
	}
	return $result;
}							
		
	
function substructDate($date1){	
	$sec = time() - strtotime($date1);	
	return $sec;
}

function getDayNumber($obj){
	
	$tab = afficherdateArticle($obj);
	$ret = array();
	$i=0;
	foreach ($tab as $e) {
		$ret[$i] = dateConverter(substructDate($e));		
		$i++;
	}
	return $ret;	
}
	
$jsonurl=$completeUrl."/w/api.php?action=query&prop=revisions&format=json&rvuser=".$contributor."&titles=".$articleName;
$json = file_get_contents($jsonurl, true);
$obj = json_decode($json, true);

$tabResult = getDayNumber($obj);		
	
			
$result = '<h1>The author: '.$contributor.'</h1>
            <table>
				<tr>            
					<th>Articles</th>
					<th>Name</th>
					<th>Day number</th>									
				</tr>';				
				
foreach ($tabResult as $ligne){
	$result .= '<tr><td>'.$articleName.'</td><td>'.$contributor.'</td><td>'.$ligne.'</td></tr>';
}				
				
$result .= '</table>';

print $result;
